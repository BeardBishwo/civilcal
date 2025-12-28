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
        return $this->db->fetchAll("
            SELECT r.*, u.username, u.full_name 
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
        $members = $this->db->fetchAll("
            SELECT m.*, u.username, u.full_name 
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
}
