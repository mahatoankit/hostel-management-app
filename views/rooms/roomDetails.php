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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            font-family: 'Poppins', sans-serif;
        }
        .container {
            margin-top: 4rem;
        }
        .room-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
        }
        .allocation-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
            background-color: #ffffff;
            transition: transform 0.3s ease-in-out;
        }
        .allocation-card:hover {
            transform: translateY(-5px);
        }
        .roommate-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            background-color: #ffffff;
            transition: transform 0.3s ease-in-out;
        }
        .roommate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #343a40;
        }
        .card-text {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .fa-icon {
            color: #007bff;
        }
        .alert-custom {
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            font-size: 1rem;
            font-weight: 500;
        }
        .btn-custom {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: transform 0.3s ease-in-out;
        }
        .btn-custom:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .divider {
            border-top: 1px solid #dee2e6;
            margin: 2rem 0;
        }
    </style>
</head>
<body>
    <?php require "../../partials/_nav.php"; ?>
    <div class="container mt-5">
        <?php if (!$roomDetails): ?>
            <div class="alert alert-warning alert-custom">
                You are not currently assigned to any room.
            </div>
        <?php else: ?>
            <!-- Room Allocation Card -->
            <div class="card allocation-card">
                <div class="room-header">
                    Room <?= htmlspecialchars($roomDetails['roomNumber']) ?>
                </div>
                <div class="card-body">
                    <h3 class="mb-4">Your Room Details</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Total Capacity:</strong> <?= htmlspecialchars($roomDetails['seaterNumber']) ?> seater(s)</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Allocated Seaters:</strong> <?= htmlspecialchars($allocatedSeaters) ?></p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Unallocated Seaters:</strong> <?= htmlspecialchars($unallocatedSeaters) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="divider"></div>

            <!-- Roommate Details Section -->
            <div class="card roommate-details-card">
                <div class="card-body">
                    <h4 class="mb-4">Roommate Details</h4>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        <?php if (empty($roommates)): ?>
                            <div class="col">
                                <div class="alert alert-info alert-custom">
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
                                                <i class="fas fa-envelope fa-icon me-2"></i>
                                                <?= htmlspecialchars($roommate['hostellersEmail']) ?>
                                            </p>
                                            <p class="card-text">
                                                <i class="fas fa-phone fa-icon me-2"></i>
                                                <?= htmlspecialchars($roommate['phoneNumber']) ?>
                                            </p>
                                            <p class="card-text">
                                                <i class="fas fa-home fa-icon me-2"></i>
                                                Room <?= htmlspecialchars($roomDetails['roomNumber']) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>`
</body>
</html>