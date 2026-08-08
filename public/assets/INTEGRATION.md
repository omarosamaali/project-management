# Meeting Embed Integration API

Server-to-server APIs so an education platform (LMS) can create 1:1 meetings, mint host/student join links, and embed the meeting UI in an iframe.

Base URL examples:

- Local Docker: `https://192.168.1.147:3000` or `https://localhost:3000` (use the same host you put in `.env.docker` `PUBLIC_APP_URL`)
- Production: `https://meet.example.com`

All **management** endpoints under `/api/v1/*` require HMAC auth from your **LMS backend**. Never put `INTEGRATION_API_SECRET` in a browser or mobile app.

---

## 1. Prerequisites

| Item                          | Where                                                    |
| ----------------------------- | -------------------------------------------------------- |
| Base URL                      | `PUBLIC_APP_URL` on the meeting service                  |
| API Key                       | `INTEGRATION_API_KEY`                                    |
| API Secret                    | `INTEGRATION_API_SECRET`                                 |
| Allowed iframe parent origins | `INTEGRATION_ALLOWED_FRAME_ORIGINS` (comma-separated)    |
| Optional global webhook       | `INTEGRATION_WEBHOOK_URL` + `INTEGRATION_WEBHOOK_SECRET` |

Local Docker already includes sample credentials in `.env.docker`:

```env
INTEGRATION_API_KEY=edu_platform_dev_key
INTEGRATION_API_SECRET=edu_platform_dev_secret_change_me_32chars_min
INTEGRATION_ALLOWED_FRAME_ORIGINS=https://localhost:5173,http://localhost:5173,https://192.168.1.147:5173
INTEGRATION_WEBHOOK_SECRET=edu_webhook_secret_change_me_16
```

Rotate secrets before any shared or production use. After changing frame origins, restart the `web` container so `next.config` CSP/`frame-ancestors` picks them up.

---

## 2. Authentication (HMAC-SHA256)

Send these headers on every `/api/v1` management request (not on the browser session endpoint):

| Header        | Value                                        |
| ------------- | -------------------------------------------- |
| `X-Api-Key`   | Your API key                                 |
| `X-Timestamp` | Unix time in **seconds** or **milliseconds** |
| `X-Signature` | Hex HMAC-SHA256                              |

**Signature string (exact concatenation, no separators):**

```text
timestamp + METHOD + pathWithQuery + rawBody
```

- `METHOD` is uppercase (`POST`, `GET`, …).
- `pathWithQuery` is the request path including query string (e.g. `/api/v1/meetings`).
- `rawBody` is the exact JSON body bytes for POST; empty string for GET.
- Reject if `|now - timestamp| > 5 minutes`.

### Node.js

```js
import crypto from "node:crypto";

function signRequest({ secret, timestamp, method, path, body = "" }) {
    const payload = `${timestamp}${method.toUpperCase()}${path}${body}`;
    return crypto
        .createHmac("sha256", secret)
        .update(payload, "utf8")
        .digest("hex");
}

async function createMeeting(baseUrl, apiKey, apiSecret, meetingBody) {
    const path = "/api/v1/meetings";
    const body = JSON.stringify(meetingBody);
    const timestamp = String(Date.now());
    const signature = signRequest({
        secret: apiSecret,
        timestamp,
        method: "POST",
        path,
        body,
    });

    const res = await fetch(`${baseUrl}${path}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-Api-Key": apiKey,
            "X-Timestamp": timestamp,
            "X-Signature": signature,
        },
        body,
    });
    if (!res.ok) throw new Error(await res.text());
    return res.json();
}
```

### PHP

```php
function sign_request(string $secret, string $timestamp, string $method, string $path, string $body = ''): string {
  return hash_hmac('sha256', $timestamp . strtoupper($method) . $path . $body, $secret);
}
```

### cURL

```bash
BASE="https://192.168.1.147:3000"
KEY="edu_platform_dev_key"
SECRET="edu_platform_dev_secret_change_me_32chars_min"
PATH="/api/v1/meetings"
BODY='{"title":"Lesson 12","externalId":"lesson-12","host":{"externalUserId":"tutor-1","name":"Alex"},"participant":{"externalUserId":"student-9","name":"Sam"}}'
TS=$(date +%s%3N)
# Git Bash / Linux: use node if date lacks %3N
SIG=$(node -e "const c=require('crypto');const ts=process.argv[1],b=process.argv[2];console.log(c.createHmac('sha256','$SECRET').update(ts+'POST'+'$PATH'+b).digest('hex'))" "$TS" "$BODY")

curl -sS -X POST "$BASE$PATH" \
  -H "Content-Type: application/json" \
  -H "X-Api-Key: $KEY" \
  -H "X-Timestamp: $TS" \
  -H "X-Signature: $SIG" \
  -d "$BODY"
```

---

## 3. Endpoint reference

| Method | Path                                             | Auth       | Purpose                           |
| ------ | ------------------------------------------------ | ---------- | --------------------------------- |
| `POST` | `/api/v1/meetings`                               | HMAC       | Create meeting + join URLs        |
| `GET`  | `/api/v1/meetings/:meetingId`                    | HMAC       | Status (no fresh join tokens)     |
| `POST` | `/api/v1/meetings/:meetingId/end`                | HMAC       | End meeting                       |
| `POST` | `/api/v1/meetings/:meetingId/join-tokens`        | HMAC       | Mint/refresh a join URL           |
| `GET`  | `/api/v1/meetings/:meetingId/session?joinToken=` | Join token | Browser exchanges token → LiveKit |

### `POST /api/v1/meetings`

Request:

```json
{
    "title": "Lesson 12 tutoring",
    "externalId": "lms-session-abc",
    "host": { "externalUserId": "tutor-1", "name": "Alex Tutor" },
    "participant": { "externalUserId": "student-9", "name": "Sam Student" },
    "maxParticipants": 2,
    "metadata": { "courseId": "MATH-101" },
    "webhookUrl": "https://lms.example/webhooks/meetings",
    "embed": true
}
```

Response (shape):

```json
{
    "meetingId": "…",
    "status": "OPEN",
    "title": "Lesson 12 tutoring",
    "externalId": "lms-session-abc",
    "hostJoinUrl": "https://meet.example/meet/{id}?joinToken=…&embed=1",
    "participantJoinUrl": "https://meet.example/meet/{id}?joinToken=…&embed=1",
    "expiresAt": "…",
    "createdAt": "…"
}
```

`participant` is optional; if omitted, `participantJoinUrl` is `null` until you call `join-tokens`.

### `POST /api/v1/meetings/:meetingId/join-tokens`

```json
{
    "role": "participant",
    "externalUserId": "student-9",
    "name": "Sam Student",
    "embed": true,
    "ttlSeconds": 7200
}
```

Returns `{ role, joinToken, joinUrl, expiresAt }`.

### `POST /api/v1/meetings/:meetingId/end`

Empty body. Sets status `ENDED` and fires `meeting.ended` webhook when configured.

### `GET /api/v1/meetings/:meetingId/session?joinToken=…`

Used by the meeting UI (and usable by your own clients). No HMAC. Returns LiveKit JWT + URL + role. Invalid/expired tokens receive 401.

---

## 4. Embed host + student iframes

1. LMS backend creates the meeting (HMAC).
2. LMS page embeds `hostJoinUrl` for the tutor and `participantJoinUrl` for the student.
3. Parent origin **must** appear in `INTEGRATION_ALLOWED_FRAME_ORIGINS`.

```html
<iframe
    id="meeting-frame"
    src="HOST_OR_PARTICIPANT_JOIN_URL"
    allow="camera; microphone; autoplay; display-capture"
    style="width:100%;height:720px;border:0"
></iframe>

<script>
    window.addEventListener("message", (event) => {
        // Prefer checking event.origin against your meeting service origin
        const msg = event.data;
        if (!msg || msg.source !== "meeting-embed") return;
        if (msg.type === "meeting:ready") console.log("in call", msg);
        if (msg.type === "meeting:ended") console.log("left", msg);
        if (msg.type === "meeting:error") console.error(msg.message);
    });
</script>
```

**Required:** Without `allow="microphone; camera; …"` on a cross-origin iframe, Chromium leaves `navigator.mediaDevices` undefined and you get `Cannot read properties of undefined (reading 'getUserMedia')`. The meeting still joins muted if that happens; fix the LMS iframe markup to enable audio.

### Join URL parameters

| Param       | Meaning                                                             |
| ----------- | ------------------------------------------------------------------- |
| `joinToken` | Short-lived JWT (meetingId, role, externalUserId, displayName, exp) |
| `embed=1`   | Compact chrome; posts `meeting:*` messages to `window.parent`       |

Without a valid `joinToken`, the browser cannot mint a LiveKit session for that meeting via `/session`.

---

## 5. Webhooks

Delivery is **best-effort** in v1 (single POST, no durable outbox).

Configure globally (`INTEGRATION_WEBHOOK_URL`) and/or per meeting (`webhookUrl` on create).

Events:

| Event                        | When                                       |
| ---------------------------- | ------------------------------------------ |
| `meeting.participant_joined` | Join token exchanged for a LiveKit session |
| `meeting.ended`              | Meeting ended via API                      |

Payload:

```json
{
    "event": "meeting.ended",
    "meetingId": "…",
    "externalId": "lms-session-abc",
    "occurredAt": "2026-08-08T12:00:00.000Z",
    "data": {}
}
```

Headers:

| Header                | Value                                                |
| --------------------- | ---------------------------------------------------- |
| `X-Meeting-Timestamp` | Unix ms                                              |
| `X-Meeting-Signature` | `HMAC-SHA256(secret, timestamp + "." + rawBody)` hex |

Verify with `INTEGRATION_WEBHOOK_SECRET` (falls back to `INTEGRATION_API_SECRET` if unset).

---

## 6. Errors and rate limits

JSON errors look like `{ "error": { "message": "…", "code": "…" } }` with HTTP status:

| Status | Typical cause                                            |
| ------ | -------------------------------------------------------- |
| 401    | Bad API key, bad/expired signature or join token         |
| 403    | Signature / role mismatch                                |
| 400    | Validation                                               |
| 404    | Unknown meeting                                          |
| 409    | Meeting ended, duplicate open `externalId`, or room full |
| 429    | Rate limited (create uses stream-create limit)           |
| 503    | Integration env not configured                           |

---

## 7. Security checklist

- Sign requests only on the **LMS server**.
- Never ship `INTEGRATION_API_SECRET` to browsers.
- Treat `joinToken` URLs like session cookies (HTTPS, short TTL, do not log full URLs).
- Keep `INTEGRATION_ALLOWED_FRAME_ORIGINS` tight (your LMS origins only).
- Rotate API and webhook secrets periodically.

---

## 8. Local Docker test against this repo

```bash
docker compose --env-file .env.docker up -d --build
# Apply schema if needed (from host or web container):
# npx prisma db push
```

1. Create a meeting with the Node or cURL recipe above against your `PUBLIC_APP_URL`.
2. Open `hostJoinUrl` in a normal tab (full page) and `participantJoinUrl` inside an iframe served from an origin in `INTEGRATION_ALLOWED_FRAME_ORIGINS`.
3. Confirm mics connect and parent receives `meeting:ready`.
4. `POST /api/v1/meetings/:id/end` → status `ENDED`; webhook fires if URL is set.
5. Opening `/meet/:id` without `joinToken` must not grant LiveKit access via the integration session route.

**Certs:** Accept self-signed certs for both `https://YOUR_LAN_IP:3000` (app) and `https://YOUR_LAN_IP:7880` (LiveKit WSS). Signaling uses `wss://…:7880`. If you still see `room connection has timed out (signal)`, hard-refresh the iframe after recreating `web` so it picks up `NEXT_PUBLIC_LIVEKIT_URL`.

See also [MEETINGS.md](./MEETINGS.md) for first-party cookie flows (unchanged by this API).
