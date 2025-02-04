<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

// Include the Complaint model to fetch complaint posts
require_once __DIR__ . '/../../models/Complaint.php';

$complaintModel = new Complaint();
$complaints = $complaintModel->getAllComplaints();
?>
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
                        <span class="text-success"> Vote Count: <?php echo $complaint['voteCount']; ?></span>
                        <!-- <span class="text-danger ms-2">↓ <?php echo $complaint['Downvotes']; ?></span> -->
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
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="text-muted">No complaints found.</p>
<?php endif; ?>
