# Quiz System: Exam Engine, Multiplayer Ghost Mode, and Admin Interfaces

**Date:** 2025-12-30  
**Purpose:** Deep-dive technical guide for the quiz platform covering single-player exams, competitive multiplayer with AI bots, gamification integration, and admin management.  
**Scope:** Exam lifecycle, lobby management, bot AI, question bank, rewards, leaderboards, and lifelines.

---

## 1. System Overview

The quiz platform is a multi-layered system:

```
Frontend (Web UI)
├── Exam Room (single-player)
├── Multiplayer Lobby & Game Room
├── Admin Question Bank
└── Leaderboards

Controllers
├── ExamEngineController (single-player)
├── MultiplayerController (lobby/game)
├── QuestionBankController (admin)
├── LifelineController (power-ups)
└── LeaderboardController (rankings)

Services
├── LobbyService (lobby lifecycle)
├── BotEngine (AI behavior)
├── GamificationService (rewards)
├── LeaderboardService (rank aggregation)
├── LifelineService (power-ups)
└── ActivityLogger (audit)

Database
├── quiz_exams
├── quiz_questions
├── quiz_attempts
├── quiz_lobbies
├── quiz_lobby_participants
├── quiz_leaderboard_aggregates
└── user_resources (wallet)
```

---

## 2. Single-Player Exam Engine

### 2.1. Exam Lifecycle

| Step | Endpoint | Controller | Key Operations |
|------|----------|------------|----------------|
| Start | `/quiz/start/{slug}` | `ExamEngineController@start` | Fetch exam, check ongoing attempt, create new attempt, redirect |
| Room | `/quiz/room/{attemptId}` | `ExamEngineController@room` | Load questions via pivot, shuffle if enabled, load saved answers |
| Save | `/quiz/save-answer` | `ExamEngineController@saveAnswer` | AJAX save per question, allow resume |
| Submit | `/quiz/submit` | `ExamEngineController@submit` | Score answers, update attempt, grant rewards, update leaderboard, redirect |

### 2.2. Question Loading & Shuffling

```php
// Fetch via exam_question pivot
$sql = "SELECT q.* FROM quiz_questions q
        JOIN exam_question eq ON q.id = eq.question_id
        WHERE eq.exam_id = :examId ORDER BY eq.order ASC";
```

- If exam `shuffle_questions` is enabled, questions are shuffled after load.
- Supports MCQ (single), true/false, and numerical types.

### 2.3. Scoring Logic

- **MCQ/True-False**: Direct comparison of user answer vs correct indices.
- **Numerical**: Exact match or tolerance-based (configurable).
- **Partial credit**: Not implemented; can be extended.

### 2.4. Reward Integration

After scoring:
```php
if ($isCorrect) {
    $this->gamificationService->rewardUser(
        $_SESSION['user_id'],
        true,
        $difficulty,
        $attemptId
    );
}
```

- Rewards are tiered by difficulty (easy/medium/hard).
- Grants coins, resources, XP, and updates missions.

### 2.5. Leaderboard Update

```php
$this->leaderboardService->updateUserRank(
    $_SESSION['user_id'],
    $score,
    $totalQuestions,
    $correctAnswers,
    $exam['category_id'] ?? null
);
```

- Aggregates weekly/monthly/yearly rankings.
- Uses moving average for accuracy.

---

## 3. Multiplayer Ghost Mode

### 3.1. Lobby Creation

1. **Controller**: `MultiplayerController@create`
2. **Service**: `LobbyService@createLobby`
   - Generate unique 5-char uppercase code.
   - Insert lobby with 30s start timer.
   - Auto-join host as participant.

### 3.2. Game Pulse & Bot Engine

Every client polls `/api/lobby/status/{lobbyId}`:

- **LobbyService@getLobbyStatus**
  - Checks remaining time.
  - Injects AI bots if underfilled (target 4 players).
  - Distributes wager rewards to top 3 after timer ends.
- **BotEngine@processGamePulse**
  - For each pending bot, calculates reaction time based on skill.
  - Submits answer with EOM (Equalizing Odds Mechanism) accuracy adjustment.

### 3.3. Bot AI Behavior

#### Skill Levels & Reaction Time
```php
$reactionTime = 2 + rand(0, 5) + ((10 - $bot['skill_level']) * 0.3);
```

- Higher skill = faster reaction.
- Humanized variance.

#### EOM Algorithm (Dynamic Difficulty)
```php
if ($host) {
    $diff = $bot['current_score'] - $host['current_score'];
    if ($diff > 10) {
        $accuracyChance -= 30; // Bot throttles back
    }
}
```

- Prevents bots from dominating.
- Keeps games competitive.

#### Answer Submission
- Bot selects answer based on adjusted accuracy.
- Updates participant score and last_pulse_at.

### 3.4. Wager System

- Players can wager coins before game starts.
- Top 3 receive 2x wager (configurable).
- Server validates wager amount and balance.

---

## 4. Admin Question Bank

### 4.1. Question Creation Flow

1. **Route**: `POST /admin/quiz/questions/store`
2. **Controller**: `QuestionBankController@store`
3. **Validation**: topic_id, question_text, type, options
4. **JSON Structure**:
   ```php
   $content = [
       'text' => $_POST['question_text'],
       'image' => $_POST['question_image'] ?? null,
       'latex' => $_POST['question_latex'] ?? null
   ];
   ```
5. **Options Array**:
   ```php
   foreach ($_POST['options'] as $idx => $opt) {
       $options[] = [
           'text' => $opt['text'],
           'image' => $opt['image'] ?? null,
           'is_correct' => isset($opt['is_correct']),
           'order' => $idx
       ];
   }
   ```
6. **Database Insert**:
   - `quiz_questions` with JSON fields for content and options.
   - `unique_code` for reference.
   - `created_by` from session.

### 4.2. Supported Question Types

- **MCQ Single**: One correct option.
- **True/False**: Two options.
- **Numerical**: Text input (no options array).

### 4.3. Media Support

- Question and option images.
- LaTeX rendering for math expressions.

---

## 5. Gamification Integration

### 5.1. Reward Tiers by Difficulty

```php
$rewards = [
    'easy' => ['coins' => 5, 'bricks' => 2],
    'medium' => ['coins' => 10, 'bricks' => 5, 'cement' => 2],
    'hard' => ['coins' => 20, 'bricks' => 10, 'cement' => 5, 'steel' => 2]
];
```

- Mapped numeric difficulty (1–5) to tiers.
- Server-side; client cannot influence.

### 5.2. XP & Battle Pass

```php
if ($resource === 'xp') {
    $bp = new BattlePassService();
    $bp->addXp($userId, $amount);
}
```

- XP updates battle pass level (1000 XP/level).
- Triggers mission progress.

### 5.3. Mission Progress

```php
$ms = new MissionService();
$ms->updateProgress($userId, 'solve_questions');
```

- Increments daily mission counters.
- Auto-claims rewards on completion.

---

## 6. Leaderboard System

### 6.1. Ranking Periods

- **Weekly**: `Y-W` (e.g., 2024-52)
- **Monthly**: `Y-m` (e.g., 2024-12)
- **Yearly**: `Y`

### 6.2. Aggregation Logic

```sql
INSERT INTO quiz_leaderboard_aggregates
    (user_id, period_type, period_value, category_id, accuracy_avg, tests_taken, total_score)
VALUES (...)
ON DUPLICATE KEY UPDATE
    accuracy_avg = ((accuracy_avg * tests_taken) + VALUES(accuracy_avg)) / (tests_taken + 1),
    total_score = total_score + VALUES(total_score),
    tests_taken = tests_taken + 1;
```

- Moving average for accuracy.
- Incremental score and test count.

### 6.3. Unique Constraints

```sql
UNIQUE KEY (user_id, period_type, period_value, category_id)
```

- One record per user per period/category.

---

## 7. Lifeline System

### 7.1. Types & Costs

| Type | Cost (coins) | Effect |
|------|--------------|--------|
| 50/50 | 50 | Returns indices of two incorrect options to hide |
| Skip | 20 | Marks question as skipped (no score) |
| Poll | 30 | Simulates audience vote favoring correct answer |

### 7.2. Activation Flow

1. **API**: `POST /api/quiz/lifeline/use`
2. **Controller**: `LifelineController@use`
3. **Service**: `LifelineService@useInQuiz`
   - Validate price and wallet balance.
   - Deduct coins.
   - Log activity.
   - Apply effect (e.g., 50/50: calculate hide_indices).

### 7.3. Effects Implementation

#### 50/50
```php
$incorrectIndices = array_diff($allIndices, [$correctIndex]);
shuffle($incorrectIndices);
$responseData['hide_indices'] = array_slice($incorrectIndices, 0, 2);
```

#### Poll
```php
$correctVote = rand(45, 80);
$votes[$correctIndex] = $correctVote;
$remaining = 100 - $correctVote;
// Distribute remaining among incorrect options
```

---

## 8. Security & Anti-Cheat

| Control | Implementation |
|---------|----------------|
| Exam session integrity | `quiz_attempts` with status and timestamps |
| Answer tampering | Server-side scoring; client only stores selections |
| Replay attacks | Nonce validation on save/submit |
| Wager fraud | Server-side balance validation and nonce |
| Bot injection | Server-controlled; no client influence |
| Question bank access | Admin-only gates; CSRF protection |

---

## 9. Performance Considerations

- **Question loading**: JOIN via exam_question pivot; index on exam_id.
- **Real-time polling**: Lightweight status JSON; consider WebSocket for scale.
- **Bot processing**: Batch per pulse; minimal DB writes.
- **Leaderboard aggregation**: Upsert per period; efficient with ON DUPLICATE KEY.
- **Audit logs**: `activity_logs` can grow; implement archival.

---

## 10. Extensibility

- **New question types**: Extend content schema and scoring logic.
- **Team multiplayer**: Add teams table and group scoring.
- **Tournaments**: Layer on top of lobbies with brackets.
- **Adaptive difficulty**: Adjust question difficulty based on performance.
- **Proctoring**: Add webcam/session monitoring.

---

## 11. Database Schema Highlights

### 11.1. Core Tables

| Table | Purpose | Key Fields |
|-------|---------|------------|
| `quiz_exams` | Exam metadata | slug, title, category_id, shuffle_questions |
| `quiz_questions` | Question bank | content JSON, options JSON, type, difficulty |
| `exam_question` | Pivot (order) | exam_id, question_id, order |
| `quiz_attempts` | User sessions | user_id, exam_id, status, score, started_at, completed_at |
| `quiz_lobbies` | Multiplayer rooms | code, exam_id, status, start_time |
| `quiz_lobby_participants` | Players/bots | lobby_id, user_id, score, wager_amount, is_bot |
| `quiz_leaderboard_aggregates` | Rankings | user_id, period_type, period_value, accuracy_avg, total_score |

### 11.2. Indexes

- `quiz_attempts(user_id, status)`
- `quiz_lobbies(code, status)`
- `quiz_lobby_participants(lobby_id, is_bot)`
- `quiz_leaderboard_aggregates(user_id, period_type, period_value)`

---

## 12. API Endpoints Summary

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/quiz/start/{slug}` | GET | Start/resume exam |
| `/quiz/room/{attemptId}` | GET | Render exam room |
| `/quiz/save-answer` | POST | AJAX save answer |
| `/quiz/submit` | POST | Submit exam |
| `/api/lobby/create` | POST | Create multiplayer lobby |
| `/api/lobby/join` | POST | Join lobby |
| `/api/lobby/status/{id}` | GET | Poll lobby status |
| `/api/lobby/wager` | POST | Place wager |
| `/api/quiz/lifeline/use` | POST | Activate lifeline |
| `/admin/quiz/questions` | GET/POST | Question bank CRUD |

---

## 13. Production Readiness Checklist

| Item | Status | Notes |
|------|--------|-------|
| Exam session integrity | ✅ | Attempts table with status |
| Anti-cheat (server scoring) | ✅ | No client-side evaluation |
| Multiplayer bot injection | ✅ | Server-controlled |
| Wager security | ✅ | Nonce + balance validation |
| Leaderboard aggregation | ✅ | Efficient upserts |
| Question bank admin | ✅ | CSRF + validation |
| Real-time polling | ⚠️ | Consider WebSocket for scale |
| Audit logging | ✅ | ActivityLogger |

---

## 14. Conclusion

The quiz system is a robust, feature-rich platform supporting single-player exams, competitive multiplayer with AI bots, gamified rewards, and comprehensive admin tools. Security is enforced server-side for scoring, rewards, and wagers. The architecture is modular, allowing easy extension of question types, game modes, and analytics. With the addition of WebSocket support for real-time updates and periodic archival of logs, the system is fully production-ready.
