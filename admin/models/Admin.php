<?php
require_once __DIR__ . '/../../php/core/Database.php';

class Admin {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Find admin by username
    public function findAdminByUsername($username) {
        $this->db->query('SELECT * FROM admin WHERE username = :username');
        $this->db->bind(':username', $username);
        $row = $this->db->single();
        return $row;
    }

    // Login admin
    public function login($username, $password) {
        $row = $this->findAdminByUsername($username);

        if ($row == false) {
            return false;
        }

        $hashedPassword = $row->password;
        if (password_verify($password, $hashedPassword)) {
            return $row;
        } else {
            return false;
        }
    }
}
?>
