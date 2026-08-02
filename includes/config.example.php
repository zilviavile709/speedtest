<?php
/**
 * Speed Score configuration.
 *
 * Copy this file to config.php and fill in your own values:
 *     cp includes/config.example.php includes/config.php
 *
 * config.php is gitignored so your analytics ID and domain never end up in a
 * public fork. Every value below is optional — with the defaults untouched the
 * site runs fine, just without analytics.
 */

// ---------------------------------------------------------------------------
// Site identity
// ---------------------------------------------------------------------------

/** Public base URL, no trailing slash. Used for canonicals, OG tags, sitemap. */
define('SITE_URL', 'https://speedtest.scorelens.space');

/** Product name shown in the header, titles and schema markup. */
define('SITE_NAME', 'Speed Score');

/** Where the source lives. Set to '' to hide the GitHub link in the nav. */
define('REPO_URL', 'https://github.com/iampopye/speedtest');

// ---------------------------------------------------------------------------
// Analytics
// ---------------------------------------------------------------------------

/**
 * GA4 measurement ID, e.g. 'G-XXXXXXXXXX'.
 * Leave empty to disable analytics entirely — when empty, no Google script is
 * requested at all, so there is nothing to consent to and nothing to block.
 */
define('GA_MEASUREMENT_ID', '');

/**
 * Send GA4 hits with IP anonymisation and no ad personalisation signals.
 * Recommended: keeps the tool consistent with its "we store nothing" promise.
 */
define('GA_PRIVACY_MODE', true);

// ---------------------------------------------------------------------------
// Search engine verification
// ---------------------------------------------------------------------------

/**
 * Google Search Console meta-tag verification token — the `content` value from
 * the <meta name="google-site-verification"> snippet GSC shows you. Leave empty
 * to omit the tag.
 */
define('GOOGLE_SITE_VERIFICATION', '');

/** Bing Webmaster Tools token. Bing's index is what feeds Microsoft Copilot. */
define('BING_SITE_VERIFICATION', '');
