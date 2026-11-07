<?php
// Playtest panel: reads the anonymous session log written by log.php and compares it with Poki's bar and our targets.
require __DIR__ . '/auth.php';
if (!mr_logged_in()) { header('Location: index.php'); exit; }
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

$from = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['from'] ?? '') ? $_GET['from'] : gmdate('Y-m-d', time() - 13 * 86400);
$to   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['to'] ?? '') ? $_GET['to'] : gmdate('Y-m-d');

// last heartbeat per session = how far that session got
$sess = []; $visits = [];
foreach (glob(__DIR__ . '/private/data/sessions-*.jsonl') ?: [] as $f) {
  $day = substr(basename($f, '.jsonl'), 9);
  if ($day < $from || $day > $to) continue;
  foreach (file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $r = json_decode($line, true); if (!is_array($r) || empty($r['sid'])) continue;
    $s = $r['sid'];
    if (!isset($sess[$s]) || $r['t'] >= $sess[$s]['t']) $sess[$s] = $r + ['day' => $day];
    if (!empty($r['vid'])) $visits[$r['vid']][gmdate('Y-m-d', $r['ts'])] = true;
  }
}

function summarize($list) {
  $n = count($list); if (!$n) return null;
  $ts = array_map(fn($r) => $r['t'], $list); sort($ts);
  $med = $n % 2 ? $ts[intdiv($n, 2)] : ($ts[$n / 2 - 1] + $ts[$n / 2]) / 2;
  return [
    'n' => $n,
    'p1' => count(array_filter($ts, fn($t) => $t >= 60)) / $n,
    'p3' => count(array_filter($ts, fn($t) => $t >= 180)) / $n,
    'med' => $med, 'avg' => array_sum($ts) / $n,
  ];
}
$all = array_values($sess);
$groups = ['All players' => $all,
  'Desktop' => array_values(array_filter($all, fn($r) => $r['dev'] === 'desktop')),
  'Phone' => array_values(array_filter($all, fn($r) => $r['dev'] === 'phone'))];

// next-day return: visitors seen on day D and again on D+1
$ret = 0; $retBase = 0;
foreach ($visits as $days) foreach ($days as $d => $_) {
  $next = gmdate('Y-m-d', strtotime($d) + 86400);
  if ($next > $to) continue; $retBase++; if (isset($days[$next])) $ret++;
}

$quitMoon = []; $quitScr = [];
foreach ($all as $r) { $quitMoon[$r['moon']] = ($quitMoon[$r['moon']] ?? 0) + 1; $quitScr[$r['scr'] ?: '?'] = ($quitScr[$r['scr'] ?: '?'] ?? 0) + 1; }
ksort($quitMoon); arsort($quitScr);

$roman = function ($n) { $r = ''; foreach ([1000=>'M',900=>'CM',500=>'D',400=>'CD',100=>'C',90=>'XC',50=>'L',40=>'XL',10=>'X',9=>'IX',5=>'V',4=>'IV',1=>'I'] as $v => $s) while ($n >= $v) { $r .= $s; $n -= $v; } return $r; };
$fmtT = fn($s) => sprintf('%d:%02d', intdiv((int)$s, 60), (int)$s % 60);
$pct = fn($x) => round($x * 100) . '%';
$cls = fn($ok) => $ok ? 'ok' : 'bad';
$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES);
?><!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Playtest stats</title>
<style>
  :root { --ink:#2a1838; --paper:#fff6ea; --panel:#3a2758; --panel2:#4c3470; --sun:#ffc43d; --mint:#9be37a; --coral:#ff6b4a; }
  body { margin:0; background:#1c1650; color:var(--paper); font:15px/1.45 ui-rounded,"Trebuchet MS",system-ui,sans-serif; }
  main { max-width:960px; margin:0 auto; padding:24px 16px 48px; }
  h1 { color:var(--sun); margin:0 0 4px; } h2 { color:var(--sun); font-size:18px; margin:24px 0 8px; }
  .card { background:var(--panel); border:3px solid var(--ink); border-radius:16px; padding:14px 16px; box-shadow:3px 4px 0 var(--ink); overflow-x:auto; }
  table { border-collapse:collapse; width:100%; } th, td { text-align:left; padding:6px 8px; border-bottom:2px solid var(--panel2); white-space:nowrap; }
  th { font-size:12px; opacity:.8; } .ok { color:var(--mint); font-weight:800; } .bad { color:var(--coral); font-weight:800; }
  form { display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin:12px 0; } input, button { font:inherit; padding:4px 8px; border-radius:8px; border:2px solid var(--ink); }
  button { background:var(--sun); font-weight:800; cursor:pointer; } .muted { opacity:.75; font-size:13px; } a { color:#7fe3ff; }
</style></head><body><main>
<h1>Playtest stats</h1>
<p class="muted">Anonymous sessions from the website build, <?= $h($from) ?> to <?= $h($to) ?> (UTC). Green = meets the target. Poki's own bar: 25% of players past 3 minutes.</p>
<form><label>From <input type="date" name="from" value="<?= $h($from) ?>"></label><label>To <input type="date" name="to" value="<?= $h($to) ?>"></label><button>Show</button> <a href="play.php">Back to the game</a></form>

<h2>Playtime</h2>
<div class="card"><table>
<tr><th></th><th>Sessions</th><th>Past 1 min (target 65%+)</th><th>Past 3 min (target 40%+)</th><th>Median (target 6:00+)</th><th>Average</th></tr>
<?php foreach ($groups as $name => $list): $s = summarize($list); ?>
<tr><td><b><?= $h($name) ?></b></td>
<?php if (!$s): ?><td colspan="5" class="muted">No sessions yet</td><?php else: ?>
<td><?= $s['n'] ?></td>
<td class="<?= $cls($s['p1'] >= 0.65) ?>"><?= $pct($s['p1']) ?></td>
<td class="<?= $cls($s['p3'] >= 0.40) ?>"><?= $pct($s['p3']) ?></td>
<td class="<?= $cls($s['med'] >= 360) ?>"><?= $fmtT($s['med']) ?></td>
<td><?= $fmtT($s['avg']) ?></td>
<?php endif; ?></tr>
<?php endforeach; ?>
</table>
<p class="muted">Next-day return: <?= $retBase ? $pct($ret / $retBase) . " ($ret of $retBase visitors)" : 'not enough days yet' ?>. Small samples swing a lot; aim for 15 to 20 players before trusting these.</p></div>

<h2>Where sessions ended</h2>
<div class="card"><table><tr><th>Moonlet</th><th>Sessions that ended there</th></tr>
<?php foreach ($quitMoon as $m => $c): ?><tr><td>Moonlet <?= $roman($m + 1) ?></td><td><?= $c ?></td></tr><?php endforeach; ?>
<?php if (!$quitMoon): ?><tr><td colspan="2" class="muted">No sessions yet</td></tr><?php endif; ?>
</table>
<table style="margin-top:12px"><tr><th>Screen at the end</th><th>Sessions</th></tr>
<?php foreach ($quitScr as $k => $c): ?><tr><td><?= $h($k) ?></td><td><?= $c ?></td></tr><?php endforeach; ?>
</table></div>
</main></body></html>
