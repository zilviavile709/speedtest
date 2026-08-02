<?php
// Shared header. Set $pageTitle, $pageDesc, $canonical before including.
require_once __DIR__ . '/bootstrap.php';

$pageTitle = isset($pageTitle) ? $pageTitle : SITE_NAME . ' — Free Internet Speed Test';
$pageDesc = isset($pageDesc) ? $pageDesc : 'Test your internet download speed, upload speed, ping and jitter in seconds. Free, accurate, no app needed.';
$canonical = isset($canonical) ? $canonical : SITE_URL . '/';
$extraHead = isset($extraHead) ? $extraHead : '';
// Per-page social image; falls back to the branded default card.
$ogImage = isset($ogImage) ? $ogImage : SITE_URL . '/assets/img/og-image.png';
// Set $noIndex = true on error pages and anything that shouldn't reach the index.
$noIndex = isset($noIndex) ? $noIndex : false;

$e = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $e($pageTitle); ?></title>
<meta name="description" content="<?php echo $e($pageDesc); ?>">
<link rel="canonical" href="<?php echo $e($canonical); ?>">
<?php if ($noIndex): ?>
<meta name="robots" content="noindex, follow">
<?php endif; ?>
<meta property="og:title" content="<?php echo $e($pageTitle); ?>">
<meta property="og:description" content="<?php echo $e($pageDesc); ?>">
<meta property="og:url" content="<?php echo $e($canonical); ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?php echo $e(SITE_NAME); ?>">
<meta property="og:image" content="<?php echo $e($ogImage); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="<?php echo $e(SITE_NAME); ?> — free internet speed test measuring download, upload, ping and jitter">
<meta property="og:locale" content="en_US">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo $e($pageTitle); ?>">
<meta name="twitter:description" content="<?php echo $e($pageDesc); ?>">
<meta name="twitter:image" content="<?php echo $e($ogImage); ?>">
<?php if (GOOGLE_SITE_VERIFICATION !== ''): ?>
<meta name="google-site-verification" content="<?php echo $e(GOOGLE_SITE_VERIFICATION); ?>">
<?php endif; ?>
<?php if (BING_SITE_VERIFICATION !== ''): ?>
<meta name="msvalidate.01" content="<?php echo $e(BING_SITE_VERIFICATION); ?>">
<?php endif; ?>
<link rel="icon" href="/favicon.ico" sizes="32x32">
<link rel="icon" href="/assets/img/logo-mark.svg" type="image/svg+xml">
<link rel="icon" href="/assets/img/favicon-16x16.png" type="image/png" sizes="16x16">
<link rel="icon" href="/assets/img/favicon-32x32.png" type="image/png" sizes="32x32">
<link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png" sizes="180x180">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#0e7490" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#070b14" media="(prefers-color-scheme: dark)">
<meta name="apple-mobile-web-app-title" content="<?php echo $e(SITE_NAME); ?>">
<link rel="stylesheet" href="/assets/css/style.css">
<script>
// Applied before first paint so the chosen theme never flashes.
(function () {
  try {
    var s = localStorage.getItem('speedscore-theme');
    var dark = s ? s === 'dark'
      : window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (dark) document.documentElement.setAttribute('data-theme', 'dark');
  } catch (e) {}
})();
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "@id": "<?php echo $e(SITE_URL); ?>/#org",
      "name": "<?php echo $e(SITE_NAME); ?>",
      "url": "<?php echo $e(SITE_URL); ?>/",
      "description": "Free browser-based internet speed test measuring download, upload, latency, jitter and bufferbloat.",
      "logo": {
        "@type": "ImageObject",
        "url": "<?php echo $e(SITE_URL); ?>/assets/img/android-chrome-512x512.png",
        "width": 512,
        "height": 512
      },
      "parentOrganization": {
        "@type": "Organization",
        "name": "RioCloud Solutions",
        "url": "https://riocloudsolutions.com",
        "email": "info@riocloudsolutions.com",
        "telephone": "+91-7508583782",
        "address": { "@type": "PostalAddress", "addressLocality": "Chandigarh", "addressCountry": "IN" }
      }
    },
    {
      "@type": "WebSite",
      "@id": "<?php echo $e(SITE_URL); ?>/#website",
      "url": "<?php echo $e(SITE_URL); ?>/",
      "name": "<?php echo $e(SITE_NAME); ?>",
      "publisher": { "@id": "<?php echo $e(SITE_URL); ?>/#org" },
      "inLanguage": "en"
    }
  ]
}
</script>
<?php if (GA_MEASUREMENT_ID !== ''): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo rawurlencode(GA_MEASUREMENT_ID); ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
<?php if (GA_PRIVACY_MODE): ?>
gtag('config', <?php echo json_encode(GA_MEASUREMENT_ID); ?>, {
  anonymize_ip: true,
  allow_google_signals: false,
  allow_ad_personalization_signals: false
});
<?php else: ?>
gtag('config', <?php echo json_encode(GA_MEASUREMENT_ID); ?>);
<?php endif; ?>
</script>
<?php endif; ?>
<?php echo $extraHead; ?>
</head>
<body>
<header class="site-header">
  <div class="container">
    <a class="logo" href="/">
      <img class="logo-mark" src="/assets/img/logo-mark.svg" width="32" height="32" alt="" decoding="async">
      <?php echo $e(SITE_NAME); ?>
    </a>
    <nav class="main-nav">
      <a href="/">Speed Test</a>
      <a href="/blog/" class="hide-m">Guides</a>
      <a href="/about.php" class="hide-m">About</a>
      <a href="/contact.php">Contact</a>
      <button type="button" class="theme-toggle" id="themeToggle" aria-label="Switch colour theme">
        <span class="tt-icon tt-sun" aria-hidden="true">☀</span>
        <span class="tt-icon tt-moon" aria-hidden="true">☾</span>
      </button>
    </nav>
  </div>
</header>
