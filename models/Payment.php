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
    public function getPaymentHistory($userID) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT * 
                FROM billing 
                WHERE userID = ? 
                ORDER BY billingDate DESC
            ");
            $stmt->execute([$userID]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching payment history: " . $e->getMessage());
            return [];
        }
    }

    // Add a new billing record
    public function addBillingRecord($userID, $amount, $billingDate, $paymentStatus) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO billing (userID, amount, billingDate, paymentStatus) 
                VALUES (?, ?, ?, ?)
            ");
            return $stmt->execute([$userID, $amount, $billingDate, $paymentStatus]);
        } catch (PDOException $e) {
            error_log("Error adding billing record: " . $e->getMessage());
            return false;
        }
    }

    // Update payment status
    public function updatePaymentStatus($billID, $paymentStatus) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                UPDATE billing 
                SET paymentStatus = ? 
                WHERE billID = ?
            ");
            return $stmt->execute([$paymentStatus, $billID]);
        } catch (PDOException $e) {
            error_log("Error updating payment status: " . $e->getMessage());
            return false;
        }
    }
}
?>