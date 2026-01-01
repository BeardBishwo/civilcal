# Quiz System – Identity, Exam Delivery & Gamification (No Personalization) Plan

## Purpose
Single reference for the dev team to understand current quiz stack, identify missing personalization/onboarding, and outline changes to add user-mode/interest-aware delivery without breaking existing exams and rewards.

## Current State (per code traces)
- **Registration**: `Api\AuthController@register` captures username/email/name/phone; no identity/stream/preferences stored. Table `users` lacks preference/study_mode columns.
- **Portal**: `Quiz\PortalController@index` shows all categories (`quiz_categories`) and latest published exams—no filtering by user.
- **Exam engine**: `ExamEngineController` creates/resumes attempts and loads fixed exam question sets via `quiz_exam_questions` pivot; no per-user filtering or adaptive selection. Shuffle is deterministic by attempt ID only.
- **Scoring/Rewards**: Submission joins `quiz_questions` with answers, computes score, then `GamificationService::processExamRewards` grants resources/XP. Rewards are mode-agnostic.
- **Admin syllabus**: `Admin\Quiz\SyllabusController` manages category→subject→topic hierarchy; question bank schema (`027_create_enterprise_quiz_tables.php`) links questions to topics but has no `target_audience`/interest fields.
- **Leaderboards**: Aggregates scores globally (see `LeaderboardController/Service`); no segmentation by user attributes.

## Gaps
- No onboarding to capture identity/goal/interests/study_mode.
- No question audience targeting (`target_audience` missing) or user preference storage.
- Portal/exam flows ignore any personalization; every user sees same categories/exams.
- Leaderboard and rewards do not consider user segments/modes.

## Data Model Additions
1) **users**: add `identity ENUM(...)`, `goal ENUM('exam','practical')`, `study_mode ENUM('psc','world') DEFAULT 'psc'`, `preferences JSON` (for future flags).
2) **quiz_questions**: add `target_audience ENUM('universal','psc_only','world_only') DEFAULT 'universal'`, `category_id INT NULL`, `subject_id INT NULL`, `type ENUM('academic','practical','general') DEFAULT 'academic'`, `is_trending TINYINT(1) DEFAULT 0`.
3) **user_interests**: `user_id`, `category_id`, `created_at` (simple upsert table).
4) **(Optional)** telemetry `user_question_events` for skips/likes/time_spent to tune feeds.

## Backend Changes (MVP personalization)
- **Onboarding APIs**:
  - `POST /api/onboarding/set_profile {identity, goal, study_mode}` → store on users.
  - `POST /api/onboarding/set_interests {category_ids: []}` → upsert `user_interests`.
- **Portal**: fetch user profile; allow filter defaults by study_mode/interest categories; show generic if guest.
- **Question fetch** (exam creation or feed): apply audience filter based on `study_mode`:
  - PSC → `target_audience IN ('psc_only','universal')`
  - World → `target_audience IN ('world_only','universal','psc_only')` (configurable)
- **ExamEngine**: when loading attempt, attach user study_mode and enforce audience filter on question queries; store study_mode snapshot in attempt for audit.
- **Leaderboards**: optional mode dimension; at minimum display user mode badge; future split per mode.
- **Admin question authoring**: add form/select for `target_audience`; persist to `quiz_questions`.

## Phased Implementation
1) **Migrations**: users (identity/goal/study_mode/preferences), quiz_questions (target_audience + category/subject/type/is_trending), user_interests table. Backfill defaults.
2) **Admin UI**: update QuestionBank create/edit to include `target_audience`; validate and display.
3) **Onboarding APIs**: implement `set_profile`, `set_interests`; rate-limit and secure.
4) **Portal filter**: read user study_mode/interests; optionally default category view; keep public view unchanged for guests.
5) **ExamEngine filter**: apply audience WHERE clause; keep shuffle; store mode in attempt.
6) **Leaderboard tag**: append study_mode metadata; prepare for mode splits later.
7) **(Optional)** telemetry + trending cron for feed weighting.

## Testing Focus
- Migrations apply cleanly; defaults set for existing users/questions.
- Admin question create/edit saves `target_audience` and surfaces in listings.
- Onboarding endpoints persist identity/goal/study_mode/interests and reject invalid values.
- Exam question query respects study_mode and falls back to universal when pools are small.
- Leaderboard updates still function; mode metadata stored when available.

## Open Questions
- Do we split leaderboards by study_mode or only badge them?
- Should World include PSC questions by default or only universal+world (config flag)?
- How strict should portal personalization be—hard filter vs soft ordering?
