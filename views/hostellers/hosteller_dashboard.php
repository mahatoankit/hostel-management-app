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
        if ($complaintModel->postComplaint($userID[-1], $complaintType, $description, $visibility)) {
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
        .dashboard-card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: transform 0.2s; }
        .dashboard-card:hover { transform: translateY(-5px); }
        .card-title { font-size: 1.25rem; font-weight: 600; }
        .card-text { color: #6c757d; }
        .complaint-section { margin-top: 2rem; }
        .complaint-card { margin-bottom: 1rem; }
        .vote-buttons { display: flex; gap: 10px; }
        .vote-counts { font-weight: 500; margin-right: 15px; }
        .complaint-card .card-body { padding: 1.25rem; }
        .complaint-card .small { font-size: 0.875em; }
    </style>
</head>
<body>
    <?php require "../../partials/_nav.php"; ?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>!</h2>
        
        <!-- Dashboard Cards -->
        <div class="row g-4">
            <!-- Profile Card -->
            <div class="col-md-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Profile</h5>
                        <p class="card-text">View and update your profile information.</p>
                        <a href="profile.php" class="btn btn-primary">Go to Profile</a>
                    </div>
                </div>
            </div>

            <!-- Room Details Card -->
            <div class="col-md-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Room Details</h5>
                        <p class="card-text">Check your room details and roommates.</p>
                        <a href="room_details.php" class="btn btn-primary">View Room Details</a>
                    </div>
                </div>
            </div>

            <!-- Notices Card -->
            <div class="col-md-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Notices</h5>
                        <p class="card-text">Read the latest notices and announcements.</p>
                        <a href="notices.php" class="btn btn-primary">View Notices</a>
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
                    <option value="Private" >Private (Only visible to admin)</option>
                    <option value="Public" selected>Public (Visible to everyone)</option>
                </select>
            </div>

            <button type="submit" name="post_complaint" class="btn btn-primary">Post Complaint</button>
        </form>
    </div>
</div>

            <!-- Complaints List -->
            <?php if (!empty($complaints)): ?>
                <?php foreach ($complaints as $complaint): ?>
                    <div class="card complaint-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="card-title"><?php echo htmlspecialchars($complaint['complaintType']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($complaint['description']); ?></p>
                                </div>
                                <div class="text-muted small">
                                    Posted by: <?php echo htmlspecialchars($complaint['firstName'].' '.$complaint['lastName']); ?>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="vote-counts">
                                    <span class="text-success">↑ <?php echo $complaint['Upvotes']; ?></span>
                                    <span class="text-danger ms-2">↓ <?php echo $complaint['Downvotes']; ?></span>
                                </div>
                                <div class="vote-buttons">
                                    <a href="../../models/complaintVote.php?action=upvote&complaint_id=<?php echo $complaint['id']; ?>&user_id=<?php echo $_SESSION['user_id']; ?>" 
                                       class="btn btn-success btn-sm">
                                       Upvote
                                    </a>
                                    <a href="../../models/complaintVote.php?action=downvote&complaint_id=<?php echo $complaint['id']; ?>&user_id=<?php echo $_SESSION['user_id']; ?>" 
                                       class="btn btn-danger btn-sm">
                                       Downvote
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No complaints found.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>