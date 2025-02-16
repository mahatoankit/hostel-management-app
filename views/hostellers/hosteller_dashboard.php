<?php
session_start();
require_once __DIR__ . '/../../utils/csrf_token.php';

// Redirect if the user is not logged in or not a hosteller
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hosteller') {
    header("Location: ../../auth/login.php");
    exit();
}

// Include necessary models and functions
require_once __DIR__ . '/../../models/Complaint.php';
$complaintModel = new Complaint();
$complaints = $complaintModel->getAllComplaints();

// Get success message from URL parameter
$successMessage = htmlspecialchars($_GET['success'] ?? '');

// Handle complaint posting
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        header("Location: hosteller_dashboard.php?error=Invalid request");
        exit();
    }

    if (isset($_POST['post_complaint'])) {
        $complaintType = trim(htmlspecialchars($_POST['complaintType']));
        $description = trim(htmlspecialchars($_POST['description']));
        $visibility = htmlspecialchars($_POST['visibility'] ?? 'Public');  // Default to Public
        $userID = $_SESSION['user_id'];

        if (strlen($complaintType) < 1 || strlen($description) < 1) {
            $error = "Complaint type and description are required.";
        } else {
            // Validate visibility input
            $allowedVisibilities = ['Private', 'Public'];
            $visibility = in_array($visibility, $allowedVisibilities) ? $visibility : 'Public';

            if ($complaintModel->postComplaint($userID, $complaintType, $description, $visibility)) {
                header("Location: hosteller_dashboard.php?success=Complaint posted successfully!");
                exit();
            } else {
                $error = "Failed to post complaint. Please try again.";
            }
        }
    } elseif (isset($_POST['update_complaint'])) {
        $complaintID = htmlspecialchars($_POST['complaintID']);
        $complaintType = htmlspecialchars($_POST['complaintType']);
        $description = htmlspecialchars($_POST['description']);
        $visibility = htmlspecialchars($_POST['visibility']);

        if (!empty($complaintID) && !empty($complaintType) && !empty($description)) {
            // Validate visibility input
            $allowedVisibilities = ['Private', 'Public'];
            if (!in_array($visibility, $allowedVisibilities)) {
                $visibility = 'Public'; // Fallback to default
            }
            if ($complaintModel->updateComplaintDetails($complaintID, $complaintType, $description, $visibility)) {
                header("Location: hosteller_dashboard.php?success=Complaint updated successfully!");
                exit();
            } else {
                $error = "Failed to update complaint. Please try again.";
            }
        } else {
            $error = "All fields are required.";
        }
    } elseif (isset($_POST['delete_complaint'])) {
        $complaintID = htmlspecialchars($_POST['complaintID']);

        if (!empty($complaintID)) {
            if ($complaintModel->deleteComplaint($complaintID)) {
                header("Location: hosteller_dashboard.php?success=Complaint deleted successfully!");
                exit();
            } else {
                $error = "Failed to delete complaint. Please try again.";
            }
        } else {
            $error = "Complaint ID is required.";
        }
    }
}

// Add CSRF token to forms
$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hosteller Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

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

        <!-- Success Message -->
        <?php if ($successMessage): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $successMessage ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Dashboard Cards -->
        <div class="row g-4">
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
        <div class="container py-5">
            <h1 class="text-center mb-4">Complaints</h1>

            <!-- Add Complaint Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="hosteller_dashboard.php">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <div class="mb-3">
                            <label for="complaintType" class="form-label">Complaint Type</label>
                            <input type="text" name="complaintType" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="visibility" class="form-label">Visibility</label>
                            <select name="visibility" class="form-select" required>
                                <option value="Public">Public</option>
                                <option value="Private">Private</option>
                            </select>
                        </div>
                        <button type="submit" name="post_complaint" class="btn btn-primary">Submit Complaint</button>
                    </form>
                </div>
            </div>

            <!-- Display Complaints -->
            <?php if (!empty($complaints)): ?>
                <?php foreach ($complaints as $complaint): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="card-title"><?php echo htmlspecialchars($complaint['complaintType']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($complaint['description']); ?></p>
                                    <p class="text-muted small">
                                        Posted by:
                                        <?php
                                        if ($complaint['visibility'] === 'Private') {
                                            echo "Anonymous";
                                        } else {
                                            echo htmlspecialchars($complaint['firstName'] . ' ' . $complaint['lastName']);
                                        }
                                        ?>
                                        on <?php echo htmlspecialchars($complaint['postingDate']); ?>
                                    </p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-<?php echo $complaint['complaintStatus'] === 'Resolved' ? 'success' : 'warning'; ?>">
                                        <?php echo htmlspecialchars($complaint['complaintStatus']); ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Voting System -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="vote-counts">
                                    <span class="text-success">Vote Count: <?php echo $complaint['voteCount']; ?></span>
                                </div>
                                <div class="vote-buttons">
                                    <a href="../../models/ComplaintVote.php?action=upvote&complaint_id=<?php echo $complaint['complaintID']; ?>&user_id=<?php echo $_SESSION['user_id']; ?>"
                                        class="btn btn-success btn-sm">
                                        Upvote
                                    </a>
                                    <a href="../../models/ComplaintVote.php?action=downvote&complaint_id=<?php echo $complaint['complaintID']; ?>&user_id=<?php echo $_SESSION['user_id']; ?>"
                                        class="btn btn-danger btn-sm">
                                        Downvote
                                    </a>
                                </div>
                            </div>

                            <!-- Edit and Delete Buttons (Only for the author) -->
                            <?php if ($complaint['userID'] === $_SESSION['user_id']): ?>
                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <!-- Edit Button (Triggers Modal) -->
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editComplaintModal<?php echo $complaint['complaintID']; ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>

                                    <!-- Delete Button -->
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="complaintID" value="<?php echo $complaint['complaintID']; ?>">
                                        <input type="hidden" name="delete_complaint" value="1">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this complaint?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>

                                <!-- Edit Complaint Modal -->
                                <div class="modal fade" id="editComplaintModal<?php echo $complaint['complaintID']; ?>" tabindex="-1" aria-labelledby="editComplaintModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editComplaintModalLabel">Edit Complaint</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <input type="hidden" name="update_complaint" value="1">
                                                    <input type="hidden" name="complaintID" value="<?php echo $complaint['complaintID']; ?>">
                                                    <div class="mb-3">
                                                        <label for="complaintType" class="form-label">Complaint Title</label>
                                                        <input type="text" name="complaintType" class="form-control" value="<?php echo htmlspecialchars($complaint['complaintType']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Description</label>
                                                        <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($complaint['description']); ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="visibility" class="form-label">Visibility</label>
                                                        <select name="visibility" class="form-select" required>
                                                            <option value="Public" <?php echo $complaint['visibility'] === 'Public' ? 'selected' : ''; ?>>Public</option>
                                                            <option value="Private" <?php echo $complaint['visibility'] === 'Private' ? 'selected' : ''; ?>>Private</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Update Complaint</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Be the first to post a complaint!</p>
            <?php endif; ?>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
<footer>
    <?php require "../../partials/_footer.php"; ?>
</footer>

</html>