<?php
require_once __DIR__ . '/../core/Database.php';

class OfferwallProvider {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all active offerwall providers
    public function getActiveProviders() {
        $this->db->query('SELECT * FROM offerwall_providers WHERE is_active = 1 ORDER BY name ASC');
        $results = $this->db->resultSet();
        return $results;
    }
}
?>
