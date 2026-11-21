<?php
require __DIR__ . '/auth.php';
if (!mr_logged_in()) { header('Location: index.php'); exit; }
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');
define('MR_OK', 1);
include __DIR__ . '/private/game.php';
