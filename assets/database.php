<?php
// Set default timezone
date_default_timezone_set('Asia/Dhaka');  // Bangladesh timezone

class Database {
    private $host = "localhost";
    private $db_name = "shop_db";
    private $username = "root";
    private $password = "";
    private $conn = null;

    public function getConnection() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
            
            // Set MySQL timezone to match PHP
            $this->conn->exec("SET time_zone = '+06:00'");  // Bangladesh timezone (UTC+6)
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>