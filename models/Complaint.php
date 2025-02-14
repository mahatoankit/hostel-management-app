<?php
require_once __DIR__ . '/Database.php';

class Complaint {
    /**
     * Fetch all complaints with hosteller details.
     *
     * @return array
     */
    public function getAllComplaints() {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT complaints.*, hostellers.firstName, hostellers.lastName 
                FROM complaints 
                JOIN hostellers ON complaints.userID = hostellers.userID 
                ORDER BY complaints.postingDate DESC;
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as associative array
        } catch (Exception $e) {
            error_log("Error fetching complaints: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch a specific complaint by ID.
     *
     * @param int $complaintID
     * @return array|null
     */
    public function getComplaintById($complaintID) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT complaints.*, hostellers.firstName, hostellers.lastName 
                FROM complaints 
                JOIN hostellers ON complaints.userID = hostellers.userID 
                WHERE complaints.complaintID = ?
            ");
            $stmt->execute([$complaintID]);
            return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch single row
        } catch (Exception $e) {
            error_log("Error fetching complaint by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Post a new complaint.
     *
     * @param int $userID
     * @param string $complaintType
     * @param string $description
     * @param string $visibility
     * @return bool
     */
    public function postComplaint($userID, $complaintType, $description, $visibility) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO complaints 
                (userID, complaintType, description, complaintStatus, postingDate, visibility) 
                VALUES (?, ?, ?, 'Open', CURDATE(), ?)
            ");
            return $stmt->execute([$userID, $complaintType, $description, $visibility]);
        } catch (Exception $e) {
            error_log("Error posting complaint: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update complaint details.
     *
     * @param int $complaintID
     * @param string $complaintType
     * @param string $description
     * @param string $visibility
     * @return bool
     */
    public function updateComplaintDetails($complaintID, $complaintType, $description, $visibility) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                UPDATE complaints 
                SET complaintType = ?, description = ?, visibility = ? 
                WHERE complaintID = ?
            ");
            return $stmt->execute([$complaintType, $description, $visibility, $complaintID]);
        } catch (PDOException $e) {
            error_log("Error updating complaint: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a complaint.
     *
     * @param int $complaintID
     * @return bool
     */
    public function deleteComplaint($complaintID) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM complaints WHERE complaintID = ?");
            return $stmt->execute([$complaintID]);
        } catch (Exception $e) {
            error_log("Error deleting complaint: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update the status of a complaint.
     *
     * @param int $complaintID
     * @param string $status
     * @return bool
     */
    public function updateComplaintStatus($complaintID, $status) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                UPDATE complaints 
                SET complaintStatus = ? 
                WHERE complaintID = ?
            ");
            return $stmt->execute([$status, $complaintID]);
        } catch (Exception $e) {
            error_log("Error updating complaint status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch complaints by a specific user.
     *
     * @param int $userID
     * @return array
     */
    public function getComplaintsByUser($userID) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT * FROM complaints 
                WHERE userID = ? 
                ORDER BY postingDate DESC
            ");
            $stmt->execute([$userID]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching complaints by user: " . $e->getMessage());
            return [];
        }
    }
}
?>