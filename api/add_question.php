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
  $raw = file_get_contents('php://input');
  $payload = json_decode($raw ?: '', true);

  if (!is_array($payload)) {
    json_out(['ok' => false, 'message' => 'Payload invalide.'], 400);
  }

  $question = trim((string)($payload['question'] ?? ''));
  $answersInput = $payload['answers'] ?? [];

  if ($question === '') {
    json_out(['ok' => false, 'message' => 'La question est obligatoire.'], 422);
  }

  if (!is_array($answersInput)) {
    json_out(['ok' => false, 'message' => 'Les réponses sont invalides.'], 422);
  }

  $answers = [];
  foreach ($answersInput as $answer) {
    if (!is_array($answer)) continue;
    $text = trim((string)($answer['text'] ?? ''));
    if ($text === '') continue;
    $answers[] = [
      'text' => $text,
      'correct' => !empty($answer['correct'])
    ];
  }

  if (count($answers) < 2) {
    json_out(['ok' => false, 'message' => 'Ajoutez au moins deux réponses.'], 422);
  }

  $hasCorrect = false;
  foreach ($answers as $answer) {
    if ($answer['correct']) {
      $hasCorrect = true;
      break;
    }
  }

  if (!$hasCorrect) {
    json_out(['ok' => false, 'message' => 'Sélectionnez au moins une bonne réponse.'], 422);
  }

  $pdo = db();
  ensureQuestionTables($pdo);

  $pdo->beginTransaction();
  $stmt = $pdo->prepare('INSERT INTO questions (question_text) VALUES (?)');
  $stmt->execute([$question]);
  $questionId = (int)$pdo->lastInsertId();

  $answerStmt = $pdo->prepare('INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)');
  foreach ($answers as $answer) {
    $answerStmt->execute([$questionId, $answer['text'], $answer['correct'] ? 1 : 0]);
  }
  $pdo->commit();

  json_out(['ok' => true, 'id' => $questionId]);
} catch (Throwable $e) {
  if (isset($pdo) && $pdo->inTransaction()) {
    $pdo->rollBack();
  }
  json_out(['ok' => false, 'message' => 'Erreur serveur.'], 500);
}
