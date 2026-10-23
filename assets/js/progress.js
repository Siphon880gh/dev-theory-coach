/**
 * Progress writer for Dev Theory Coach (THEORY_CORE).
 *
 * Storage key and fields MUST match src/ProgressSchema.php:
 *   KEY: theory_core_progress_v1
 *   version: 1
 *   pathId: string (default "beginner")
 *   milestones: { "beginner:<slug>": { status, guideRead, coachComplete,
 *     quizSubmitted, quizScore, quizAccuracy, drillAccuracy,
 *     drillMedianLatencyMs, caseChoiceId, caseCorrect } }
 *   scores: object
 *   latencies: object
 *   caseGateOpen: boolean
 *   recovery: boolean
 *
 * This file is the only writer of theory_core_progress_v1.
 */
(function (global) {
  'use strict';

  var KEY = 'theory_core_progress_v1';
  var VERSION = 1;
  var PATH_BEGINNER = 'beginner';
  var BEGINNER_LEARNING_ORDER = [
    'arrays',
    'strings',
    'hash-maps',
    'linked-lists',
    'stacks',
    'queues',
    'complexity',
    'recursion',
    'two-pointers',
    'binary-search',
    'trees',
    'sliding-window'
  ];

  function beginnerMilestoneIds() {
    return BEGINNER_LEARNING_ORDER.map(function (slug) {
      return PATH_BEGINNER + ':' + slug;
    });
  }

  function fresh(recovery) {
    var milestones = {};
    beginnerMilestoneIds().forEach(function (id, i) {
      milestones[id] = {
        status: i === 0 ? 'unlocked' : 'locked',
        guideRead: false,
        coachComplete: false,
        quizSubmitted: false,
        quizScore: null,
        quizAccuracy: null,
        drillAccuracy: null,
        drillMedianLatencyMs: null,
        caseChoiceId: null,
        caseCorrect: null
      };
    });
    return {
      version: VERSION,
      pathId: PATH_BEGINNER,
      milestones: milestones,
      scores: {},
      latencies: {},
      caseGateOpen: false,
      recovery: !!recovery
    };
  }

  function decode(raw) {
    if (raw == null || raw === '') {
      return fresh(false);
    }
    var decoded;
    try {
      decoded = JSON.parse(raw);
    } catch (e) {
      return fresh(true);
    }
    if (!decoded || typeof decoded !== 'object' || decoded.version !== VERSION) {
      return fresh(true);
    }
    var out = fresh(false);
    if (typeof decoded.pathId === 'string') out.pathId = decoded.pathId;
    if (typeof decoded.caseGateOpen === 'boolean') out.caseGateOpen = decoded.caseGateOpen;
    if (typeof decoded.recovery === 'boolean') out.recovery = decoded.recovery;
    if (decoded.scores && typeof decoded.scores === 'object') out.scores = decoded.scores;
    if (decoded.latencies && typeof decoded.latencies === 'object') out.latencies = decoded.latencies;
    if (decoded.milestones && typeof decoded.milestones === 'object') {
      Object.keys(out.milestones).forEach(function (id) {
        if (decoded.milestones[id] && typeof decoded.milestones[id] === 'object') {
          Object.keys(out.milestones[id]).forEach(function (k) {
            if (Object.prototype.hasOwnProperty.call(decoded.milestones[id], k)) {
              out.milestones[id][k] = decoded.milestones[id][k];
            }
          });
        }
      });
    }
    return out;
  }

  function storage() {
    try {
      return global.localStorage;
    } catch (e) {
      return null;
    }
  }

  function load() {
    var s = storage();
    if (!s) return fresh(false);
    return decode(s.getItem(KEY));
  }

  function save(state) {
    var s = storage();
    if (!s) return state;
    var payload = decode(JSON.stringify(state));
    // saving a good state clears recovery unless caller kept it
    s.setItem(KEY, JSON.stringify(payload));
    return payload;
  }

  function getPathId() {
    return load().pathId;
  }

  function setPathId(pathId) {
    var state = load();
    state.pathId = String(pathId);
    state.recovery = false;
    return save(state);
  }

  function getMilestone(id) {
    var state = load();
    return state.milestones[id] || null;
  }

  function setMilestoneStatus(id, status) {
    var state = load();
    if (!state.milestones[id]) return state;
    state.milestones[id].status = status;
    state.recovery = false;
    return save(state);
  }

  function setScore(key, value) {
    var state = load();
    state.scores[key] = value;
    state.recovery = false;
    return save(state);
  }

  function setLatency(key, value) {
    var state = load();
    state.latencies[key] = value;
    state.recovery = false;
    return save(state);
  }

  function reset(confirmed) {
    if (!confirmed) return load();
    var state = fresh(false);
    return save(state);
  }



  function recordCoachComplete(slug) {
    var state = load();
    var id = state.pathId + ':' + slug;
    if (!state.milestones[id]) {
      state.milestones[id] = {
        status: 'unlocked',
        guideRead: false,
        coachComplete: false,
        quizSubmitted: false,
        quizScore: null,
        quizAccuracy: null,
        drillAccuracy: null,
        drillMedianLatencyMs: null,
        caseChoiceId: null,
        caseCorrect: null
      };
    }
    state.milestones[id].coachComplete = true;
    state.recovery = false;
    return save(state);
  }

  function recordQuizResult(slug, correct, total) {
    var state = load();
    var id = state.pathId + ':' + slug;
    if (!state.milestones[id]) {
      state.milestones[id] = {
        status: 'unlocked',
        guideRead: false,
        coachComplete: false,
        quizSubmitted: false,
        quizScore: null,
        quizAccuracy: null,
        drillAccuracy: null,
        drillMedianLatencyMs: null,
        caseChoiceId: null,
        caseCorrect: null
      };
    }
    var accuracy = total > 0 ? correct / total : 0;
    state.milestones[id].quizSubmitted = true;
    state.milestones[id].quizScore = correct;
    state.milestones[id].quizAccuracy = accuracy;
    state.scores[id] = { correct: correct, total: total, accuracy: accuracy };
    state.recovery = false;
    return save(state);
  }

  global.TheoryCoreProgress = {
    KEY: KEY,
    VERSION: VERSION,
    BEGINNER_LEARNING_ORDER: BEGINNER_LEARNING_ORDER.slice(),
    beginnerMilestoneIds: beginnerMilestoneIds,
    fresh: fresh,
    decode: decode,
    load: load,
    save: save,
    getPathId: getPathId,
    setPathId: setPathId,
    getMilestone: getMilestone,
    setMilestoneStatus: setMilestoneStatus,
    setScore: setScore,
    setLatency: setLatency,
    reset: reset,
    recordQuizResult: recordQuizResult,
    recordCoachComplete: recordCoachComplete
  };
})(typeof window !== 'undefined' ? window : globalThis);
