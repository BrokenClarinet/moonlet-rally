<?php
require __DIR__ . '/auth.php';

// Optional: put your support address here to show it on the page.
$contact = '';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!mr_password_is_set()) {
    $error = 'The game password has not been set yet.';
  } elseif (hash_equals(mr_password(), (string)($_POST['password'] ?? ''))) {
    session_regenerate_id(true);
    $_SESSION['mr_ok'] = 1;
    header('Location: play.php');
    exit;
  } else {
    sleep(1); // slows down guessing
    $error = 'That password is not right.';
  }
}
$in = mr_logged_in();
$h = fn($s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Moonlet Rally</title>
<meta name="description" content="Moonlet Rally: drive a little car around dead moonlets and bring them back to life with your tire tracks.">
<style>
  :root { --ink:#2a1838; --paper:#fff6ea; --panel:#3a2758; --panel2:#4c3470; --sun:#ffc43d; --coral:#ff6b4a; --mint:#9be37a; --gem:#7fe3ff;
          --font: ui-rounded, "SF Pro Rounded", "Arial Rounded MT Bold", "Trebuchet MS", system-ui, sans-serif; }
  * { box-sizing:border-box; }
  body { margin:0; min-height:100vh; font-family:var(--font); color:var(--paper); line-height:1.5;
         background:radial-gradient(ellipse at 50% 0%, rgba(255,159,134,.35), transparent 60%), #1c1650; }
  main { max-width:880px; margin:0 auto; padding:32px 16px 48px; }
  h1 { font-size:clamp(44px,9vw,80px); margin:0; line-height:.95; color:var(--sun); font-weight:900; transform:rotate(-3deg);
       text-shadow:3px 4px 0 var(--ink), -2px -2px 0 var(--ink), 2px -2px 0 var(--ink), -2px 2px 0 var(--ink); }
  h1 span { display:block; color:var(--mint); font-size:.62em; transform:rotate(2deg); }
  .hero { display:grid; grid-template-columns:1.1fr .9fr; gap:24px; align-items:center; }
  .hero img { width:100%; height:auto; filter:drop-shadow(4px 6px 0 rgba(42,24,56,.6)); }
  .lead { font-size:18px; max-width:30em; }
  .card { background:var(--panel); border:3px solid var(--ink); border-radius:18px; box-shadow:4px 5px 0 var(--ink); padding:18px 20px; margin-top:24px; }
  .card h2 { margin:0 0 8px; color:var(--sun); font-size:22px; text-shadow:2px 2px 0 var(--ink); }
  ul { margin:0; padding-left:20px; } li { margin:4px 0; }
  form { display:flex; gap:10px; flex-wrap:wrap; }
  input { font:inherit; font-weight:700; padding:10px 14px; border:3px solid var(--ink); border-radius:12px; min-width:0; flex:1 1 200px; color:var(--ink); background:var(--paper); }
  .btn { font:inherit; font-weight:900; font-size:18px; padding:10px 26px; border-radius:30px; border:3px solid var(--ink); background:var(--coral); color:var(--paper);
         box-shadow:3px 4px 0 var(--ink); cursor:pointer; text-decoration:none; display:inline-block; }
  .btn:active { transform:translate(2px,3px); box-shadow:none; }
  .err { color:#ffb4a3; font-weight:800; margin:8px 0 0; }
  .muted { opacity:.8; font-size:14px; }
  a { color:var(--gem); }
  footer { margin-top:32px; font-size:13px; opacity:.7; }
  @media (max-width:640px) { .hero { grid-template-columns:1fr; } .hero img { max-width:320px; margin:0 auto; } }
</style>
</head>
<body>
<main>
  <section class="hero">
    <div>
      <h1>MOONLET<span>RALLY</span></h1>
      <p class="lead">Dead little planets. One little car. Every tire track brings the ground back to life, and living planets grow gems for you, even while you're away.</p>
    </div>
    <img src="assets/car.png" alt="The Moonlet Rally buggy: a coral low-poly car with a yellow antenna" width="975" height="823">
  </section>

  <section class="card">
    <h2>What you do</h2>
    <ul>
      <li>Drive across seven tiny moonlets, each with its own twist: ice, geysers, wind, wisps, hoops and falling stars.</li>
      <li>Drift, boost, jump and flip. Clean tricks earn gems.</li>
      <li>Spend gems in the garage on new cars and upgrades.</li>
      <li>Restored moonlets keep growing gems in your Gem Garden.</li>
    </ul>
  </section>

  <section class="card">
    <h2>Play the test build</h2>
    <?php if ($in): ?>
      <p>You're signed in.</p>
      <a class="btn" href="play.php">PLAY</a> <a href="logout.php" class="muted" style="margin-left:12px">Sign out</a>
    <?php else: ?>
      <p class="muted">The game is in private testing. Enter the password to play.</p>
      <form method="post" action="index.php">
        <input type="password" name="password" placeholder="Password" autocomplete="current-password" required aria-label="Password">
        <button class="btn" type="submit">PLAY</button>
      </form>
      <?php if ($error): ?><p class="err"><?= $h($error) ?></p><?php endif; ?>
    <?php endif; ?>
  </section>

  <?php if ($contact): ?>
  <section class="card">
    <h2>Bugs and support</h2>
    <p>Found a bug or need help? Email <a href="mailto:<?= $h($contact) ?>"><?= $h($contact) ?></a>.</p>
  </section>
  <?php endif; ?>

  <footer>Moonlet Rally. Built with <a href="https://threejs.org">three.js</a> (MIT license).</footer>
</main>
</body>
</html>
