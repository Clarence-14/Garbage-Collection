<?php
namespace Src\Config;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $pdo;

    private $host;
    private $db;
    private $user;
    private $pass;
    private $charset;

    private function __construct() {
        $this->host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $this->db   = $_ENV['DB_NAME'] ?? 'garbage_collection_db';
        $this->user = $_ENV['DB_USER'] ?? 'root';
        $this->pass = $_ENV['DB_PASS'] ?? '';
        $this->charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            // In production, log this, don't show to user
            $logMessage = "[" . date('Y-m-d H:i:s') . "] Database Connection Failed: " . $e->getMessage() . PHP_EOL;
            file_put_contents(__DIR__ . '/../../logs/error.log', $logMessage, FILE_APPEND);
            die("A system error occurred. Please try again later.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
