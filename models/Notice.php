<?php
require_once __DIR__ . '/Database.php'; // Adjust the path as needed

class Notice {
    /**
     * Add a new notice.
     *
     * @param string $title
     * @param string $description
     * @param string $postedBy
     * @return bool
     */
    public function addNotice($title, $description) {
        $sql = "
            INSERT INTO notices (
                title, 
                description, 
                postedDate
            ) VALUES (
                :title, 
                :description, 
                :postedDate
            )
        ";
        $stmt = Database::query($sql);
        Database::bind($stmt, [
            ':title' => $title,
            ':description' => $description,
            ':postedDate' => date('Y-m-d'), // Current date
        ]);

        return Database::execute($stmt);
    }

    /**
     * Get all notices.
     *
     * @return array
     */
    public function getAllNotices() {
        $sql = "SELECT * FROM notices ORDER BY postedDate DESC";
        $stmt = Database::query($sql);
        return Database::fetchAll($stmt);
    }

    /**
     * Delete a notice by ID.
     *
     * @param int $noticeID
     * @return bool
     */
    public function deleteNotice($noticeID) {
        $sql = "DELETE FROM notices WHERE noticeID = :noticeID";
        $stmt = Database::query($sql);
        Database::bind($stmt, [':noticeID' => $noticeID]);
        return Database::execute($stmt);
    }
}