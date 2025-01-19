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
    public function getId() {
        return $this->voteID;
    }

    public function getComplaintId() {
        return $this->complaintID;
    }

    public function getUserId() {
        return $this->userID;
    }

    public function getVoteDate() {
        return $this->voteDate;
    }

    public function getTotalVotes() {
        return $this->totalVotes;
    }

    // Upvote functionality
    public function upVote() {
        $db = Database::getConnection();
        
        // Check if the user has already voted
        $stmt = $db->prepare("SELECT * FROM complaint_votes WHERE complaintID = ? AND userID = ?");
        $stmt->execute([$this->complaintID, $this->userID]);
        $existingVote = $stmt->fetch();

        if ($existingVote) {
            if ($existingVote['voteType'] === 'upvote') {
                // User already upvoted, do nothing
                return false;
            } else {
                // User previously downvoted, change to upvote
                $stmt = $db->prepare("UPDATE complaint_votes SET voteType = 'upvote' WHERE complaintID = ? AND userID = ?");
                $stmt->execute([$this->complaintID, $this->userID]);

                // Update the total votes in the complaints table
                $stmt = $db->prepare("UPDATE complaints SET Upvotes = Upvotes + 1, Downvotes = Downvotes - 1 WHERE id = ?");
                $stmt->execute([$this->complaintID]);
                return true;
            }
        } else {
            // User hasn't voted yet, add an upvote
            $stmt = $db->prepare("INSERT INTO complaint_votes (complaintID, userID, voteType) VALUES (?, ?, 'upvote')");
            $stmt->execute([$this->complaintID, $this->userID]);

            // Update the total votes in the complaints table
            $stmt = $db->prepare("UPDATE complaints SET Upvotes = Upvotes + 1 WHERE id = ?");
            $stmt->execute([$this->complaintID]);
            return true;
        }
    }

    // Downvote functionality
    public function downVote() {
        $db = Database::getConnection();

        // Check if the user has already voted
        $stmt = $db->prepare("SELECT * FROM complaint_votes WHERE complaintID = ? AND userID = ?");
        $stmt->execute([$this->complaintID, $this->userID]);
        $existingVote = $stmt->fetch();

        if ($existingVote) {
            if ($existingVote['voteType'] === 'downvote') {
                // User already downvoted, do nothing
                return false;
            } else {
                // User previously upvoted, change to downvote
                $stmt = $db->prepare("UPDATE complaint_votes SET voteType = 'downvote' WHERE complaintID = ? AND userID = ?");
                $stmt->execute([$this->complaintID, $this->userID]);

                // Update the total votes in the complaints table
                $stmt = $db->prepare("UPDATE complaints SET Upvotes = Upvotes - 1, Downvotes = Downvotes + 1 WHERE id = ?");
                $stmt->execute([$this->complaintID]);
                return true;
            }
        } else {
            // User hasn't voted yet, add a downvote
            $stmt = $db->prepare("INSERT INTO complaint_votes (complaintID, userID, voteType) VALUES (?, ?, 'downvote')");
            $stmt->execute([$this->complaintID, $this->userID]);

            // Update the total votes in the complaints table
            $stmt = $db->prepare("UPDATE complaints SET Downvotes = Downvotes + 1 WHERE id = ?");
            $stmt->execute([$this->complaintID]);
            return true;
        }
    }

    // Fetch total votes for a complaint
    public static function getTotalVotesForComplaint($complaintID) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT Upvotes, Downvotes FROM complaints WHERE id = ?");
        $stmt->execute([$complaintID]);
        $result = $stmt->fetch();
        return $result ? $result : ['Upvotes' => 0, 'Downvotes' => 0];
    }
}
?>