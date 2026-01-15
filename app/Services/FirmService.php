<?php

namespace App\Services;

use App\Core\Database;
use Exception;

class FirmService
{
    private $db;
    const CREATION_COST = 5000;

    public function __construct()
    {
        $this->db = \App\Core\Database::getInstance();
    }

    /**
     * Create a new Engineering Firm
     */
    public function createFirm($userId, $name, $description)
    {
        // 1. Check if user already in a guild
        $member = $this->db->findOne('guild_members', ['user_id' => $userId]);
        if ($member) throw new Exception("You are already part of a firm.");

        // 2. Check costs
        $gs = new GamificationService();
        $wallet = $gs->getWallet($userId);
        if ($wallet['coins'] < self::CREATION_COST) {
            throw new Exception("Not enough coins to start a firm. You need " . self::CREATION_COST);
        }

        // 3. Create Guild
        $this->db->query("INSERT INTO guilds (name, description, leader_id) VALUES (:name, :desc, :lid)", [
            'name' => $name,
            'desc' => $description,
            'lid' => $userId
        ]);

        $guildId = $this->db->getPdo()->lastInsertId();

        // 4. Add Leader as Member
        $this->db->query("INSERT INTO guild_members (guild_id, user_id, role) VALUES (:gid, :uid, 'Leader')", [
            'gid' => $guildId,
            'uid' => $userId
        ]);

        // 5. Initialize Vault
        $resources = ['bricks', 'cement', 'steel', 'coins'];
        foreach ($resources as $res) {
            $this->db->query("INSERT INTO guild_vault (guild_id, resource_type, amount) VALUES (:gid, :type, 0)", [
                'gid' => $guildId,
                'type' => $res
            ]);
        }

        // 6. Deduct Cost
        $this->db->query("UPDATE user_resources SET coins = coins - :cost WHERE user_id = :uid", [
            'cost' => self::CREATION_COST,
            'uid' => $userId
        ]);

        return $guildId;
    }

    /**
     * Donate resources to the shared vault
     */
    public function donate($userId, $resourceType, $amount)
    {
        if ($amount <= 0) throw new Exception("Amount must be positive.");

        $member = $this->db->findOne('guild_members', ['user_id' => $userId]);
        if (!$member) throw new Exception("You are not in a firm.");

        // Check user balance
        $wallet = (new GamificationService())->getWallet($userId);
        if (($wallet[$resourceType] ?? 0) < $amount) {
            throw new Exception("Not enough $resourceType.");
        }

        // Deduct from user
        $this->db->query("UPDATE user_resources SET $resourceType = $resourceType - :amt WHERE user_id = :uid", [
            'amt' => $amount,
            'uid' => $userId
        ]);

        // Add to vault
        $this->db->query("UPDATE guild_vault SET amount = amount + :amt WHERE guild_id = :gid AND resource_type = :type", [
            'amt' => $amount,
            'gid' => $member['guild_id'],
            'type' => $resourceType
        ]);

        // Reward with Guild XP (Simplified: 1 XP per resource donated)
        $this->db->query("UPDATE guilds SET xp = xp + :xp WHERE id = :id", [
            'xp' => $amount,
            'id' => $member['guild_id']
        ]);

        return true;
    }

    /**
     * Request to join a firm
     */
    public function requestJoin($userId, $guildId)
    {
        $member = $this->db->findOne('guild_members', ['user_id' => $userId]);
        if ($member) throw new Exception("You are already in a firm.");

        $check = $this->db->findOne('guild_join_requests', ['guild_id' => $guildId, 'user_id' => $userId]);
        if ($check) throw new Exception("Your request is already pending.");

        $this->db->query("INSERT INTO guild_join_requests (guild_id, user_id) VALUES (:gid, :uid)", [
            'gid' => $guildId,
            'uid' => $userId
        ]);

        return true;
    }

    /**
     * Approve or Decline a request (Leaders only)
     */
    public function handleRequest($leaderId, $requestId, $action)
    {
        $request = $this->db->findOne('guild_join_requests', ['id' => $requestId]);
        if (!$request) throw new Exception("Request not found.");

        $guild = $this->db->findOne('guilds', ['id' => $request['guild_id']]);
        if ($guild['leader_id'] != $leaderId) throw new Exception("Unauthorized.");

        if ($action === 'approve') {
            // Add member
            $this->db->query("INSERT INTO guild_members (guild_id, user_id, role) VALUES (:gid, :uid, 'Intern')", [
                'gid' => $request['guild_id'],
                'uid' => $request['user_id']
            ]);
            $this->db->query("DELETE FROM guild_join_requests WHERE id = :id", ['id' => $requestId]);
        } else {
            $this->db->query("UPDATE guild_join_requests SET status = 'declined' WHERE id = :id", ['id' => $requestId]);
        }

        return true;
    }

    /**
     * Leave or Kick member
     */
    public function leaveFirm($userId)
    {
        $member = $this->db->findOne('guild_members', ['user_id' => $userId]);
        if (!$member) throw new Exception("Not in a firm.");

        if ($member['role'] === 'Leader') {
            throw new Exception("Leaders cannot leave. Dissolve the firm instead or transfer leadership.");
        }

        $this->db->query("DELETE FROM guild_members WHERE id = :id", ['id' => $member['id']]);
        return true;
    }

    public function getJoinRequests($guildId)
    {
        return $this->db->query("
            SELECT r.*, u.username 
            FROM guild_join_requests r
            JOIN users u ON r.user_id = u.id
            WHERE r.guild_id = :gid AND r.status = 'pending'
        ", ['gid' => $guildId])->fetchAll();
    }

    public function getFirmData($guildId)
    {
        $guild = $this->db->findOne('guilds', ['id' => $guildId]);
        if (!$guild) return null;

        $vault = $this->db->query("SELECT resource_type, amount FROM guild_vault WHERE guild_id = :gid", ['gid' => $guildId])->fetchAll();
        $members = $this->db->query("
            SELECT m.*, u.username 
            FROM guild_members m
            JOIN users u ON m.user_id = u.id
            WHERE m.guild_id = :gid
        ", ['gid' => $guildId])->fetchAll();

        return [
            'guild' => $guild,
            'vault' => $vault,
            'members' => $members
        ];
    }

    // ============================================
    // PERK SYSTEM
    // ============================================

    /**
     * Purchase a perk for the firm
     */
    public function purchasePerk($guildId, $perkId, $purchasedBy)
    {
        // 1. Verify leader/co-leader permission
        if (!$this->canManageFirm($guildId, $purchasedBy)) {
            throw new Exception("Only leaders and co-leaders can purchase perks.");
        }

        // 2. Get perk details
        $perk = $this->db->findOne('firm_perks', ['id' => $perkId, 'is_active' => 1]);
        if (!$perk) throw new Exception("Perk not found.");

        // 3. Check firm level requirement
        $guild = $this->db->findOne('guilds', ['id' => $guildId]);
        if ($guild['level'] < $perk['min_firm_level']) {
            throw new Exception("Your firm must be level {$perk['min_firm_level']} to purchase this perk.");
        }

        // 4. Check vault balance
        $vaultRows = $this->db->query("SELECT resource_type, amount FROM guild_vault WHERE guild_id = :gid", ['gid' => $guildId])->fetchAll();
        $vault = [];
        foreach ($vaultRows as $row) {
            $vault[$row['resource_type']] = $row['amount'];
        }

        $coins = (int)($vault['coins'] ?? 0);
        $cost = (int)$perk['cost_coins'];

        if ($coins < $cost) {
            throw new Exception("Insufficient coins. Need $cost, have $coins");
        }
        if (($vault['bricks'] ?? 0) < $perk['cost_bricks']) {
            throw new Exception("Insufficient bricks.");
        }
        if (($vault['steel'] ?? 0) < $perk['cost_steel']) {
            throw new Exception("Insufficient steel.");
        }
        if (($vault['cement'] ?? 0) < $perk['cost_cement']) {
            throw new Exception("Insufficient cement.");
        }

        // 5. Deduct costs
        $costs = [
            'coins' => $perk['cost_coins'],
            'bricks' => $perk['cost_bricks'],
            'steel' => $perk['cost_steel'],
            'cement' => $perk['cost_cement']
        ];
        $this->spendVaultResources($guildId, $costs, $purchasedBy, "Purchased perk: {$perk['name']}");

        // 6. Activate perk
        $expiresAt = $perk['duration_hours'] > 0
            ? date('Y-m-d H:i:s', strtotime("+{$perk['duration_hours']} hours"))
            : null;

        $this->db->query("INSERT INTO firm_perk_purchases (guild_id, perk_id, purchased_by, expires_at, is_active) 
                          VALUES (:gid, :pid, :uid, :exp, 1)", [
            'gid' => $guildId,
            'pid' => $perkId,
            'uid' => $purchasedBy,
            'exp' => $expiresAt
        ]);

        // 7. Handle special perks
        if ($perk['perk_type'] === 'member_cap') {
            $this->db->query("UPDATE guilds SET max_members = max_members + 10 WHERE id = :gid", ['gid' => $guildId]);
        }

        return [
            'success' => true,
            'message' => "Perk activated: {$perk['name']}!",
            'perk' => $perk,
            'expires_at' => $expiresAt
        ];
    }

    /**
     * Get all active perks for a firm
     */
    public function getActivePerks($guildId)
    {
        $sql = "SELECT pp.*, p.name, p.perk_type, p.boost_multiplier, p.duration_hours, p.icon
                FROM firm_perk_purchases pp
                JOIN firm_perks p ON pp.perk_id = p.id
                WHERE pp.guild_id = :gid 
                AND pp.is_active = 1
                AND (pp.expires_at IS NULL OR pp.expires_at > NOW())
                ORDER BY pp.purchased_at DESC";

        return $this->db->query($sql, ['gid' => $guildId])->fetchAll();
    }

    /**
     * Apply perk bonus to a value (for XP, coins, resources)
     */
    public function applyPerkBonus($userId, $baseAmount, $perkType)
    {
        // Get user's guild
        $member = $this->db->findOne('guild_members', ['user_id' => $userId]);
        if (!$member) return $baseAmount;

        // Get active perks
        $perks = $this->getActivePerks($member['guild_id']);

        $multiplier = 1.0;
        foreach ($perks as $perk) {
            if ($perk['perk_type'] === $perkType) {
                $multiplier *= $perk['boost_multiplier'];
            }
        }

        return (int)($baseAmount * $multiplier);
    }

    /**
     * Get all available perks (shop)
     */
    public function getAvailablePerks($guildId)
    {
        $guild = $this->db->findOne('guilds', ['id' => $guildId]);

        $sql = "SELECT * FROM firm_perks 
                WHERE is_active = 1 
                AND min_firm_level <= :level
                ORDER BY min_firm_level ASC, cost_coins ASC";

        return $this->db->query($sql, ['level' => $guild['level']])->fetchAll();
    }

    // ============================================
    // VAULT SPENDING
    // ============================================

    /**
     * Spend resources from vault with audit logging
     */
    public function spendVaultResources($guildId, $costs, $spentBy, $reason)
    {
        // Get current balance
        $vaultRows = $this->db->query("SELECT resource_type, amount FROM guild_vault WHERE guild_id = :gid", ['gid' => $guildId])->fetchAll();
        $vault = [];
        foreach ($vaultRows as $row) {
            $vault[$row['resource_type']] = $row['amount'];
        }

        // Deduct each resource type
        foreach ($costs as $resource => $amount) {
            if ($amount > 0) {
                $currentBalance = $vault[$resource] ?? 0;
                $newBalance = $currentBalance - $amount;
                if ($newBalance < 0) {
                    throw new Exception("Insufficient $resource in vault.");
                }

                $this->db->query("UPDATE guild_vault SET amount = :bal WHERE guild_id = :gid AND resource_type = :res", [
                    'bal' => $newBalance,
                    'gid' => $guildId,
                    'res' => $resource
                ]);

                // Log transaction
                $this->db->query("INSERT INTO firm_vault_transactions 
                                  (guild_id, transaction_type, resource_type, amount, balance_after, performed_by, reason)
                                  VALUES (:gid, 'perk_purchase', :res, :amt, :bal, :uid, :reason)", [
                    'gid' => $guildId,
                    'res' => $resource,
                    'amt' => -$amount,
                    'bal' => $newBalance,
                    'uid' => $spentBy,
                    'reason' => $reason
                ]);
            }
        }
    }

    /**
     * Distribute dividends to all members
     */
    public function distributeDividends($guildId, $coinsPerMember, $distributedBy)
    {
        if (!$this->canManageFirm($guildId, $distributedBy)) {
            throw new Exception("Only leaders can distribute dividends.");
        }

        $members = $this->db->query("SELECT user_id FROM guild_members WHERE guild_id = :gid", ['gid' => $guildId])->fetchAll();
        $totalCost = count($members) * $coinsPerMember;

        // Check vault
        $vaultRow = $this->db->findOne('guild_vault', ['guild_id' => $guildId, 'resource_type' => 'coins']);
        $currentCoins = $vaultRow['amount'] ?? 0;

        if ($currentCoins < $totalCost) {
            throw new Exception("Insufficient coins in vault. Need $totalCost, have $currentCoins.");
        }

        // Deduct full amount from vault
        // HARDENING 2: Dividend Tax (15% Burn)
        // Vault pays full amount, but members receive only 85%.
        // The 15% difference is destroyed (economy sink).
        $taxRate = 0.15;
        $netPayout = (int)($coinsPerMember * (1.0 - $taxRate));
        $taxAmount = $coinsPerMember - $netPayout;

        $this->spendVaultResources($guildId, ['coins' => $totalCost], $distributedBy, "Dividend distribution: $coinsPerMember/member ($taxAmount tax burned)");

        // Pay each member (Net Amount)
        foreach ($members as $member) {
            $this->db->query("UPDATE user_resources SET coins = coins + :amt WHERE user_id = :uid", [
                'amt' => $netPayout,
                'uid' => $member['user_id']
            ]);
        }

        return [
            'success' => true,
            'message' => "Distributed dividends! Paid $netPayout coins to " . count($members) . " members. ($taxAmount coins/member burned as tax)",
            'total_spent' => $totalCost,
            'net_payout' => $netPayout,
            'tax_burned' => $taxAmount * count($members)
        ];
    }

    // ============================================
    // LEADERBOARD SYSTEM
    // ============================================

    /**
     * Calculate bi-weekly tier-based rewards
     */
    public function calculateBiWeeklyRewards()
    {
        $periodStart = date('Y-m-d', strtotime('monday this week -1 week'));
        $periodEnd = date('Y-m-d', strtotime('sunday this week'));

        // Get all guilds with their XP
        $guilds = $this->db->query("SELECT id, name, current_period_xp FROM guilds WHERE current_period_xp > 0")->fetchAll();

        // HARDENING 1: Efficiency Leaderboard
        // Calculate Efficiency Score = XP / Members
        foreach ($guilds as $guild) {
            $memberCount = $this->db->query("SELECT COUNT(*) as c FROM guild_members WHERE guild_id = :gid", ['gid' => $guild['id']])->fetch()['c'];
            // Avoid division by zero
            $memberCount = max($memberCount, 1);

            $efficiencyScore = $guild['current_period_xp'] / $memberCount;

            // Tier based on EFFICIENCY, not raw volume
            // Thresholds adjusted for per-person performance
            $tiers = [
                'Platinum' => ['min' => 3000, 'reward' => 5000], // e.g., 100 XP/day for 30 days? No, this is bi-weekly. 200 XP/day/person = 2800.
                'Gold' => ['min' => 1500, 'reward' => 2500],
                'Silver' => ['min' => 500, 'reward' => 1000],
                'Bronze' => ['min' => 100, 'reward' => 500]
            ];

            $tier = 'None';
            $reward = 0;

            foreach ($tiers as $tierName => $tierData) {
                if ($efficiencyScore >= $tierData['min']) {
                    $tier = $tierName;
                    $reward = $tierData['reward'];
                    break;
                }
            }

            // Record stats
            $sql = "INSERT INTO firm_biweekly_stats 
                    (guild_id, period_start, period_end, total_xp_earned, efficiency_score, active_member_count, tier, reward_coins, is_finalized)
                    VALUES (:gid, :start, :end, :xp, :eff, :count, :tier, :reward, 1)
                    ON DUPLICATE KEY UPDATE total_xp_earned = :xp, efficiency_score = :eff, active_member_count = :count, tier = :tier, reward_coins = :reward";

            $this->db->query($sql, [
                'gid' => $guild['id'],
                'start' => $periodStart,
                'end' => $periodEnd,
                'xp' => $guild['current_period_xp'],
                'eff' => $efficiencyScore,
                'count' => $memberCount,
                'tier' => $tier,
                'reward' => $reward
            ]);

            // Award coins to vault
            if ($reward > 0) {
                $this->db->query("UPDATE guild_vault SET coins = coins + :reward WHERE guild_id = :gid", [
                    'reward' => $reward,
                    'gid' => $guild['id']
                ]);
            }
        }

        // Reset period XP - Consider doing this only if not already reset? For now doing it as instructed.
        $this->db->query("UPDATE guilds SET current_period_xp = 0, last_xp_reset = CURDATE()");

        return ['success' => true, 'message' => 'Bi-weekly rewards calculated'];
    }

    /**
     * Calculate monthly bonus for top 10
     */
    public function calculateMonthlyBonus()
    {
        $monthStart = date('Y-m-01');

        // Get top 10 guilds by total XP
        $topGuilds = $this->db->query("SELECT id, name, total_xp_earned 
                                        FROM guilds 
                                        ORDER BY total_xp_earned DESC 
                                        LIMIT 10")->fetchAll();

        $bonuses = [
            1 => 5000,
            2 => 3000,
            3 => 2000,
            4 => 1000,
            5 => 1000,
            6 => 1000,
            7 => 1000,
            8 => 1000,
            9 => 1000,
            10 => 1000
        ];

        $rank = 1;
        foreach ($topGuilds as $guild) {
            $bonus = $bonuses[$rank] ?? 0;

            // Record ranking
            $sql = "INSERT INTO firm_monthly_rankings 
                    (guild_id, month_start, total_xp, rank_position, bonus_coins, is_finalized)
                    VALUES (:gid, :month, :xp, :rank, :bonus, 1)
                    ON DUPLICATE KEY UPDATE total_xp = :xp, rank_position = :rank, bonus_coins = :bonus";

            $this->db->query($sql, [
                'gid' => $guild['id'],
                'month' => $monthStart,
                'xp' => $guild['total_xp_earned'],
                'rank' => $rank,
                'bonus' => $bonus
            ]);

            // Award bonus
            if ($bonus > 0) {
                $this->db->query("UPDATE guild_vault SET coins = coins + :bonus WHERE guild_id = :gid", [
                    'bonus' => $bonus,
                    'gid' => $guild['id']
                ]);
            }

            $rank++;
        }

        return ['success' => true, 'message' => 'Monthly bonuses awarded'];
    }

    /**
     * Add XP to firm (called when member earns XP)
     */
    public function addFirmXP($userId, $xpAmount)
    {
        $member = $this->db->findOne('guild_members', ['user_id' => $userId]);
        if (!$member) return;

        // Apply catch-up multiplier
        $guild = $this->db->findOne('guilds', ['id' => $member['guild_id']]);
        $multiplier = $guild['catch_up_multiplier'] ?? 1.0;
        $bonusXP = (int)($xpAmount * $multiplier);

        $this->db->query("UPDATE guilds 
                          SET total_xp_earned = total_xp_earned + :xp,
                              current_period_xp = current_period_xp + :xp
                          WHERE id = :gid", [
            'xp' => $bonusXP,
            'gid' => $member['guild_id']
        ]);
    }

    // ============================================
    // LEADER POWERS
    // ============================================

    /**
     * Promote member to co-leader
     */
    public function promoteMember($guildId, $userId, $promotedBy)
    {
        $guild = $this->db->findOne('guilds', ['id' => $guildId]);

        // Only leader can promote
        if ($guild['leader_id'] != $promotedBy) {
            throw new Exception("Only the firm leader can promote members.");
        }

        // Check co-leader limit (max 2)
        $coLeaders = json_decode($guild['co_leaders'] ?? '[]', true);
        if (!is_array($coLeaders)) $coLeaders = [];
        if (count($coLeaders) >= 2) {
            throw new Exception("Maximum 2 co-leaders allowed.");
        }

        // Add to co-leaders
        if (!in_array($userId, $coLeaders)) {
            $coLeaders[] = $userId;
            $this->db->query("UPDATE guilds SET co_leaders = :cls WHERE id = :gid", [
                'cls' => json_encode($coLeaders),
                'gid' => $guildId
            ]);
        }

        // Update role
        $this->db->query("UPDATE guild_members SET role = 'Co-Leader' WHERE guild_id = :gid AND user_id = :uid", [
            'gid' => $guildId,
            'uid' => $userId
        ]);

        return ['success' => true, 'message' => 'Member promoted to Co-Leader'];
    }

    /**
     * Demote co-leader to member
     */
    public function demoteMember($guildId, $userId, $demotedBy)
    {
        $guild = $this->db->findOne('guilds', ['id' => $guildId]);

        if ($guild['leader_id'] != $demotedBy) {
            throw new Exception("Only the firm leader can demote co-leaders.");
        }

        // Remove from co-leaders
        $coLeaders = json_decode($guild['co_leaders'] ?? '[]', true);
        if (!is_array($coLeaders)) $coLeaders = [];
        $coLeaders = array_values(array_filter($coLeaders, fn($id) => $id != $userId));

        $this->db->query("UPDATE guilds SET co_leaders = :cls WHERE id = :gid", [
            'cls' => json_encode($coLeaders),
            'gid' => $guildId
        ]);

        // Update role
        $this->db->query("UPDATE guild_members SET role = 'Member' WHERE guild_id = :gid AND user_id = :uid", [
            'gid' => $guildId,
            'uid' => $userId
        ]);

        return ['success' => true, 'message' => 'Co-Leader demoted to Member'];
    }

    /**
     * Check if user can manage firm (leader or co-leader)
     */
    private function canManageFirm($guildId, $userId)
    {
        $guild = $this->db->findOne('guilds', ['id' => $guildId]);

        if ($guild['leader_id'] == $userId) return true;

        $coLeaders = json_decode($guild['co_leaders'] ?? '[]', true);
        if (!is_array($coLeaders)) $coLeaders = [];
        return in_array($userId, $coLeaders);
    }

    /**
     * Get level benefits
     */
    public function getLevelBenefits($level)
    {
        $benefits = [
            1 => ['max_members' => 10, 'unlocks' => ['Basic Vault']],
            3 => ['max_members' => 20, 'unlocks' => ['XP Boost Perks']],
            5 => ['max_members' => 30, 'unlocks' => ['Coin Boost Perks', 'Steel Vault']],
            10 => ['max_members' => 50, 'unlocks' => ['Resource Boost Perks', 'Member Cap Expansion']],
            15 => ['max_members' => 100, 'unlocks' => ['Co-Leader Slots (2)']],
            20 => ['max_members' => 200, 'unlocks' => ['Dividend System']],
            25 => ['max_members' => 500, 'unlocks' => ['Elite Exam Access']]
        ];

        $currentBenefits = ['max_members' => 10, 'unlocks' => []];
        foreach ($benefits as $lvl => $data) {
            if ($level >= $lvl) {
                $currentBenefits = $data;
            }
        }

        return $currentBenefits;
    }
}
