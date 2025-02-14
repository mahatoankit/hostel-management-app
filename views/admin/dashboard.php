<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
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
                        <a href="../admin/billingManagement.php" class="btn btn-primary w-100">Manage</a>
                    </div>
                </div>
            </div>

            <!-- Rooms Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Room Management</h5>
                        <p class="card-text">View and manage room allocations.</p>
                        <a href="../admin/roomManagement.php" class="btn btn-primary w-100">Manage</a>
                    </div>
                </div>
            </div>

            <!-- Hosteller Management Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Hosteller Management</h5>
                        <p class="card-text">Add or remove hostel residents.</p>
                        <a href="../admin/hostellerManagement.php" class="btn btn-primary w-100">Manage</a>
                    </div>
                </div>
            </div>

            <!-- Notice Management Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Notice Board</h5>
                        <p class="card-text">Post and manage important notices.</p>
                        <a href="../admin/notice.php" class="btn btn-primary w-100">Manage</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaint Section -->
        <div class="complaint-section mt-5">
            <h3 class="mb-4">Recent Complaints</h3>
            <div class="row">
            <div class="container py-5">
        <h1 class="text-center mb-4">Complaint Management</h1>

        <?php if (!empty($complaints)): ?>
            <?php foreach ($complaints as $complaint): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="card-title"><?php echo htmlspecialchars($complaint['complaintType']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($complaint['description']); ?></p>
                                <p class="text-muted small">
                                    Posted by: <?php echo htmlspecialchars($complaint['firstName'] . ' ' . $complaint['lastName']); ?>
                                    on <?php echo htmlspecialchars($complaint['postingDate']); ?>
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-<?php echo $complaint['complaintStatus'] === 'Resolved' ? 'success' : 'warning'; ?>">
                                    <?php echo htmlspecialchars($complaint['complaintStatus']); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Admin Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <form method="POST" action="../../models/ComplaintStatus.php" class="d-inline">
                                <input type="hidden" name="complaintID" value="<?php echo $complaint['complaintID']; ?>">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="Open" <?php echo $complaint['complaintStatus'] === 'Open' ? 'selected' : ''; ?>>Open</option>
                                    <option value="In Progress" <?php echo $complaint['complaintStatus'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="Resolved" <?php echo $complaint['complaintStatus'] === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                                    <option value="Pending" <?php echo $complaint['complaintStatus'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                </select>
                            </form>

                            <form method="POST" action="../../models/ComplaintVisibility.php" class="d-inline">
                                <input type="hidden" name="complaintID" value="<?php echo $complaint['complaintID']; ?>">
                                <select name="visibility" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="Public" <?php echo $complaint['visibility'] === 'Public' ? 'selected' : ''; ?>>Public</option>
                                    <option value="Private" <?php echo $complaint['visibility'] === 'Private' ? 'selected' : ''; ?>>Private</option>
                                </select>
                            </form>

                            <a href="../../models/DeleteComplaint.php?complaintID=<?php echo $complaint['complaintID']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this complaint?')">
                               Delete
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No complaints found.</p>
        <?php endif; ?>
    </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>