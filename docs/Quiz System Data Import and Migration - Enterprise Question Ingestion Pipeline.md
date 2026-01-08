# Quiz System Data Import & Migration — Enterprise Question Ingestion Pipeline

Single reference for engineers working on the Excel/CSV import workflow (template → chunked upload → staging → publish/conflict resolution → cleanup). Includes current architecture, data contracts, pain points, and improvement backlog.

## 1) Objectives & Success Criteria
- Allow large-scale ingestion of enterprise question banks without timeouts or memory spikes.
- Preserve syllabus hierarchy, position levels, and per-stream difficulty when questions are published.
- Detect duplicates prior to publishing while giving admins tools to resolve conflicts safely.
- Provide observable pipeline execution (progress, staging stats, error feedback) for operations teams.

## 2) Pipeline Overview
1. **Template generation** – Admin downloads XLSX with dynamic reference sheets, data validation, and instructions via `QuestionImportController::downloadTemplate()` using PhpSpreadsheet helpers. Reference sheets (courses, levels, categories, subcategories, position levels) are hydrated from live syllabus data.
2. **Chunked upload** – Frontend JS streams 50-row chunks to `/admin/quiz/import/upload`; backend uses `ChunkReadFilter` to load only the requested rows before handing each chunk to `ImportProcessor` and inserting staging records.
3. **Row processing** – `ImportProcessor::processRow()` resolves syllabus categories, infers question type, normalizes options, fingerprints content (SHA-256), and flags duplicates before returning a staging-ready payload.
4. **Conflict resolution** – Admin reviews staging batches, skipping or overwriting duplicates through `QuestionImportController::resolve()`, which can update live questions after re-resolving syllabus filters and level maps.
5. **Publishing clean rows** – `publishClean()` re-resolves hierarchy, writes new `quiz_questions`, processes level maps into `question_stream_map`, then deletes staging rows.
6. **Batch management** – `StagingQueueController` aggregates per-batch stats, surfaces duplicate comparisons, and supports deletion/cleanup beyond 30 days.

## 3) Data Model & Storage
- **Staging table (`question_import_staging`)** – Created in migration 035, evolved by later migrations to include JSON answers, type, level_map, contest_id, theory_type; indexes on `batch_id`, `content_hash`, and duplicate metadata support lookups.
- **Question stream map** – Multi-context mapping table (stream, difficulty, priority, is_primary) that `processLevelMap()` writes to during publish operations.
- **Supporting services** – `SyllabusService::resolveFilterContext()` climbs parent chains for course/level/category linkage; reused during creation, publish, and conflict resolution.

## 4) Pain Points, Bugs, & Risks
1. **Duplicate detection collisions** – Fingerprint strips non-alphanumerics and ignores options, so distinct questions with similar wording (or different answers) may hash identically, creating false positives/negatives.
2. **Unvalidated level_map syntax** – Raw `level_map` column from spreadsheet is inserted into staging and later parsed without schema validation; malformed values may trigger runtime parse errors or silent skips during publish.
3. **Publish/overwrite without transactions** – `publishClean()` and `resolve()` perform multiple inserts/updates/deletes per row without wrapping in DB transactions; partial failures can leave staging rows deleted while live data remains inconsistent.
4. **Hierarchy resolution fragility** – `resolveCategoryId()` relies on exact title matching; mismatched casing or renamed syllabus nodes can fail silently, producing null context and downstream validation issues.
5. **Chunk filter does not guard columns** – `ChunkReadFilter` filters rows only; malformed Excel files with unexpected column counts may still load entire rows and blow up memory or cause undefined indices during processing.
6. **Overwrite path lacks usage guardrails** – Conflict overwrite updates live questions even if they appear in many exams; usage count is fetched for UI but no hard block prevents overwriting heavily-used questions when action=overwrite.
7. **Limited observability** – Pipeline lacks structured logging/metrics for chunk failures, duplicate rates, or publish results, making incident triage difficult.

## 5) Recommended Improvements (Prioritized)
1. **Transactional safety** – Wrap publish/overwrite sequences in DB transactions with retry; only delete staging rows after successful commit.
2. **Stronger fingerprinting** – Include normalized options, difficulty, and level_map in hash and store a per-question UUID to reduce collisions; consider Levenshtein similarity for near-duplicates.
3. **Schema validation & linting** – Validate `level_map` format (e.g., regex) at staging insert time; reject or quarantine rows with malformed syntax before publish.
4. **Hierarchy resolution fallback** – Normalize case, allow slug-based lookups, and flag unresolved nodes to admins instead of silently proceeding.
5. **Usage-aware overwrite policy** – Enforce thresholds (e.g., block overwrite if `quiz_exam_questions` count > N) unless admin explicitly forces with confirmation.
6. **Enhanced logging/metrics** – Emit structured logs per chunk (rows processed, duplicates found, errors), capture staging→publish KPIs, and surface them on the staging dashboard.
7. **Chunk validation hardening** – Validate column headers and row length before processing; consider per-chunk schema checks to avoid rogue spreadsheets.

## 6) Open Questions
- Should we support incremental updates (only changed rows) vs full re-import per batch?
- Do we need a staging retention policy beyond 30 days or archive exports for compliance?
- Should publish operations auto-create audit trails linking staging batch → production question IDs?
- How will conflicts behave when blueprint/exam caches reference overwritten questions (need cache invalidation)?
