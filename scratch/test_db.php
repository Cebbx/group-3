<?php
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    echo "Database file does not exist at $dbPath\n";
    exit(1);
}

echo "Database file size: " . filesize($dbPath) . " bytes\n";

try {
    $start = microtime(true);
    echo "Opening database...\n";
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_TIMEOUT, 5); // 5 seconds timeout
    
    echo "Querying sqlite_master...\n";
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in database:\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
    echo "Time taken: " . (microtime(true) - $start) . " seconds\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
