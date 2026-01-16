/**
 * BotManager.js
 * "Bulletproof" Client-Side Bot Logic
 * 
 * Responsibilities:
 * 1. Identify if current user is the Host.
 * 2. If Host, drive the behavior of all Bot participants in the lobby.
 * 3. Update Firebase directly (Zero Server Load).
 */

class BotManager {
    constructor(lobbyCode, firebaseDb, hostId, currentUserId) {
        this.lobbyCode = lobbyCode;
        this.db = firebaseDb;
        this.hostId = Number(hostId); // Ensure numeric comparison if needed
        this.currentUserId = Number(currentUserId);
        this.isHost = (this.hostId === this.currentUserId);
        this.isRunning = false;

        // Cache for bot reaction times (re-rolled per question)
        this.botStates = {};

        console.log(`[BotManager] Init. Is Host? ${this.isHost} (Host: ${this.hostId}, Me: ${this.currentUserId})`);
    }

    start() {
        if (!this.isHost) return;
        if (this.isRunning) return;

        console.log("[BotManager] Starting Bot Engine (Host Mode)");
        this.isRunning = true;

        // Monitor Game State
        const roomRef = this.db.ref(`rooms/${this.lobbyCode}`);

        roomRef.child('game_status').on('value', (snapshot) => {
            const status = snapshot.val();
            if (status === 'active') {
                this.startLoop();
                if (this.autoFillInterval) clearInterval(this.autoFillInterval);
            } else {
                this.stopLoop();
                if (status === 'waiting') {
                    this.startAutoFillCheck();
                }
            }
        });

        // Monitor Question Changes to reset reaction times
        roomRef.child('current_question_index').on('value', (snapshot) => {
            const qIndex = snapshot.val();
            this.resetBotStates(qIndex);
        });
    }

    startLoop() {
        if (this.loopInterval) clearInterval(this.loopInterval);

        this.loopInterval = setInterval(() => {
            this.processGameTick();
        }, 1000); // 1Hz Tick is sufficient for quiz bots
    }

    startAutoFillCheck() {
        if (this.autoFillInterval) clearInterval(this.autoFillInterval);

        console.log("[BotManager] Starting Auto-Fill Check");
        this.autoFillInterval = setInterval(() => {
            this.checkAutoFill();
        }, 2000);
    }

    stopLoop() {
        if (this.loopInterval) clearInterval(this.loopInterval);
        if (this.autoFillInterval) clearInterval(this.autoFillInterval);
        this.loopInterval = null;
        this.autoFillInterval = null;
    }

    async checkAutoFill() {
        // Only run if Waiting
        const roomRef = this.db.ref(`rooms/${this.lobbyCode}`);
        const snapshot = await roomRef.once('value');
        const data = snapshot.val();

        if (!data || data.status !== 'waiting') return;

        // Count Players
        const players = data.players || {};
        const count = Object.keys(players).length;
        const TARGET_PLAYERS = 4; // Or 2 for stricy 1v1 on low traffic

        // Calculate Time Left
        // We need accurate start time. 
        // Assuming data.start_time is TIMESTAMP or ISO String
        let startTime = new Date(data.start_time).getTime();
        // If data.start_time is stored as "2023-..." SQL formatted string, JS handles it.
        // If stored as timestamp, good.

        const now = Date.now();
        const timeLeft = data.server_time_offset ? (startTime - (now + data.server_time_offset)) : (startTime - now);
        // Simplified:
        const secondsLeft = (startTime - now) / 1000;

        if (secondsLeft <= 10 && count < TARGET_PLAYERS && !this.injectionTriggered) {
            console.log(`[BotManager] Triggering Injection! (Time: ${secondsLeft.toFixed(1)}s, Players: ${count})`);
            this.injectionTriggered = true; // Prevent spam

            try {
                await fetch(window.appConfig.baseUrl + `/api/lobby/${this.lobbyCode}/inject`, {
                    method: 'POST'
                });
            } catch (e) {
                console.error("Injection Failed", e);
                this.injectionTriggered = false; // Retry?
            }
        }
    }

    async processGameTick() {
        // Fetch current Lobby State
        const roomRef = this.db.ref(`rooms/${this.lobbyCode}`);
        const snapshot = await roomRef.once('value');
        const data = snapshot.val();

        if (!data || !data.players) return;

        // --- TIMEKEEPER LOGIC ---
        // The Host drives the Question Index
        this.driveGameClock(data, roomRef);

        const now = Date.now();
        const questionStartTime = data.question_start_time || now;
        const currentQIndex = data.current_question_index || 0;

        Object.keys(data.players).forEach(key => {
            const player = data.players[key];

            // Only drive Bots
            if (player.is_bot && !player.has_surrendered) {
                this.handleBot(key, player, currentQIndex, questionStartTime, data.players);
            }
        });
    }

    driveGameClock(data, roomRef) {
        if (!data.start_time) return;

        let startTime = new Date(data.start_time).getTime();
        const now = Date.now();
        // Adjust for server offset if present, else assume synced clocks
        const elapsed = data.server_time_offset ? (now + data.server_time_offset) - startTime : now - startTime;

        const Q_DURATION = 20000; // 20s per question
        const TOTAL_QUESTIONS = data.total_questions || 10;

        let qIndex = Math.floor(elapsed / Q_DURATION);

        // Game Over Check
        if (qIndex >= TOTAL_QUESTIONS) {
            if (data.status !== 'finished' && !this.finishing) {
                console.log("🏁 Game Over! Syncing results...");
                this.finishing = true;
                this.syncScores(data.players);
                roomRef.update({ status: 'finished' });
            }
            return;
        }

        // New Question Trigger
        if (qIndex !== data.current_question_index) {
            console.log(`[GameManager] Advancing to Q${qIndex + 1}`);
            roomRef.update({
                current_question_index: qIndex,
                question_start_time: now // Reset Q Timer
            });
        }
    }

    async syncScores(players) {
        // Format scores for PHP
        const scores = [];
        Object.values(players).forEach(p => {
            scores.push({
                id: p.id,
                score: p.current_score || 0,
                is_bot: !!p.is_bot
            });
        });

        try {
            await fetch(window.appConfig.baseUrl + `/api/lobby/${this.lobbyCode}/finish`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ scores: scores })
            });
            console.log("✅ Scores Synced to MySQL");

            // Reload to show results after short delay
            setTimeout(() => window.location.reload(), 2000);

        } catch (e) {
            console.error("Score Sync Failed", e);
        }
    }

    handleBot(botKey, botData, qIndex, startTime, allPlayers) {
        // Init State if missing
        if (!this.botStates[botKey]) {
            this.botStates[botKey] = {
                currentQ: -1,
                reactionTime: 0,
                answered: false
            };
        }

        const state = this.botStates[botKey];

        // New Question? Roll Packet
        if (state.currentQ !== qIndex) {
            state.currentQ = qIndex;
            state.answered = false;
            // Roll Reaction Time: Base 3s + Random(0-5) - (Skill * 0.2)
            const skill = botData.skill_level || 1;
            const baseReaction = 3000;
            const variance = Math.random() * 5000;
            const skillBonus = skill * 300;

            state.reactionTime = Math.max(1000, baseReaction + variance - skillBonus);
            console.log(`[BotManager] Bot ${botData.name} (Lvl ${skill}) reaction set to ${(state.reactionTime / 1000).toFixed(1)}s`);
        }

        if (state.answered) return;

        // Check Time
        const elapsed = Date.now() - startTime;

        if (elapsed > state.reactionTime) {
            // ACTION: Bot Answers
            this.submitBotAnswer(botKey, botData, qIndex, allPlayers);
            state.answered = true;
        }
    }

    submitBotAnswer(botKey, botData, qIndex, allPlayers) {
        // --- EOM: Engagement Optimized Matchmaking ---
        // Goal: Keep the game close and addictive.

        let leaderScore = -9999;
        let myScore = botData.current_score || 0;

        // Find Leader Score
        Object.values(allPlayers).forEach(p => {
            if (p.current_score > leaderScore) leaderScore = p.current_score;
        });

        const skill = botData.skill_level || 5;
        let accuracyBase = 50 + (skill * 5); // Base (55% - 100%)

        // DDA: Rubber Banding
        const diff = myScore - leaderScore;

        if (diff < -10) {
            // Bot is losing badly -> BOOST
            accuracyBase += 20;
            console.log(`[BotManager] Boosting ${botData.name} (Losing by ${diff})`);
        } else if (diff > 10) {
            // Bot is winning too hard -> NERF
            accuracyBase -= 20;
            console.log(`[BotManager] Nerfing ${botData.name} (Winning by ${diff})`);
        }

        // Clamp
        accuracyBase = Math.max(30, Math.min(98, accuracyBase));

        const roll = Math.random() * 100;
        const isCorrect = roll <= accuracyBase;

        const marks = isCorrect ? 4 : -1;
        const newScore = myScore + marks;

        console.log(`[BotManager] Bot ${botData.name} submitting answer. Correct? ${isCorrect} (Rolled ${roll.toFixed(1)} vs ${accuracyBase})`);

        // Update Firebase
        // We intentionally do NOT update MySQL here. 
        // MySQL is updated only at Game End (Sync) or via the 'pulse' replacement if needed for persistence.
        // But for gameplay speed, Firebase is the source of truth.

        const updates = {
            current_score: newScore,
            last_answered_index: qIndex,
            last_move: {
                correct: isCorrect,
                timestamp: Date.now(),
                q_index: qIndex
            }
        };

        this.db.ref(`rooms/${this.lobbyCode}/players/${botKey}`).update(updates)
            .catch(err => console.error("Bot Write Error", err));
    }

    resetBotStates(qIndex) {
        // Clear answered flags for new question (handled in handleBot logically, but good to flush)
        // this.botStates = {}; // Ideally keep keys but reset props.
    }
}

// Export for global use
window.BotManager = BotManager;
