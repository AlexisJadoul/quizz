<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function ensureQuestionTables(PDO $pdo): void {
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS questions (
      id INT AUTO_INCREMENT PRIMARY KEY,
      question_text VARCHAR(500) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  ");

  $pdo->exec("
    CREATE TABLE IF NOT EXISTS answers (
      id INT AUTO_INCREMENT PRIMARY KEY,
      question_id INT NOT NULL,
      answer_text VARCHAR(255) NOT NULL,
      is_correct TINYINT(1) NOT NULL DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  ");
}

try {
  $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
  if ($limit <= 0) $limit = 10;
  if ($limit > 20) $limit = 20;

  $pdo = db();
  ensureQuestionTables($pdo);

  $stmt = $pdo->query("SELECT id, question_text FROM questions ORDER BY RAND() LIMIT {$limit}");
  $questions = $stmt->fetchAll();

  if (!$questions) {
    json_out(['ok' => false, 'message' => 'Aucune question disponible en base.', 'questions' => []]);
  }

  $answerStmt = $pdo->prepare('SELECT answer_text, is_correct FROM answers WHERE question_id = ? ORDER BY id');

  $formatted = [];
  foreach ($questions as $question) {
    $answerStmt->execute([$question['id']]);
    $answers = $answerStmt->fetchAll();
    if (!$answers || count($answers) < 2) {
      continue;
    }

    $answerTexts = [];
    $correctIndex = null;
    foreach ($answers as $idx => $answer) {
      $answerTexts[] = $answer['answer_text'];
      if ($correctIndex === null && (int)$answer['is_correct'] === 1) {
        $correctIndex = $idx;
      }
    }

    if ($correctIndex === null) {
      continue;
    }

    $formatted[] = [
      'q' => $question['question_text'],
      'a' => $answerTexts,
      'correct' => $correctIndex
    ];
  }

  if (!$formatted) {
    json_out(['ok' => false, 'message' => 'Aucune question utilisable en base.', 'questions' => []]);
  }

  json_out(['ok' => true, 'questions' => $formatted]);
} catch (Throwable $e) {
  json_out(['ok' => false, 'message' => 'Erreur serveur.'], 500);
}
