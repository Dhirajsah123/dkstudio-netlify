<?php
require_once __DIR__ . '/../core/Database.php';

class Transaction {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get recent transactions for a user
    public function getRecentTransactions($user_id, $limit = 5) {
        $this->db->query('SELECT * FROM transactions WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit');
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':limit', $limit);

        $results = $this->db->resultSet();

        return $results;
    }

    // Create transaction
    public function create($data) {
        $this->db->query('INSERT INTO transactions (user_id, type, description, amount) VALUES (:user_id, :type, :description, :amount)');
        // Bind values
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':amount', $data['amount']);

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>
