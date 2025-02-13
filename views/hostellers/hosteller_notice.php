<?php
session_start();
require_once __DIR__ . '/../../models/Notice.php';

// Check if hosteller is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$notice = new Notice();

// Get all notices
$notices = $notice->getAllNotices();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notices</title>
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
            <h1 class="display-5 fw-bold">Notices</h1>
            <p class="lead text-muted">Important notices posted by the admin.</p>
        </div>

        <!-- Notices Table -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">All Notices</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Posted By</th>
                                <th>Posted Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notices as $notice): ?>
                                <tr>
                                    <td><?= htmlspecialchars($notice['title']) ?></td>
                                    <td><?= htmlspecialchars($notice['description']) ?></td>
                                    <td><?= htmlspecialchars($notice['postedBy']) ?></td>
                                    <td><?= date('M d, Y', strtotime($notice['postedDate'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>