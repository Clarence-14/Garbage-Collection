<?php
namespace Src\Models;

use Src\Config\Database;
use PDO;

class ServiceRequest {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllRequests() {
        $sql = "SELECT r.*, u.username, u.full_name, u.address 
                FROM service_requests r 
                JOIN users u ON r.user_id = u.id 
                ORDER BY r.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getRequestsByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM service_requests WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function createRequest($userId, $type, $description) {
        $stmt = $this->db->prepare("INSERT INTO service_requests (user_id, request_type, description) VALUES (:user_id, :type, :description)");
        return $stmt->execute([
            'user_id' => $userId,
            'type' => $type,
            'description' => $description
        ]);
    }

    public function updateStatus($id, $status, $notes = null) {
        $sql = "UPDATE service_requests SET status = :status";
        $params = ['id' => $id, 'status' => $status];
        
        if ($notes !== null) {
            $sql .= ", admin_notes = :notes";
            $params['notes'] = $notes;
        }
        
        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
