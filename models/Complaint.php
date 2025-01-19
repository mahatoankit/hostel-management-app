<?php
require_once __DIR__ . '/Database.php';

class Complaint {
    // Fetch all complaints
    public function getAllComplaints() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM complaints");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Post a new complaint
    public function postComplaint($hostellerId, $title, $description) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO complaints (hostellerId, title, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $hostellerId, $title, $description);
        return $stmt->execute();
    }
}
?>