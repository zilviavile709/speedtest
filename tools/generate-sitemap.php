<?php
/**
 * Regenerates sitemap.xml from the pages on disk.
 *
 *     php tools/generate-sitemap.php
 *
 * lastmod comes from each file's modification time, so the dates are real
 * rather than invented — search engines discount sitemaps where every URL
 * claims to have changed today. Re-run after publishing or editing a page.
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require_once $root . '/includes/bootstrap.php';

$base = rtrim(SITE_URL, '/');

/** Pages that should never appear in the sitemap. */
const EXCLUDED = ['404.php', '500.php'];

/** path => [changefreq, priority] */
$pages = [
    ''                => ['weekly',  '1.0'],
    'blog/'           => ['weekly',  '0.8'],
    'methodology.php' => ['monthly', '0.6'],
    'about.php'       => ['yearly',  '0.5'],
    'contact.php'     => ['yearly',  '0.4'],
    'privacy.php'     => ['yearly',  '0.3'],
    'terms.php'       => ['yearly',  '0.3'],
];

// Blog posts are discovered rather than listed, so a new guide only needs to be
// dropped into blog/ and this script re-run.
foreach (glob($root . '/blog/*.php') as $post) {
    $name = basename($post);
    if ($name === 'index.php') {
        continue;
    }
    $pages['blog/' . $name] = ['monthly', '0.8'];
}

/** Resolve a URL path to the file that serves it. */
function sourceFile(string $root, string $path): string
{
    if ($path === '')      return $root . '/index.php';
    if ($path === 'blog/') return $root . '/blog/index.php';
    return $root . '/' . $path;
}

$entries = [];
foreach ($pages as $path => [$freq, $priority]) {
    if (in_array(basename($path), EXCLUDED, true)) {
        continue;
    }

    $file = sourceFile($root, $path);
    if (!is_file($file)) {
        fwrite(STDERR, "  skipped (missing): $path\n");
        continue;
    }

    $entries[] = sprintf(
        "  <url>\n    <loc>%s/%s</loc>\n    <lastmod>%s</lastmod>\n"
        . "    <changefreq>%s</changefreq>\n    <priority>%s</priority>\n  </url>",
        $base,
        htmlspecialchars($path, ENT_XML1),
        date('Y-m-d', (int) filemtime($file)),
        $freq,
        $priority
    );
}

$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
     . "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n"
     . implode("\n", $entries)
     . "\n</urlset>\n";

file_put_contents($root . '/sitemap.xml', $xml);

printf("Wrote sitemap.xml — %d URLs, %s\n", count($entries), number_format(strlen($xml)) . ' B');
