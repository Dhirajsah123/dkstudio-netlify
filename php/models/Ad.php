<?php
require_once __DIR__ . '/../core/Database.php';

class Ad {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all active ads
    public function getActiveAds() {
        $this->db->query('SELECT * FROM ads WHERE is_active = 1 ORDER BY created_at DESC');
        $results = $this->db->resultSet();
        return $results;
    }

    // Get ad by ID
    public function getAdById($id) {
        $this->db->query('SELECT * FROM ads WHERE id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        return $row;
    }

    // Add ad
    public function addAd($data) {
        $this->db->query('INSERT INTO ads (title, description, points, url, is_active) VALUES (:title, :description, :points, :url, :is_active)');
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':points', $data['points']);
        $this->db->bind(':url', $data['url']);
        $this->db->bind(':is_active', $data['is_active']);
        return $this->db->execute();
    }

    // Update ad
    public function updateAd($data) {
        $this->db->query('UPDATE ads SET title = :title, description = :description, points = :points, url = :url, is_active = :is_active WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':points', $data['points']);
        $this->db->bind(':url', $data['url']);
        $this->db->bind(':is_active', $data['is_active']);
        return $this->db->execute();
    }

    // Delete ad
    public function deleteAd($id) {
        $this->db->query('DELETE FROM ads WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
?>
