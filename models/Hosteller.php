<?php
require_once __DIR__ . '/Database.php';

class Hosteller {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Authenticate a hosteller using email and password.
     *
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function authenticate(string $email, string $password) {
        try {
            $stmt = $this->db->prepare("
                SELECT hostellerID, firstName, lastName, hostellersEmail, password 
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

    /**
     * Get all complaints with visibility filtering.
     *
     * @param int|null $hostellerId
     * @return array
     */
    public function getAllComplaints(?int $hostellerId = null): array {
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

    /**
     * Post a new complaint.
     *
     * @param int $hostellerId
     * @param string $complaintType
     * @param string $description
     * @param string $visibility
     * @return bool
     */
    public function postComplaint(int $hostellerId, string $complaintType, string $description, string $visibility = 'Private'): bool {
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

    /**
     * Get room details for a hosteller.
     *
     * @param int $userID
     * @return array|false
     */
    public function getCurrentRoomDetails(int $userID) {
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
            error_log("Error fetching current room details: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get roommates for a hosteller.
     *
     * @param int $roomNumber
     * @param int $excludeHostellerId
     * @return array
     */
    public function getRoommates(int $roomNumber, int $excludeHostellerId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT h.hostellerID, h.firstName, h.lastName, h.hostellersEmail, h.phoneNumber 
                FROM roomAllocation ra 
                JOIN hostellers h ON ra.userID = h.hostellerID 
                WHERE ra.roomNumber = ? AND ra.userID != ?
            ");
            $stmt->execute([$roomNumber, $excludeHostellerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get roommates error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all hostellers.
     *
     * @return array
     */
    public function getAllHostellers(): array {
        try {
            $stmt = $this->db->prepare("SELECT userID, firstName, lastName FROM hostellers");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching hostellers: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Add a new hosteller.
     *
     * @param string $hostellerID
     * @param string $hostellersEmail
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     * @param string $phoneNumber
     * @param string $occupation
     * @param string $address
     * @param string $joinedDate
     * @param string $departureDate
     * @param string $dietaryPreference
     * @param int $roomNumber
     * @return bool
     */
    public function addHosteller(
        string $hostellerID,
        string $hostellersEmail,
        string $password,
        string $firstName,
        string $lastName,
        string $phoneNumber,
        string $occupation,
        string $address,
        string $joinedDate,
        string $departureDate,
        string $dietaryPreference
    ): bool {
        try {
            $sql = "
                INSERT INTO hostellers 
                (hostellerID, hostellersEmail, password, firstName, lastName, phoneNumber, occupation, address, joinedDate, departureDate, dietaryPreference) 
                VALUES 
                (:hostellerID, :hostellersEmail, :password, :firstName, :lastName, :phoneNumber, :occupation, :address, :joinedDate, :departureDate, :dietaryPreference)
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':hostellerID' => $hostellerID,
                ':hostellersEmail' => $hostellersEmail,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':firstName' => $firstName,
                ':lastName' => $lastName,
                ':phoneNumber' => $phoneNumber,
                ':occupation' => $occupation,
                ':address' => $address,
                ':joinedDate' => $joinedDate,
                ':departureDate' => $departureDate,
                ':dietaryPreference' => $dietaryPreference
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error adding hosteller: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a hosteller.
     *
     * @param int $userID
     * @return bool
     */
    public function deleteHosteller(int $userID): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM hostellers WHERE userID = ?");
            return $stmt->execute([$userID]);
        } catch (PDOException $e) {
            error_log("Error deleting hosteller: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Allocate a hosteller to a room.
     *
     * @param int $userID
     * @param int $roomNumber
     * @return bool
     */
    public function allocateRoom(int $userID, int $roomNumber): bool {
        try {
            // Check if the room has available space
            $stmt = $this->db->prepare("
                SELECT seaterNumber, 
                       (SELECT COUNT(*) FROM roomAllocation WHERE roomNumber = ?) AS currentAllocations 
                FROM rooms 
                WHERE roomNumber = ?
            ");
            $stmt->execute([$roomNumber, $roomNumber]);
            $room = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($room && $room['currentAllocations'] < $room['seaterNumber']) {
                $stmt = $this->db->prepare("
                    INSERT INTO roomAllocation (userID, roomNumber, allocationDate) 
                    VALUES (?, ?, CURDATE())
                ");
                return $stmt->execute([$userID, $roomNumber]);
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error allocating room: " . $e->getMessage());
            return false;
        }
    }
}