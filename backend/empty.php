<?php
// Ping + upload sink endpoint. Accepts any payload, discards it, returns instantly.
header('HTTP/1.1 200 OK');
header('Content-Type: text/plain');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Connection: keep-alive');
// Drain request body (uploads) without storing anything.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fp = fopen('php://input', 'r');
    while (!feof($fp)) { fread($fp, 1048576); }
    fclose($fp);
}
echo 'ok';
