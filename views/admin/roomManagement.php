<?php
session_start();
require_once __DIR__ . '/../../models/Room.php';
require_once __DIR__ . '/../../models/Hosteller.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$room = new Room();
$hosteller = new Hosteller();

// Handle form submissions for re-allocation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reallocate_hosteller'])) {
        $userID = $_POST['userID'];
        $newRoomNumber = $_POST['newRoomNumber'];
        $room->reallocateHosteller($userID, $newRoomNumber);
    }
}

// Get all rooms and their allocations
$rooms = $room->getAllRoomsWithAllocations();
$hostellers = $hosteller->getAllHostellers();
$availableRooms = $room->getRoomsWithAvailableSpace(); // Fetch rooms with available space
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <?php require "../../partials/_nav.php"; ?>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">Room Management</h1>
            <p class="lead text-muted">Manage room allocations for hostellers.</p>
        </div>

        <!-- Rooms and Allocations Table -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Room Allocations</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Room Number</th>
                                <th>Capacity</th>
                                <th>Hostellers Allocated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td><?= htmlspecialchars($room['roomNumber']) ?></td>
                                    <td><?= htmlspecialchars($room['seaterNumber']) ?></td>
                                    <td>
                                        <?php if (!empty($room['allocations'])): ?>
                                            <ul>
                                                <?php foreach ($room['allocations'] as $allocation): ?>
                                                    <li>
                                                        <?= htmlspecialchars($allocation['firstName'] . ' ' . $allocation['lastName']) ?>
                                                        (<?= htmlspecialchars($allocation['hostellersEmail']) ?>)
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            No hostellers allocated.
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reallocateModal<?= $room['roomNumber'] ?>">
                                            <i class="bi bi-arrow-repeat"></i> Re-allocate
                                        </button>
                                    </td>
                                </tr>

                                <!-- Re-allocate Modal -->
                                <div class="modal fade" id="reallocateModal<?= $room['roomNumber'] ?>" tabindex="-1" aria-labelledby="reallocateModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="reallocateModalLabel">Re-allocate Hostellers</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST">
                                                    <div class="mb-3">
                                                        <label class="form-label">Select Hosteller</label>
                                                        <select name="userID" class="form-select" required>
                                                            <?php foreach ($room['allocations'] as $allocation): ?>
                                                                <option value="<?= $allocation['userID'] ?>">
                                                                    <?= htmlspecialchars($allocation['firstName'] . ' ' . $allocation['lastName']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">New Room Number</label>
                                                        <select name="newRoomNumber" class="form-select" required>
                                                            <?php foreach ($availableRooms as $availableRoom): ?>
                                                                <?php if ($availableRoom['roomNumber'] != $room['roomNumber']): ?>
                                                                    <option value="<?= $availableRoom['roomNumber'] ?>">
                                                                        <?= htmlspecialchars($availableRoom['roomNumber']) ?> (Available Space: <?= $availableRoom['availableSpace'] ?>)
                                                                    </option>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <button type="submit" name="reallocate_hosteller" class="btn btn-primary">
                                                            <i class="bi bi-arrow-repeat"></i> Re-allocate
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>