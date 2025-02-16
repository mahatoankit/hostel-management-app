<?php
session_start();
require_once __DIR__ . '/../../models/Payment.php';
require_once __DIR__ . '/../../models/Hosteller.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$payment = new Payment();
$hosteller = new Hosteller();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_billing'])) {
        $userID = $_POST['userID'];
        $amount = $_POST['amount'];
        $billingDate = $_POST['billingDate'];
        $paymentStatus = $_POST['paymentStatus'];
        $payment->addBillingRecord($userID, $amount, $billingDate, $paymentStatus);
    } elseif (isset($_POST['update_status'])) {
        $billID = $_POST['billID'];
        $paymentStatus = $_POST['paymentStatus'];
        $payment->updatePaymentStatus($billID, $paymentStatus);
    }
}

// Get all billing records
$billingRecords = $payment->getAllBillingRecords();

// Get all hostellers for dropdown
$hostellers = $hosteller->getAllHostellers();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Billing Management</title>
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

        .payment-status {
            font-size: 0.9rem;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .payment-status.Paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .payment-status.Unpaid {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .payment-status.Pending {
            background-color: #fef3c7;
            color: #92400e;
        }
    </style>
</head>

<body>
    <?php require "../../partials/_nav.php"; ?>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">Billing Management</h1>
            <p class="lead text-muted">Manage billing and payments for all hostellers.</p>
        </div>

        <!-- Add Billing Form -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Add New Billing Record</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Hosteller</label>
                            <select name="userID" class="form-select" required>
                                <option value="">Select Hosteller</option>
                                <?php foreach ($hostellers as $hosteller): ?>
                                    <option value="<?= $hosteller['userID'] ?>">
                                        <?= htmlspecialchars($hosteller['firstName'] . ' ' . $hosteller['lastName']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Amount</label>
                            <input type="number" name="amount" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Billing Date</label>
                            <input type="date" name="billingDate" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="paymentStatus" class="form-select" required>
                                <option value="Paid">Paid</option>
                                <option value="Unpaid">Unpaid</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" name="add_billing" class="btn btn-primary w-100">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Billing Records Table -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">All Billing Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Bill ID</th>
                                <th>Hosteller</th>
                                <th>Amount</th>
                                <th>Billing Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($billingRecords as $record): ?>
                                <tr>
                                    <td><?= $record['billID'] ?></td>
                                    <td><?= htmlspecialchars($record['firstName'] . ' ' . $record['lastName']) ?></td>
                                    <td>₹<?= number_format($record['amount'], 2) ?></td>
                                    <td><?= date('M d, Y', strtotime($record['billingDate'])) ?></td>
                                    <td>
                                        <span class="payment-status <?= $record['paymentStatus'] ?>">
                                            <?= $record['paymentStatus'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="billID" value="<?= $record['billID'] ?>">
                                            <select name="paymentStatus" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="Paid" <?= $record['paymentStatus'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                                                <option value="Unpaid" <?= $record['paymentStatus'] === 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
                                                <option value="Pending" <?= $record['paymentStatus'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            </select>
                                            <input type="hidden" name="update_status">
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
<footer>
    <?php require "../../partials/_footer.php"; ?>
</footer>

</html>