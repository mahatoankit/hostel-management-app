<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Debugging: Print session data
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

// Redirect if the user is not logged in or not a hosteller
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hosteller') {
    header("Location: ../../auth/login.php");
    exit();
}

// Include necessary models
require_once __DIR__ . '/../../models/Complaint.php';
$complaintModel = new Complaint();

// Fetch all complaints
$complaints = $complaintModel->getAllComplaints();

// Handle form submission for posting a new complaint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_complaint'])) {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $hostellerId = $_SESSION['user_id']; // Use the logged-in user's ID from the session

    if (!empty($title) && !empty($description)) {
        if ($complaintModel->postComplaint($hostellerId, $title, $description)) {
            header("Location: hosteller_dashboard.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Failed to post complaint. Please try again.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Title and description are required.</div>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hosteller Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        .card-text {
            color: #6c757d;
        }
        .complaint-section {
            margin-top: 2rem;
        }
        .complaint-card {
            margin-bottom: 1rem;
        }
        .vote-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <?php require "../../partials/_nav.php"; ?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>!</h2>
        <div class="row g-4">
            <!-- Profile Card -->
            <div class="col-md-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Profile</h5>
                        <p class="card-text">View and update your profile information.</p>
                        <a href="#" class="btn btn-primary">Go to Profile</a>
                    </div>
                </div>
            </div>

            <!-- Room Details Card -->
            <div class="col-md-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Room Details</h5>
                        <p class="card-text">Check your room details and roommates.</p>
                        <a href="#" class="btn btn-primary">View Room Details</a>
                    </div>
                </div>
            </div>

            <!-- Notices Card -->
            <div class="col-md-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Notices</h5>
                        <p class="card-text">Read the latest notices and announcements.</p>
                        <a href="#" class="btn btn-primary">View Notices</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaint Section -->
        <div class="complaint-section mt-5">
            <h3>Complaints</h3>
            <!-- Form to Post a New Complaint -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Post a Complaint</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="post_complaint" class="btn btn-primary">Post Complaint</button>
                    </form>
                </div>
            </div>

            <!-- List of Complaints -->
            <?php if (!empty($complaints)): ?>
                <?php foreach ($complaints as $complaint): ?>
                    <div class="card complaint-card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($complaint['complaintType']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($complaint['description']); ?></p>
                            <div class="vote-buttons">
                                <a href="complaintVote.php?action=upvote&complaint_id=<?php echo $complaint['id']; ?>&user_id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-success btn-sm">Upvote (<?php echo $complaint['Upvotes']; ?>)</a>
                                <a href="complaintVote.php?action=downvote&complaint_id=<?php echo $complaint['id']; ?>&user_id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-danger btn-sm">Downvote (<?php echo $complaint['Downvotes']; ?>)</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No complaints found.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>