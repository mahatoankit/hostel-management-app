<?php
session_start();

// Redirect if the user is not logged in or not a hosteller
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hosteller') {
    header("Location: ../../auth/login.php");
    exit();
}

// Include necessary models
require_once __DIR__ . '/../../models/Complaint.php';
$complaintModel = new Complaint();
$complaints = $complaintModel->getAllComplaints();

// Handle complaint posting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_complaint'])) {
    $complaintType = htmlspecialchars($_POST['complaintType']);
    $description = htmlspecialchars($_POST['description']);
    $visibility = htmlspecialchars($_POST['visibility'] ?? 'Public');  // Default to Private
    $userID = $_SESSION['user_id'];

    if (!empty($complaintType) && !empty($description)) {
        // Validate visibility input
        $allowedVisibilities = ['Private', 'Public'];
        if (!in_array($visibility, $allowedVisibilities)) {
            $visibility = 'Private'; // Fallback to default
        }
        if ($complaintModel->postComplaint($userID, $complaintType, $description, $visibility)) {
            header("Location: hosteller_dashboard.php");
            exit();
        } else {
            $error = "Failed to post complaint. Please try again.";
        }
    } else {
        $error = "Complaint type and description are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hosteller Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .dashboard-card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
            transition: transform 0.2s, box-shadow 0.2s; 
            background: linear-gradient(135deg, #ffffff, #f1f3f5); 
            margin-bottom: 1.5rem; 
        }
        .dashboard-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); 
        }
        .card-title { 
            font-size: 1.5rem; 
            font-weight: 600; 
            color: #343a40; 
        }
        .card-text { 
            color: #6c757d; 
            font-size: 0.95rem; 
        }
        .btn-primary { 
            background-color: #0d6efd; 
            border: none; 
            padding: 0.5rem 1.5rem; 
            font-size: 0.95rem; 
            border-radius: 8px; 
        }
        .btn-primary:hover { 
            background-color: #0b5ed7; 
        }
        .complaint-section { 
            margin-top: 2rem; 
        }
        .complaint-card { 
            margin-bottom: 1rem; 
        }
    </style>
</head>
<body>
    <?php require "../../partials/_nav.php"; ?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>!</h2>
        
        <!-- Dashboard Cards -->
        <div class="row g-4"> <!-- Added g-4 for gap between cards -->
            <!-- Profile Card -->
            <div class="col-md-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Profile</h5>
                        <p class="card-text">View and update your profile information.</p>
                        <a href="profile.php" class="btn btn-primary">Go to Profile</a>
                    </div>
                </div>
            </div>

            <!-- Room Details Card -->
            <div class="col-md-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Room Details</h5>
                        <p class="card-text">Check your room details and roommates.</p>
                        <a href="../rooms/roomDetails.php" class="btn btn-primary">View Room Details</a>
                    </div>
                </div>
            </div>

            <!-- Billing Card -->
            <div class="col-md-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Billing and Payments</h5>
                        <p class="card-text">View and manage your hostel billing details.</p>
                        <a href="../billing/billing.php" class="btn btn-primary">View Billing</a>
                    </div>
                </div>
            </div>

            <!-- Notices Card -->
            <div class="col-md-6">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Notices</h5>
                        <p class="card-text">See important notices posted by the admin.</p>
                        <a href="../hostellers/hosteller_notice.php" class="btn btn-primary">View Notices</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaint Section -->
        <div class="complaint-section mt-5">
            <h3>Complaints</h3>

            <!-- Post Complaint Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Post a Complaint</h5>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <!-- Complaint Type Field -->
                        <div class="mb-3">
                            <label for="complaintType" class="form-label">Complaint Type</label>
                            <input type="text" class="form-control" id="complaintType" name="complaintType" required>
                        </div>

                        <!-- Description Field -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>

                        <!-- Visibility Dropdown -->
                        <div class="mb-3">
                            <label for="visibility" class="form-label">Visibility</label>
                            <select class="form-select" id="visibility" name="visibility" required>
                                <option value="Private">Private (Only visible to admin)</option>
                                <option value="Public" selected>Public (Visible to everyone)</option>
                            </select>
                        </div>

                        <button type="submit" name="post_complaint" class="btn btn-primary">Post Complaint</button>
                    </form>
                </div>
            </div>

            <!-- Complaints List -->
            <?php require "../complaints/complaintPosts.php"; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>