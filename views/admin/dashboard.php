<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../models/Complaint.php';
$complaintModel = new Complaint();
$complaints = $complaintModel->getAllComplaints();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .dashboard-card { 
            border: none; 
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
            transition: transform 0.2s; 
            height: 100%;
        }
        .dashboard-card:hover { 
            transform: translateY(-5px); 
        }
        .card-title { 
            font-size: 1.25rem; 
            font-weight: 600; 
            margin-bottom: 1rem;
        }
        .card-text { 
            color: #6c757d; 
            min-height: 60px;
        }
        .complaint-section { 
            margin-top: 2rem; 
        }
        .complaint-card { 
            margin-bottom: 1.5rem; 
        }
    </style>
</head>
<body>
    <?php require "../../partials/_nav.php"; ?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Admin Dashboard</h2>
        
        <!-- Management Cards -->
        <div class="row g-4">
            <!-- Payment Billing Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Payment Billing</h5>
                        <p class="card-text">Manage hostel fee payments and generate bills.</p>
                        <a href="payment_billing.php" class="btn btn-primary w-100">Manage</a>
                    </div>
                </div>
            </div>

            <!-- Rooms Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Room Management</h5>
                        <p class="card-text">View and manage room allocations.</p>
                        <a href="rooms.php" class="btn btn-primary w-100">Manage</a>
                    </div>
                </div>
            </div>

            <!-- Hosteller Management Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Hosteller Management</h5>
                        <p class="card-text">Add or remove hostel residents.</p>
                        <a href="hosteller_management.php" class="btn btn-primary w-100">Manage</a>
                    </div>
                </div>
            </div>

            <!-- Notice Management Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Notice Board</h5>
                        <p class="card-text">Post and manage important notices.</p>
                        <a href="notice_posting.php" class="btn btn-primary w-100">Manage</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaint Section -->
        <div class="complaint-section mt-5">
            <h3 class="mb-4">Recent Complaints</h3>
            <div class="row">
                <?php require "../complaints/complaintPosts.php"?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>