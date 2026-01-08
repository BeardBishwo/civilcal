# Dual-Track PSC/World Integration Plan

## Purpose
Single-source implementation brief to align devs on adding PSC vs World study modes across quiz, onboarding, admin authoring, and leaderboards. Builds on existing docs:
- `Dual-Track Career System and Rank Ladder.md` (mode filters, rank ladder)
- `Suggestion Engine and Onboarding Controller.md` (identity/goal/interests, feed logic)

## Current Gaps (from codebase traces)
- **Question fetch**: No `target_audience` filter in `ExamEngineController` question query (Room data + fetch). Missing WHERE for PSC/World modes.
- **Exam metadata**: Room load lacks study_mode info; attempts created without mode context.
- **Rank calculation**: `ProfileController`/`RankService` ignores study_mode and new ladder (still legacy power-based ranks).
- **Admin authoring**: `QuestionBankController` lacks `target_audience` field; exam builder UI missing audience filter/column.
- **Schema**: `quiz_questions` table missing `target_audience`; `users` table missing `study_mode` and `rank_title`; no category/subject mappings for feed.
- **Leaderboards/rewards**: Aggregation not segmented by mode; rewards not mode-aware but likely unaffected.
- **Onboarding/feed**: APIs not present; study_mode not captured from onboarding.

## Data Model & Migration
1) **Users**
- Add columns: `study_mode ENUM('psc','world') DEFAULT 'psc'`, `rank_title VARCHAR(50) DEFAULT 'Intern'`, optional `identity ENUM(...)`, `goal ENUM('exam','practical')` (from onboarding), `preferences JSON`.
- Backfill: set `study_mode='psc'`, `rank_title='Intern'` for existing users.

2) **Quiz Questions** (`quiz_questions`)
- Add `target_audience ENUM('universal','psc_only','world_only') DEFAULT 'universal'`.
- Add optional `category_id INT NULL`, `subject_id INT NULL`, `type ENUM('academic','practical','general') DEFAULT 'academic'`, `is_trending TINYINT(1) DEFAULT 0` (supports feed weighting).
- Indexes: `(target_audience)`, `(category_id)`, `(subject_id)`, `(is_trending)`.

3) **Taxonomy**
- New tables: `categories(id, name, slug, icon)`, `subjects(id, name, category_id, code_ref, level, is_practical)`.
- New join: `user_interests(user_id, category_id, created_at)`.

4) **Promotion Exams/Telemetry (optional now)**
- `exam_attempts` additions: store `study_mode` snapshot, `target_rank`, `cooldown_until`.
- `user_question_events` (optional) for feed tuning.

5) **Migration steps**
- Create migration scripts for added columns/tables; include safe defaults.
- Data backfill for `study_mode` and `rank_title`.
- Update import CSV templates to include `target_audience`, `category_id`, `subject_id`.

## Backend Changes by Area

### Quiz Flow (ExamEngineController)
- Load user `study_mode` with attempt/exam load.
- Question fetch: add WHERE `q.target_audience IN (:audiences)` mapping `psc -> ['psc_only','universal']`, `world -> ['world_only','universal','psc_only']` (configurable), and optional category/subject filters.
- Ensure ordering/shuffle respects existing flags; store selected questions and shuffle seed in attempt for audit.
- Submission: leave rewards unchanged; pass study_mode to leaderboard updates if we segment by mode.

### Attempt Creation
- When creating quiz_attempt, capture user study_mode for traceability.
- If study_mode toggled mid-attempt, keep attempt-mode immutable.

### Rank & Ladder
- Replace legacy power-based rank calc with 7-tier ladder (Intern → Surveyor → Site Supervisor → Assistant Engineer → Senior Engineer → Project Manager → Chief Engineer).
- Gate promotions via XP + promotion exams per dual-track doc; store `rank_title` on users.
- Update `ProfileController` to fetch ladder-aware rank; add study_mode-specific messaging if ladder diverges by mode.

### Leaderboard
- Add optional `study_mode` dimension to aggregates (weekly/monthly/yearly). If not splitting, at least ensure rank ladder gating (Top 100) uses unified scores.
- Update queries to read attempt.study_mode; cache keys include mode when split.

### Admin Question Authoring (QuestionBankController)
- Add form field and validation for `target_audience` ENUM (default universal).
- Persist to `quiz_questions`.
- Display audience tag in list views and exam builder selections.

### Exam Builder (Admin ExamController)
- When listing/selecting questions, show `target_audience` and allow filter by audience and category/subject.
- Warn if exam mode conflicts with selected question audiences (e.g., PSC exam contains world_only questions).

### Onboarding & Suggestion Engine APIs
- `POST /api/onboarding/set_profile`: save identity, goal, study_mode, preferences.
- `POST /api/onboarding/set_interests`: upsert `user_interests` for categories.
- `GET /api/feed/questions?limit=`: assemble interest + trending + challenge pools with mode filter; return metadata (category, subject, difficulty, target_audience).
- `GET /api/explore/categories` & `GET /api/explore/subjects?category_id=`: expose taxonomy with counts; accept mode filter for available questions.
- Rate-limit onboarding writes; validate user ownership.

### Mode Toggle
- `POST /api/user/mode`: switch study_mode; audit and rate-limit; invalidate feed cache; does not retro-change in-flight attempts.

### Integrity & Telemetry
- Sign exam payloads (HMAC) including study_mode, question_ids, shuffle seed.
- Log economy transactions with attempt_id; store mode in audit.
- Track skips/likes/time_spent for feed tuning; weekly cron to set `is_trending`.

## Implementation Sequence (suggested)
1) Ship DB migrations + backfill scripts (users, quiz_questions, taxonomy tables).
2) Update admin question create/edit to capture `target_audience`; enforce in exam builder.
3) Wire study_mode into quiz attempt load and question fetch; add filter logic and tests.
4) Implement onboarding/profile APIs to set study_mode + interests; expose mode toggle endpoint.
5) Build feed/explore endpoints using category/subject mappings and mode-aware filters.
6) Replace rank service with ladder logic; add promotion exam stubs if not immediate.
7) Extend leaderboard aggregation to optionally segment by study_mode.
8) Add telemetry + cron for trending flags; tighten audit/rate limits.

## Testing Checklist
- **Unit**: question query audience filter; fallback to universal when pool insufficient; onboarding save validations; rank ladder progression logic; leaderboard mode dimension.
- **Integration**: end-to-end exam start→submit with mode-locked attempt; admin question create with audience; feed returns mode-filtered mix; mode toggle persists and respected in subsequent exams.
- **Data**: migration adds columns with defaults; import/export handles `target_audience`; backfill success.
- **Security**: HMAC tamper check on submit; rate-limit mode toggle and onboarding; permission checks on admin endpoints.

## Open Questions (for team)
- Should World mode include PSC questions by default or only universal+world? (config flag)
- Do leaderboards split by study_mode or remain unified with mode badges?
- Promotion exams per mode: single pool with filter vs dedicated exams per rank+mode?
- Are category/subject mappings already available elsewhere to reuse?
