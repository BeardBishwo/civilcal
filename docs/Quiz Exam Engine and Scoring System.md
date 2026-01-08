# Quiz Exam Engine and Scoring System

Single-file deep brief for engineers on the live exam runtime, scoring, rewards, and daily quests. Anchored to current code (ExamEngineController, ScoringService, ShuffleService, GamificationService, DailyQuizService, StreakService) and codemap “Quiz Exam Engine and Scoring System”.

## 1) Objectives & Success Criteria
- Serve exams with resilient JSON-cached sessions per attempt; support resume and regeneration.
- Enforce integrity: auth, nonce/CSRF, honeypot, per-attempt user binding.
- Grade multi-type questions consistently with negative marking options.
- Persist answers + scores, trigger rewards (coins/XP/resources), update leaderboards.
- Support daily quests (laddered difficulty 5-3-2) with streak bonuses.
- Provide shuffle (“Chaos Engine”) that re-maps answers safely.

## 2) Current Architecture (inventory)
- Controller: `App\Controllers\Quiz\ExamEngineController` handles start/startDaily, initializeCache/regenerateCache, room, saveAnswer (AJAX), submit, result @app/Controllers/Quiz/ExamEngineController.php.
- Services:
  - `Quiz\ShuffleService`: 2-level shuffle (questions + options) with answer translation @app/Services/Quiz/ShuffleService.php.
  - `Quiz\ScoringService`: type-based correctness + negative marking @app/Services/Quiz/ScoringService.php.
  - `GamificationService`: reward distribution by difficulty, XP/battle pass, missions, resource updates @app/Services/GamificationService.php.
  - `Quiz\DailyQuizService`: auto-generate week (5 easy, 3 medium, 2 hard), fetch daily quiz, record attempt @app/Services/Quiz/DailyQuizService.php.
  - `Quiz\StreakService`: daily streak multiplier with freeze protection @app/Services/Quiz/StreakService.php.
  - `NonceService`, `Security`, `SecurityMonitor` for nonce and honeypot.
- Data:
  - `quiz_exams`: exam metadata (shuffle_questions, negative_marking_rate/unit/basis, mode).
  - `quiz_exam_questions`: ordered exam-question mapping.
  - `quiz_attempts`: attempt status, score, timestamps.
  - `quiz_attempt_answers`: per-question answers & marks.
  - `daily_quiz_schedule`: generated daily quiz questions JSON + reward_coins.
  - `user_resources`, `user_streaks` for rewards/streaks.
- Storage: per-attempt JSON cache in `storage/app/exams/{attemptId}.json`.

## 3) Key Flows (happy paths)
1) **Start Exam (`start`)**
   - Fetch exam by slug; ensure user auth.
   - Reuse ongoing attempt if present; regenerate cache if JSON missing.
   - Else insert attempt, build JSON cache with questions (ordered by quiz_exam_questions), optional shuffle via ShuffleService, decode content/options, save to file; redirect to room.
2) **Room**
   - Load JSON; verify user_id; strip is_correct/explanation in exam mode; generate nonce + CSRF; render arena view.
3) **Save Answer (AJAX)**
   - Honeypot trap; load JSON; check user; update answers map; write back.
4) **Submit**
   - Validate nonce & honeypot; load JSON; iterate questions:
     - gradeQuestion -> isCorrect + marks (negative marking supported).
     - accumulate totalScore, correctCount, correctAnswersList (difficulty-level).
     - bulk insert quiz_attempt_answers.
   - Mark attempt completed with score.
   - GamificationService.processExamRewards(user_id, correctAnswersList, attemptId).
   - LeaderboardService update; daily quest bonus via StreakService + DailyQuizService when applicable.
   - Delete JSON; redirect to result.
5) **Result**
   - Load attempt summary, fetch sample incorrect answers for analysis view.
6) **Daily Quest (`startDaily`)**
   - Fetch scheduled quiz for date/stream; prevent duplicate attempt; create attempt; initialize cache with provided question IDs (ordered); redirect to room.

## 4) Scoring Logic (ScoringService)
- Supported types: `mcq_single`, `true_false` (string compare); `MULTI` (order-insensitive array compare); `ORDER` (order-sensitive array compare).
- Marks:
  - Default marks from question (`default_marks` or 1).
  - Negative marking: percent or fixed based on exam settings (`negative_marking_unit`, `negative_marking_basis` per-q); uses `negative_marking_rate`.
  - Returns marks (can be negative) and isCorrect flag.

## 5) Shuffle Logic (ShuffleService)
- Optional seed; vertical shuffle with `array_multisort`.
- Horizontal shuffle per question: shuffle options, translate answers (MCQ/MULTI) to new positions; inject back.
- Ensures answer correctness after shuffle via remapping indices/IDs.

## 6) Daily Quiz Logic (DailyQuizService + StreakService)
- Auto-generate 7 days: per day, general + per stream quizzes using ladder 5 easy / 3 medium / 2 hard; shuffle; insert into `daily_quiz_schedule`.
- `startDaily` consumes schedule; `recordAttempt` stores score/coins.
- StreakService: continues streak if played yesterday; consumes freeze if gap; multiplier 1.0→2.0 max (5% per day); applies to base coins (e.g., 50).

## 7) Risks / Gaps
- JSON cache is single point; if deleted mid-attempt, regeneration loses answers (submit relies on file). No DB autosave/backup.
- No checksum/signature on JSON; tampering risk if file accessible.
- Negative marking basis/unit may diverge between exam and question defaults; limited validation.
- Question options in room strip is_correct but not other metadata; ensure no leakage of correct_answer_json.
- Daily quiz question order may differ from generated ladder if SQL order changes; manual reorder attempted but depends on ID mapping.
- No rate limiting on saveAnswer/submit; potential abuse.
- Missing concurrency/versioning on attempts; multiple tabs could race.
- Limited audit logs for reward issuance and leaderboard updates.

## 8) Target Improvements (suggested backlog)
1) Add hash/nonce inside JSON cache and verify on submit; store backup (small) of answers in DB for resume.
2) Add autosave to DB every N seconds/answers to recover without file.
3) Harden validation: ensure attempt user matches session; reject unknown question IDs; enforce exam mode on fields.
4) Strengthen negative marking config: validate unit/basis; clamp rates; expose per-exam defaults in UI.
5) Daily quests: guarantee deterministic order == schedule input; store reward_coins in cache for transparency.
6) Observability: log shuffle seed, grading summary, reward payloads; alert on regeneration events.
7) Rate limit submit and saveAnswer per attempt/user; add idempotency for submit.

## 9) API/Endpoints (current)
- GET `/quiz/start/{slug}` → start
- GET `/quiz/start-daily` → startDaily
- GET `/quiz/room/{attemptId}` → room
- POST `/quiz/save-answer` → saveAnswer (AJAX)
- POST `/quiz/submit` → submit
- GET `/quiz/result/{attemptId}` → result

## 10) Data Contracts (runtime JSON cache)
```json
{
  "attempt_id": int,
  "user_id": int,
  "exam": {
    "title": "...",
    "duration_minutes": int,
    "shuffle_questions": bool,
    "negative_marking_rate": float,
    "negative_marking_unit": "percent|fixed",
    "negative_marking_basis": "per-q",
    "mode": "exam|practice"
  },
  "questions": [
    {
      "id": int,
      "type": "mcq_single|true_false|MULTI|ORDER",
      "content": {...}, "options": [...],
      "correct_answer" / "correct_answer_json": present but not exposed in view,
      "default_marks": float,
      "default_negative_marks": float,
      "difficulty_level": int,
      "explanation": string|null
    }
  ],
  "answers": { "question_id": selected_options },
  "start_time": timestamp,
  "daily_quiz_id": int|null
}
```

## 11) Open Questions
- Should we persist per-answer timestamps for anti-cheat and pacing analytics?
- Should “practice” mode bypass negative marking or leaderboard updates?
- Should we support partial credit for ORDER/MULTI?
- Do we need deterministic shuffle with seed for dispute resolution?
- Should daily quests reuse blueprint-based generation instead of ladder?
