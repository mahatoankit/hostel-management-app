<?php
require_once __DIR__ . '/Database.php';

if (isset($_GET['userID'])) {
    $userID = $_GET['userID'];

    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM guardian WHERE userID = ?");
        $stmt->execute([$userID]);
        $guardian = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($guardian) {
            echo json_encode($guardian);
        } else {
            echo json_encode(['error' => 'Guardian not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
