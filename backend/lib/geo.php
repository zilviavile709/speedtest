<?php
declare(strict_types=1);

/**
 * Best-effort IP → network/location lookup with an on-disk cache.
 *
 * Design notes:
 *  - Never blocks the speed test. Short timeouts, and every failure path
 *    returns null rather than throwing.
 *  - Results are cached per /24 (IPv4) or /48 (IPv6) so we neither hammer the
 *    free upstream quota nor store anything that identifies a single visitor.
 *  - Two providers are tried in order; both are keyless free tiers.
 */

const GEO_CACHE_TTL = 86400;      // 24h
const GEO_HTTP_TIMEOUT = 2;       // seconds — must never stall a page render

function geo_cache_dir(): string
{
    return dirname(__DIR__, 2) . '/data/geocache';
}

/** Coarsen an IP so the cache key can't single out one household. */
function geo_prefix(string $ip): ?string
{
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $p = explode('.', $ip);
        return $p[0] . '.' . $p[1] . '.' . $p[2] . '.0';
    }
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        $bin = @inet_pton($ip);
        if ($bin === false) {
            return null;
        }
        // Keep the first 48 bits, zero the rest.
        $masked = substr($bin, 0, 6) . str_repeat("\0", 10);
        $out = @inet_ntop($masked);
        return $out === false ? null : $out;
    }
    return null;
}

function geo_cache_get(string $key): ?array
{
    $file = geo_cache_dir() . '/' . sha1($key) . '.json';
    if (!is_readable($file)) {
        return null;
    }
    if (time() - (int) @filemtime($file) > GEO_CACHE_TTL) {
        return null;
    }
    $raw = @file_get_contents($file);
    if ($raw === false) {
        return null;
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : null;
}

function geo_cache_put(string $key, array $data): void
{
    $dir = geo_cache_dir();
    if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
        return;
    }
    $file = $dir . '/' . sha1($key) . '.json';
    // Atomic write so a concurrent reader never sees a half-written file.
    $tmp = $file . '.' . getmypid() . '.tmp';
    if (@file_put_contents($tmp, json_encode($data)) !== false) {
        @rename($tmp, $file);
    }
}

function geo_http_get(string $url): ?array
{
    $ctx = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => GEO_HTTP_TIMEOUT,
            'header' => "Accept: application/json\r\nUser-Agent: Speed Score/1.0 (+https://speedtest.scorelens.space)\r\n",
            'ignore_errors' => true,
        ],
        'https' => [
            'method' => 'GET',
            'timeout' => GEO_HTTP_TIMEOUT,
        ],
    ]);
    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false || $raw === '') {
        return null;
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : null;
}

/** Normalised shape returned to callers. */
function geo_blank(): array
{
    return ['isp' => null, 'asn' => null, 'city' => null, 'region' => null, 'country' => null, 'countryCode' => null];
}

function geo_from_ipwhois(string $ip): ?array
{
    $d = geo_http_get('https://ipwho.is/' . rawurlencode($ip) . '?fields=success,city,region,country,country_code,connection');
    if (!$d || empty($d['success'])) {
        return null;
    }
    $conn = is_array($d['connection'] ?? null) ? $d['connection'] : [];
    $asn = isset($conn['asn']) && $conn['asn'] ? 'AS' . $conn['asn'] : null;
    return [
        'isp' => $conn['isp'] ?? ($conn['org'] ?? null),
        'asn' => $asn,
        'city' => $d['city'] ?? null,
        'region' => $d['region'] ?? null,
        'country' => $d['country'] ?? null,
        'countryCode' => $d['country_code'] ?? null,
    ];
}

function geo_from_ipapi(string $ip): ?array
{
    $d = geo_http_get('http://ip-api.com/json/' . rawurlencode($ip) . '?fields=status,country,countryCode,regionName,city,isp,as');
    if (!$d || ($d['status'] ?? '') !== 'success') {
        return null;
    }
    $asn = null;
    if (!empty($d['as']) && preg_match('/^AS\d+/', (string) $d['as'], $m)) {
        $asn = $m[0];
    }
    return [
        'isp' => $d['isp'] ?? null,
        'asn' => $asn,
        'city' => $d['city'] ?? null,
        'region' => $d['regionName'] ?? null,
        'country' => $d['country'] ?? null,
        'countryCode' => $d['countryCode'] ?? null,
    ];
}

/**
 * Look up an IP. Returns the normalised array, or a blank record if every
 * provider fails — callers should treat null fields as "unknown", not an error.
 */
function geo_lookup(string $ip): array
{
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return geo_blank();
    }
    // Private / reserved ranges will never resolve; skip the round trip.
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return geo_blank();
    }

    $key = geo_prefix($ip) ?? $ip;
    $hit = geo_cache_get($key);
    if ($hit !== null) {
        return $hit + geo_blank();
    }

    $res = geo_from_ipwhois($ip) ?? geo_from_ipapi($ip);
    if ($res === null) {
        // Cache the miss briefly too, so an outage doesn't cause a lookup storm.
        geo_cache_put($key, geo_blank());
        return geo_blank();
    }

    geo_cache_put($key, $res);
    return $res;
}

/** Where this server itself sits — used to label the test endpoint. */
function geo_server_location(): array
{
    $key = 'self:' . ($_SERVER['SERVER_ADDR'] ?? 'unknown');
    $hit = geo_cache_get($key);
    if ($hit !== null) {
        return $hit + geo_blank();
    }
    $ip = $_SERVER['SERVER_ADDR'] ?? '';
    $res = geo_blank();
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        $res = geo_from_ipwhois($ip) ?? geo_from_ipapi($ip) ?? geo_blank();
    }
    geo_cache_put($key, $res);
    return $res;
}
