<?php
class Database {
    private static $connection = null;

    /**
     * Get the database connection (singleton pattern).
     *
     * @return PDO
     */
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

    /**
     * Prepare a SQL query.
     *
     * @param string $sql
     * @return PDOStatement
     */
    public static function query($sql) {
        return self::getConnection()->prepare($sql);
    }

    /**
     * Bind parameters to a prepared statement.
     *
     * @param PDOStatement $stmt
     * @param array $params
     */
    public static function bind($stmt, $params) {
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, self::getParamType($value));
        }
    }

    /**
     * Execute a prepared statement.
     *
     * @param PDOStatement $stmt
     * @return bool
     */
    public static function execute($stmt) {
        return $stmt->execute();
    }

    /**
     * Fetch all rows from a query result.
     *
     * @param PDOStatement $stmt
     * @return array
     */
    public static function fetchAll($stmt) {
        self::execute($stmt);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a single row from a query result.
     *
     * @param PDOStatement $stmt
     * @return array|null
     */
    public static function fetch($stmt) {
        self::execute($stmt);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get the number of rows affected by the last query.
     *
     * @param PDOStatement $stmt
     * @return int
     */
    public static function rowCount($stmt) {
        return $stmt->rowCount();
    }

    /**
     * Determine the PDO parameter type based on the value.
     *
     * @param mixed $value
     * @return int
     */
    private static function getParamType($value) {
        switch (true) {
            case is_int($value):
                return PDO::PARAM_INT;
            case is_bool($value):
                return PDO::PARAM_BOOL;
            case is_null($value):
                return PDO::PARAM_NULL;
            default:
                return PDO::PARAM_STR;
        }
    }
}