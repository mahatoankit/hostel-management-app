<?php
require_once __DIR__ . '/Database.php';

class Complaint {
    // Fetch all complaints
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

    // Post a new complaint
    public function postComplaint($userID, $complaintType, $description, $visibility) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO complaints 
                                (userID, complaintType, description, complaintStatus, postingDate, visibility) 
                                VALUES (?, ?, ?, 'Open', CURDATE(), ?)");
            return $stmt->execute([$userID, $complaintType, $description, $visibility]);
        } catch (Exception $e) {
            error_log("Error posting complaint: " . $e->getMessage());
            return false;
        }
    }

    public function updateComplaintDetails($complaintID, $complaintType, $description, $visibility) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE complaints 
                                SET complaintType = ?, description = ?, visibility = ? 
                                WHERE complaintID = ?");
            return $stmt->execute([$complaintType, $description, $visibility, $complaintID]);
        } catch (Exception $e) {
            error_log("Error updating complaint: " . $e->getMessage());
            return false;
        }
    }
}
?>