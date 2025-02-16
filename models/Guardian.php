<?php
require_once __DIR__ . '/Database.php';

class Guardian {
    /**
     * Add a new guardian.
     *
     * @param int $userID
     * @param string $guardianFirstName
     * @param string $guardianLastName
     * @param string $phoneNumber
     * @param string $relationship
     * @return bool
     */
    public function addGuardian($userID, $guardianFirstName, $guardianLastName, $phoneNumber, $relationship = null) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO guardians 
                (userID, guardianFirstName, guardianLastName, phoneNumber, relationship) 
                VALUES (?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$userID, $guardianFirstName, $guardianLastName, $phoneNumber, $relationship]);
        } catch (Exception $e) {
            error_log("Error adding guardian: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch a guardian by their ID.
     *
     * @param int $guardianID
     * @return array|null
     */
    public function getGuardianById($guardianID) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM guardians WHERE guardianID = ?");
            $stmt->execute([$guardianID]);
            return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch single row
        } catch (Exception $e) {
            error_log("Error fetching guardian by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch all guardians for a specific user.
     *
     * @param int $userID
     * @return array
     */
    public function getGuardiansByUser($userID) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM guardians WHERE userID = ?");
            $stmt->execute([$userID]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows
        } catch (Exception $e) {
            error_log("Error fetching guardians by user: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update guardian details.
     *
     * @param int $guardianID
     * @param string $guardianFirstName
     * @param string $guardianLastName
     * @param string $phoneNumber
     * @param string $relationship
     * @return bool
     */
    public function updateGuardian($guardianID, $guardianFirstName, $guardianLastName, $phoneNumber, $relationship = null) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                UPDATE guardians 
                SET guardianFirstName = ?, guardianLastName = ?, phoneNumber = ?, relationship = ? 
                WHERE guardianID = ?
            ");
            return $stmt->execute([$guardianFirstName, $guardianLastName, $phoneNumber, $relationship, $guardianID]);
        } catch (Exception $e) {
            error_log("Error updating guardian: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a guardian by their ID.
     *
     * @param int $guardianID
     * @return bool
     */
    public function deleteGuardian($guardianID) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM guardians WHERE guardianID = ?");
            return $stmt->execute([$guardianID]);
        } catch (Exception $e) {
            error_log("Error deleting guardian: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch all guardians.
     *
     * @return array
     */
    public function getAllGuardians() {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM guardians");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows
        } catch (Exception $e) {
            error_log("Error fetching all guardians: " . $e->getMessage());
            return [];
        }
    }
    
}
?>