<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

try {
  $pdo = db();
  $stmt = $pdo->query("SELECT id, name, score, created_at FROM scores ORDER BY score DESC, created_at ASC LIMIT 10");
  $rows = $stmt->fetchAll();
  json_out(['ok' => true, 'scores' => $rows]);
} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Erreur serveur'], 500);
}
