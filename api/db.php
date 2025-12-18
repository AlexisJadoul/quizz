<?php
declare(strict_types=1);

function db(): PDO {
  $host = 'localhost';
  $db   = 'quiz_app';
  $user = 'root';
  $pass = ''; // WAMP par défaut
  $charset = 'utf8mb4';

  $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
  $opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ];
  return new PDO($dsn, $user, $pass, $opt);
}

function json_out(array $data, int $code = 200): void {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-store');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function ensureServiceColumn(PDO $pdo): void {
  try {
    $pdo->exec("ALTER TABLE scores ADD COLUMN service VARCHAR(120) NULL AFTER name");
  } catch (Throwable $e) {}
}

function client_ip_bin(): ?string {
  $ip = $_SERVER['REMOTE_ADDR'] ?? null;
  if (!$ip) return null;
  $packed = @inet_pton($ip);
  return $packed === false ? null : $packed;
}
