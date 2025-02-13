<?php
session_start();
require_once __DIR__ . '/../../models/Hosteller.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$hosteller = new Hosteller();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_hosteller'])) {
        $hostellerID = $_POST['hostellerID'];
        $hostellersEmail = $_POST['hostellersEmail'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $phoneNumber = $_POST['phoneNumber'];
        $occupation = $_POST['occupation'];
        $address = $_POST['address'];
        $joinedDate = $_POST['joinedDate'];
        $departureDate = $_POST['departureDate'];
        $dietaryPreference = $_POST['dietaryPreference'];
        $roomNumber = $_POST['roomNumber']; // New field

        $hosteller->addHosteller(
            $hostellerID, $hostellersEmail, $password, $firstName, $lastName,
            $phoneNumber, $occupation, $address, $joinedDate, $departureDate, $dietaryPreference, $roomNumber
        );
    } elseif (isset($_POST['delete_hosteller'])) {
        $userID = $_POST['userID'];
        $hosteller->deleteHosteller($userID);
    }
}

// Get all hostellers
$hostellers = $hosteller->getAllHostellers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hosteller Management</title>
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
            <h1 class="display-5 fw-bold">Hosteller Management</h1>
            <p class="lead text-muted">Manage hostellers in the system.</p>
        </div>

        <!-- Add Hosteller Form -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Add New Hosteller</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Hosteller ID</label>
                            <input type="text" name="hostellerID" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="hostellersEmail" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dietary Preference</label>
                            <select name="dietaryPreference" class="form-select" required>
                                <option value="Vegetarian">Vegetarian</option>
                                <option value="Non-Vegetarian">Non-Vegetarian</option>
                                <option value="Vegan">Vegan</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="firstName" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="lastName" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phoneNumber" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Joined Date</label>
                            <input type="date" name="joinedDate" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Departure Date</label>
                            <input type="date" name="departureDate" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Room Number</label>
                            <input type="text" name="roomNumber" class="form-control" required>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="submit" name="add_hosteller" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Add Hosteller
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Hostellers Table -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">All Hostellers</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Hosteller ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Occupation</th>
                                <th>Dietary Preference</th>
                                <th>Room Number</th> <!-- New column -->
                                <th>Joined Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hostellers as $hosteller): ?>
                                <tr>
                                    <td><?= htmlspecialchars($hosteller['hostellerID']) ?></td>
                                    <td><?= htmlspecialchars($hosteller['firstName'] . ' ' . $hosteller['lastName']) ?></td>
                                    <td><?= htmlspecialchars($hosteller['hostellersEmail']) ?></td>
                                    <td><?= htmlspecialchars($hosteller['phoneNumber']) ?></td>
                                    <td><?= htmlspecialchars($hosteller['occupation']) ?></td>
                                    <td><?= htmlspecialchars($hosteller['dietaryPreference']) ?></td>
                                    <td><?= htmlspecialchars($hosteller['roomNumber']) ?></td> <!-- New column -->
                                    <td><?= date('M d, Y', strtotime($hosteller['joinedDate'])) ?></td>
                                    <td>
                                        <a href="edit_hosteller.php?id=<?= $hosteller['userID'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="userID" value="<?= $hosteller['userID'] ?>">
                                            <button type="submit" name="delete_hosteller" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
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