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

// Database connection
require_once __DIR__ . '/../../models/Database.php';

try {
    $db = Database::getConnection();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle password change
$passwordError = $passwordSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        // Fetch current password hash from the database
        $stmt = $db->prepare("SELECT password FROM hostellers WHERE hostellerID = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $storedPassword = $stmt->fetchColumn();

        // Verify current password
        if (!password_verify($currentPassword, $storedPassword)) {
            $passwordError = "Current password is incorrect.";
        } elseif ($newPassword !== $confirmPassword) {
            $passwordError = "New passwords do not match.";
        } else {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password in the database
            $stmt = $db->prepare("UPDATE hostellers SET password = ? WHERE hostellerID = ?");
            $stmt->execute([$hashedPassword, $_SESSION['user_id']]);
            $passwordSuccess = "Password updated successfully!";
        }

    } catch (PDOException $e) {
        $passwordError = "Error updating password: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Hostel Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .password-card {
            max-width: 600px;
            margin: 2rem auto;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .password-header {
            background: #007bff;
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            text-align: center;
        }
        .password-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .password-details {
            padding: 2rem;
        }
    </style>
</head>
<body>
    <?php require "../../partials/_nav.php"; ?>

    <div class="container">
        <div class="card password-card">
            <div class="password-header">
                <i class="fas fa-key password-icon"></i>
                <h2>Change Password</h2>
            </div>

            <div class="password-details">
                <?php if ($passwordError): ?>
                    <div class="alert alert-danger"><?= $passwordError ?></div>
                <?php endif; ?>
                <?php if ($passwordSuccess): ?>
                    <div class="alert alert-success"><?= $passwordSuccess ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Change Password
                        </button>
                    </div>
                </form>

                <!-- Back to Profile Button -->
                <div class="d-grid gap-2 mt-3">
                    <a href="profile.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>