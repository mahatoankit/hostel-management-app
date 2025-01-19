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
    <title>Hostel Management Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .dashboard {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 15px;
            padding: 20px;
            width: 300px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-10px);
        }
        .card h2 {
            font-size: 22px;
            margin-bottom: 10px;
        }
        .card p {
            font-size: 16px;
            color: #555;
        }
        .card button {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .card button:hover {
            background: #0056b3;
        }
        .complaint-section {
            margin-top: 2rem;
            text-align: center;
        }
        .complaint-card {
            margin-bottom: 1rem;
            display: inline-block;
            width: 300px;
        }
        .vote-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    </style>
</head>
<body>
  <?php require "../../partials/_nav.php"; ?>
    <div class="dashboard">
        <!-- Payment Billing Card -->
        <div class="card">
            <h2>Payment Billing</h2>
            <p>Manage hostel fee payments and generate bills.</p>
            <button onclick="openPaymentBilling()">Manage</button>
        </div>

        <!-- Rooms Card -->
        <div class="card">
            <h2>Rooms</h2>
            <p>View and manage room allocations.</p>
            <button onclick="openRooms()">Manage</button>
        </div>

        <!-- Add/Remove Hosteller Card -->
        <div class="card">
            <h2>Add/Remove Hosteller</h2>
            <p>Add new hostellers or remove existing ones.</p>
            <button onclick="openHostellerManagement()">Manage</button>
        </div>

        <!-- Notice Posting Card -->
        <div class="card">
            <h2>Notice Posting</h2>
            <p>Post important notices for hostellers.</p>
            <button onclick="openNoticePosting()">Manage</button>
        </div>
    </div>

    <!-- Complaint Section -->
    <div class="complaint-section">
        <h3>Complaints</h3>
        <?php if (!empty($complaints)): ?>
            <?php foreach ($complaints as $complaint): ?>
                <div class="card complaint-card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($complaint['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($complaint['description']); ?></p>
                        <div class="vote-buttons">
                            <span class="btn btn-success btn-sm">Upvotes: <?php echo $complaint['Upvotes']; ?></span>
                            <span class="btn btn-danger btn-sm">Downvotes: <?php echo $complaint['Downvotes']; ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No complaints found.</p>
        <?php endif; ?>
    </div>

    <script>
        function openPaymentBilling() {
            alert("Redirecting to Payment Billing Management...");
            // window.location.href = "payment_billing.php";
        }

        function openRooms() {
            alert("Redirecting to Rooms Management...");
            // window.location.href = "rooms.php";
        }

        function openHostellerManagement() {
            alert("Redirecting to Hosteller Management...");
            // window.location.href = "hosteller_management.php";
        }

        function openNoticePosting() {
            alert("Redirecting to Notice Posting...");
            // window.location.href = "notice_posting.php";
        }
    </script>
</body>
</html>