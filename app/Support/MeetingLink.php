<?php

namespace App\Support;

class MeetingLink
{
    /**
     * Known online meeting platforms (host patterns).
     * Only YouTube is embedded in-app; others open in a new tab beside chat.
     */
    public const PLATFORMS = [
        'youtube' => [
            'label' => 'YouTube Live',
            'hosts' => ['youtube.com', 'youtu.be', 'youtube-nocookie.com', 'm.youtube.com'],
            'embeddable' => true,
        ],
        'zoom' => [
            'label' => 'Zoom',
            'hosts' => ['zoom.us', 'zoom.com', 'zoomgov.com'],
            'embeddable' => false,
        ],
        'google_meet' => [
            'label' => 'Google Meet',
            'hosts' => ['meet.google.com'],
            'embeddable' => false,
        ],
        'microsoft_teams' => [
            'label' => 'Microsoft Teams',
            'hosts' => ['teams.microsoft.com', 'teams.live.com'],
            'embeddable' => false,
        ],
        'webex' => [
            'label' => 'Cisco Webex',
            'hosts' => ['webex.com', 'cisco.com'],
            'embeddable' => false,
        ],
        'jitsi' => [
            'label' => 'Jitsi Meet',
            'hosts' => ['meet.jit.si', '8x8.vc', 'jitsi.org'],
            'embeddable' => false,
        ],
        'whereby' => [
            'label' => 'Whereby',
            'hosts' => ['whereby.com'],
            'embeddable' => false,
        ],
        'gotomeeting' => [
            'label' => 'GoTo Meeting',
            'hosts' => ['gotomeeting.com', 'goto.com'],
            'embeddable' => false,
        ],
        'skype' => [
            'label' => 'Skype',
            'hosts' => ['skype.com', 'join.skype.com'],
            'embeddable' => false,
        ],
        'discord' => [
            'label' => 'Discord',
            'hosts' => ['discord.com', 'discord.gg'],
            'embeddable' => false,
        ],
        'bigbluebutton' => [
            'label' => 'BigBlueButton',
            'hosts' => ['bigbluebutton.org'],
            'embeddable' => false,
        ],
    ];

    public static function analyze(?string $url): array
    {
        $url = trim((string) $url);
        $result = [
            'valid' => false,
            'known' => false,
            'use_iframe' => false,
            'embeddable' => false,
            'platform' => null,
            'platform_label' => null,
            'url' => $url,
        ];

        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return $result;
        }

        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        if (!in_array($scheme, ['http', 'https'], true)) {
            return $result;
        }

        $result['valid'] = true;

        if (YouTubeLive::isYouTubeUrl($url)) {
            $result['known'] = true;
            $result['platform'] = 'youtube';
            $result['platform_label'] = self::PLATFORMS['youtube']['label'];
            $result['embeddable'] = true;
            $result['use_iframe'] = true;

            return $result;
        }

        $host = strtolower($parts['host'] ?? '');
        $host = preg_replace('/^www\./', '', $host) ?? $host;

        foreach (self::PLATFORMS as $key => $meta) {
            if ($key === 'youtube') {
                continue;
            }
            foreach ($meta['hosts'] as $knownHost) {
                if ($host === $knownHost || str_ends_with($host, '.' . $knownHost)) {
                    $result['known'] = true;
                    $result['platform'] = $key;
                    $result['platform_label'] = $meta['label'];
                    $result['embeddable'] = false;
                    $result['use_iframe'] = false;

                    return $result;
                }
            }
        }

        return $result;
    }

    /**
     * Enrolled users always enter the lecture chat page; embed vs new-tab is decided there.
     */
    public static function shouldOpenInApp(?string $url): bool
    {
        $info = self::analyze($url);

        return $info['valid'];
    }
}
