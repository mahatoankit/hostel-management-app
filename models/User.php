class User {
    // ...existing code...

    public static function verifyLogin($email, $password) {
        global $conn;
        $sql = "SELECT * FROM admin WHERE email=? AND password=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // ...existing code...
}
