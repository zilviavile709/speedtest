<?php
/**
 * Regenerates every brand asset from assets/img/logo-mark.svg.
 *
 *     php tools/generate-assets.php
 *
 * Produces the favicon set, the Apple touch icon, the PWA icons and the
 * Open Graph card. Edit the SVG (or the palette below) and re-run to rebrand —
 * nothing else in the codebase hardcodes the artwork.
 *
 * Requires the Imagick PHP extension with SVG and ICO support.
 */

declare(strict_types=1);

if (!extension_loaded('imagick')) {
    fwrite(STDERR, "Imagick extension is required.\n");
    exit(1);
}

$root   = dirname(__DIR__);
$imgDir = $root . '/assets/img';
$svgSrc = $imgDir . '/logo-mark.svg';

if (!is_file($svgSrc)) {
    fwrite(STDERR, "Missing source artwork: $svgSrc\n");
    exit(1);
}

@mkdir($imgDir, 0755, true);

$svg = file_get_contents($svgSrc);

// Palette — keep in sync with the CSS custom properties in assets/css/style.css.
const BRAND_DARK   = '#070b14';
const BRAND_CYAN   = '#22d3ee';
const BRAND_DIM    = '#9db0cd';
const BRAND_INDIGO = '#4f46e5';
const FONT_BOLD    = 'Nimbus-Sans-Bold';
const FONT_REG     = 'Nimbus-Sans-Regular';

/** Rasterise an SVG string at a given square size. */
function rasterise(string $svg, int $size): Imagick
{
    $im = new Imagick();
    $im->setBackgroundColor(new ImagickPixel('transparent'));
    $im->readImageBlob($svg);
    $im->setImageFormat('png32');
    $im->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1);
    return $im;
}

function report(string $path): void
{
    printf("  %-34s %s\n", basename($path), number_format((int) filesize($path)) . ' B');
}

// ---------------------------------------------------------------------------
// 1. Favicon + PWA icon set (rounded corners, transparent outside the radius)
// ---------------------------------------------------------------------------

echo "Icons\n";

$master = rasterise($svg, 1024);

$sizes = [
    'favicon-16x16.png'          => 16,
    'favicon-32x32.png'          => 32,
    'favicon-48x48.png'          => 48,
    'android-chrome-192x192.png' => 192,
    'android-chrome-512x512.png' => 512,
];

foreach ($sizes as $name => $size) {
    $icon = clone $master;
    $icon->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1);
    $icon->stripImage();
    $icon->writeImage("$imgDir/$name");
    report("$imgDir/$name");
    $icon->destroy();
}

// ---------------------------------------------------------------------------
// 2. Apple touch icon — full bleed, no transparency.
//    iOS applies its own corner mask, so shipping pre-rounded corners leaves
//    black wedges on the home screen.
// ---------------------------------------------------------------------------

$squareSvg = preg_replace('/\srx="\d+"\sry="\d+"/', ' rx="0" ry="0"', $svg, 1);
$apple = rasterise($squareSvg, 180);
$flat  = new Imagick();
$flat->newImage(180, 180, new ImagickPixel(BRAND_INDIGO));
$flat->setImageFormat('png24');
$flat->compositeImage($apple, Imagick::COMPOSITE_OVER, 0, 0);
$flat->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
$flat->stripImage();
$flat->writeImage("$imgDir/apple-touch-icon.png");
report("$imgDir/apple-touch-icon.png");
$apple->destroy();
$flat->destroy();

// ---------------------------------------------------------------------------
// 3. Multi-resolution favicon.ico (16 / 32 / 48) for legacy browsers and the
//    bare /favicon.ico request every crawler makes.
// ---------------------------------------------------------------------------

$ico = new Imagick();
foreach ([16, 32, 48] as $size) {
    $frame = clone $master;
    $frame->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1);
    $frame->setImageFormat('png32');
    $ico->addImage($frame);
    $frame->destroy();
}
$ico->setFormat('ico');
$ico->writeImages($root . '/favicon.ico', true);
report($root . '/favicon.ico');
$ico->destroy();
$master->destroy();

// ---------------------------------------------------------------------------
// 4. Open Graph / Twitter card, 1200x630
// ---------------------------------------------------------------------------

echo "\nSocial card\n";

$W = 1200;
$H = 630;

$card = new Imagick();
$card->newImage($W, $H, new ImagickPixel(BRAND_DARK));
$card->setImageFormat('png24');

// Diagonal accent wash in the top-left, faded out so text stays legible.
$glow = new Imagick();
$glow->newPseudoImage($W, $H, 'radial-gradient:' . BRAND_CYAN . '-' . BRAND_DARK);
$glow->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
$glow->evaluateImage(Imagick::EVALUATE_MULTIPLY, 0.30, Imagick::CHANNEL_ALPHA);
$glow->rollImage(-380, -210);
$card->compositeImage($glow, Imagick::COMPOSITE_SCREEN, 0, 0);
$glow->destroy();

// Logo mark
$mark = rasterise($svg, 108);
$card->compositeImage($mark, Imagick::COMPOSITE_OVER, 80, 68);
$mark->destroy();

$draw = new ImagickDraw();

// Wordmark, vertically centred against the 108px mark
$draw->setFillColor(new ImagickPixel('#ffffff'));
$draw->setFont(FONT_BOLD);
$draw->setFontSize(58);
$draw->annotation(212, 142, 'Speed Score');

// Hairline rule under the masthead
$draw->setStrokeColor(new ImagickPixel('#22304d'));
$draw->setStrokeWidth(2);
$draw->line(80, 228, $W - 80, 228);
$draw->setStrokeWidth(0);
$draw->setStrokeColor(new ImagickPixel('transparent'));

// Headline
$draw->setFillColor(new ImagickPixel('#ffffff'));
$draw->setFontSize(76);
$draw->annotation(80, 340, 'Free Internet Speed Test');

// Supporting line
$draw->setFont(FONT_REG);
$draw->setFontSize(35);
$draw->setFillColor(new ImagickPixel(BRAND_DIM));
$draw->annotation(80, 400, 'Download · Upload · Ping · Jitter · Latency under load');

// Feature chips
$chips = ['No signup', 'No stored results', 'Open source'];
$x = 80;
foreach ($chips as $chip) {
    $metrics = $card->queryFontMetrics($draw, $chip);
    $w = (int) $metrics['textWidth'] + 44;

    $chipDraw = new ImagickDraw();
    $chipDraw->setFillColor(new ImagickPixel('#101a2c'));
    $chipDraw->setStrokeColor(new ImagickPixel('#22304d'));
    $chipDraw->setStrokeWidth(2);
    $chipDraw->roundRectangle($x, 452, $x + $w, 512, 30, 30);
    $card->drawImage($chipDraw);
    $chipDraw->destroy();

    $draw->setFillColor(new ImagickPixel(BRAND_CYAN));
    $draw->setFontSize(28);
    $draw->annotation($x + 22, 491, $chip);

    $x += $w + 16;
}

// Footer URL
$draw->setFont(FONT_BOLD);
$draw->setFontSize(32);
$draw->setFillColor(new ImagickPixel(BRAND_CYAN));
$draw->annotation(80, 578, 'speedtest.scorelens.space');

$card->drawImage($draw);
$draw->destroy();

$card->stripImage();
$card->setImageCompressionQuality(92);
$card->writeImage("$imgDir/og-image.png");
report("$imgDir/og-image.png");
$card->destroy();

echo "\nDone.\n";
