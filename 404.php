<?php
http_response_code(404);
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Page Not Found | ' . SITE_NAME;
$pageDesc = 'The page you are looking for does not exist. Run a free internet speed test instead.';
$canonical = SITE_URL . '/';
$noIndex = true;
include __DIR__ . '/includes/header.php';
?>
<main class="article" style="text-align:center;">
  <h1>404 — Page not found</h1>
  <p>The page you're looking for doesn't exist or has moved.</p>
  <div class="cta-box">
    <p><strong>While you're here — how fast is your internet?</strong></p>
    <p><a href="/">Run a free speed test →</a></p>
  </div>
  <p><a href="/blog/">Browse our internet speed guides</a></p>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
