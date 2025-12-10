<?php
namespace Src\Models;

use Src\Config\Database;
use PDO;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllUsers() {
        $stmt = $this->db->query("SELECT id, username, email, role, full_name, created_at, phone FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Admin Create User (Drivers/Admins)
    public function createUser($data) {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, role, full_name, address, phone) VALUES (:username, :email, :password_hash, :role, :full_name, :address, :phone)");
        $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role' => $data['role'],
            'full_name' => $data['full_name'],
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function updateUser($id, $data) {
        $sql = "UPDATE users SET username = :username, email = :email, role = :role, full_name = :full_name, address = :address, phone = :phone";
        
        $params = [
            'id' => $id,
            'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'],
            'full_name' => $data['full_name'],
            'address' => $data['address'],
            'phone' => $data['phone']
        ];

        if (!empty($data['password'])) {
            $sql .= ", password_hash = :password_hash";
            $params['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
