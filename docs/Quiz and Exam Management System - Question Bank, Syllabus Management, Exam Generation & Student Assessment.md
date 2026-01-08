# Quiz & Exam Management System — Question Bank, Syllabus, Exam Generation & Student Assessment

Single-file deep brief for devs covering end-to-end quiz/exam management: question authoring, syllabus editing, exam generation (auto/manual), student attempts, grading, and analytics. Includes observed risks/bugs for remediation.

## 1) Objectives & Success Criteria
- Maintain a high-quality question bank mapped to syllabus hierarchy and position levels.
- Provide robust syllabus authoring with hierarchical integrity and level settings.
- Generate exams from syllabus weights or manual selection with ordering and shuffle support.
- Deliver reliable student attempt lifecycle: start, cache, save answers, submit, grade, reward, report.
- Offer admin analytics for attempts, scores, and performance.

## 2) Core Components (inventory)
- Question bank: `Admin\Quiz\QuestionBankController` (store) + `quiz_questions`, `question_stream_map`, `question_position_levels`.
- Syllabus management: `Admin\Quiz\SyllabusController` (manage, bulkSave, generateExam), `syllabus_nodes`, `syllabus_settings`.
- Exam generation:
  - Auto: `ExamGeneratorService::generateFromSyllabus()` uses weighted syllabus nodes and multi-linkage SQL.
  - Manual: `Admin\Quiz\ExamController` (create/store/builder/addQuestionToExam) with `quiz_exam_questions`.
- Student engine: `Quiz\ExamEngineController` (start, initializeCache, saveAnswer, submit, result) + JSON cache in `storage/app/exams`.
- Scoring: `Quiz\ScoringService` (type-based correctness, negative marking).
- Analytics: `Admin\Quiz\ResultsController` (attempt list, stats, top performers).
- Gamification hooks: `GamificationService` invoked on submit; LeaderboardService updates rank.

## 3) Key Flows (happy paths)
1) **Question creation**  
   POST /admin/quiz/questions/store → QuestionBankController::store → resolveFilterContext via SyllabusService → insert quiz_questions → map to position_levels and syllabus nodes (question_stream_map).
2) **Syllabus manage**  
   manage(level) loads tree → grid UI → bulkSave deletes existing level nodes, rebuilds hierarchy from depth, saves syllabus_settings.
3) **Auto exam generation (syllabus)**  
   Admin trigger → ExamGeneratorService.generateFromSyllabus(level) → fetch weighted nodes → queryQuestions (multi-linkage) with fallbacks → optional shuffle → saveGeneratedExam inserts quiz_exams + quiz_exam_questions.
4) **Manual exam build**  
   ExamController.create/store → builder loads existing questions → addQuestionToExam inserts quiz_exam_questions with order.
5) **Student attempt lifecycle**  
   start(slug): fetch exam, reuse or create attempt, initializeCache (questions join quiz_exam_questions, optional shuffle, decode JSON) → room displays cached questions sans answers → saveAnswer AJAX updates JSON → submit validates nonce/trap, reads JSON, grades all, bulk inserts quiz_attempt_answers, updates quiz_attempts, triggers rewards & leaderboard, deletes cache → result shows score + incorrect answers.
6) **Analytics**  
   ResultsController.index: fetch attempts (users/exams), compute avg/max score, pass rate, top performers; paginate.

## 4) Data & Contracts (runtime)
- quiz_questions: content/options JSON, correct_answer/json, difficulty_level, mappings to syllabus and position levels.
- syllabus_nodes: parent_id, level, order/depth, type; syllabus_settings: total_marks, total_time, pass_marks, negative_rate.
- quiz_exams: metadata (duration, marks, shuffle, neg marking); quiz_exam_questions: ordered links.
- quiz_attempts/quiz_attempt_answers: attempt status, scores, per-question answers.
- Attempt cache JSON: {attempt_id, user_id, exam, questions[], answers{}, start_time, daily_quiz_id?}

## 5) Risks / Bugs / Gaps (actionable)
1) **Cache single point & tampering**: Answers live only in JSON; cache loss => data loss; no checksum/signature → tamper risk.  
2) **Bulk save syllabus destructive**: Deletes all nodes for level before rebuild; failure mid-loop can leave level empty; no transaction/backup.  
3) **Question mapping minimal validation**: question_stream_map insert trusts posted unit_id; no check on level/type match; duplicates possible.  
4) **Exam generation duplicates**: queryQuestions across multiple linkages can return same question; dedup safeguards unclear → possible repeated questions.  
5) **Shuffle correctness**: ShuffleService remaps answers, but ORDER type grading expects arrays; ensure options have stable IDs—risk if missing IDs.  
6) **Negative marking config drift**: Exam uses negative_marking_rate/unit/basis; ScoringService defaults per question mark; inconsistent configs can misgrade.  
7) **No attempt versioning/locking**: Multiple tabs could submit same attempt; no idempotency on submit or saveAnswer rate limit.  
8) **Syllabus bulkSave depth stack**: Depth > stack size or malformed depths could mis-parent nodes; no validation of type/level consistency.  
9) **Exam builder order**: addQuestionToExam sets order = max+1; concurrent adds can collide; no unique constraint on (exam_id, question_id).  
10) **Analytics queries**: ResultsController uses aggregate queries without filters; heavy tables may impact performance; lacks date/user filters.

## 6) Suggested Fixes / Improvements (prioritized)
1) Add checksum (HMAC) to attempt JSON + optional periodic DB autosave of answers; verify on submit.  
2) Wrap syllabus bulkSave in transaction; pre-backup nodes; validate depth/parent consistency before delete.  
3) Enforce unique (exam_id, question_id); use transaction + FOR UPDATE or insert-ignore with ordering token in builder.  
4) Deduplicate question selection in ExamGeneratorService (tracking IDs across linkages/fallbacks); enforce no repeats per exam.  
5) Validate question_stream_map target level/type; prevent duplicates; ensure resolveFilterContext fallback not null.  
6) Harden negative marking: clamp rates, validate unit/basis; ensure UI surfaces exam-level settings; default per-q negative marks if present.  
7) Add rate limits and idempotency keys for submit and saveAnswer; reject duplicate submits for same attempt.  
8) Add ORDER/MULTI option ID validation in shuffle/grading; ensure options carry stable IDs.  
9) Add filters/pagination options to ResultsController (date range, exam, user) and indexes to attempts/answers for reporting.  
10) Transactionalize multi-step operations in generation/save (insert exam + questions) to avoid partial writes.

## 7) Open Questions
- Should question bank enforce per-level uniqueness of syllabus mapping or allow multi-level tagging?  
- Should autosave answers to DB periodically to reduce cache-loss risk?  
- How to handle retakes: allow resume or force new attempt per exam?  
- Do we need per-exam configurable shuffle seed for dispute resolution?  
- Should syllabus types/levels be validated against allowed hierarchies (course → level → category → unit)?  
- Should generation enforce difficulty quotas or respect question difficulty from syllabus nodes?
