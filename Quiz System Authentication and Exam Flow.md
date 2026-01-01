# Quiz System Authentication and Exam Flow

## Overview
End-to-end flow for quiz exams: request entry, middleware security (auth, CSRF), exam lifecycle (start → room → save → submit), nonce and honeypot protections, scoring, rewards, and leaderboard updates. Includes full localhost URLs for quick verification.

## 1) URLs (Base: http://localhost/Bishwo_Calculator)
- Portal: `/quiz`
- Overview: `/quiz/overview/{slug}`
- Start exam (auth): `/quiz/start/{slug}`
- Exam room (auth): `/quiz/room/{attemptId}`
- Save answer (POST, auth, CSRF): `/quiz/save-answer`
- Submit exam (POST, auth, CSRF): `/quiz/submit`
- Result (auth): `/quiz/result/{id}`
- Leaderboard: `/quiz/leaderboard`

## 2) Request Entry & Middleware Pipeline
- Entry: `public/index.php` → `Security::startSession()`, secure headers, HTTPS enforcement.
- Router: parses URI/method, auto-injects CSRF middleware for POST/PUT/PATCH/DELETE (`app/Core/Router.php`@48-50).
- Pipeline: SecurityMiddleware → AuthMiddleware → CsrfMiddleware → Controller.

## 3) Authentication (AuthMiddleware)
- Checks HTTP Basic Auth, else session (`$_SESSION['user_id']`).
- API failures → 401 JSON; web → redirect /login.
- Runs before controllers like ExamEngineController.

## 4) CSRF Protection (CsrfMiddleware)
- Ensures token exists; validates header `HTTP_X_CSRF_TOKEN` or `csrf_token` POST.
- On invalid: 419 + JSON error or text.
- Token generation: `Security::generateCsrfToken()` stores in session.

## 5) Exam Start Flow (`GET /quiz/start/{slug}`)
Controller: `ExamEngineController@start`
1) Auth required (`requireAuth`).
2) Fetch exam by slug.
3) Check ongoing attempt for user/exam. If exists → redirect `/quiz/room/{attemptId}`.
4) Else insert new `quiz_attempts` (status=ongoing, started_at=NOW()), redirect to room.

## 6) Exam Room Load (`GET /quiz/room/{attemptId}`)
Controller: `ExamEngineController@room`
- Validate attempt ownership (user_id matches).
- Fetch exam + questions (join exam_questions → questions); optional shuffle.
- Hide correct answers; load saved answers for resume.
- Generate submission nonce (`NonceService->generate(user_id,'quiz')`).
- Generate CSRF token for AJAX saves.
- Render `quiz/arena/room.php` with questions, attemptId, exam, savedAnswers, nonce, csrfToken, review flags.
- Frontend sets timer, loads first question, creates honeypot input `trap_answer` (hidden).

## 7) Save Answer (AJAX) (`POST /quiz/save-answer`)
Controller: `ExamEngineController@saveAnswer`
- Inputs: attempt_id, question_id, selected_options, trap_answer, csrf_token.
- Security:
  - Auth + CSRF middleware.
  - Honeypot check: if `trap_answer` filled → log via `SecurityMonitor`, 400 response.
  - Validate attempt ownership (user_id matches attempt).
- Upsert answer: `INSERT ... ON DUPLICATE KEY UPDATE selected_options` into `quiz_attempt_answers`.

## 8) Submit Exam (POST) (`/quiz/submit`)
Controller: `ExamEngineController@submit`
- Inputs: attempt_id, nonce, trap_answer, csrf_token.
- Security:
  - Honeypot check → log + reject if filled.
  - Nonce validation/consume (`NonceService::validateAndConsume`) prevents replay; 30m expiry.
  - Auth + CSRF middleware.
- Flow:
  1) Fetch attempt + exam; load all answers.
  2) For each question: compare saved answer vs correct; apply marks/negative; update answer record.
  3) Process rewards (`GamificationService::processExamRewards`) → coins/resources, logs; also BattlePass XP, Mission progress.
  4) Update attempt status to completed with total score.
  5) Leaderboard update (`LeaderboardService::updateUserRank`).
  6) Redirect to `/quiz/result/{attemptId}`.

## 9) Security Components
- **NonceService**: stores nonces in `quiz_sessions`; checks consumed/expired; logs replay attempts (`SecurityMonitor`).
- **Honeypot traps**: hidden field `trap_answer`; triggers security log and optional IP ban on critical events.
- **CSRF**: middleware enforced on state-changing routes.
- **Auth**: session/HTTP Basic auth via AuthMiddleware.
- **SecurityMonitor**: logs events to `security_logs`; auto-ban on critical (e.g., honeypot endpoints).

## 10) Data Touchpoints
- `quiz_attempts`: attempt lifecycle (status, started_at, completed_at, score).
- `quiz_exam_questions` + `questions`: source of exam content; optional shuffle.
- `quiz_attempt_answers`: saved answers per question (upserted).
- `quiz_sessions`: nonces for submission/security.
- `user_resources`: rewards credited on completion.
- `security_logs`: honeypot, nonce replay, CSRF failures.
- `leaderboard` tables: updated after submission.

## 11) Frontend Notes (room.php)
- Sets timer; loads questions; handles review markers.
- AJAX save uses CSRF token and honeypot value.
- Submit uses nonce + CSRF + honeypot.
- Graceful errors on expired/invalid tokens.

## 12) Quick Test Checklist
- Start exam: new attempt created; ongoing attempts resume room.
- Room load: nonce + CSRF present; questions shuffled if enabled; answers hidden.
- Save answer: honeypot empty → 200; ownership enforced; DB upsert.
- Submit: nonce consumed once; honeypot triggers reject; scoring correct; attempt status completed; rewards granted; leaderboard updated.
- Invalid tokens: CSRF → 419; nonce replay → reject/log; honeypot filled → log/ban.

## 13) Future Hardening
- Per-attempt rate limits on save/submit.
- Lock exam after submission to block further saves.
- Add signature to payloads for tamper detection.
- WebSocket/long-poll for timer sync in low-latency modes.
