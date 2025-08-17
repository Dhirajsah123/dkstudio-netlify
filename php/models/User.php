<?php
require_once __DIR__ . '/../core/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Register user
    public function register($data) {
        $this->db->query('INSERT INTO users (username, email, password, verification_token, ip_address) VALUES (:username, :email, :password, :verification_token, :ip_address)');
        // Bind values
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':verification_token', $data['verification_token']);
        $this->db->bind(':ip_address', $data['ip_address']);

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Find user by email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Check row
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    // Find user by username
    public function findUserByUsername($username) {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        // Check row
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    // Login user
    public function login($email, $password) {
        $row = $this->findUserByEmail($email);

        if ($row == false) {
            return false;
        }

        $hashedPassword = $row->password;
        if (password_verify($password, $hashedPassword)) {
            // Check if email is verified
            if ($row->email_verified == 1) {
                return $row;
            } else {
                return false; // Or return a specific error for unverified email
            }
        } else {
            return false;
        }
    }

    // Verify email
    public function verifyEmail($email) {
        $this->db->query('UPDATE users SET email_verified = 1, verification_token = NULL WHERE email = :email');
        $this->db->bind(':email', $email);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Find user by ID
    public function findUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        // Generate referral code if it doesn't exist
        if ($row && empty($row->referral_code)) {
            $this->generateReferralCode($id);
            $row = $this->findUserById($id); // Re-fetch user data
        }

        return $row;
    }

    // Generate referral code
    public function generateReferralCode($user_id) {
        $referral_code = 'REF' . strtoupper(substr(md5($user_id . time()), 0, 8));
        $this->db->query('UPDATE users SET referral_code = :referral_code WHERE id = :id');
        $this->db->bind(':referral_code', $referral_code);
        $this->db->bind(':id', $user_id);
        $this->db->execute();
    }

    // Get referred users
    public function getReferredUsers($user_id) {
        $this->db->query('SELECT u.username, r.created_at, r.commission_earned FROM referrals r JOIN users u ON r.referred_id = u.id WHERE r.referrer_id = :user_id ORDER BY r.created_at DESC');
        $this->db->bind(':user_id', $user_id);
        $results = $this->db->resultSet();
        return $results;
    }

    // Get referral summary
    public function getReferralSummary($user_id) {
        $this->db->query('SELECT COUNT(*) as count, SUM(commission_earned) as earnings FROM referrals WHERE referrer_id = :user_id');
        $this->db->bind(':user_id', $user_id);

        $row = $this->db->single();

        return (array)$row;
    }

    // Update profile
    public function updateProfile($data) {
        $this->db->query('UPDATE users SET username = :username, email = :email WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update password
    public function updatePassword($id, $password) {
        $this->db->query('UPDATE users SET password = :password WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':password', $password);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update balance
    public function updateBalance($user_id, $amount) {
        $this->db->query('UPDATE users SET balance = balance + :amount WHERE id = :user_id');
        $this->db->bind(':amount', $amount);
        $this->db->bind(':user_id', $user_id);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get all users
    public function getAllUsers() {
        $this->db->query('SELECT * FROM users ORDER BY created_at DESC');
        $results = $this->db->resultSet();
        return $results;
    }

    // Delete user
    public function deleteUser($id) {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update profile by admin
    public function updateProfileByAdmin($data) {
        $this->db->query('UPDATE users SET username = :username, email = :email, balance = :balance, email_verified = :email_verified WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':balance', $data['balance']);
        $this->db->bind(':email_verified', $data['email_verified']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update last login IP
    public function updateLastLoginIp($id, $ip) {
        $this->db->query('UPDATE users SET last_login_ip = :ip WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':ip', $ip);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>
