<?php
require_once __DIR__ . '/../core/Database.php';

class Offer {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all active offers
    public function getActiveOffers() {
        $this->db->query('SELECT * FROM offers WHERE is_active = 1 ORDER BY created_at DESC');
        $results = $this->db->resultSet();
        return $results;
    }

    // Get offer by ID
    public function getOfferById($id) {
        $this->db->query('SELECT * FROM offers WHERE id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        return $row;
    }

    // Add offer
    public function addOffer($data) {
        $this->db->query('INSERT INTO offers (name, description, points, url, is_active) VALUES (:name, :description, :points, :url, :is_active)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':points', $data['points']);
        $this->db->bind(':url', $data['url']);
        $this->db->bind(':is_active', $data['is_active']);
        return $this->db->execute();
    }

    // Update offer
    public function updateOffer($data) {
        $this->db->query('UPDATE offers SET name = :name, description = :description, points = :points, url = :url, is_active = :is_active WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':points', $data['points']);
        $this->db->bind(':url', $data['url']);
        $this->db->bind(':is_active', $data['is_active']);
        return $this->db->execute();
    }

    // Delete offer
    public function deleteOffer($id) {
        $this->db->query('DELETE FROM offers WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
?>
