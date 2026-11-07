<?php
// Anonymous playtest logging for the website build only (the Poki build never calls this).
// Stores one JSON line per heartbeat: random ids, seconds played, moonlet, screen, device. No IPs, no personal data.
require __DIR__ . '/auth.php';
header('Cache-Control: no-store');
if (!mr_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(204); exit; }
$raw = file_get_contents('php://input', false, null, 0, 2048);
$in = json_decode($raw, true);
if (!is_array($in)) { http_response_code(204); exit; }
$clean = fn($v, $n) => substr(preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$v), 0, $n);
$row = [
  'ts'  => time(),
  'sid' => $clean($in['sid'] ?? '', 24),
  'vid' => $clean($in['vid'] ?? '', 24),
  't'   => max(0, min(86400, (int)($in['t'] ?? 0))),
  'moon'=> max(0, min(9999, (int)($in['moon'] ?? 0))),
  'scr' => $clean($in['scr'] ?? '', 12),
  'dev' => ($in['dev'] ?? '') === 'phone' ? 'phone' : 'desktop',
  'ret' => !empty($in['ret']) ? 1 : 0,
  'end' => !empty($in['end']) ? 1 : 0,
];
if ($row['sid'] === '') { http_response_code(204); exit; }
$dir = __DIR__ . '/private/data';
if (!is_dir($dir)) @mkdir($dir, 0750, true);
@file_put_contents($dir . '/sessions-' . gmdate('Y-m-d') . '.jsonl', json_encode($row) . "\n", FILE_APPEND | LOCK_EX);
http_response_code(204);
