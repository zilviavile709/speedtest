<footer class="site-footer">
  <div class="container">
    <div>© <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars(parse_url(SITE_URL, PHP_URL_HOST) ?: '', ENT_QUOTES, 'UTF-8'); ?></div>
    <nav class="footer-nav">
      <a href="/">Speed Test</a>
      <a href="/blog/">Guides</a>
      <a href="/about.php">About</a>
      <a href="/methodology.php">Methodology</a>
      <a href="/privacy.php">Privacy Policy</a>
      <a href="/terms.php">Terms of Use</a>
      <a href="/contact.php">Contact</a>
    </nav>
  </div>
</footer>
<script src="/assets/js/theme.js"></script>
</body>
</html>
