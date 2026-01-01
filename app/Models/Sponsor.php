<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Sponsor
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO sponsors (name, website_url, contact_person, contact_email, phone, status, logo_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['website_url'] ?? null,
            $data['contact_person'] ?? null,
            $data['contact_email'] ?? null,
            $data['phone'] ?? null,
            $data['status'] ?? 'active',
            $data['logo_path'] ?? null
        ]);
    }

    public function update($id, $data)
    {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;

        $sql = "UPDATE sponsors SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM sponsors WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll($status = null)
    {
        $sql = "SELECT * FROM sponsors";
        $params = [];
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getActiveCampaigns($sponsorId) {
        $stmt = $this->pdo->prepare("SELECT * FROM campaigns WHERE sponsor_id = ? AND status = 'active'");
        $stmt->execute([$sponsorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
