<?php
session_start();

// Redirect if not logged in as hosteller
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hosteller') {
    header("Location: ../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../models/Database.php';
require_once __DIR__ . '/../../models/Hosteller.php';

$db = Database::getConnection();
$hostellerModel = new Hosteller($db);

// Get current hosteller's room details
$currentHostellerId = $_SESSION['user_id'];
$roomDetails = $hostellerModel->getCurrentRoomDetails($currentHostellerId);
$roommates = [];

if ($roomDetails) {
    // Get all roommates excluding current user
    $roommates = $hostellerModel->getRoommates($roomDetails['roomNumber'], $currentHostellerId);

    // Calculate allocated and unallocated seaters
    $allocatedSeaters = count($roommates) + 1; // Include the current user
    $unallocatedSeaters = $roomDetails['seaterNumber'] - $allocatedSeaters;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .roommate-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .roommate-card:hover {
            transform: translateY(-5px);
        }
        .room-header {
            background: #007bff;
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1.5rem;
        }
        .allocation-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .roommate-details-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <?php require "../../partials/_nav.php"; ?>
    <h1>Debugger</h1>
    <?php
    echo "<pre>"; // Use <pre> for better readability
    print_r($roommates);
    echo "</pre>";
    ?>
    <div class="container mt-5">
        <?php if (!$roomDetails): ?>
            <div class="alert alert-warning">
                You are not currently assigned to any room.
            </div>
        <?php else: ?>
            <!-- Room Allocation Card -->
            <div class="card allocation-card">
                <h3 class="mb-3">You are allocated to Room <?= htmlspecialchars($roomDetails['roomNumber']) ?></h3>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-0"><strong>Total Capacity:</strong> <?= htmlspecialchars($roomDetails['seaterNumber']) ?> seater(s)</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-0"><strong>Allocated Seaters:</strong> <?= htmlspecialchars($allocatedSeaters) ?></p>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <p class="mb-0"><strong>Unallocated Seaters:</strong> <?= htmlspecialchars($unallocatedSeaters) ?></p>
                    </div>
                </div>
            </div>

            <!-- Roommate Details Section -->
            <div class="card roommate-details-card">
                <h4 class="mb-4">Roommate Details</h4>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php if (empty($roommates)): ?>
                        <div class="col">
                            <div class="alert alert-info">
                                No other roommates currently assigned to this room.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($roommates as $roommate): ?>
                            <div class="col">
                                <div class="card roommate-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <?= htmlspecialchars($roommate['firstName'] . ' ' . $roommate['lastName']) ?>
                                        </h5>
                                        <p class="card-text">
                                            <i class="fas fa-envelope me-2"></i>
                                            <?= htmlspecialchars($roommate['email']) ?>
                                        </p>
                                        <p class="card-text">
                                            <i class="fas fa-phone me-2"></i>
                                            <?= htmlspecialchars($roommate['phoneNumber']) ?>
                                        </p>
                                        <p class="card-text">
                                            <i class="fas fa-home me-2"></i>
                                            Room <?= htmlspecialchars($roomDetails['roomNumber']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>