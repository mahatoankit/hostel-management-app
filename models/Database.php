<?php
class Database {
    private static $connection = null;

    public static function getConnection() {
        if (self::$connection === null) {
            // Database credentials
            $host = "localhost";
            $username = "root";
            $password = ""; // Add your MySQL password here if applicable
            $database = "ankitMahato24128422"; // Correct database name

            // Create connection
            self::$connection = new mysqli($host, $username, $password, $database);

            // Check connection
            if (self::$connection->connect_error) {
                die("Connection failed: " . self::$connection->connect_error);
            }
        }
        return self::$connection;
    }
}

// Test the connection
$conn = Database::getConnection();
?>