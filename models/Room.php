<?php
require_once __DIR__ . '/Database.php'; // Adjust the path as needed

class Room {
    /**
     * Fetch all rooms with their current allocations.
     *
     * @return array
     */
    public function getAllRoomsWithAllocations() {
        $sql = "
            SELECT 
                r.roomNumber, 
                r.seaterNumber, 
                h.userID, 
                h.firstName, 
                h.lastName, 
                h.hostellersEmail
            FROM 
                rooms r
            LEFT JOIN 
                roomAllocation ra ON r.roomNumber = ra.roomNumber
            LEFT JOIN 
                hostellers h ON ra.userID = h.userID
            ORDER BY 
                r.roomNumber ASC
        ";

        $stmt = Database::query($sql);
        $results = Database::fetchAll($stmt);

        // Organize data by room number
        $rooms = [];
        foreach ($results as $row) {
            $roomNumber = $row['roomNumber'];
            if (!isset($rooms[$roomNumber])) {
                $rooms[$roomNumber] = [
                    'roomNumber' => $roomNumber,
                    'seaterNumber' => $row['seaterNumber'],
                    'allocations' => []
                ];
            }
            if ($row['userID'] !== null) {
                $rooms[$roomNumber]['allocations'][] = [
                    'userID' => $row['userID'],
                    'firstName' => $row['firstName'],
                    'lastName' => $row['lastName'],
                    'hostellersEmail' => $row['hostellersEmail']
                ];
            }
        }

        return array_values($rooms);
    }

    /**
     * Re-allocate a hosteller to a new room.
     *
     * @param int $userID
     * @param int $newRoomNumber
     * @return bool
     * @throws Exception If the new room is at full capacity
     */
    public function reallocateHosteller($userID, $newRoomNumber) {
        // Check if the new room has available space
        $currentAllocations = $this->getCurrentAllocations($newRoomNumber);
        $roomCapacity = $this->getRoomCapacity($newRoomNumber);

        if ($currentAllocations >= $roomCapacity) {
            throw new Exception("Room $newRoomNumber is already at full capacity.");
        }

        // Update the room allocation
        $sql = "
            UPDATE 
                roomAllocation 
            SET 
                roomNumber = :roomNumber 
            WHERE 
                userID = :userID
        ";
        $stmt = Database::query($sql);
        Database::bind($stmt, [
            ':roomNumber' => $newRoomNumber,
            ':userID' => $userID
        ]);

        return Database::execute($stmt);
    }

    /**
     * Get the current number of allocations for a room.
     *
     * @param int $roomNumber
     * @return int
     */
    private function getCurrentAllocations($roomNumber) {
        $sql = "
            SELECT 
                COUNT(*) as count 
            FROM 
                roomAllocation 
            WHERE 
                roomNumber = :roomNumber
        ";
        $stmt = Database::query($sql);
        Database::bind($stmt, [':roomNumber' => $roomNumber]);
        $result = Database::fetch($stmt);
        return (int) $result['count'];
    }

    /**
     * Get the capacity (seaterNumber) of a room.
     *
     * @param int $roomNumber
     * @return int
     */
    private function getRoomCapacity($roomNumber) {
        $sql = "
            SELECT 
                seaterNumber 
            FROM 
                rooms 
            WHERE 
                roomNumber = :roomNumber
        ";
        $stmt = Database::query($sql);
        Database::bind($stmt, [':roomNumber' => $roomNumber]);
        $result = Database::fetch($stmt);
        return (int) $result['seaterNumber'];
    }

    /**
     * Get all rooms with available space.
     *
     * @return array
     */
    public function getRoomsWithAvailableSpace() {
        $sql = "
            SELECT 
                r.roomNumber, 
                r.seaterNumber, 
                (r.seaterNumber - IFNULL(ra.allocationCount, 0)) as availableSpace
            FROM 
                rooms r
            LEFT JOIN 
                (SELECT roomNumber, COUNT(*) as allocationCount 
                 FROM roomAllocation 
                 GROUP BY roomNumber) ra 
            ON r.roomNumber = ra.roomNumber
            HAVING 
                availableSpace > 0
        ";
        $stmt = Database::query($sql);
        return Database::fetchAll($stmt);
    }
}