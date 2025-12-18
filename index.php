<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ultimate Quiz - WAMP</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>

  <style>
    .fade-in { animation: fadeIn .35s ease-out both; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    .pulse-soft { animation: pulseSoft 1.6s ease-in-out infinite; }
    @keyframes pulseSoft { 0%,100% { opacity: .25; } 50% { opacity: .45; } }
  </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 font-sans p-4 md:p-8 flex items-center justify-center">
  <div class="w-full" id="app"></div>

  <div class="fixed bottom-4 left-1/2 -translate-x-1/2 text-[10px] text-slate-400 font-mono tracking-tighter opacity-50 uppercase pointer-events-none text-center">
    Quiz App v1.2 - WAMP + MySQL
  </div>

  <script>
    const QUESTIONS_DATABASE = [
      { q: "Quelle est la capitale de la France ?", a: ["Paris", "Lyon", "Marseille", "Bordeaux"], correct: 0 },
      { q: "Qui a peint la Joconde ?", a: ["Van Gogh", "Picasso", "Léonard de Vinci", "Monet"], correct: 2 },
      { q: "Quel est le plus grand océan du monde ?", a: ["Atlantique", "Indien", "Arctique", "Pacifique"], correct: 3 },
      { q: "Combien de planètes compte le système solaire ?", a: ["7", "8", "9", "10"], correct: 1 },
      { q: "Quelle est la monnaie du Japon ?", a: ["Yuan", "Won", "Yen", "Dollar"], correct: 2 },
      { q: "Quel pays a gagné la Coupe du Monde 2018 ?", a: ["Brésil", "Allemagne", "France", "Croatie"], correct: 2 },
      { q: "Quel est l'élément chimique du symbole Au ?", a: ["Argent", "Or", "Cuivre", "Fer"], correct: 1 },
      { q: "Quelle est la montagne la plus haute du monde ?", a: ["K2", "Mont Blanc", "Everest", "Annapurna"], correct: 2 },
      { q: "Qui a écrit 'Les Misérables' ?", a: ["Molière", "Victor Hugo", "Zola", "Balzac"], correct: 1 },
      { q: "Quel est le plus petit pays du monde ?", a: ["Monaco", "Malte", "Vatican", "Saint-Marin"], correct: 2 },
      { q: "En quelle année l'homme a-t-il marché sur la Lune ?", a: ["1965", "1969", "1972", "1959"], correct: 1 },
      { q: "Quelle est la langue la plus parlée au monde ?", a: ["Espagnol", "Anglais", "Chinois Mandarin", "Hindi"], correct: 2 },
      { q: "Quel organe pompe le sang ?", a: ["Poumons", "Foie", "Cerveau", "Cœur"], correct: 3 },
      { q: "Quel animal est le plus rapide ?", a: ["Guépard", "Léopard", "Lion", "Antilope"], correct: 0 },
      { q: "Quelle planète est surnommée la Planète Rouge ?", a: ["Vénus", "Jupiter", "Mars", "Saturne"], correct: 2 },
      { q: "Combien de cœurs possède une pieuvre ?", a: ["1", "2", "3", "4"], correct: 2 },
      { q: "Quel est le fleuve le plus long du monde ?", a: ["Nil", "Amazone", "Mississippi", "Yangtsé"], correct: 1 },
      { q: "Qui a inventé le téléphone ?", a: ["Edison", "Graham Bell", "Tesla", "Newton"], correct: 1 },
      { q: "De quel pays vient le sushi ?", a: ["Chine", "Thaïlande", "Japon", "Corée"], correct: 2 },
      { q: "Combien y a-t-il de secondes dans une heure ?", a: ["60", "360", "1200", "3600"], correct: 3 },
      { q: "Quelle est la capitale de l'Italie ?", a: ["Milan", "Rome", "Naples", "Venise"], correct: 1 },
      { q: "Quel métal est liquide à température ambiante ?", a: ["Plomb", "Mercure", "Étain", "Aluminium"], correct: 1 },
      { q: "Qui est l'auteur de Harry Potter ?", a: ["Stephen King", "J.K. Rowling", "George R.R. Martin", "Tolkien"], correct: 1 },
      { q: "Quel pays possède la Tour de Pise ?", a: ["Espagne", "France", "Grèce", "Italie"], correct: 3 },
      { q: "Quelle est la couleur de l'émeraude ?", a: ["Bleu", "Rouge", "Vert", "Jaune"], correct: 2 },
      { q: "Combien d'os y a-t-il dans le corps humain adulte ?", a: ["186", "206", "226", "256"], correct: 1 },
      { q: "Quel est l'animal terrestre le plus lourd ?", a: ["Rhinocéros", "Hippopotame", "Éléphant d'Afrique", "Girafe"], correct: 2 },
      { q: "Dans quelle ville se trouve la Statue de la Liberté ?", a: ["Washington", "New York", "Los Angeles", "Chicago"], correct: 1 },
      { q: "Quelle est la capitale de l'Espagne ?", a: ["Barcelone", "Valence", "Madrid", "Séville"], correct: 2 },
      { q: "Qui a découvert la gravité ?", a: ["Einstein", "Darwin", "Galilée", "Newton"], correct: 3 },
      { q: "Quel est le gaz le plus présent dans l'air ?", a: ["Oxygène", "Azote", "Carbone", "Hydrogène"], correct: 1 },
      { q: "Quel instrument de musique a 88 touches ?", a: ["Guitare", "Violon", "Piano", "Flûte"], correct: 2 },
      { q: "Combien y a-t-il de continents ?", a: ["5", "6", "7", "8"], correct: 2 },
      { q: "Quel est l'oiseau qui ne vole pas ?", a: ["Aigle", "Autruche", "Moineau", "Perroquet"], correct: 1 },
      { q: "Quelle est la capitale de l'Allemagne ?", a: ["Munich", "Francfort", "Hambourg", "Berlin"], correct: 3 },
      { q: "Qui était Napoléon Bonaparte ?", a: ["Un peintre", "Un empereur", "Un musicien", "Un écrivain"], correct: 1 },
      { q: "Combien d'États compte les USA ?", a: ["48", "50", "52", "55"], correct: 1 },
      { q: "Quel est le fruit de l'amandier ?", a: ["Amande", "Noix", "Noisette", "Pistache"], correct: 0 },
      { q: "Quel célèbre détective vit au 221B Baker Street ?", a: ["Poirot", "Sherlock Holmes", "Lupin", "Maigret"], correct: 1 },
      { q: "Quelle est la monnaie de l'Angleterre ?", a: ["Euro", "Livre Sterling", "Dollar", "Franc"], correct: 1 },
      { q: "Quel est le pays du sirop d'érable ?", a: ["USA", "Canada", "Suède", "Norvège"], correct: 1 },
      { q: "Comment s'appelle le bébé du cheval ?", a: ["Veau", "Poulain", "Chevreau", "Agneau"], correct: 1 },
      { q: "Quelle est la capitale de l'Egypte ?", a: ["Le Caire", "Alexandrie", "Louxor", "Gizeh"], correct: 0 },
      { q: "Quel est le plus grand désert du monde ?", a: ["Sahara", "Gobi", "Antarctique", "Kalahari"], correct: 2 },
      { q: "Quel organe sert à respirer ?", a: ["Estomac", "Rein", "Poumon", "Cœur"], correct: 2 },
      { q: "Quel héros porte un bouclier étoilé ?", a: ["Iron Man", "Thor", "Captain America", "Spider-Man"], correct: 2 },
      { q: "Dans quel film trouve-t-on Simba ?", a: ["Bambi", "Le Roi Lion", "Aladdin", "Tarzan"], correct: 1 },
      { q: "Quelle est la capitale du Portugal ?", a: ["Porto", "Lisbonne", "Faro", "Braga"], correct: 1 },
      { q: "Combien de côtés a un hexagone ?", a: ["5", "6", "7", "8"], correct: 1 },
      { q: "Quelle est la couleur du rubis ?", a: ["Bleu", "Jaune", "Rouge", "Vert"], correct: 2 }
    ];

    const $app = document.getElementById('app');

    const state = {
      gameState: 'menu', // menu | playing | finished | leaderboard
      playerName: '',
      playerService: '',
      currentQuestions: [],
      currentIndex: 0,
      score: 0,
      selectedAnswer: null,
      leaderboard: [],
      loadingScores: true,
      pollTimer: null,
    };

    function shuffle(arr) {
      return [...arr].sort(() => 0.5 - Math.random());
    }

    async function apiGetScores() {
      try {
        state.loadingScores = true;
        render();
        const res = await fetch('api/scores.php', { cache: 'no-store' });
        const data = await res.json();
        state.leaderboard = data.ok ? (data.scores || []) : [];
      } catch (e) {
        state.leaderboard = [];
      } finally {
        state.loadingScores = false;
        render();
      }
    }

    async function apiAddScore(name, service, score) {
      try {
        await fetch('api/add_score.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ name, service, score })
        });
      } catch (e) {}
    }

    function startPolling() {
      stopPolling();
      state.pollTimer = setInterval(apiGetScores, 3000);
    }
    function stopPolling() {
      if (state.pollTimer) clearInterval(state.pollTimer);
      state.pollTimer = null;
    }

    function startNewGame() {
      if (!state.playerName.trim() || !state.playerService.trim()) return;
      state.currentQuestions = shuffle(QUESTIONS_DATABASE).slice(0, 5);
      state.currentIndex = 0;
      state.score = 0;
      state.selectedAnswer = null;
      state.gameState = 'playing';
      stopPolling();
      render();
    }

    function finishGame(finalScore) {
      state.gameState = 'finished';
      render();
      apiAddScore(state.playerName.trim(), state.playerService.trim(), finalScore).then(() => {
        apiGetScores();
      });
    }

    function handleAnswer(idx) {
      if (state.selectedAnswer !== null) return;
      state.selectedAnswer = idx;

      const q = state.currentQuestions[state.currentIndex];
      const correct = idx === q.correct;
      const newScore = correct ? state.score + 1 : state.score;
      if (correct) state.score = newScore;

      render();

      setTimeout(() => {
        if (state.currentIndex < 4) {
          state.currentIndex += 1;
          state.selectedAnswer = null;
          render();
        } else {
          finishGame(newScore);
        }
      }, 1500);
    }

    function setView(view) {
      state.gameState = view;
      if (view === 'leaderboard') {
        apiGetScores();
        startPolling();
      } else {
        stopPolling();
      }
      render();
    }

    function icon(name, cls) {
      return `<i data-lucide="${name}" class="${cls}"></i>`;
    }

    function renderMenu() {
      return `
        <div class="flex flex-col items-center justify-center space-y-8 fade-in">
          <div class="text-center">
            <h1 class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-600 mb-4">
              Ultimate Quiz
            </h1>
            <p class="text-slate-500 text-lg">5 questions. 50 possibilités. Serez-vous le meilleur ?</p>
          </div>

          <div class="w-full max-w-sm bg-white p-8 rounded-3xl shadow-xl border border-slate-100">
            <div class="mb-6">
              <label class="block text-sm font-semibold text-slate-700 mb-2">Votre Prénom</label>
              <div class="relative">
                ${icon('user', 'absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5')}
                <input
                  id="playerName"
                  type="text"
                  value="${escapeHtml(state.playerName)}"
                  placeholder="Ex: Jean Dupont"
                  class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                />
              </div>
            </div>

            <div class="mb-6">
              <label class="block text-sm font-semibold text-slate-700 mb-2">Votre Service</label>
              <div class="relative">
                ${icon('building-2', 'absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5')}
                <input
                  id="playerService"
                  type="text"
                  value="${escapeHtml(state.playerService)}"
                  placeholder="Ex: Ressources Humaines"
                  class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                />
              </div>
            </div>

            <button
              id="startBtn"
              ${state.playerName.trim() && state.playerService.trim() ? '' : 'disabled'}
              class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-200 hover:opacity-90 transition-all active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2"
            >
              ${icon('play', 'w-5 h-5')} Commencer
            </button>
          </div>

          <button
            id="goLeaderboard"
            class="text-indigo-600 font-semibold hover:underline flex items-center gap-1"
          >
            ${icon('trophy', 'w-4 h-4')} Voir le classement
          </button>
        </div>
      `;
    }

    function renderPlaying() {
      const q = state.currentQuestions[state.currentIndex];
      const progress = ((state.currentIndex + 1) / 5) * 100;

      const answers = q.a.map((ans, idx) => {
        let cls = "bg-white hover:bg-slate-50 border-slate-200";
        if (state.selectedAnswer !== null) {
          if (idx === q.correct) cls = "bg-green-100 border-green-500 text-green-700";
          else if (idx === state.selectedAnswer) cls = "bg-red-100 border-red-500 text-red-700";
          else cls = "bg-slate-50 border-slate-100 opacity-50";
        }

        let rightIcon = "";
        if (state.selectedAnswer !== null && idx === q.correct) rightIcon = icon('check-circle-2', 'w-6 h-6 text-green-600');
        if (state.selectedAnswer !== null && idx === state.selectedAnswer && idx !== q.correct) rightIcon = icon('x-circle', 'w-6 h-6 text-red-600');

        return `
          <button
            class="answerBtn p-6 text-left rounded-2xl border-2 font-semibold transition-all duration-200 shadow-sm flex justify-between items-center ${cls}"
            data-idx="${idx}"
            ${state.selectedAnswer !== null ? 'disabled' : ''}
          >
            <span>${escapeHtml(ans)}</span>
            ${rightIcon}
          </button>
        `;
      }).join('');

      return `
        <div class="w-full max-w-2xl mx-auto space-y-8 fade-in">
          <div class="flex justify-between items-end px-2">
            <div>
              <span class="text-indigo-600 font-bold text-sm uppercase tracking-widest">Question ${state.currentIndex + 1} / 5</span>
              <h2 class="text-2xl font-bold text-slate-800 mt-1">${escapeHtml(q.q)}</h2>
            </div>
            <div class="text-right">
              <div class="text-xs text-slate-400 font-bold uppercase">Score actuel</div>
              <div class="text-3xl font-black text-indigo-600">${state.score}</div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            ${answers}
          </div>

          <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-indigo-500 transition-all duration-500" style="width:${progress}%"></div>
          </div>
        </div>
      `;
    }

    function renderFinished() {
      return `
        <div class="text-center space-y-8 py-10 fade-in">
          <div class="relative inline-block">
            <div class="absolute -inset-4 bg-indigo-500 rounded-full blur-xl opacity-20 pulse-soft"></div>
            ${icon('trophy', 'w-24 h-24 text-yellow-500 mx-auto relative')}
          </div>

          <div>
            <h2 class="text-4xl font-black text-slate-800 mb-2">Terminé !</h2>
            <p class="text-xl text-slate-500">Bravo <span class="text-indigo-600 font-bold">${escapeHtml(state.playerName)}</span> !</p>
          </div>

          <div class="bg-white p-8 rounded-3xl shadow-xl max-w-sm mx-auto border border-slate-100">
            <div class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Score Final</div>
            <div class="text-6xl font-black text-indigo-600 mb-6">${state.score} <span class="text-2xl text-slate-300">/ 5</span></div>

            <div class="space-y-3">
              <button id="seeLeaderboard" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition-colors">
                Voir le Classement
              </button>
              <button id="replay" class="w-full bg-slate-100 text-slate-700 font-bold py-3 rounded-xl hover:bg-slate-200 transition-colors flex items-center justify-center gap-2">
                ${icon('refresh-cw', 'w-4 h-4')} Rejouer
              </button>
            </div>
          </div>
        </div>
      `;
    }

    function renderLeaderboard() {
      const rows = state.loadingScores
        ? `<div class="p-10 text-center text-slate-400 italic">Chargement des scores...</div>`
        : (state.leaderboard.length === 0
            ? `<div class="p-10 text-center text-slate-400 italic">Aucun score pour le moment.</div>`
            : `
              <div class="divide-y divide-slate-50">
                ${state.leaderboard.map((e, i) => {
                  const badge =
                    i === 0 ? 'bg-yellow-100 text-yellow-700' :
                    i === 1 ? 'bg-slate-100 text-slate-700' :
                    i === 2 ? 'bg-orange-100 text-orange-700' : 'text-slate-400';

                  return `
                    <div class="flex items-center justify-between p-4 px-6 hover:bg-slate-50 transition-colors">
                      <div class="flex items-center gap-4">
                        <span class="w-8 h-8 flex items-center justify-center rounded-full font-bold text-sm ${badge}">${i + 1}</span>
                        <div class="flex flex-col">
                          <span class="font-bold text-slate-700">${escapeHtml(e.name || 'Anonyme')}</span>
                          ${e.service ? `<span class="text-xs text-slate-500">${escapeHtml(e.service)}</span>` : ''}
                        </div>
                      </div>
                      <div class="flex items-center gap-2">
                        <span class="text-xl font-black text-indigo-600">${Number(e.score) || 0}</span>
                        <span class="text-xs text-slate-400 font-bold">PTS</span>
                      </div>
                    </div>
                  `;
                }).join('')}
              </div>
            `
          );

      return `
        <div class="w-full max-w-md mx-auto space-y-6 fade-in">
          <div class="text-center">
            <h2 class="text-3xl font-black text-slate-800">Classement</h2>
            <p class="text-slate-500">Les 10 meilleurs scores</p>
          </div>

          <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
            ${rows}
          </div>

          <button id="backMenu" class="w-full py-4 text-slate-500 font-bold hover:text-indigo-600 transition-colors">
            Retour au menu
          </button>
        </div>
      `;
    }

    function escapeHtml(str) {
      return String(str ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
    }

    function wireEvents() {
      if (state.gameState === 'menu') {
        const input = document.getElementById('playerName');
        const serviceInput = document.getElementById('playerService');
        const btn = document.getElementById('startBtn');
        const lb = document.getElementById('goLeaderboard');

        input?.addEventListener('input', (e) => {
          state.playerName = e.target.value;
          render();
        });
        serviceInput?.addEventListener('input', (e) => {
          state.playerService = e.target.value;
          render();
        });
        btn?.addEventListener('click', startNewGame);
        lb?.addEventListener('click', () => setView('leaderboard'));
      }

      if (state.gameState === 'playing') {
        document.querySelectorAll('.answerBtn').forEach(b => {
          b.addEventListener('click', () => handleAnswer(Number(b.dataset.idx)));
        });
      }

      if (state.gameState === 'finished') {
        document.getElementById('seeLeaderboard')?.addEventListener('click', () => setView('leaderboard'));
        document.getElementById('replay')?.addEventListener('click', () => setView('menu'));
      }

      if (state.gameState === 'leaderboard') {
        document.getElementById('backMenu')?.addEventListener('click', () => setView('menu'));
      }
    }

    function render() {
      let html = '';
      if (state.gameState === 'menu') html = renderMenu();
      if (state.gameState === 'playing') html = renderPlaying();
      if (state.gameState === 'finished') html = renderFinished();
      if (state.gameState === 'leaderboard') html = renderLeaderboard();

      $app.innerHTML = html;
      lucide.createIcons();
      wireEvents();
    }

    render();
  </script>
</body>
</html>
