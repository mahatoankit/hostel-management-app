<?php
session_start();
require_once __DIR__ . '/../../models/Notice.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$notice = new Notice();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_notice'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];

        if ($notice->addNotice($title, $description)) {
            $successMessage = "Notice posted successfully!";
        } else {
            $errorMessage = "Failed to post notice.";
        }
    } elseif (isset($_POST['delete_notice'])) {
        $noticeID = $_POST['noticeID'];
        if ($notice->deleteNotice($noticeID)) {
            $successMessage = "Notice deleted successfully!";
        } else {
            $errorMessage = "Failed to delete notice.";
        }
    }
}

// Get all notices
$notices = $notice->getAllNotices();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice Management</title>
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
            <h1 class="display-5 fw-bold">Notice Management</h1>
            <p class="lead text-muted">Post and manage important notices.</p>
        </div>

        <!-- Add Notice Form -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Post New Notice</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" name="add_notice" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Post Notice
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Notices Table -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">All Notices</h5>
            </div>
            <div class="card-body">
                <?php if (isset($successMessage)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
                <?php elseif (isset($errorMessage)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Posted Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notices as $notice): ?>
                                <tr>
                                    <td><?= htmlspecialchars($notice['title']) ?></td>
                                    <td><?= htmlspecialchars($notice['description']) ?></td>
                                    <td><?= date('M d, Y', strtotime($notice['postedDate'])) ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="noticeID" value="<?= $notice['noticeID'] ?>">
                                            <button type="submit" name="delete_notice" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
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