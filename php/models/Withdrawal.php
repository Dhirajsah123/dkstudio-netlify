<?php
require_once __DIR__ . '/../core/Database.php';

class Withdrawal {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Create withdrawal request
    public function create($data) {
        $this->db->query('INSERT INTO withdrawals (user_id, method, amount, details, status) VALUES (:user_id, :method, :amount, :details, :status)');
        // Bind values
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':method', $data['method']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':details', $data['details']);
        $this->db->bind(':status', 'pending');

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get user's withdrawal history
    public function getWithdrawalHistory($user_id) {
        $this->db->query('SELECT * FROM withdrawals WHERE user_id = :user_id ORDER BY created_at DESC');
        $this->db->bind(':user_id', $user_id);
        $results = $this->db->resultSet();
        return $results;
    }

    // Get all withdrawal requests
    public function getAllWithdrawals() {
        $this->db->query('SELECT w.*, u.username FROM withdrawals w JOIN users u ON w.user_id = u.id ORDER BY w.created_at DESC');
        $results = $this->db->resultSet();
        return $results;
    }

    // Update withdrawal status
    public function updateStatus($id, $status) {
        $this->db->query('UPDATE withdrawals SET status = :status WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    // Get withdrawal by ID
    public function getWithdrawalById($id) {
        $this->db->query('SELECT * FROM withdrawals WHERE id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        return $row;
    }
}
?>
