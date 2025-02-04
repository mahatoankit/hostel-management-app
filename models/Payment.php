<?php
require_once __DIR__ . '/Database.php';

class Payment {
    // Get billing details for a user
    public function getBillingDetails($userID) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT b.*, u.firstName, u.lastName 
                FROM billing b
                JOIN users u ON b.userID = u.userID
                WHERE b.userID = ?
            ");
            $stmt->execute([$userID]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching billing details: " . $e->getMessage());
            return [];
        }
    }

    // Get payment history for a user

}
?>