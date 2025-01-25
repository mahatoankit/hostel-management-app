<?php
require_once __DIR__ . '/Database.php';

class Hosteller {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Authenticate a hosteller with password hashing
    public function authenticate($email, $password) {
        try {
            $stmt = $this->db->prepare("
                SELECT hostellerID, firstName, lastName, email, password 
                FROM hostellers 
                WHERE hostellersEmail = ?
            ");
            $stmt->execute([$email]);
            $hosteller = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($hosteller && password_verify($password, $hosteller['password'])) {
                return $hosteller;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Authentication error: " . $e->getMessage());
            return false;
        }
    }

    // Get all complaints with visibility filtering
    public function getAllComplaints($hostellerId = null) {
        try {
            $sql = "SELECT c.*, h.firstName, h.lastName 
                    FROM complaints c
                    JOIN hostellers h ON c.userID = h.hostellerID
                    WHERE c.visibility = 'Public'";

            if ($hostellerId) {
                $sql .= " OR c.userID = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$hostellerId]);
            } else {
                $stmt = $this->db->query($sql);
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get complaints error: " . $e->getMessage());
            return [];
        }
    }

    // Post a new complaint with full parameters
    public function postComplaint($hostellerId, $complaintType, $description, $visibility = 'Private') {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO complaints 
                (userID, complaintType, description, complaintStatus, postingDate, visibility) 
                VALUES (?, ?, ?, 'Open', CURDATE(), ?)
            ");
            return $stmt->execute([
                $hostellerId,
                $complaintType,
                $description,
                $visibility
            ]);
        } catch (PDOException $e) {
            error_log("Post complaint error: " . $e->getMessage());
            return false;
        }
    }

    // Get room details for a hosteller
    public function getCurrentRoomDetails($userID) {
        try {
            $stmt = $this->db->prepare("
                SELECT r.roomNumber, r.seaterNumber
                FROM roomAllocation ra
                JOIN rooms r ON ra.roomNumber = r.roomNumber
                WHERE ra.userID = ?
            ");
            $stmt->execute([$userID]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching current room details: ".$e->getMessage());
            return false;
        }
    }

    // Get roommates for a hosteller
    public function getRoommates($roomNumber, $excludeHostellerId) {
        try {
            $stmt = $this->db->prepare("
SELECT h.hostellerID, h.firstName, h.lastName, h.hostellersEmail, h.phoneNumber FROM roomAllocation ra JOIN hostellers h ON ra.userID = h.userID WHERE ra.roomNumber = ? AND ra.userID != ?;
            ");
            $stmt->execute([$roomNumber, $excludeHostellerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get roommates error: " . $e->getMessage());
            return [];
        }
    }
}
?>