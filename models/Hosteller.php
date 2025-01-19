<?php
require_once __DIR__ . '/Database.php';

class Hosteller {
    // Authenticate a hosteller
    public function authenticate($email, $password) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM hostellers WHERE hostellersEmail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $hosteller = $result->fetch_assoc();
            if ($password == $hosteller['password']) { // Plain-text comparison for simplicity
                return $hosteller;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

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