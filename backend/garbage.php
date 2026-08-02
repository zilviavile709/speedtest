<?php
// Download test endpoint — streams incompressible random data.
// ?ckSize=N requests N chunks of 1 MiB (capped to protect shared hosting).

@ini_set('zlib.output_compression', 'Off');
@ini_set('output_buffering', 'Off');
@ini_set('output_handler', '');

header('HTTP/1.1 200 OK');
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Connection: keep-alive');

while (ob_get_level() > 0) { ob_end_clean(); }

// ?bytes=N streams an exact byte count (used for the small transfer tests).
if (isset($_GET['bytes'])) {
    $bytes = intval($_GET['bytes']);
    if ($bytes < 1) { $bytes = 1; }
    if ($bytes > 104857600) { $bytes = 104857600; } // 100 MB ceiling
    $block = random_bytes(65536);
    $sent = 0;
    while ($sent < $bytes) {
        $n = min(65536, $bytes - $sent);
        echo $n === 65536 ? $block : substr($block, 0, $n);
        $sent += $n;
        flush();
    }
    exit;
}

// ?ckSize=N streams N chunks of 1 MiB.
$chunks = isset($_GET['ckSize']) ? intval($_GET['ckSize']) : 4;
if ($chunks < 1) { $chunks = 1; }
if ($chunks > 100) { $chunks = 100; }

$data = random_bytes(1048576); // 1 MiB of incompressible data

for ($i = 0; $i < $chunks; $i++) {
    echo $data;
    flush();
}
