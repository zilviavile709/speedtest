<?php
declare(strict_types=1);

/**
 * Connection info endpoint.
 * Returns the visitor's IP plus best-effort network/location detail, and the
 * location of the server they are testing against. Nothing is stored beyond a
 * coarse (/24, /48) geo cache — see backend/lib/geo.php.
 */

require __DIR__ . '/lib/geo.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

function client_ip(): string
{
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(explode(',', (string) $_SERVER[$key])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '';
}

/** "City, Country" — skipping whichever parts are unknown. */
function place(array $g): ?string
{
    $parts = array_filter([$g['city'] ?? null, $g['country'] ?? null]);
    return $parts ? implode(', ', $parts) : null;
}

$ip = client_ip();
$client = $ip !== '' ? geo_lookup($ip) : geo_blank();
$server = geo_server_location();

echo json_encode([
    // Kept for backward compatibility with earlier callers.
    'ip' => $ip,
    'client' => [
        'ip' => $ip,
        'isp' => $client['isp'],
        'asn' => $client['asn'],
        'city' => $client['city'],
        'region' => $client['region'],
        'country' => $client['country'],
        'countryCode' => $client['countryCode'],
        'location' => place($client),
    ],
    'server' => [
        'host' => $_SERVER['HTTP_HOST'] ?? 'speedtest.scorelens.space',
        'city' => $server['city'],
        'country' => $server['country'],
        'location' => place($server),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
