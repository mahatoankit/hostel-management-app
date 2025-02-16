<?php
session_start();
require_once __DIR__ . '/../../models/Payment.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$payment = new Payment();
$userID = $_SESSION['user_id'];
$billingDetails = $payment->getBillingDetails($userID);
$paymentHistory = $payment->getPaymentHistory($userID);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Information</title>
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
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
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
            <h1 class="display-5 fw-bold">Billing Information</h1>
            <p class="lead text-muted">View and manage your hostel billing details.</p>
        </div>

        <!-- Current Billing Details -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Current Billing Details</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($billingDetails)): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Amount Due:</strong> ₹<?= number_format($billingDetails['amount'], 2) ?></p>
                            <p class="mb-2">
                                <strong>Payment Status:</strong>
                                <span class="payment-status <?= $billingDetails['paymentStatus'] ?>">
                                    <?= $billingDetails['paymentStatus'] ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Billing Date:</strong> <?= date('M d, Y', strtotime($billingDetails['billingDate'])) ?></p>
                            <p class="mb-0"><strong>Name:</strong> <?= htmlspecialchars($billingDetails['firstName'] . ' ' . $billingDetails['lastName']) ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No billing details found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment History -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Payment History</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($paymentHistory)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Billing Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($paymentHistory as $payment): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($payment['billingDate'])) ?></td>
                                        <td>₹<?= number_format($payment['amount'], 2) ?></td>
                                        <td>
                                            <span class="payment-status <?= $payment['paymentStatus'] ?>">
                                                <?= $payment['paymentStatus'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No payment history found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<footer>
    <?php require "../../partials/_footer.php"; ?>
</footer>

</html>