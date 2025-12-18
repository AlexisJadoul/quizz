<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  json_out(['ok' => false, 'error' => 'Méthode non autorisée'], 405);
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw ?: '[]', true);
if (!is_array($payload)) {
  json_out(['ok' => false, 'error' => 'JSON invalide'], 400);
}

$name = trim((string)($payload['name'] ?? ''));
$score = $payload['score'] ?? null;

if ($name === '' || mb_strlen($name) > 60) {
  json_out(['ok' => false, 'error' => 'Nom invalide (1 à 60 caractères)'], 400);
}
if (!is_int($score) && !(is_string($score) && ctype_digit($score))) {
  json_out(['ok' => false, 'error' => 'Score invalide'], 400);
}
$score = (int)$score;
if ($score < 0 || $score > 5) {
  json_out(['ok' => false, 'error' => 'Score hors limite'], 400);
}

try {
  $pdo = db();
  $stmt = $pdo->prepare("INSERT INTO scores (name, score, ip, user_agent) VALUES (:name, :score, :ip, :ua)");
  $stmt->execute([
    ':name' => $name,
    ':score' => $score,
    ':ip' => client_ip_bin(),
    ':ua' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
  ]);
  json_out(['ok' => true]);
} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Erreur serveur'], 500);
}
