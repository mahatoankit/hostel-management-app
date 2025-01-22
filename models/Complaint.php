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
    public function postComplaint($hostellerId, $title, $description) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO complaints (hostellerId, title, description) VALUES (?, ?, ?)");
            return $stmt->execute([$hostellerId, $title, $description]);
        } catch (Exception $e) {
            error_log("Error posting complaint: " . $e->getMessage());
            return false;
        }
    }
}
?>