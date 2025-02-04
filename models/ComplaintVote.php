<?php
require_once __DIR__ . '/Database.php';

class ComplaintVote {
    private $voteID;
    private $complaintID;
    private $userID;
    private $voteDate;
    private $totalVotes;

    public function __construct($voteID = null, $complaintID = null, $userID = null, $voteDate = null, $totalVotes = 0) {
        $this->voteID = $voteID;
        $this->complaintID = $complaintID;
        $this->userID = $userID;
        $this->voteDate = $voteDate;
        $this->totalVotes = $totalVotes;
    }

    // Getters
    public function getId() { return $this->voteID; }
    public function getComplaintId() { return $this->complaintID; }
    public function getUserId() { return $this->userID; }
    public function getVoteDate() { return $this->voteDate; }
    public function getTotalVotes() { return $this->totalVotes; }

    // Upvote functionality
    public function upVote() {
        $db = Database::getConnection();
        
        // Check existing vote
        $stmt = $db->prepare("SELECT * FROM complaintVotes WHERE complaintID = ? AND userID = ?");
        $stmt->execute([$this->complaintID, $this->userID]);
        $existingVote = $stmt->fetch();

        try {
            if ($existingVote) {
                if ($existingVote['voteType'] === 'upvote') {
                    return false;
                } else {
                    // Update to upvote
                    $stmt = $db->prepare("UPDATE complaintVotes SET voteType = 'upvote' WHERE complaintID = ? AND userID = ?");
                    $stmt->execute([$this->complaintID, $this->userID]);

                    // Update complaint counts
                    $stmt = $db->prepare("UPDATE complaints SET Upvotes = Upvotes + 1, Downvotes = Downvotes - 1 WHERE id = ?");
                    $stmt->execute([$this->complaintID]);
                }
            } else {
                // New upvote
                $stmt = $db->prepare("INSERT INTO complaintVotes (complaintID, userID, voteType) VALUES (?, ?, 'upvote')");
                $stmt->execute([$this->complaintID, $this->userID]);

                // Update complaint counts
                $stmt = $db->prepare("UPDATE complaints SET Upvotes = Upvotes + 1 WHERE id = ?");
                $stmt->execute([$this->complaintID]);
            }
            return true;
        } catch (PDOException $e) {
            error_log("Upvote error: " . $e->getMessage());
            return false;
        }
    }

    // Downvote functionality
    public function downVote() {
        $db = Database::getConnection();

        // Check existing vote
        $stmt = $db->prepare("SELECT * FROM complaintVotes WHERE complaintID = ? AND userID = ?");
        $stmt->execute([$this->complaintID, $this->userID]);
        $existingVote = $stmt->fetch();

        try {
            if ($existingVote) {
                if ($existingVote['voteType'] === 'downvote') {
                    return false;
                } else {
                    // Update to downvote
                    $stmt = $db->prepare("UPDATE complaintVotes SET voteType = 'downvote' WHERE complaintID = ? AND userID = ?");
                    $stmt->execute([$this->complaintID, $this->userID]);

                    // Update complaint counts
                    $stmt = $db->prepare("UPDATE complaints SET Upvotes = Upvotes - 1, Downvotes = Downvotes + 1 WHERE id = ?");
                    $stmt->execute([$this->complaintID]);
                }
            } else {
                // New downvote
                $stmt = $db->prepare("INSERT INTO complaintVotes (complaintID, userID, voteType) VALUES (?, ?, 'downvote')");
                $stmt->execute([$this->complaintID, $this->userID]);

                // Update complaint counts
                $stmt = $db->prepare("UPDATE complaints SET Downvotes = Downvotes + 1 WHERE id = ?");
                $stmt->execute([$this->complaintID]);
            }
            return true;
        } catch (PDOException $e) {
            error_log("Downvote error: " . $e->getMessage());
            return false;
        }
    }

    public static function getTotalVotesForComplaint($complaintID) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT Upvotes, Downvotes FROM complaints WHERE id = ?");
            $stmt->execute([$complaintID]);
            return $stmt->fetch() ?: ['Upvotes' => 0, 'Downvotes' => 0];
        } catch (PDOException $e) {
            error_log("Get votes error: " . $e->getMessage());
            return ['Upvotes' => 0, 'Downvotes' => 0];
        }
    }
}

// Handle voting request
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hosteller') {
    header("Location: ../../auth/login.php");
    exit();
}

if (isset($_GET['action'], $_GET['complaint_id'], $_GET['user_id'])) {
    $action = $_GET['action'];
    $complaintId = (int)$_GET['complaint_id'];
    $userId = (int)$_GET['user_id'];

    $vote = new ComplaintVote(null, $complaintId, $userId);
    
    try {
        if ($action === 'upvote') {
            $vote->upVote();
        } elseif ($action === 'downvote') {
            $vote->downVote();
        }
        header("Location: ../views/hostellers/hosteller_dashboard.php");
        exit();
    } catch (Exception $e) {
        die("Error processing vote: " . $e->getMessage());
    }
} else {
    die("Invalid request parameters.");
}
?>