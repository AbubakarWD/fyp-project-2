<?php
/**
 * BloodLife — Database Initialization CLI Script
 * Executes schema creation DDL and seeds initial demo data into MySQL.
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';

echo "========================================================\n";
echo "BloodLife — Database Setup & Seeding Script\n";
echo "========================================================\n";

try {
    $pdo = Database::getInstance();
    echo "[+] Database connection successful to DB: " . DB_NAME . "\n";

    // 1. Execute Schema DDL
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("schema.sql not found at: " . $schemaFile);
    }

    echo "[*] Importing schema.sql DDL...\n";
    $schemaSql = file_get_contents($schemaFile);
    $pdo->exec($schemaSql);
    echo "[✓] Schema tables created successfully!\n";

    // 2. Execute Seed Data SQL
    $seedFile = __DIR__ . '/seed.sql';
    if (!file_exists($seedFile)) {
        throw new Exception("seed.sql not found at: " . $seedFile);
    }

    echo "[*] Importing seed.sql demo data...\n";
    $seedSql = file_get_contents($seedFile);
    $pdo->exec($seedSql);
    echo "[✓] Seed data inserted successfully!\n";

    echo "========================================================\n";
    echo "SUCCESS: BloodLife Database is initialized and ready!\n";
    echo "========================================================\n";

} catch (Exception $e) {
    echo "[!] ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
