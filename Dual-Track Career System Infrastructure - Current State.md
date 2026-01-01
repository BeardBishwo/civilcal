# Dual-Track Career System Infrastructure – Current State

## Overview
Current quiz/gamification stack: exams, rewards, ranks, shop, battle pass. Dual-track modes (PSC vs World) and target_audience filtering are **not yet implemented**. This document captures what exists today and what is missing to support the planned dual-track career system.

## URLs (Base: http://localhost/Bishwo_Calculator)
- Quiz portal: `/quiz`
- Start exam: `/quiz/start/{slug}` (auth)
- Exam room: `/quiz/room/{attemptId}` (auth)
- Submit exam: `/quiz/submit` (POST, auth, CSRF, nonce)
- Shop: `/quiz/shop` (auth)
- Battle pass: `/quiz/battle-pass` (auth)

## 1) Quiz Lifecycle (Implemented)
- Portal load: `/quiz` → `PortalController@index`; triggers daily login bonus (`GamificationService::processDailyLoginBonus`).
- Start exam: `ExamEngineController@start` checks existing attempt or inserts `quiz_attempts` (status=ongoing), redirects to room.
- Room: loads exam/questions via `quiz_exam_questions` join; optional shuffle; renders interface.
- Submit: `ExamEngineController@submit` grades all answers, computes score, calls `processExamRewards`, updates attempt status completed, updates leaderboard.

## 2) Rewards & Economy (Implemented)
- `GamificationService::processExamRewards` aggregates correct answers by difficulty (easy/medium/hard) → coins/materials/xp; single transaction updates `user_resources`; logs.
- Hooks: `MissionService::updateProgress`, `BattlePassService::addXp` (1000 XP/level), leaderboard update.
- Shop purchases: `GamificationController@purchaseResource` with rate limit, nonce, security validation; atomic coin deduction + resource add.

## 3) Ranks (Implemented, legacy power-based)
- `RankService::getUserRankData` computes tier from knowledge/precision/status scores (quizzes/calcs/news + weighted resources). Not yet mapped to new 7-rank ladder from dual-track spec.

## 4) Taxonomy (Implemented)
- Admin syllabus: `/admin/quiz/syllabus` manages `quiz_categories` → `quiz_subjects` → `quiz_topics` (FK chain). Needed for mapping category/subject/topic in future mode/interest filters.
- Question bank: stores `difficulty_level`, topic_id, marks/negative, content/options JSON, tags.

## 5) Leaderboard (Implemented)
- `LeaderboardService::updateUserRank` upserts aggregates per user/period/category in `quiz_leaderboard_aggregates` (weekly/monthly/yearly). Uses score and accuracy.

## 6) Shop & Economy Security (Implemented)
- Rate limiter, nonce validation, resource whitelist, economic validation before DB update. Transactions logged in `user_resource_logs` and coin moves in `user_transactions` (where applicable).

## 7) Battle Pass (Implemented)
- XP accrual via rewards; 1000 XP/level; claim rewards grants coins/materials/lifelines/buildings; persists to `user_battle_pass` and user inventory/resources.

## 8) What’s Missing for Dual-Track (PSC vs World)
- **study_mode** field (psc/world) on users/profile.
- **target_audience** usage in question fetch (currently not filtering by mode).
- **Rank ladder mapping** to new 7-tier system with promotion exams and coin sinks.
- **Promotion exams & cooldowns** (state machine not wired).
- **Mode-aware feeds/exams**: selection of exams/questions per mode not implemented.
- **Onboarding toggle UI/API** to choose mode.

## 9) Data Tables (Current Core)
- `quiz_attempts`, `quiz_attempt_answers`
- `quiz_questions`, `quiz_exam_questions`, `quiz_exams`
- `quiz_categories`, `quiz_subjects`, `quiz_topics`
- `user_resources`, `user_battle_pass`, `quiz_leaderboard_aggregates`
- Security: `quiz_sessions` (nonces), `security_logs`

## 10) Minimal Next Steps to Enable Dual-Track
1) Schema: add `study_mode ENUM('psc','world')` to users (or profile), default 'psc'.
2) Apply `target_audience` filter when fetching questions/exams based on `study_mode`.
3) Define 7-rank ladder config (thresholds, fees, rewards) and promotion exam pools.
4) Implement promotion state machine with coin sink (exam fee), cooldowns, and rank updates.
5) UI: mode toggle in profile/onboarding; rank ladder UI reflecting new tiers.
6) Update leaderboard views to show mode/category context if needed.

## 11) Quick QA Checklist (Current System)
- Start/room/submit flows work; nonce + CSRF + honeypot enforced.
- Rewards applied by difficulty; leaderboard updates per submission.
- Shop purchase enforces rate limit, nonce, validation; DB updates atomic.
- Battle pass XP increments and rewards claimable; resources granted.

## 12) Hardening Ideas
- Rate limits per attempt for save/submit; lock attempt post-submit.
- Payload signing for answer/submit.
- Mode-specific leaderboards once study_mode exists.
- Align RankService with new 7-tier ladder and promotion exams.
