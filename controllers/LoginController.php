<?php
// Start the session
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database configuration
require "../config/db.php";

// Debugging: Print session and POST data
echo "Script started.<br>";
echo "<pre>";
print_r($_SESSION);
print_r($_POST);
echo "</pre>";

// Sanitize and validate user inputs
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];
$userType = filter_var($_POST['user_type'], FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Sanitize user type
$errorMessage = "";

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errorMessage = "Invalid email address";
    $_SESSION['error_message'] = $errorMessage;
    header("Location: ../auth/login.php");
    exit();
}

// Rate limiting to prevent brute-force attacks
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if ($_SESSION['login_attempts'] >= 9999) { // Lowered to 5 attempts for better security
    $errorMessage = "Too many login attempts. Please try again later.";
    $_SESSION['error_message'] = $errorMessage;
    header("Location: ../auth/login.php");
    exit();
}
$_SESSION['login_attempts']++;

// Log the login attempt
error_log("Login attempt: email=$email, userType=$userType");

// Function to authenticate user
function authenticateUser($conn, $email, $password, $table, $emailColumn, $passwordColumn, $redirectPath, $userType) {
    $sql = "SELECT * FROM $table WHERE $emailColumn = ?";
    try {
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Debugging: Print user data
            echo "<pre>";
            print_r($user);
            echo "</pre>";

            // Plain-text password comparison (to be replaced with password_verify() later)
            if ($password == $user[$passwordColumn]) {
                // Set session variables
                $_SESSION['email'] = $email;
                $_SESSION['user_type'] = $userType;

                // Set user_id for both admin and hosteller
                if ($userType === 'hosteller') {
                    $_SESSION['user_id'] = $user['userID']; // Corrected column name
                } else if ($userType === 'admin') {
                    $_SESSION['user_id'] = $user['adminID']; // Adjust if needed
                }

                // Debugging: Print session data
                echo "<pre>";
                print_r($_SESSION);
                echo "</pre>";

                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);

                // Log successful login
                error_log("Login successful for $userType, redirecting to $redirectPath");

                // Redirect to the appropriate dashboard
                header("Location: $redirectPath");
                exit();
            } else {
                error_log("Password verification failed for $userType");
                return "Invalid credentials";
            }
        } else {
            error_log("User not found for $userType");
            return "Invalid credentials";
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        return 'Internal server error';
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
    }
}

// Get the database connection
$conn = Database::getConnection();

// Debugging: Print connection status
echo "Database connection: ";
print_r($conn);

// Authenticate based on user type
if ($userType == 'admin') {
    $errorMessage = authenticateUser($conn, $email, $password, 'admin', 'email', 'password', '../views/admin/dashboard.php', 'admin');
} else if ($userType == 'hosteller') {
    $errorMessage = authenticateUser($conn, $email, $password, 'hostellers', 'hostellersEmail', 'password', '../views/hostellers/hosteller_dashboard.php', 'hosteller');
} else {
    $errorMessage = "Invalid user type";
}

// Close the database connection
$conn->close();

// Handle errors and redirect back to login page
if (!empty($errorMessage)) {
    $_SESSION['error_message'] = $errorMessage;
    header("Location: ../auth/login.php");
    exit();
}
?>