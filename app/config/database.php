<?php
/**
 * BloodLife — Database Singleton Connection Class
 * Provides a secure, single-instance PDO connection with prepared statement enforcement.
 */

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct() {}

    /**
     * Private clone method to prevent cloning.
     */
    private function __clone() {}

    /**
     * Get the PDO database connection instance.
     *
     * @return PDO
     * @throws PDOException
     */
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_PORT,
                DB_NAME,
                DB_CHARSET
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Log database connection error securely without exposing credentials
                error_log("BloodLife DB Connection Error: " . $e->getMessage());
                throw new Exception("Database connection failed. Please ensure MySQL service is running and credentials are valid.");
            }
        }

        return self::$instance;
    }
}
