<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ajouter une question - Quiz</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 font-sans p-4 md:p-8">
  <div class="max-w-3xl mx-auto space-y-6">
    <header class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-800">Ajouter une question</h1>
        <p class="text-slate-500">Créez des questions avec une ou plusieurs bonnes réponses.</p>
      </div>
      <a href="index.php" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:text-indigo-600 hover:border-indigo-200 transition-colors flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Retour au quiz
      </a>
    </header>

    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-6 md:p-8 space-y-6">
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">Question</label>
        <textarea
          id="questionInput"
          rows="3"
          placeholder="Ex: Quels sont les langages utilisés pour le web ?"
          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all resize-none"
        ></textarea>
      </div>

      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-sm font-semibold text-slate-700">Réponses possibles</h2>
            <p class="text-xs text-slate-500">Cochez toutes les réponses correctes.</p>
          </div>
          <button id="addAnswer" class="px-3 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Ajouter une réponse
          </button>
        </div>

        <div id="answersList" class="space-y-3"></div>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <button id="saveQuestion" class="px-5 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 hover:opacity-90 transition-all">
          Enregistrer la question
        </button>
        <button id="resetForm" class="px-5 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition-colors">
          Réinitialiser
        </button>
        <div id="statusMessage" class="text-sm font-semibold"></div>
      </div>
    </div>
  </div>

  <script>
    const state = {
      answers: [
        { text: '', correct: false },
        { text: '', correct: false },
        { text: '', correct: false },
        { text: '', correct: false }
      ]
    };

    const answersList = document.getElementById('answersList');
    const statusMessage = document.getElementById('statusMessage');
    const questionInput = document.getElementById('questionInput');

    function icon(name, cls) {
      return `<i data-lucide="${name}" class="${cls}"></i>`;
    }

    function renderAnswers() {
      answersList.innerHTML = state.answers.map((answer, index) => `
        <div class="flex flex-wrap items-center gap-3 bg-slate-50 border border-slate-200 rounded-2xl p-4">
          <label class="flex items-center gap-2 text-xs font-semibold text-slate-600">
            <input type="checkbox" class="answerCorrect h-4 w-4 text-indigo-600 border-slate-300 rounded" data-index="${index}" ${answer.correct ? 'checked' : ''} />
            Bonne réponse
          </label>
          <input
            type="text"
            class="answerText flex-1 min-w-[200px] px-4 py-2 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
            placeholder="Réponse ${index + 1}"
            data-index="${index}"
            value="${escapeHtml(answer.text)}"
          />
          <button class="removeAnswer text-slate-400 hover:text-red-500 transition-colors" data-index="${index}" ${state.answers.length <= 2 ? 'disabled' : ''}>
            ${icon('trash-2', 'w-4 h-4')}
          </button>
        </div>
      `).join('');

      lucide.createIcons();
    }

    function escapeHtml(str) {
      return String(str ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
    }

    function updateStatus(message, type = 'info') {
      statusMessage.textContent = message;
      statusMessage.className = type === 'error'
        ? 'text-sm font-semibold text-red-500'
        : type === 'success'
          ? 'text-sm font-semibold text-emerald-600'
          : 'text-sm font-semibold text-slate-500';
    }

    function resetForm() {
      questionInput.value = '';
      state.answers = [
        { text: '', correct: false },
        { text: '', correct: false },
        { text: '', correct: false },
        { text: '', correct: false }
      ];
      renderAnswers();
      updateStatus('', 'info');
    }

    function collectPayload() {
      return {
        question: questionInput.value.trim(),
        answers: state.answers.map((answer) => ({
          text: answer.text.trim(),
          correct: Boolean(answer.correct)
        }))
      };
    }

    async function saveQuestion() {
      const payload = collectPayload();
      const validAnswers = payload.answers.filter((answer) => answer.text.length > 0);
      const hasCorrect = validAnswers.some((answer) => answer.correct);

      if (!payload.question) {
        updateStatus('Merci de saisir la question.', 'error');
        return;
      }
      if (validAnswers.length < 2) {
        updateStatus('Ajoutez au moins deux réponses.', 'error');
        return;
      }
      if (!hasCorrect) {
        updateStatus('Sélectionnez au moins une bonne réponse.', 'error');
        return;
      }

      updateStatus('Enregistrement en cours...', 'info');

      try {
        const res = await fetch('api/add_question.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.ok) {
          updateStatus('Question enregistrée avec succès !', 'success');
          resetForm();
        } else {
          updateStatus(data.message || 'Impossible d’enregistrer la question.', 'error');
        }
      } catch (error) {
        updateStatus('Erreur réseau lors de l’enregistrement.', 'error');
      }
    }

    document.getElementById('addAnswer').addEventListener('click', () => {
      state.answers.push({ text: '', correct: false });
      renderAnswers();
    });

    document.getElementById('resetForm').addEventListener('click', resetForm);
    document.getElementById('saveQuestion').addEventListener('click', saveQuestion);

    answersList.addEventListener('input', (event) => {
      const target = event.target;
      const index = Number(target.dataset.index);
      if (Number.isNaN(index)) return;
      if (target.classList.contains('answerText')) {
        state.answers[index].text = target.value;
      }
      if (target.classList.contains('answerCorrect')) {
        state.answers[index].correct = target.checked;
      }
    });

    answersList.addEventListener('click', (event) => {
      const button = event.target.closest('.removeAnswer');
      if (!button) return;
      const index = Number(button.dataset.index);
      if (Number.isNaN(index)) return;
      if (state.answers.length <= 2) return;
      state.answers.splice(index, 1);
      renderAnswers();
    });

    renderAnswers();
  </script>
</body>
</html>
