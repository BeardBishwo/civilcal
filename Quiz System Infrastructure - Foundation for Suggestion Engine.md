# Quiz System Infrastructure – Foundation for Suggestion Engine

## Overview
Current quiz stack provides taxonomy, exam engine, reward loop, and leaderboard updates. This document maps what exists and what remains to implement for the planned Suggestion Engine and Onboarding (identity/goal/interest capture, personalized feeds). Includes localhost URLs for quick verification and highlights missing tables/fields.

## URLs (Base: http://localhost/Bishwo_Calculator)
- Quiz portal: `http://localhost/Bishwo_Calculator/quiz`
- Exam overview: `http://localhost/Bishwo_Calculator/quiz/overview/{slug}`
- Start exam: `http://localhost/Bishwo_Calculator/quiz/start/{slug}` (auth)
- Exam room: `http://localhost/Bishwo_Calculator/quiz/room/{attemptId}` (auth)
- Save answer: `http://localhost/Bishwo_Calculator/quiz/save-answer` (POST, auth, CSRF)
- Submit exam: `http://localhost/Bishwo_Calculator/quiz/submit` (POST, auth, CSRF, nonce)
- Leaderboard: `http://localhost/Bishwo_Calculator/quiz/leaderboard`

## 1) Taxonomy (Implemented)
- Admin syllabus manager: `/admin/quiz/syllabus` (auth+admin).
- Controllers: `Admin\Quiz\SyllabusController`.
- Tables (from `027_create_enterprise_quiz_tables.php`):
  - `quiz_categories` (user-facing streams)
  - `quiz_subjects` (FK → category)
  - `quiz_topics` (FK → subject)
- Admin CRUD routes for categories/subjects/topics are implemented.
- This hierarchy is the bridge for future category→subject mapping for suggestions.

## 2) Question Bank (Implemented)
- Admin routes: `/admin/quiz/questions`, `/admin/quiz/questions/store` etc.
- Controller: `Admin\Quiz\QuestionBankController`.
- Stored fields: topic_id (→ subject → category), difficulty_level (1–5), marks/negative marks, content/options JSON, tags JSON.
- Difficulty present and usable for challenge/easy mixes.

## 3) Quiz Discovery (Pre-Suggestion Engine)
- Portal: `/quiz` → `Quiz\PortalController@index`.
- Current behavior: fetch categories for filters; list latest exams (no personalization).
- This will be replaced/augmented by personalized feed (interests 60%, trending 20%, challenge 20%) when Suggestion Engine is built.

## 4) Exam Lifecycle (Implemented)
- Start: `/quiz/start/{slug}` → `ExamEngineController@start` creates/resumes `quiz_attempts` (status=ongoing) then redirects to room.
- Room: `/quiz/room/{attemptId}` → loads questions (`quiz_exam_questions` join questions), optional shuffle, hides correct answers, loads saved answers, issues nonce + CSRF, renders timer and honeypot field.
- Save answer: `/quiz/save-answer` → auth + CSRF + honeypot check, ownership validation, upsert into `quiz_attempt_answers`.
- Submit: `/quiz/submit` → honeypot + nonce validation (quiz_sessions), scores answers, updates attempt status completed, triggers rewards, updates leaderboard, redirects to result.

## 5) Rewards & Economy (Implemented)
- `GamificationService::processExamRewards` maps difficulty to rewards (coins/materials/xp), aggregates, and updates `user_resources` in one transaction.
- Reward table (current): easy ~ 5 coins / 1 brick / xp; medium ~ 10 coins / bricks+cement / xp; hard ~ 20 coins / steel / xp.
- Mission/Battle Pass hooks exist (XP added, mission progress updated).

## 6) Leaderboard (Implemented)
- After submission, `LeaderboardService::updateUserRank` aggregates per period (weekly/monthly/yearly) and category.
- Table: `quiz_leaderboard_aggregates` with unique constraint (user_id, period_type, period_value, category_id) for upserts.

## 7) Security (Implemented)
- Middleware: Auth (session or HTTP Basic), CSRF auto-injected for POST/PUT/PATCH/DELETE.
- Nonce: `quiz_sessions` for submission; replay attempts logged.
- Honeypot: hidden `trap_answer` in room/save/submit; triggers security log/ban on fill.
- Secure headers, session hardening in `public/index.php` and Security services.

## 8) Data Tables (Core Quiz)
- `quiz_categories`, `quiz_subjects`, `quiz_topics`
- `quiz_questions` (difficulty_level, content/options JSON, topic_id, marks)
- `quiz_exams`, `quiz_exam_questions`
- `quiz_attempts`, `quiz_attempt_answers`
- `quiz_sessions` (nonces)
- `quiz_leaderboard_aggregates`
- `user_resources`, `security_logs`

## 9) Missing for Suggestion Engine (Not Implemented)
- `user_interests` table (user_id, category_id, created_at).
- User profile fields: identity, goal, study_mode (psc/world) as per Spec. Extend `users` or add `user_profile` table.
- Feed endpoints: `/api/feed/questions`, onboarding APIs (`/api/onboarding/set_profile`, `/api/onboarding/set_interests`).
- Weighted feed logic (interests/trending/challenge), difficulty balancing, fallback rules, telemetry events.

## 10) Bridging Plan (Minimal Next Steps)
1) DB migrations:
   - Add `user_interests` (user_id FK, category_id FK, created_at).
   - Extend `users` (or new `user_profile`) with identity ENUM, goal ENUM('exam','practical'), study_mode ENUM('psc','world').
2) Seed/align taxonomy with desired 12–15 categories (map subjects to categories).
3) Implement onboarding APIs to capture identity/goal/interests.
4) Implement feed API to assemble pools: interests (60–70%), trending (15–20%), challenge (15–20%), filtered by target_audience and difficulty mix.
5) Add telemetry for skips/likes/time_spent to tune weights later.

## 11) Quick QA Checklist (Current System)
- Admin taxonomy CRUD works; topics/subjects/categories saved.
- Questions store difficulty/topic properly; options/content JSON saved.
- Start exam creates attempt; room shows questions; nonce+CSRF+honeypot present.
- Save answer enforces ownership; submit enforces nonce/honeypot; scoring correct; rewards applied; leaderboard updated.

## 12) Future Hardening
- Per-attempt rate limits on save/submit; lock attempt after submit.
- Payload signing for answer/submit to prevent tampering.
- WebSocket/long-poll for timer sync if latency-sensitive.
- Add category-aware leaderboard views to surface relevance in future feeds.
