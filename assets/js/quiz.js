/**
 * Clickable untimed quiz — shows whyRight/whyWrong, writes score via progress.js.
 */
(function () {
  'use strict';

  var dataEl = document.getElementById('quiz-data');
  var live = document.querySelector('.quiz-live');
  if (!dataEl || !live) return;

  var quiz;
  try {
    quiz = JSON.parse(dataEl.textContent || '');
  } catch (e) {
    return;
  }
  if (!quiz || !Array.isArray(quiz.items) || quiz.items.length === 0) return;

  var slug = live.getAttribute('data-slug') || quiz.slug || '';
  var answers = [];
  var answered = 0;
  var finished = false;
  var scoreEl = live.querySelector('.quiz-score');

  function finishIfDone() {
    if (finished || answered < quiz.items.length) return;
    finished = true;
    var correct = 0;
    quiz.items.forEach(function (item, i) {
      if (answers[i] === item.answerIndex) correct++;
    });
    var total = quiz.items.length;
    if (scoreEl) {
      scoreEl.hidden = false;
      scoreEl.textContent =
        'Score: ' + correct + ' / ' + total + ' (' + Math.round((correct / total) * 100) + '%)';
    }
    var api = window.TheoryCoreProgress;
    if (api && typeof api.recordQuizResult === 'function' && slug) {
      try {
        api.recordQuizResult(slug, correct, total);
      } catch (e) {}
    }
  }

  live.querySelectorAll('.quiz-item').forEach(function (section) {
    var index = parseInt(section.getAttribute('data-index') || '-1', 10);
    if (isNaN(index) || index < 0 || !quiz.items[index]) return;
    var item = quiz.items[index];
    var feedback = section.querySelector('.quiz-feedback');
    var locked = false;

    section.querySelectorAll('button.choice[data-choice]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        if (locked) return;
        var choice = parseInt(btn.getAttribute('data-choice') || '-1', 10);
        if (isNaN(choice) || choice < 0 || choice > 3) return;
        locked = true;
        answers[index] = choice;
        answered++;

        var ok = choice === item.answerIndex;
        section.querySelectorAll('button.choice').forEach(function (b) {
          b.disabled = true;
          var ci = parseInt(b.getAttribute('data-choice') || '-1', 10);
          b.classList.remove('is-correct', 'is-wrong', 'is-picked');
          if (ci === item.answerIndex) b.classList.add('is-correct');
          if (ci === choice) b.classList.add('is-picked');
          if (ci === choice && !ok) b.classList.add('is-wrong');
        });

        if (feedback) {
          feedback.hidden = false;
          feedback.classList.toggle('is-correct', ok);
          feedback.classList.toggle('is-wrong', !ok);
          feedback.textContent = ok
            ? String(item.whyRight || 'Correct.')
            : String((item.whyWrong && item.whyWrong[choice]) || 'Not quite.');
        }
        finishIfDone();
      });
    });
  });
})();
