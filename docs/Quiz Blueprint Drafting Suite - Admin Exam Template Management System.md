# Quiz Blueprint Drafting Suite – Admin Exam Template Management System

Single-file deep brief for engineers to plan/administer reusable quiz/exam templates. Anchored to current implementation (BlueprintController, ExamBlueprintService, ExamGeneratorService, SyllabusService) and codemap Trace IDs 1–7.

## 1) Objectives & Success Criteria
- Enable admins to build reusable exam blueprints with rule-based question distribution by syllabus node and difficulty.
- Guarantee blueprint integrity (rule totals match blueprint totals) before generation.
- Provide editor UX for CRUD on blueprints and per-node rules with syllabus tree selection.
- Support generation to live exams (preview + save) with wildcard injection and shuffling.
- Be ready for daily/auto generation integration without breaking existing quiz flows.

## 2) Current State (inventory)
- Routes: CRUD + rule ops + validation + generation @ app/routes.php lines 2050-2059 @app/routes.php#2049-2059.
- Controller: Admin\Quiz\BlueprintController handles index/create/edit/store/update/delete, rule add/update/delete, validate, generate, preview @app/Controllers/Admin/Quiz/BlueprintController.php.
- Services:
  - ExamBlueprintService: CRUD, rules join, rule order, JSON difficulty, validation, summary @app/Services/ExamBlueprintService.php.
  - ExamGeneratorService: validate then assemble questions per rule, wildcard %, shuffle; supports saveGeneratedExam (further down) @app/Services/ExamGeneratorService.php.
  - SyllabusService: level-scoped tree builder used by editor @app/Services/SyllabusService.php.
- Views: admin/quiz/blueprints/index.php, editor.php (rules table, syllabus dropdown, AJAX handlers for add-rule, validate, generate).
- Data: Tables exam_blueprints, blueprint_rules, syllabus_nodes, quiz_questions (joins in generator), quiz_exams + quiz_exam_questions (for save).

## 3) Gaps / Issues to address
- Validation only checks sum of questions vs blueprint total; no per-difficulty or availability checks.
- Difficulty distribution is free-form JSON; no schema validation or defaulting.
- Rule update/delete lack authorization granularity or audit trail.
- No optimistic concurrency / versioning; concurrent edits can collide.
- Generator query may return duplicates across rules if syllabus nodes overlap.
- Wildcard logic may reuse already-selected questions; dedup safeguards unclear.
- No quotas/checks for sufficient inventory before generation; failures thrown late.
- Editor UX relies on synchronous fetch without retry/toast standardization.
- No spec coverage in openspec/specs; change management untracked.

## 4) Target Scope (what we plan to spec/implement)
1) Blueprint lifecycle: create/update/delete, activate toggle, slug uniqueness, audit fields.
2) Rule management: add/update/delete, per-rule difficulty schema, order management (drag/drop future), conflict detection on duplicate syllabus_node_id within a blueprint.
3) Validation:
   - Sum questions == total_questions.
   - For each rule: difficulty weights sum to 100 (or allowed null meaning uniform).
   - Preflight availability: ensure enough questions per node/difficulty before generation.
4) Generation:
   - Deterministic deduping across rules.
   - Wildcard selection excluding already-picked questions.
   - Optional shuffle flag; include_wildcard flag.
5) UX/APIs:
   - JSON endpoints remain; add error contract and status codes.
   - Editor: expose validation pulse + blocking states; show shortages.
6) Observability & safety:
   - Log generation attempts (blueprint_id, admin_id, validation_result, shortages).
   - Feature flag for new validation gates.

## 5) Domain & Data Model (existing + adjustments)
- exam_blueprints: id, title, slug, description, level, total_questions, total_marks, duration_minutes, negative_marking_rate, wildcard_percentage, is_active, created_by, created_at.
- blueprint_rules: id, blueprint_id (FK), syllabus_node_id (FK), questions_required, difficulty_distribution (JSON), order, created_at.
- quiz_questions: must surface difficulty, syllabus_node_id, linked_category_id, etc. used in generator queries.
- Proposal adjustments:
  - Enforce UNIQUE (blueprint_id, syllabus_node_id) to prevent duplicate rules (or allow multiples with distinct type/difficulty? decide).
  - Add updated_at, created_by for rules for audit.
  - Consider difficulty_distribution JSON schema: {easy:int, medium:int, hard:int} summing to 100.

## 6) Key Flows (happy paths)
1) Blueprint index → list all @index() then view @themes/admin/views/quiz/blueprints/index.php.
2) Create blueprint → POST store → redirect to editor with empty rules.
3) Editor load → GET edit/{id} → fetch blueprint+rules+syllabus tree → render rules table & dropdown.
4) Add rule → POST {id}/add-rule with node_id, qty, difficulty JSON → service validates node + blueprint → inserts with order → returns rule_id.
5) Validate → GET {id}/validate → returns {valid, errors, total_required, blueprint_total}; editor shows pulse.
6) Generate → POST /generate/{id} with shuffle/include_wildcard (+save optional) → service validates then assembles questions, optional save to quiz_exams.

## 7) Functional Requirements (draft)
### Blueprint authoring
- SHALL require title, level, total_questions, duration_minutes.
- SHALL default wildcard_percentage=0 unless explicitly set; must be 0–100.
- SHALL block saving if total_questions <=0 or duration_minutes <=0.
### Rule authoring
- SHALL require syllabus_node_id belonging to same level as blueprint (enforce).
- SHALL require questions_required >0.
- Difficulty distribution:
  - MAY be null → treated as uniform selection.
  - If provided, MUST be object with numeric weights summing to 100.
### Validation
- SHALL fail if sum(rule.questions_required) != blueprint.total_questions.
- SHALL fail if any rule syllabus_node_id is inactive or level-mismatched.
- SHOULD warn if estimated question inventory per rule/difficulty is insufficient (preflight).
### Generation
- SHALL dedupe questions across rules and against wildcard picks.
- Wildcard questions SHALL exclude syllabus nodes already used if possible; otherwise allow but dedup IDs.
- SHALL honor shuffle flag; default true.
- SHALL return metadata: level, total_marks, duration_minutes, negative_marking_rate, counts.
### Security/permissions
- All endpoints remain admin-authenticated; enforce CSRF where applicable (check framework hooks).
- Rate-limit generation to prevent abuse (optional).

## 8) API & Payload Contracts (desired shape)
- POST /admin/quiz/blueprints/store|update: form-encoded today; consider JSON later.
- POST /admin/quiz/blueprints/{id}/add-rule:
  - Input: syllabus_node_id:int, questions_required:int, difficulty_distribution: JSON string or object.
  - Response: {success: bool, rule_id?, error?}
- GET /admin/quiz/blueprints/{id}/validate:
  - Response: {valid: bool, errors: string[], total_required:int, blueprint_total:int, shortages?: [{node_id, need, have, difficulty_scope}]}
- POST /admin/quiz/blueprints/generate/{id}:
  - Input: shuffle?:bool, include_wildcard?:bool, save?:bool, exam_title?:string.
  - Response: preview {exam} or saved {exam_id, redirect}.

## 9) UX Notes (editor.php alignment)
- Keep syllabus dropdown hierarchical (renderSyllabusOptions) with indentation.
- Add rule form should surface difficulty helper (e.g., sliders summing to 100).
- Validation pulse already present; enhance to show message list.
- On generation, show spinner and final counts (regular vs wildcard).

## 10) Risks & Edge Cases
- Inventory shortage per syllabus node → need graceful error with actionable message.
- Overlapping syllabus nodes causing duplicate questions across rules.
- Wildcard percentage rounding (ceil) may overshoot total; ensure final count == total_questions.
- Difficulty distribution malformed JSON crashes add/update; need validation.
- Legacy data with null/empty distributions must not break strict validation (use defaults).

## 11) Observability & Ops
- Log audit: admin_id, blueprint_id, action (create/update/rule add/delete/generate), timestamp.
- Generation logs: shortages, relaxations, question ids chosen count.
- Metrics: validation failures, generation failures, average generation time.

## 12) Rollout Checklist (incremental)
1) Schema tighten: optional UNIQUE on (blueprint_id, syllabus_node_id); add updated_at/created_by to rules.
2) Add validation of difficulty JSON & level match in service layer.
3) Add inventory preflight (COUNT queries per node/difficulty) before generate.
4) Adjust generator to dedupe + honor wildcard exclusions.
5) Enhance editor UX messaging (non-breaking).
6) Backfill data if new constraints added; migrate legacy rules with null difficulty to uniform (33/33/34).
7) Add tests: service validation, generator dedupe, wildcard count correctness.

## 13) Open Questions
- Allow multiple rules per syllabus node (e.g., separated by question type) or enforce uniqueness?
- Should wildcard selection respect level-only or any level? (Currently level-based.)
- Negative marking unit/rate: per question or per exam? Align with existing quiz_exams fields.
- Do we need section ordering UI (drag/drop) vs auto-increment order?
- Should daily quiz auto-generation leverage blueprints or stay independent?
