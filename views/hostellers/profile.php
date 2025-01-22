<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session and validate user
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hosteller') {
    header("Location: ../auth/login.php");
    exit();
}

// Database connection - Adjusted relative path
require_once __DIR__ . '/../../models/Database.php';

try {
    // Fetch user data
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT * FROM hostellers WHERE userID = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found in database.");
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle profile update
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize inputs
        $phone = htmlspecialchars($_POST['phone']);
        $address = htmlspecialchars($_POST['address']);
        $diet = htmlspecialchars($_POST['diet']);

        // Update query
        $stmt = $db->prepare("UPDATE hostellers 
                            SET phoneNumber = ?, address = ?, dietaryPreference = ?
                            WHERE userID = ?");
        $stmt->execute([$phone, $address, $diet, $_SESSION['user_id']]);
        $success = "Profile updated successfully!";

        // Refresh user data
        $stmt = $db->prepare("SELECT * FROM hostellers WHERE userID = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $error = "Error updating profile: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Hostel Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-card {
            max-width: 600px;
            margin: 2rem auto;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .profile-header {
            background: #007bff;
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            text-align: center;
        }
        .profile-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .profile-details {
            padding: 2rem;
        }
    </style>
</head>
<body>
    <?php require "../partials/_nav.php"; ?>

    <div class="container">
        <div class="card profile-card">
            <div class="profile-header">
                <i class="fas fa-user-circle profile-icon"></i>
                <h2><?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?></h2>
                <p class="mb-0"><?= htmlspecialchars($user['hostellersEmail']) ?></p>
            </div>

            <div class="profile-details">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST">
                    <!-- Read-only fields -->
                    <div class="mb-3">
                        <label class="form-label">Hosteller ID</label>
                        <input type="text" class="form-control" 
                               value="<?= htmlspecialchars($user['hostellerID']) ?>" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" 
                               value="<?= htmlspecialchars($user['firstName']) ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" 
                               value="<?= htmlspecialchars($user['lastName']) ?>" readonly>
                    </div>

                    <!-- Editable fields -->
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" 
                               value="<?= htmlspecialchars($user['phoneNumber']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3" required><?= 
                            htmlspecialchars($user['address']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dietary Preference</label>
                        <select name="diet" class="form-select" required>
                            <option value="Vegetarian" <?= $user['dietaryPreference'] === 'Vegetarian' ? 'selected' : '' ?>>Vegetarian</option>
                            <option value="Non-Vegetarian" <?= $user['dietaryPreference'] === 'Non-Vegetarian' ? 'selected' : '' ?>>Non-Vegetarian</option>
                            <option value="Vegan" <?= $user['dietaryPreference'] === 'Vegan' ? 'selected' : '' ?>>Vegan</option>
                            <option value="Others" <?= $user['dietaryPreference'] === 'Others' ? 'selected' : '' ?>>Others</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>