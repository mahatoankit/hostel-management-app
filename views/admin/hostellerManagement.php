<?php
session_start();
require_once __DIR__ . '/../../models/Hosteller.php';
require_once __DIR__ . '/../../models/Room.php'; // Include the Room class
require_once __DIR__ . '/../../models/Guardian.php'; // Include the Guardian class
require_once __DIR__ . '/../../utils/csrf_token.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$hosteller = new Hosteller();
$room = new Room(); // Create an instance of the Room class
$guardian = new Guardian(); // Create an instance of the Guardian class

// Fetch available rooms for the dropdown
$availableRooms = $room->getRoomsWithAvailableSpace();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        header("Location: hostellerManagement.php?error=Invalid request");
        exit();
    }

    if (isset($_POST['add_hosteller'])) {
        // Validate and sanitize input
        $hostellerID = filter_input(INPUT_POST, 'hostellerID', FILTER_SANITIZE_STRING);
        $hostellersEmail = filter_input(INPUT_POST, 'hostellersEmail', FILTER_VALIDATE_EMAIL);
        $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
        $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
        $phoneNumber = filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_STRING);
        $occupation = filter_input(INPUT_POST, 'occupation', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $joinedDate = filter_input(INPUT_POST, 'joinedDate', FILTER_SANITIZE_STRING);
        $departureDate = filter_input(INPUT_POST, 'departureDate', FILTER_SANITIZE_STRING);
        $dietaryPreference = filter_input(INPUT_POST, 'dietaryPreference', FILTER_SANITIZE_STRING);
        $roomNumber = filter_input(INPUT_POST, 'roomNumber', FILTER_VALIDATE_INT);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        if (!$hostellersEmail || !$hostellerID || !$firstName || !$lastName || !$roomNumber) {
            $error = "Please fill in all required fields with valid data.";
        } else {
            // Add hosteller with validated data
            $result = $hosteller->addHosteller(
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

            if ($result) {
                $hostellerUserID = $hosteller->getUserIdbyHostellerID($hostellerID)['userID'];

                // Validate guardian data if provided
                if (!empty($_POST['guardianFirstName']) && !empty($_POST['guardianLastName'])) {
                    $guardianFirstName = filter_input(INPUT_POST, 'guardianFirstName', FILTER_SANITIZE_STRING);
                    $guardianLastName = filter_input(INPUT_POST, 'guardianLastName', FILTER_SANITIZE_STRING);
                    $guardianPhoneNumber = filter_input(INPUT_POST, 'guardianPhoneNumber', FILTER_SANITIZE_STRING);
                    $relationship = filter_input(INPUT_POST, 'relationship', FILTER_SANITIZE_STRING);

                    if ($guardianFirstName && $guardianLastName) {
                        $guardian->addGuardian(
                            $hostellerUserID,
                            $guardianFirstName,
                            $guardianLastName,
                            $guardianPhoneNumber,
                            $relationship
                        );
                    }
                }

                // Allocate room
                $room->allocateHosteller($hostellerUserID, $roomNumber, $departureDate);
                header("Location: hostellerManagement.php?success=Hosteller added successfully");
                exit();
            }
        }
    } elseif (isset($_POST['edit_hosteller'])) {
        // Similar validation for edit operation
        $userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT);
        $hostellerID = filter_input(INPUT_POST, 'hostellerID', FILTER_SANITIZE_STRING);
        $hostellersEmail = filter_input(INPUT_POST, 'hostellersEmail', FILTER_VALIDATE_EMAIL);
        $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
        $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
        $phoneNumber = filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_STRING);
        $occupation = filter_input(INPUT_POST, 'occupation', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $joinedDate = filter_input(INPUT_POST, 'joinedDate', FILTER_SANITIZE_STRING);
        $departureDate = filter_input(INPUT_POST, 'departureDate', FILTER_SANITIZE_STRING);
        $dietaryPreference = filter_input(INPUT_POST, 'dietaryPreference', FILTER_SANITIZE_STRING);
        $roomNumber = filter_input(INPUT_POST, 'roomNumber', FILTER_VALIDATE_INT);

        if (!$userID || !$hostellersEmail || !$hostellerID) {
            $error = "Invalid data provided for editing.";
        } else {
            // Process edit with validated data
            $result = $hosteller->updateHosteller(
                $userID,
                $hostellerID,
                $hostellersEmail,
                $firstName,
                $lastName,
                $phoneNumber,
                $occupation,
                $address,
                $joinedDate,
                $departureDate,
                $dietaryPreference
            );

            if ($result) {
                // Update guardian details (if provided)
                if (!empty($_POST['guardianFirstName']) && !empty($_POST['guardianLastName'])) {
                    $guardianFirstName = filter_input(INPUT_POST, 'guardianFirstName', FILTER_SANITIZE_STRING);
                    $guardianLastName = filter_input(INPUT_POST, 'guardianLastName', FILTER_SANITIZE_STRING);
                    $guardianPhoneNumber = filter_input(INPUT_POST, 'guardianPhoneNumber', FILTER_SANITIZE_STRING);
                    $relationship = filter_input(INPUT_POST, 'relationship', FILTER_SANITIZE_STRING);

                    // Check if guardian already exists
                    $existingGuardian = $guardian->getGuardianByUser($userID);
                    if ($existingGuardian) {
                        // Update existing guardian
                        $guardian->updateGuardian(
                            $userID, // Changed from $existingGuardian['guardianID']
                            $guardianFirstName,
                            $guardianLastName,
                            $guardianPhoneNumber,
                            $relationship
                        );
                    } else {
                        // Add new guardian
                        $guardian->addGuardian(
                            $userID,
                            $guardianFirstName,
                            $guardianLastName,
                            $guardianPhoneNumber,
                            $relationship
                        );
                    }
                }
            }
        }
    } elseif (isset($_POST['delete_hosteller'])) {
        $userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT);

        if (!$userID) {
            $error = "Invalid user ID for deletion.";
        } else {
            if ($hosteller->deleteHosteller($userID)) {
                header("Location: hostellerManagement.php?success=Hosteller deleted successfully");
                exit();
            }
        }
    }
}

// Get all hostellers
$hostellers = $hosteller->getAllHostellers();

// Generate CSRF token for forms
$csrf_token = generateCSRFToken();
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
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div class="row g-3">
                        <!-- Hosteller Details -->
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

                        <!-- Guardian Details -->
                        <div class="col-md-12">
                            <h5 class="mt-4">Guardian Details</h5>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Guardian First Name</label>
                            <input type="text" name="guardianFirstName" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Guardian Last Name</label>
                            <input type="text" name="guardianLastName" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Guardian Phone Number</label>
                            <input type="text" name="guardianPhoneNumber" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Relationship</label>
                            <input type="text" name="relationship" class="form-control">
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
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Occupation</th>
                                <th>Dietary Preference</th>
                                <th>Room Number</th>
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
                                    <td><?= htmlspecialchars($hosteller['roomNumber']) ?></td>
                                    <td><?= date('M d, Y', strtotime($hosteller['joinedDate'])) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-hosteller-btn" data-user-id="<?= $hosteller['userID'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
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
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="userID" id="editUserID">
                        <div class="row g-3">
                            <!-- Hosteller Details -->
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

                            <!-- Guardian Details -->
                            <div class="col-md-12">
                                <h5 class="mt-4">Guardian Details</h5>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Guardian First Name</label>
                                <input type="text" name="guardianFirstName" id="editGuardianFirstName" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Guardian Last Name</label>
                                <input type="text" name="guardianLastName" id="editGuardianLastName" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Guardian Phone Number</label>
                                <input type="text" name="guardianPhoneNumber" id="editGuardianPhoneNumber" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Relationship</label>
                                <input type="text" name="relationship" id="editRelationship" class="form-control">
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.edit-hosteller-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userID = this.getAttribute('data-user-id');
                    console.log('Fetching data for userID:', userID);

                    // Fetch hosteller data
                    fetch(`../../models/GetHostellerData.php?userID=${userID}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Received hosteller data:', data);
                            if (data && !data.error) {
                                // Populate hosteller fields
                                Object.keys(data).forEach(key => {
                                    const element = document.getElementById('edit' + key.charAt(0).toUpperCase() + key.slice(1));
                                    if (element) {
                                        element.value = data[key] || '';
                                    }
                                });

                                // Fetch guardian data
                                return fetch(`../../models/GetGuardianData.php?userID=${userID}`);
                            }
                        })
                        .then(response => response.json())
                        .then(guardianData => {
                            console.log('Received guardian data:', guardianData);
                            if (guardianData && !guardianData.error) {
                                // Populate guardian fields
                                document.getElementById('editGuardianFirstName').value = guardianData.guardianFirstName || '';
                                document.getElementById('editGuardianLastName').value = guardianData.guardianLastName || '';
                                document.getElementById('editGuardianPhoneNumber').value = guardianData.phoneNumber || '';
                                document.getElementById('editRelationship').value = guardianData.relationship || '';
                            }

                            // Show the modal after all data is populated
                            const modal = new bootstrap.Modal(document.getElementById('editHostellerModal'));
                            modal.show();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error fetching data. Please try again.');
                        });
                });
            });
        });
    </script>
</body>
<footer>
    <?php require "../../partials/_footer.php"; ?>
</footer>

</html>