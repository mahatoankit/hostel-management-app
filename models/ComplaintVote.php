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
        
        try {
            $db->beginTransaction();
            
            $stmt = $db->prepare("SELECT voteType FROM complaintVotes 
                                WHERE complaintID = ? AND userID = ?
                                FOR UPDATE");
            $stmt->execute([$this->complaintID, $this->userID]);
            $existingVote = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingVote) {
                if ($existingVote['voteType'] === 'Upvote') {
                    // Remove upvote
                    $db->prepare("DELETE FROM complaintVotes 
                                WHERE complaintID = ? AND userID = ?")
                       ->execute([$this->complaintID, $this->userID]);
                    $db->prepare("UPDATE complaints 
                                SET voteCount = voteCount - 1 
                                WHERE complaintID = ?")
                       ->execute([$this->complaintID]);
                } else {
                    // Change from Downvote to Upvote
                    $db->prepare("UPDATE complaintVotes 
                                SET voteType = 'Upvote' 
                                WHERE complaintID = ? AND userID = ?")
                       ->execute([$this->complaintID, $this->userID]);
                    $db->prepare("UPDATE complaints 
                                SET voteCount = voteCount + 2 
                                WHERE complaintID = ?")
                       ->execute([$this->complaintID]);
                }
            } else {
                // Add new upvote
                $db->prepare("INSERT INTO complaintVotes 
                            (complaintID, userID, voteType) 
                            VALUES (?, ?, 'Upvote')")
                   ->execute([$this->complaintID, $this->userID]);
                $db->prepare("UPDATE complaints 
                            SET voteCount = voteCount + 1 
                            WHERE complaintID = ?")
                   ->execute([$this->complaintID]);
            }
            
            $db->commit();
            return true;
            
        } catch (PDOException $e) {
            $db->rollBack();
            if ($e->errorInfo[1] == 1062) {
                error_log("Duplicate vote attempted");
                return false;
            }
            error_log("Upvote error: " . $e->getMessage());
            return false;
        }
    }

    // Downvote functionality
    public function downVote() {
        $db = Database::getConnection();
        
        try {
            $db->beginTransaction();
            
            $stmt = $db->prepare("SELECT voteType FROM complaintVotes 
                                WHERE complaintID = ? AND userID = ?
                                FOR UPDATE");
            $stmt->execute([$this->complaintID, $this->userID]);
            $existingVote = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingVote) {
                if ($existingVote['voteType'] === 'Downvote') {
                    // Remove downvote
                    $db->prepare("DELETE FROM complaintVotes 
                                WHERE complaintID = ? AND userID = ?")
                       ->execute([$this->complaintID, $this->userID]);
                    $db->prepare("UPDATE complaints 
                                SET voteCount = voteCount + 1 
                                WHERE complaintID = ?")
                       ->execute([$this->complaintID]);
                } else {
                    // Change from Upvote to Downvote
                    $db->prepare("UPDATE complaintVotes 
                                SET voteType = 'Downvote' 
                                WHERE complaintID = ? AND userID = ?")
                       ->execute([$this->complaintID, $this->userID]);
                    $db->prepare("UPDATE complaints 
                                SET voteCount = voteCount - 2 
                                WHERE complaintID = ?")
                       ->execute([$this->complaintID]);
                }
            } else {
                // Add new downvote
                $db->prepare("INSERT INTO complaintVotes 
                            (complaintID, userID, voteType) 
                            VALUES (?, ?, 'Downvote')")
                   ->execute([$this->complaintID, $this->userID]);
                $db->prepare("UPDATE complaints 
                            SET voteCount = voteCount - 1 
                            WHERE complaintID = ?")
                   ->execute([$this->complaintID]);
            }
            
            $db->commit();
            return true;
            
        } catch (PDOException $e) {
            $db->rollBack();
            if ($e->errorInfo[1] == 1062) {
                error_log("Duplicate vote attempted");
                return false;
            }
            error_log("Downvote error: " . $e->getMessage());
            return false;
        }
    }

    public static function getTotalVotesForComplaint($complaintID) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT voteCount FROM complaints WHERE complaintID = ?");
            $stmt->execute([$complaintID]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['voteCount'] : 0;
        } catch (PDOException $e) {
            error_log("Get votes error: " . $e->getMessage());
            return 0;
        }
    }
    public function getUserVotes($userID) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT complaintID, voteType 
                FROM complaintVotes 
                WHERE userID = ?
            ");
            $stmt->execute([$userID]);
            
            $votes = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $votes[$row['complaintID']] = $row['voteType'];
            }
            return $votes;
            
        } catch (PDOException $e) {
            error_log("Error fetching user votes: " . $e->getMessage());
            return [];
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
        $success = false;
        if ($action === 'upvote') {
            $success = $vote->upVote();
        } elseif ($action === 'downvote') {
            $success = $vote->downVote();
        }
        
        if ($success) {
            header("Location: ../views/hostellers/hosteller_dashboard.php");
        } else {
            header("Location: ../views/hostellers/hosteller_dashboard.php?error=vote_failed");
        }
        exit();
    } catch (Exception $e) {
        die("Error processing vote: " . $e->getMessage());
    }
} else {
    die("Invalid request parameters.");
}