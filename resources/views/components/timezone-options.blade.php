@php $sel = $selected ?? 'Asia/Dubai'; @endphp
<optgroup label="الخليج العربي">
    <option value="Asia/Dubai"       {{ $sel === 'Asia/Dubai'    ? 'selected' : '' }}>الإمارات - دبي (GMT+4)</option>
    <option value="Asia/Riyadh"      {{ $sel === 'Asia/Riyadh'   ? 'selected' : '' }}>السعودية - الرياض (GMT+3)</option>
    <option value="Asia/Kuwait"      {{ $sel === 'Asia/Kuwait'   ? 'selected' : '' }}>الكويت (GMT+3)</option>
    <option value="Asia/Bahrain"     {{ $sel === 'Asia/Bahrain'  ? 'selected' : '' }}>البحرين (GMT+3)</option>
    <option value="Asia/Muscat"      {{ $sel === 'Asia/Muscat'   ? 'selected' : '' }}>عُمان - مسقط (GMT+4)</option>
    <option value="Asia/Qatar"       {{ $sel === 'Asia/Qatar'    ? 'selected' : '' }}>قطر (GMT+3)</option>
</optgroup>
<optgroup label="المشرق العربي">
    <option value="Asia/Baghdad"     {{ $sel === 'Asia/Baghdad'  ? 'selected' : '' }}>العراق - بغداد (GMT+3)</option>
    <option value="Asia/Beirut"      {{ $sel === 'Asia/Beirut'   ? 'selected' : '' }}>لبنان - بيروت (GMT+2/3)</option>
    <option value="Asia/Damascus"    {{ $sel === 'Asia/Damascus' ? 'selected' : '' }}>سوريا - دمشق (GMT+2/3)</option>
    <option value="Asia/Amman"       {{ $sel === 'Asia/Amman'    ? 'selected' : '' }}>الأردن - عمّان (GMT+2/3)</option>
    <option value="Asia/Jerusalem"   {{ $sel === 'Asia/Jerusalem'? 'selected' : '' }}>فلسطين (GMT+2/3)</option>
    <option value="Asia/Aden"        {{ $sel === 'Asia/Aden'     ? 'selected' : '' }}>اليمن - عدن (GMT+3)</option>
</optgroup>
<optgroup label="أفريقيا">
    <option value="Africa/Cairo"     {{ $sel === 'Africa/Cairo'  ? 'selected' : '' }}>مصر - القاهرة (GMT+2/3)</option>
    <option value="Africa/Tripoli"   {{ $sel === 'Africa/Tripoli'? 'selected' : '' }}>ليبيا - طرابلس (GMT+2)</option>
    <option value="Africa/Tunis"     {{ $sel === 'Africa/Tunis'  ? 'selected' : '' }}>تونس (GMT+1)</option>
    <option value="Africa/Algiers"   {{ $sel === 'Africa/Algiers'? 'selected' : '' }}>الجزائر (GMT+1)</option>
    <option value="Africa/Casablanca"{{ $sel === 'Africa/Casablanca'? 'selected' : '' }}>المغرب - الدار البيضاء (GMT+1)</option>
    <option value="Africa/Khartoum"  {{ $sel === 'Africa/Khartoum'? 'selected' : '' }}>السودان - الخرطوم (GMT+3)</option>
</optgroup>
<optgroup label="أوروبا">
    <option value="Europe/London"    {{ $sel === 'Europe/London'  ? 'selected' : '' }}>المملكة المتحدة - لندن (GMT+0/1)</option>
    <option value="Europe/Paris"     {{ $sel === 'Europe/Paris'   ? 'selected' : '' }}>فرنسا - باريس (GMT+1/2)</option>
    <option value="Europe/Istanbul"  {{ $sel === 'Europe/Istanbul'? 'selected' : '' }}>تركيا - إسطنبول (GMT+3)</option>
</optgroup>
<optgroup label="آسيا">
    <option value="Asia/Kolkata"     {{ $sel === 'Asia/Kolkata'  ? 'selected' : '' }}>الهند (GMT+5:30)</option>
    <option value="Asia/Karachi"     {{ $sel === 'Asia/Karachi'  ? 'selected' : '' }}>باكستان - كراتشي (GMT+5)</option>
    <option value="Asia/Singapore"   {{ $sel === 'Asia/Singapore'? 'selected' : '' }}>سنغافورة (GMT+8)</option>
</optgroup>
<optgroup label="أمريكا">
    <option value="America/New_York" {{ $sel === 'America/New_York'    ? 'selected' : '' }}>نيويورك (GMT-5/-4)</option>
    <option value="America/Chicago"  {{ $sel === 'America/Chicago'     ? 'selected' : '' }}>شيكاغو (GMT-6/-5)</option>
    <option value="America/Los_Angeles"{{ $sel === 'America/Los_Angeles'? 'selected' : '' }}>لوس أنجلوس (GMT-8/-7)</option>
</optgroup>
