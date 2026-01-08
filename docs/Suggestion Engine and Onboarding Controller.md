# Suggestion Engine and Onboarding Controller

## Overview
A three-layer filtering system delivers personalized quiz/content feeds across 52 subjects condensed into ~12–15 user-facing categories. The experience begins with a Medium/Tumblr-style onboarding, captures identity, interests, and goals, and drives a Netflix-like feed with balanced relevance (interests), discovery (trending), and challenge (expansion).

## 0) Architecture Snapshot
- **Onboarding collector**: identity → interests → goal. Stores user preferences and mode.
- **Taxonomy bridge**: categories (user-facing) ← subjects (academic reality) → questions (tagged).
- **Suggestion engine**: pulls user interests, blends with trending/challenge pools; mode-aware (PSC vs World) if combined with dual-track.
- **APIs**: set interests, get feed questions, explore categories/subjects.
- **Storage**: user_interests, categories, subjects, question tags, optional telemetry for tuning weights.

## 1) Onboarding Flow (Collector)
- **Step 1: Identity** (one choice)
  - 🎓 Student (B.E./Diploma)
  - 👷 Site Engineer (Field Work)
  - 📚 PSC Aspirant (Loksewa Focus)
  - 🏗️ Senior Professional
  - Store as `user_profile.identity` (ENUM or lookup).

- **Step 2: Interests** (multi-select bubbles)
  - Show ~12–15 consolidated categories (e.g., Structure, Surveying, Water, Road, Management, Geotech, Hydro, Environment, Materials, Estimation/Contracts, Architecture/Planning, BIM/Software).
  - Each bubble maps to multiple academic subjects; store selected category_ids in `user_interests`.

- **Step 3: Goal**
  - "Pass exams" (bias to academic/PSC, easy/medium)
  - "Learn practical skills" (bias to site/practical, medium/hard)
  - Store as `user_profile.goal` and feed-weight preset.

- **Optional**: integrate existing `study_mode` (psc/world) toggle from dual-track system; pre-select goal accordingly.

## 2) Data Model
- **categories** (user-facing tags)
  - id, name, slug, icon
- **subjects** (academic reality)
  - id, name, category_id (FK), code_ref (optional: e.g., Eurocode/ACI/NS), level (I/II), is_practical BOOL
- **questions** (extend existing)
  - category_id (FK), subject_id (FK), target_audience ENUM('universal','psc_only','world_only'), difficulty ENUM('easy','medium','hard'), type ENUM('academic','practical','general'), tags JSON (topics), is_trending BOOL, last_used_at DATETIME
- **user_interests**
  - user_id, category_id (FK), created_at
- **user_profile** (or extend users)
  - identity ENUM(...), goal ENUM('exam','practical'), study_mode ENUM('psc','world'), rank_title (if dual-track), preferences JSON
- **telemetry (optional)**
  - user_question_events (user_id, question_id, outcome, time_spent, liked, skipped)

## 3) Category/Subject Consolidation (Example)
- Category: Structure → Subjects: Applied Mechanics, Strength of Materials, Structural Analysis I/II, RCC Design, Steel Design, Bridge, Earthquake Eng.
- Category: Surveying → Subjects: Surveying I, Surveying II, Survey Camp.
- Category: Water → Subjects: Hydrology, Irrigation, Sanitary/Water Supply, Hydraulics.
- Category: Road → Subjects: Transportation, Highway, Pavement, Traffic.
- Category: Management/Contracts → Subjects: Estimation & Costing, Project Mgmt, CPM/PERT, Contracts, FIDIC.
- Category: Geotech → Subjects: Soil Mechanics, Foundation Engineering.
- Category: Materials → Subjects: Construction Materials, Concrete Technology.
- Category: Environment → Subjects: Environmental Eng, Solid Waste, Air/Noise.
- Category: Architecture/Planning → Subjects: Town Planning, Building Planning.
- Category: BIM/Software → Subjects: AutoCAD, Revit, ETABS (for theory/usage questions).

## 4) Suggestion Engine Logic (Controller)
- **Inputs**: user_id, study_mode (psc/world), goal, interests (categories), identity.
- **Pools**:
  - **Interest Pool**: questions where `category_id IN user_interests`.
  - **Trending Pool**: `is_trending` OR high recent engagement.
  - **Challenge Pool**: categories not in interests; higher difficulty or adjacent domains.
- **Weights (baseline)**: 60% interests, 20% trending, 20% challenge. Make configurable per goal:
  - Exam goal: 70% interests (academic/easy/medium), 15% trending, 15% challenge.
  - Practical goal: 60% interests (practical/medium/hard), 20% trending, 20% challenge.
- **Mode filter** (if dual-track enabled):
  - PSC: `target_audience IN ('psc_only','universal')`.
  - World: `target_audience IN ('world_only','universal','psc_only')` (tunable to include syllabus).
- **Difficulty mix**: bias easy/medium for exam goal; medium/hard for practical goal; ensure at least one hard item to stretch.
- **Diversity**: enforce max per subject to avoid repetition; shuffle final set.
- **Fallback**: if interests empty, use trending+universal; if pool too small, backfill with universal.
- **Telemetry tuning**: adjust weights over time based on skips/likes/time-on-question.

## 5) Explore Page (Drill-down)
- Level 1: show categories (12–15 tiles) with counts.
- Level 2: on category click, list subjects; user can filter/take quiz per subject.
- Provide toggle to “All PSC” / “All Practical” filters if dual-track applies.

## 6) APIs (Suggested)
- `POST /api/onboarding/set_profile` → body: {identity, goal, study_mode?}
- `POST /api/onboarding/set_interests` → body: {category_ids: []} (upsert user_interests)
- `GET /api/feed/questions?limit=20` → uses user profile to assemble weighted pools; returns mixed list with metadata (difficulty, type, subject, category, target_audience)
- `GET /api/explore/categories` → returns categories with subject counts
- `GET /api/explore/subjects?category_id=` → list subjects with counts; supports mode filter
- `POST /api/telemetry/question_event` → optional; capture skip/like/complete/time_spent to improve tuning

### Feed Query Sketch
```
-- interest pool
SELECT * FROM questions
WHERE category_id IN (:interest_ids)
  AND target_audience IN (:aud_filter)
ORDER BY RAND() LIMIT :n_interest;

-- trending pool
SELECT * FROM questions
WHERE is_trending = 1 AND target_audience IN (:aud_filter)
ORDER BY RAND() LIMIT :n_trending;

-- challenge pool (outside interests)
SELECT * FROM questions
WHERE category_id NOT IN (:interest_ids)
  AND target_audience IN (:aud_filter)
ORDER BY RAND() LIMIT :n_challenge;
```
Combine, shuffle, cap to limit.

## 7) UI/UX Notes
- Onboarding screen before dashboard; allow “Skip for now” with default generic feed.
- Interest bubbles with search/filter; show count of available questions per category.
- Progressively disclose subjects on Explore page; breadcrumbs back to categories.
- Mode indicator (PSC/World) on quiz list and question cards to avoid confusion.
- Save/toggle interests in Settings; show “Refine my feed” CTA.

## 8) Data Quality & Curation
- Ensure every question has: category_id, subject_id, target_audience, difficulty, type.
- Normalize subject imports: map “Surveying I/II/Camp” → Surveying category; “SA I/II/RCC/Steel” → Structure.
- Mark `is_trending` via weekly cron using engagement metrics.
- Add tags for code references (e.g., IS 456, Eurocode 2) for fine-grained filtering.

## 9) Testing Checklist
- **Unit**: audience filter, weight calculations, fallback when no interests, diversity constraints.
- **Integration**: onboarding save → feed output; mode toggle respected; explore queries return correct counts.
- **Data**: import validation for category/subject mapping; ensure no orphan questions.
- **Performance**: RAND() scaling—add precomputed random keys or use ORDER BY RAND() LIMIT small after preselection; add indexes on category_id, subject_id, target_audience, is_trending.
- **Security**: validate user_id ownership on set_interests; rate-limit onboarding calls; sanitize outputs.

## 10) Action Plan
1) Create tables: categories, subjects, user_interests; migrate questions to include category_id, subject_id, target_audience, difficulty, type.
2) Seed categories (12–15) and map all 52 subjects to categories.
3) Build onboarding UI (identity → interests → goal) with API hooks.
4) Implement feed API with weighting, mode filter, diversity rules, and fallback.
5) Build Explore page: categories list, subject drill-down, mode filters.
6) Add telemetry endpoint; set cron to compute trending flags.
7) Optimize queries with proper indexes and RAND()-safe sampling strategies for large datasets.

## 11) Confirmation
This suggestion engine personalizes the experience by collecting identity, interests, and goals, then serving a weighted blend of relevant, trending, and challenging questions. It keeps PSC-focused users on syllabus while letting practitioners explore practical scenarios, all via a clean category/subject mapping and mode-aware filtering.
