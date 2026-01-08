# Dual-Track Career System and Rank Ladder

## Overview
Civil City serves two distinct audiences: PSC exam aspirants and practicing engineers. Mixing their content degrades engagement. The dual-track system lets each user choose a mode that filters quiz content and tailors progression, while keeping a unified economy and PUBG-style rank ladder.

## 0. Architecture Snapshot (What to Build)
- **Toggleable Study Mode**: Stored on user profile; filters question pools and promotion exams.
- **Question Tagging**: `target_audience` plus optional granular metadata (subject, difficulty, regulation, code).
- **XP/Coins Engine**: Unified economy; XP for learning actions; coins for sinks (promotion exams).
- **Promotion Exams**: Timed, mode-aware, monetized; promotion state machine with cooldowns.
- **Badges & Leaderboards**: Visual rank assets, shareable; Chief Engineer limited to Top 100.
- **Telemetry & Abuse Controls**: Rate limits, anti-skipping, audit trails.

## 1. Choose Your License (Onboarding Toggle)
- **Modes**:
  - **🛡️ Loksewa Warrior (PSC Focus)**
    - Goal: Pass government exams.
    - Content filter: syllabus-only questions (NEMA, NEA, national codes).
    - Query: `SELECT * FROM questions WHERE target_audience IN ('psc_only','universal')`.
  - **🏗️ Site Master (World Focus)**
    - Goal: Master practical engineering and international standards.
    - Content filter: practical + syllabus.
    - Query: `SELECT * FROM questions WHERE target_audience IN ('world_only','universal','psc_only')` optional inclusion; base design includes practical + universal (configurable).
- Users toggle mode during onboarding or in settings. Toggle can be changed anytime (“Today PSC prep, tomorrow site reality”).

## 2. Rank Ladder (PUBG Style Progression)
Seven tiers; XP + promotion exam required:
| Rank | Icon | XP | Promotion Exam |
| --- | --- | --- | --- |
| 1. **Intern** (Bronze) | 👷 Yellow Helmet | 0 | Starting rank |
| 2. **Surveyor** (Silver) | 🔭 Theodolite | 500 XP | Pass “Basic Surveying” (20 Qs) |
| 3. **Site Supervisor** (Gold) | 📋 Clipboard | 2,000 XP | Pass “Site Management” (hard) |
| 4. **Assistant Engineer** (Platinum) | 🏗️ Crane | 5,000 XP | PSC Checkpoint exam |
| 5. **Senior Engineer** (Diamond) | 💻 Blueprints | 15,000 XP | Pass “Structural Analysis” |
| 6. **Project Manager** (Crown) | 🏙️ Skyline | 50,000 XP | Pass “Estimation & Costing” |
| 7. **Chief Engineer** (Conqueror) | 🎖️ Golden Hard Hat | 100,000 XP | Restricted to Top 100 leaderboard |

## 3. Promotion Exam Event Flow
1. **Eligibility Trigger**: When XP threshold met, prompt user: “Eligible for promotion to [Next Rank]. Take exam now?”
2. **Entry Fee**: Costs 100 Coins (economy sink). Locked coin deducted via transaction.
3. **Exam**:
   - Timed (e.g., 50 questions / 45 minutes).
   - PSC mode: questions from `target_audience IN ('psc_only','universal')`.
   - World mode: heavier on practical (`'world_only'` + universal + optional syllabus).
4. **Outcome**:
   - Pass: upgrade rank, show badge animation, increase daily coin earning rate.
   - Fail: retry after 24h; coins not refunded.

## 4. Visual Assets (Badges)
- Prepare badge images: `badge_intern.png`, `badge_surveyor.png`, …, `badge_chief_engineer.png`.
- Include PSC vs World flair if desired (e.g., subtle overlays).
- Encourage shareability (high-polish PNGs, transparent background).

## 5. Database Updates
```sql
ALTER TABLE questions ADD COLUMN target_audience ENUM('universal','psc_only','world_only') DEFAULT 'universal';
ALTER TABLE users ADD COLUMN rank_title VARCHAR(50) DEFAULT 'Intern';
ALTER TABLE users ADD COLUMN study_mode ENUM('psc','world') DEFAULT 'psc';
```
- Ensure question import CSV includes `target_audience`.
- Optionally add `rank_icon_path`, `promotion_cooldown_until` fields for advanced features.

## 6. Quiz Filtering Logic
- On quiz fetch, apply user’s `study_mode` to filter by `target_audience`.
- For mixed practice sessions (World mode), include universal + practical (optionally PSC for cross-training).
- Provide admin UI to tag questions accordingly.
- **Difficulty & Topic filters** (recommended columns): `difficulty ENUM('easy','medium','hard')`, `topic VARCHAR`, `code_ref VARCHAR` (e.g., IS 456, Eurocode 2) to build curated exam pools.
- **Fallback rule**: If pool < required questions, backfill with `universal` to prevent empty exams.

## 7. XP & Economy Mechanics
- **XP Sources**: Quiz completions, streak bonuses, practice sessions, promotion exam passes. Weight PSC-mode quizzes slightly higher for syllabus retention; weight practical cases for World mode.
- **Coin Sinks**: Promotion exam entry, cosmetic badge effects, optional rematch boosts.
- **Anti-grind caps**: Daily XP soft cap with diminishing returns to prevent farming; streaks bypass cap for consistent learners.
- **Salary Boosts**: Each rank increases daily coin stipend; store `salary_rate` per rank in config table.

## 8. Promotion State Machine (Backend)
- States: `ineligible` → `eligible` → `in_exam` → (`passed` | `failed_cooldown`).
- **Transactions**: Deduct exam fee + lock exam attempt in one transaction. On pass, update rank + salary; on fail, set cooldown timestamp.
- **Cooldown**: 24h (configurable). Block re-entry until cooldown expires.
- **Attempt logging**: Store exam_id, questions served, score, duration, IP/device for audits.

## 9. UX Implementation Notes
- **Onboarding Modal**: after signup, display “Choose Your License: Loksewa Warrior / Site Master”.
- **Profile Settings Switch**: toggle with explanatory text (“Toggle to switch syllabus focus”).
- **Rank Ladder UI**: show current rank, XP progress bar, upcoming promotion requirement.
- **Promotion Exam Dialog**: summarize cost, time, subject coverage.
- **Failure Messaging**: encourage retry; show countdown timer.
- **PSC vs World cues**: Use iconography/colors to indicate current mode on quiz lists and exams to avoid mode confusion.

## 10. APIs & Services (Suggested Endpoints)
- `GET /api/quiz/questions?mode=psc|world&topic=...&limit=...` — applies audience filter and topic/difficulty constraints.
- `POST /api/exams/promotion/start` — checks eligibility, deducts coins, creates attempt, returns question set.
- `POST /api/exams/promotion/submit` — grades, updates rank/salary/cooldown, records telemetry.
- `POST /api/user/mode` — switch study mode (rate-limited, audit).
- `GET /api/ladder` — returns rank definitions, XP thresholds, benefits.

## 11. Data Model Extensions
- `user_ranks` table (optional) to map rank → icon, salary, xp_min, xp_max, exam_pool_id, exam_fee, cooldown_hours.
- `exam_attempts` table: user_id, rank_target, score, passed, started_at, finished_at, ip, device_id, cooldown_until.
- `question_tags` (bridge) for flexible tagging beyond audience (subject, code_ref).
- `leaderboards` cache table/json for Top 100 Chief Engineer eligibility.

## 12. Integrity, Security, and Abuse Controls
- **Rate limiting**: Mode switches and exam starts to prevent spam.
- **Integrity**: Sign exam payloads with HMAC; validate on submit to prevent tampering.
- **Randomization**: Shuffle question order and options; seed stored in attempt record.
- **Proctor-lite**: Track focus loss events, rapid submits, repeated IP/device anomalies.
- **Audit**: Log economy transactions (coin deduction, salary grants) with references to exam_attempt_id.

## 13. Telemetry & Analytics
- Track: mode distribution, pass/fail per rank, median score per audience, dropout points, XP velocity.
- A/B: exam fee pricing, cooldown duration, PSC-only vs blended pools for World mode.
- Leaderboard health: monitor how many reach rank 7; tune XP thresholds accordingly.

## 14. Action Checklist (Expanded)
1. Badge assets → `public/assets/badges/` with light/dark variants and share cards.
2. DB migrations: questions/users, plus optional `user_ranks`, `exam_attempts`, `leaderboards`.
3. Import pipeline: accept `target_audience`, difficulty, topic, code_ref columns.
4. Mode toggle UI + API with audit and rate limit.
5. XP thresholds + salary table seeded; promotion exam pools defined per rank/mode.
6. Economy hooks: coin deduction on exam start; salary accrual on schedule; cap daily XP/coins.
7. Exam engine: timed delivery, HMAC-signed payloads, cooldown logic, telemetry.
8. Leaderboards: daily/weekly refresh; enforce Top 100 gate for Chief Engineer.
9. QA/Test: see section 15.

## 15. Testing Plan (Essentials)
- **Unit**: audience filtering, XP calculations, economy transactions, cooldown math.
- **Integration**: start/submit promotion exam transactional integrity; rank upgrades; mode toggle persistence.
- **Security**: HMAC tampering, rate limits, duplicate submissions, replay protection.
- **UX**: countdown timers, failure retry messaging, badge display per rank.
- **Data**: import validations for `target_audience` and tags; fallback pools when insufficient questions.

## 16. Confirmation
This dual-mode system keeps PSC aspirants focused while motivating seasoned engineers with practical challenges and competitive progression, with detailed implementation steps, security controls, and testing guidance to ship confidently.
