<?php
/**
 * Loads configuration and backfills anything a stale config.php is missing, so
 * a fresh clone (or an out-of-date copy) always boots instead of fataling on an
 * undefined constant.
 */

$speedScoreConfig = __DIR__ . '/config.php';
require_once file_exists($speedScoreConfig) ? $speedScoreConfig : __DIR__ . '/config.example.php';
unset($speedScoreConfig);

foreach ([
    'SITE_URL'          => 'https://speedtest.scorelens.space',
    'SITE_NAME'         => 'Speed Score',
    'REPO_URL'          => 'https://github.com/iampopye/speedtest',
    'GA_MEASUREMENT_ID' => '',
    'GA_PRIVACY_MODE'   => true,
    'GOOGLE_SITE_VERIFICATION' => '',
    'BING_SITE_VERIFICATION'   => '',
] as $name => $fallback) {
    if (!defined($name)) {
        define($name, $fallback);
    }
}
