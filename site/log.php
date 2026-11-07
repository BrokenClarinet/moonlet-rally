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
$day = $dir . '/sessions-' . gmdate('Y-m-d') . '.jsonl';
if (@filesize($day) > 20 * 1024 * 1024) { http_response_code(204); exit; } // disk guard: 20 MB a day is far beyond any real playtest
// rate cap: one line per session every 25 s, plus a single closing line, so a rogue client can't flood the log
$fh = @fopen($dir . '/ratelimit.json', 'c+');
if ($fh && flock($fh, LOCK_EX)) {
  $rl = json_decode(stream_get_contents($fh), true) ?: [];
  $last = $rl[$row['sid']] ?? null; $now = $row['ts'];
  $ok = !$last || $now - $last[0] >= 25 || ($row['end'] && empty($last[1]));
  if ($ok) {
    $rl[$row['sid']] = [$now, $row['end']];
    if (count($rl) > 500) $rl = array_filter($rl, fn($v) => $now - $v[0] < 3600);
    ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($rl));
  }
  flock($fh, LOCK_UN); fclose($fh);
  if (!$ok) { http_response_code(204); exit; }
}
@file_put_contents($day, json_encode($row) . "\n", FILE_APPEND | LOCK_EX);
http_response_code(204);
