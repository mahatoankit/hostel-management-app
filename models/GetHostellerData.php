<?php
require_once __DIR__ . '/Database.php';

if (isset($_GET['userID'])) {
    $userID = $_GET['userID'];

    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM hostellers WHERE userID = ?");
        $stmt->execute([$userID]);
        $hosteller = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($hosteller) {
            echo json_encode($hosteller);
        } else {
            echo json_encode(['error' => 'Hosteller not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>