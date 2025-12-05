<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;

class Role
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        $pdo = $this->db->getPdo();

        $pdo->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                slug VARCHAR(100) NOT NULL UNIQUE,
                name VARCHAR(150) NOT NULL,
                description TEXT NULL,
                permissions JSON NULL,
                is_system TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $defaults = [
            ['slug' => 'admin', 'name' => 'Administrator', 'description' => 'Full system access', 'is_system' => 1],
            ['slug' => 'user', 'name' => 'Regular User', 'description' => 'Standard user access', 'is_system' => 1],
            ['slug' => 'engineer', 'name' => 'Engineer', 'description' => 'Engineering tools access', 'is_system' => 1],
        ];

        foreach ($defaults as $role) {
            if (!$this->findBySlug($role['slug'])) {
                $stmt = $pdo->prepare('INSERT INTO roles (slug, name, description, is_system) VALUES (:slug, :name, :description, :is_system)');
                $stmt->execute([
                    'slug' => $role['slug'],
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'is_system' => $role['is_system'],
                ]);
            }
        }
    }

    public function all(): array
    {
        $stmt = $this->db->getPdo()->query('SELECT * FROM roles ORDER BY name');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->getPdo()->prepare('SELECT * FROM roles WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        return $role ?: null;
    }

    public function create(array $data): array
    {
        $slug = $this->slugify($data['slug'] ?? $data['name'] ?? '');
        $name = trim($data['name'] ?? '');

        if ($slug === '' || $name === '') {
            throw new Exception('Role name is required.');
        }

        if ($this->findBySlug($slug)) {
            throw new Exception('A role with this identifier already exists.');
        }

        $stmt = $this->db->getPdo()->prepare('INSERT INTO roles (slug, name, description, permissions, is_system) VALUES (:slug, :name, :description, :permissions, :is_system)');
        $stmt->execute([
            'slug' => $slug,
            'name' => $name,
            'description' => trim($data['description'] ?? ''),
            'permissions' => !empty($data['permissions']) ? json_encode($data['permissions']) : null,
            'is_system' => !empty($data['is_system']) ? 1 : 0,
        ]);

        return $this->findBySlug($slug);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->getPdo()->prepare('DELETE FROM roles WHERE id = :id AND is_system = 0');
        return $stmt->execute(['id' => $id]);
    }

    public function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        return trim($value, '-');
    }
}
