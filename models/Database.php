<?php
class Database {
    private static $connection = null;

    public static function getConnection() {
        if (self::$connection === null) {
            $host = "localhost";
            $username = "root";
            $password = ""; // Add your MySQL password here if applicable
            $database = "ankitMahato24128422"; // Correct database name

            try {
                // Create PDO connection
                self::$connection = new PDO(
                    "mysql:host=$host;dbname=$database",
                    $username,
                    $password
                );

                // Set PDO to throw exceptions on errors
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
?>