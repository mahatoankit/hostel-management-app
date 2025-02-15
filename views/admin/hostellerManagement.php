<?php
session_start();
require_once __DIR__ . '/../../models/Hosteller.php';
require_once __DIR__ . '/../../models/Room.php'; // Include the Room class

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$hosteller = new Hosteller();
$room = new Room(); // Create an instance of the Room class

// Fetch available rooms for the dropdown
$availableRooms = $room->getRoomsWithAvailableSpace();

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
        $roomNumber = $_POST['roomNumber'];

        $hosteller->addHosteller(
            $hostellerID,
            $hostellersEmail,
            $password,
            $firstName,
            $lastName,
            $phoneNumber,
            $occupation,
            $address,
            $joinedDate,
            $departureDate,
            $dietaryPreference
        );

        $hostellerUserID = $hosteller->getUserIdbyHostellerID($hostellerID)['userID'];

        $room->allocateHosteller($hostellerUserID, $roomNumber, $departureDate); // Allocate the hosteller to the selected room
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
                            <select name="roomNumber" class="form-select" required>
                                <?php foreach ($availableRooms as $room): ?>
                                    <option value="<?= $room['roomNumber'] ?>">
                                        <?= htmlspecialchars($room['roomNumber']) ?> (Available Space: <?= $room['availableSpace'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
<?php
// echo '<pre>';
// var_dump($hostellers);
// echo '</pre>';
?>
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
                                <th>User ID</th>
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
                                    <td><?= htmlspecialchars($hosteller['userID']) ?></td>
                                    <td><?= htmlspecialchars($hosteller['firstName'] . ' ' . $hosteller['lastName']) ?></td>
                                    <td><?= htmlspecialchars($hosteller['hostellersEmail']) ?></td>
                                    <td><?= htmlspecialchars($hosteller['phoneNumber']) ?></td>
                                    <td><?= htmlspecialchars($hosteller['occupation']) ?></td>
                                    <td><?= htmlspecialchars($hosteller['dietaryPreference']) ?></td>
                                    <td><?= htmlspecialchars($hosteller['roomNumber']) ?></td> <!-- New column -->
                                    <td><?= date('M d, Y', strtotime($hosteller['joinedDate'])) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-hosteller-btn">
                                            <i class="bi bi-pencil"></i>
                                        </button>
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
    <!-- Edit Hosteller Modal -->
    <div class="modal fade" id="editHostellerModal" tabindex="-1" aria-labelledby="editHostellerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editHostellerModalLabel">Edit Hosteller</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editHostellerForm" method="POST">
                        <input type="hidden" name="userID" id="editUserID">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Hosteller ID</label>
                                <input type="text" name="hostellerID" id="editHostellerID" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="hostellersEmail" id="editHostellersEmail" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="firstName" id="editFirstName" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="lastName" id="editLastName" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phoneNumber" id="editPhoneNumber" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Occupation</label>
                                <input type="text" name="occupation" id="editOccupation" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" id="editAddress" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Joined Date</label>
                                <input type="date" name="joinedDate" id="editJoinedDate" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Departure Date</label>
                                <input type="date" name="departureDate" id="editDepartureDate" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dietary Preference</label>
                                <select name="dietaryPreference" id="editDietaryPreference" class="form-select" required>
                                    <option value="Vegetarian">Vegetarian</option>
                                    <option value="Non-Vegetarian">Non-Vegetarian</option>
                                    <option value="Vegan">Vegan</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Room Number</label>
                                <select name="roomNumber" id="editRoomNumber" class="form-select" required>
                                    <?php foreach ($availableRooms as $room): ?>
                                        <option value="<?= $room['roomNumber'] ?>">
                                            <?= htmlspecialchars($room['roomNumber']) ?> (Available Space: <?= $room['availableSpace'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="editHostellerForm" name="edit_hosteller" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to all edit buttons
            document.querySelectorAll('.edit-hosteller-btn').forEach(button => {
                button.addEventListener('click', function() {
                    // Fetch hosteller data from the row
                    const row = this.closest('tr');
                    const hostellerID = row.querySelector('td:nth-child(1)').innerText;
                    const name = row.querySelector('td:nth-child(2)').innerText.split(' ');
                    const firstName = name[0];
                    const lastName = name[1];
                    const email = row.querySelector('td:nth-child(3)').innerText;
                    const phone = row.querySelector('td:nth-child(4)').innerText;
                    const occupation = row.querySelector('td:nth-child(5)').innerText;
                    const dietaryPreference = row.querySelector('td:nth-child(6)').innerText;
                    const roomNumber = row.querySelector('td:nth-child(7)').innerText;
                    const joinedDate = row.querySelector('td:nth-child(8)').innerText;
                    const userID = row.querySelector('input[name="userID"]').value;
                    // const address = row.querySelector('input[name="address"]').value;
                    // Populate the modal form fields
                    document.getElementById('editUserID').value = userID;
                    document.getElementById('editHostellerID').value = hostellerID;
                    document.getElementById('editFirstName').value = firstName;
                    document.getElementById('editLastName').value = lastName;
                    document.getElementById('editHostellersEmail').value = email;
                    document.getElementById('editPhoneNumber').value = phone;
                    document.getElementById('editOccupation').value = occupation;
                    document.getElementById('editDietaryPreference').value = dietaryPreference;
                    document.getElementById('editRoomNumber').value = roomNumber;
                    document.getElementById('editJoinedDate').value = new Date(joinedDate).toISOString().split('T')[0];
                    // document.getElementById('editAddress').value = address;

                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('editHostellerModal'));
                    modal.show();
                });
            });
        });
    </script>
</body>

</html>