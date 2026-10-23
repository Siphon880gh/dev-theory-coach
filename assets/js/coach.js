/**
 * Interactive coach tree — mirrors TheoryCore\Coach::choose.
 */
(function () {
  'use strict';

  var dataEl = document.getElementById('coach-session-data');
  var root = document.querySelector('.coach-live');
  if (!dataEl || !root) return;

  var session;
  try {
    session = JSON.parse(dataEl.textContent || '');
  } catch (e) {
    return;
  }
  if (!session || !session.nodes || !session.start) return;

  var slug = root.getAttribute('data-slug') || (session.meta && session.meta.slug) || '';
  var state = {
    current: String(session.start),
    history: [String(session.start)],
    explanations: [],
    done: (session.nodes[session.start] && session.nodes[session.start].type) === 'success'
  };

  var feedback = root.querySelector('.coach-feedback');
  var nodeEl = root.querySelector('.node');

  function escapeHtml(s) {
    return String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function showFeedback(text) {
    if (!feedback) return;
    if (!text) {
      feedback.hidden = true;
      feedback.textContent = '';
      feedback.classList.remove('is-wrong');
      return;
    }
    feedback.hidden = false;
    feedback.classList.add('is-wrong');
    feedback.textContent = text;
  }

  function render() {
    var node = session.nodes[state.current];
    if (!node || !nodeEl) return;
    nodeEl.setAttribute('data-node-id', state.current);
    nodeEl.setAttribute('data-type', String(node.type || ''));

    var html = '<p class="prompt">' + escapeHtml(node.prompt || '') + '</p>';
    if (node.diagramHtml) {
      html += node.diagramHtml;
    }
    if (node.type === 'success') {
      html += '<p class="success">Scenario complete.</p>';
    } else {
      html += '<ol class="choices">';
      (node.choices || []).forEach(function (choice, i) {
        html +=
          '<li><button type="button" class="choice" data-choice-index="' +
          i +
          '">' +
          escapeHtml(choice.label || 'Choice ' + i) +
          '</button></li>';
      });
      html += '</ol>';
    }
    nodeEl.innerHTML = html;
    bindChoices();
  }

  function markComplete() {
    var api = window.TheoryCoreProgress;
    if (!api || typeof api.recordCoachComplete !== 'function' || !slug) return;
    try {
      api.recordCoachComplete(slug);
    } catch (e) {}
  }

  function choose(choiceIndex) {
    if (state.done) return;
    var node = session.nodes[state.current];
    if (!node || !node.choices || !node.choices[choiceIndex]) return;
    var to = String(node.choices[choiceIndex].to);
    var next = session.nodes[to];
    if (!next) return;

    state.history.push(to);
    var current = to;
    var explanation = '';

    if (next.type === 'wrong') {
      explanation = String(next.explanation || 'That choice is not the best fit.');
      state.explanations.push(explanation);
      var rewind = String(next.rewind_to);
      state.history.push(rewind);
      current = rewind;
    }

    state.current = current;
    state.done = (session.nodes[current] && session.nodes[current].type) === 'success';
    showFeedback(explanation);
    render();
    if (state.done) markComplete();
  }

  function bindChoices() {
    if (!nodeEl) return;
    nodeEl.querySelectorAll('button.choice[data-choice-index]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var idx = parseInt(btn.getAttribute('data-choice-index') || '-1', 10);
        if (isNaN(idx) || idx < 0) return;
        choose(idx);
      });
    });
  }

  if (state.done) {
    markComplete();
  }
  bindChoices();
})();
