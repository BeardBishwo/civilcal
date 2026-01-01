# Multiplayer Quiz Lobby System - Real-time Competitive Quiz Platform

## Overview
Server-authoritative multiplayer quiz with lobby creation/join, ghost/bot injection, secure wagering, per-second status polling, and automated payouts. This document covers routes, flows, security controls, and payout rules for developers.

## 1) Routes (Base: http://localhost/Bishwo_Calculator)
- Lobby index: `/quiz/multiplayer` (GET, auth)
- Create lobby: `/quiz/lobby/create` (POST, auth)
- Join lobby: `/quiz/lobby/join` (POST, auth)
- Lobby room: `/quiz/lobby/{code}` (GET, auth)
- Status pulse: `/api/lobby/{code}/status` (GET, auth)
- Place wager: `/api/lobby/wager` (POST, auth)

## 2) Key Flows
### 2.1 Create Lobby
- Controller: `MultiplayerController@create`
- Service: `LobbyService->createLobby(examId, userId)`
- Steps:
  1) Generate 5-char code (MD5 substring).
  2) Insert `quiz_lobbies` with status=waiting, start_time = now + 30s.
  3) Auto-join host into `quiz_lobby_participants` (status=ready).
  4) Redirect to `/quiz/lobby/{code}`.

### 2.2 Join Lobby
- Controller: `MultiplayerController@join`
- Service: `LobbyService->joinLobby(code, userId)`
- Steps:
  1) Validate lobby exists.
  2) Check duplicate join; reject if already in.
  3) Insert participant (status=ready).
  4) Redirect to `/quiz/lobby/{code}`.

### 2.3 Lobby Page Load
- Route: GET `/quiz/lobby/{code}`
- Controller: `MultiplayerController@lobby`
- Steps:
  1) Fetch lobby by code; redirect if missing.
  2) Load wallet via `GamificationService`.
  3) Fetch participant record.
  4) Generate wager nonce + lifeline nonce (stored in `quiz_sessions`).
  5) Render `themes/default/views/quiz/multiplayer/lobby.php` with data.

### 2.4 Status Pulse (1s)
- Frontend: `setInterval(pulse, 1000)` in lobby.js.
- Backend: `MultiplayerController@status(code)`
- Steps per pulse:
  1) Validate access.
  2) If active: compute elapsed time, question index, trigger bot engine.
  3) `LobbyService->getLobbyStatus(lobbyId, userId)` returns lobby, participants, bot events; handles ghost injection and payouts.
  4) JSON response updates UI (participants, leaderboard, bot toasts, timers).

### 2.5 Wager Placement
- Route: POST `/api/lobby/wager`
- Security layers:
  1) Honeypot trap field check.
  2) Nonce validation/consume (`NonceService`): single-use, 30m expiry.
  3) Rate limit: 5 requests / 30s per user.
- Transaction:
  - Deduct coins from `user_resources`.
  - Update `quiz_lobby_participants.wager_amount`.
  - Return success JSON.

### 2.6 Ghost Protocol (Bot Injection)
- Triggered in `LobbyService->getLobbyStatus()` when:
  - timeLeft <= 10s before start AND current players < 4.
- Action: inject bots to reach 4 players using random active bot_profiles; insert as `quiz_lobby_participants` with `is_bot=1`.

### 2.7 Bot Engine (During Game)
- Triggered each pulse when lobby active.
- For pending bots on current question:
  - Simulate reaction time (2–7s + skill adjustment).
  - Skill → base accuracy (skill*10%).
  - EOM rubber-banding: if bot ahead >10 pts, -30% accuracy; if behind >10 pts, +20%.
  - Decide correctness; update score (+4 / -1) and `last_answered_index`.

### 2.8 Payout Distribution
- Triggered when lobby status = finished and payouts not yet distributed.
- Steps:
  1) Mark `quiz_lobbies.payout_distributed = 1` (one-time).
  2) Fetch participants; sort by `current_score` desc.
  3) Take top 3 winners; skip bots.
  4) Reward = 2x wager; credit coins to `user_resources`.
  5) Update mission progress (`win_battles`).

## 3) Data Touchpoints
- Tables: `quiz_lobbies`, `quiz_lobby_participants`, `bot_profiles`, `quiz_sessions` (nonces), `user_resources` (coins), optional mission tables.
- Key fields: status (waiting/active/finished), start_time, payout_distributed, wager_amount, current_score, is_bot.

## 4) Security & Anti-Abuse
- Honeypot trap on wager POST.
- Nonce (HMAC-like) stored in DB; single-use; 30m expiry.
- Rate limiter: 5 req/30s for wager endpoint.
- Server-authoritative scoring; client only polls.
- Bot injection controlled server-side; avoid client tampering.

## 5) Frontend Notes
- Pulse every 1s; show connection status; update participant list/leaderboard and bot events.
- Wager form sends nonce + honeypot hidden field.
- Handle redirects if lobby missing/invalid.

## 6) Quick Test URLs
- Lobby index: http://localhost/Bishwo_Calculator/quiz/multiplayer
- Create lobby (POST): http://localhost/Bishwo_Calculator/quiz/lobby/create
- Join lobby (POST): http://localhost/Bishwo_Calculator/quiz/lobby/join
- Lobby view: http://localhost/Bishwo_Calculator/quiz/lobby/{code}
- Status pulse: http://localhost/Bishwo_Calculator/api/lobby/{code}/status
- Wager: http://localhost/Bishwo_Calculator/api/lobby/wager

## 7) What to Verify (QA)
- Create+join flows, redirect to lobby.
- Pulse returns lobby/participants; timers progress; bot events appear.
- Ghost injection when <4 players and <10s to start.
- Wager: honeypot, nonce, rate limit enforced; coins deducted; wager stored.
- Payout after game ends: top 3 (non-bots) get 2x wager; no double payouts.
- Nonce invalid after use/expiry; rate limiting blocks bursts.

## 8) Future Hardening
- Add signature on pulse responses; client-side stale detection.
- Move to WebSockets when infra allows; keep long-poll as fallback.
- Add per-lobby wager caps; anti-collusion checks; audit logs for payouts.
- Make question duration configurable; store per-exam durations.
