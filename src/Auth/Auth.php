<?php
namespace Src\Auth;

use Src\Config\Database;
use PDO;

class Auth {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Prevent session fixation
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'full_name' => $user['full_name']
            ];
            return true;
        }
        return false;
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();
    }

    public function register($data) {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, role, full_name, address, phone) VALUES (:username, :email, :password_hash, :role, :full_name, :address, :phone)");
        
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
        
        return $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => $passwordHash,
            'role' => $data['role'] ?? 'resident',
            'full_name' => $data['full_name'],
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null
        ]);
    }

    public static function requireRole($role) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== $role) {
            header("Location: /index.php?error=unauthorized");
            exit();
        }
    }
    
    public static function requireLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
             header("Location: /index.php?error=login_required");
             exit();
        }
    }
}
