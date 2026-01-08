# Suggestion Engine & Onboarding Flow – Existing vs Planned

## Overview
Current quiz stack has taxonomy, portal, exam engine, rewards, and leaderboard, but no onboarding or personalized feed. This document contrasts implemented pieces with missing/planned features for the future suggestion engine.

## URLs (Base: http://localhost/Bishwo_Calculator)
- Quiz portal (non-personalized): `http://localhost/Bishwo_Calculator/quiz`
- Exam overview: `/quiz/overview/{slug}`
- Start exam: `/quiz/start/{slug}` (auth)
- Exam room: `/quiz/room/{attemptId}` (auth)
- Submit: `/quiz/submit` (POST, auth, CSRF, nonce)

## Implemented Infrastructure (Existing)
- **Taxonomy**: `quiz_categories` → `quiz_subjects` → `quiz_topics`; admin CRUD at `/admin/quiz/syllabus`.
- **Question Bank**: Questions store `topic_id`, `difficulty_level`, content/options JSON, marks, tags.
- **Portal**: `/quiz` lists categories/exams without personalization (PortalController@index).
- **Exam Engine**: start → room → save → submit; secure with auth + CSRF + nonce + honeypot; scoring and reward distribution.
- **Rewards**: `GamificationService::processExamRewards` by difficulty; updates `user_resources`, missions, battle pass.
- **Leaderboard**: `LeaderboardService::updateUserRank` aggregates weekly/monthly/yearly with accuracy and score.
- **Security**: Auth middleware, CSRF middleware, nonces (quiz_sessions), honeypot traps.

## Missing / Planned (Not Implemented)
- **Onboarding data capture**: identity (Student/Site Engineer/PSC Aspirant/Senior), goal (exam/practical), study_mode (psc/world).
- **user_interests table**: user_id, category_id, created_at.
- **Question target_audience** filtering (universal/psc_only/world_only) tied to study_mode.
- **Personalized feed API**: `/api/feed/questions` with weighted pools (interests ~60–70%, trending 15–20%, challenge 15–20%).
- **Onboarding APIs**: `POST /api/onboarding/set_profile`, `POST /api/onboarding/set_interests`.
- **Explore APIs**: categories/subjects for browsing with counts.
- **Telemetry**: skip/like/time_spent to tune feed; trending flagging automation.

## Gaps in Current DB Schema
- `users` table missing: identity, goal, study_mode (psc/world), rank_title for dual-track ladder.
- `quiz_questions` missing: target_audience ENUM for mode-aware filtering.
- `user_interests` table not present.

## Bridging Plan (Minimal Next Steps)
1) **Migrations**:
   - Add `users.study_mode ENUM('psc','world')`, `users.identity`, `users.goal`, `users.rank_title` (if using ladder), default study_mode='psc'.
   - Add `quiz_questions.target_audience ENUM('universal','psc_only','world_only')`.
   - Create `user_interests(user_id FK, category_id FK, created_at)`.
2) **APIs**:
   - `POST /api/onboarding/set_profile` (identity, goal, study_mode).
   - `POST /api/onboarding/set_interests` (category_ids[] upsert).
   - `GET /api/feed/questions?limit=20` (assemble interest/trending/challenge pools with audience + difficulty mix).
3) **Feed Logic**:
   - Interest pool from `user_interests`; trending from engagement flag; challenge from outside interests with higher difficulty.
   - Weight presets: exam goal → 70/15/15 (easy/medium bias); practical goal → 60/20/20 (medium/hard bias).
   - Fallback to universal/trending when interests empty or pool too small.
4) **Portal Integration**:
   - Replace `/quiz` list with personalized feed; keep explore categories/subjects as secondary navigation.
5) **Telemetry**:
   - Log skips/likes/time_spent; periodic job to set `is_trending`.

## Quick QA (Current State)
- Syllabus admin CRUD works; question bank stores difficulty/topic.
- Portal shows exams but not personalized.
- Exams run end-to-end with security (auth, CSRF, nonce, honeypot) and rewards/leaderboard updates.

## Future Hardening
- Rate limit feed and onboarding APIs.
- Payload signing for feed selection (optional).
- Mode-specific leaderboards once study_mode exists.
- Privacy/consent messaging for personalization data.
