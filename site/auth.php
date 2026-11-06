<?php
// Shared login helpers. The password lives in secret.php on the server only.
session_set_cookie_params([
  'lifetime' => 0,
  'path' => '/',
  'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
  'httponly' => true,
  'samesite' => 'Lax',
]);
session_start();

function mr_password() {
  $p = @include __DIR__ . '/secret.php';
  return is_string($p) ? $p : '';
}
function mr_password_is_set() {
  $p = mr_password();
  return $p !== '' && $p !== 'CHANGE-ME';
}
function mr_logged_in() {
  return !empty($_SESSION['mr_ok']);
}
