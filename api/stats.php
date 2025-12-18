<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

try {
  $pdo = db();
  ensureServiceColumn($pdo);
  $stmt = $pdo->query(
    "SELECT service, COUNT(*) AS games, SUM(score) AS total_points, AVG(score) AS avg_score
     FROM scores
     WHERE service IS NOT NULL AND service <> ''
     GROUP BY service
     ORDER BY avg_score DESC, total_points DESC"
  );
  $stats = $stmt->fetchAll();
  json_out(['ok' => true, 'stats' => ['services' => $stats]]);
} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Erreur serveur'], 500);
}
