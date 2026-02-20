<?php
namespace Src\Models;

use Src\Config\Database;
use PDO;

class Route {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // For Admin: Get all routes
    public function getAllRoutes() {
        $sql = "SELECT r.*, u.full_name as driver_name, s.zone_name, s.waste_type 
                FROM routes r
                LEFT JOIN users u ON r.driver_id = u.id
                JOIN schedules s ON r.schedule_id = s.id
                ORDER BY r.collection_date DESC";
        return $this->db->query($sql)->fetchAll();
    }

    // For Driver: Get assigned routes for today/future
    public function getRoutesByDriver($driverId, $driverCounty = null) {
        $sql = "SELECT r.*, s.zone_name, s.waste_type, s.description 
                FROM routes r
                JOIN schedules s ON r.schedule_id = s.id
                WHERE r.driver_id = :driver_id";
        
        if ($driverCounty) {
            $sql .= " AND s.zone_name LIKE :county";
        }
        
        $sql .= " ORDER BY r.collection_date, r.status";
        $stmt = $this->db->prepare($sql);
        
        $params = ['driver_id' => $driverId];
        if ($driverCounty) {
            $params['county'] = '%' . $driverCounty . '%';
        }
        
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function createRoute($driverId, $scheduleId, $date) {
        $stmt = $this->db->prepare("INSERT INTO routes (driver_id, schedule_id, collection_date, status) VALUES (:driver_id, :schedule_id, :date, 'Pending')");
        return $stmt->execute([
            'driver_id' => $driverId,
            'schedule_id' => $scheduleId,
            'date' => $date
        ]);
    }

    public function updateStatus($routeId, $status) {
        $stmt = $this->db->prepare("UPDATE routes SET status = :status WHERE id = :id");
        return $stmt->execute([
            'status' => $status,
            'id' => $routeId
        ]);
    }
}
