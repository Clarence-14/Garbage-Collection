<?php
namespace Src\Models;

use Src\Config\Database;
use PDO;

class Schedule {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllSchedules() {
        $stmt = $this->db->query("SELECT * FROM schedules ORDER BY zone_name, collection_day");
        return $stmt->fetchAll();
    }

    public function createSchedule($data) {
        $stmt = $this->db->prepare("INSERT INTO schedules (zone_name, collection_day, waste_type, description) VALUES (:zone_name, :collection_day, :waste_type, :description)");
        return $stmt->execute([
            'zone_name' => $data['zone_name'],
            'collection_day' => $data['collection_day'],
            'waste_type' => $data['waste_type'],
            'description' => $data['description'] ?? ''
        ]);
    }

    public function deleteSchedule($id) {
        $stmt = $this->db->prepare("DELETE FROM schedules WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
