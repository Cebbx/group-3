<?php
header('Content-Type: text/html; charset=utf-8');
echo "<h1>Database Connection Test</h1>";

$connection = getenv('DB_CONNECTION') ?: 'not set';
$host = getenv('DB_HOST') ?: 'not set';
$port = getenv('DB_PORT') ?: 'not set';
$database = getenv('DB_DATABASE') ?: 'not set';
$username = getenv('DB_USERNAME') ?: 'not set';
$password = getenv('DB_PASSWORD') ? '****** (is set)' : 'not set';

echo "<h3>Environment Variables in Render:</h3>";
echo "<ul>";
echo "<li><b>DB_CONNECTION:</b> $connection</li>";
echo "<li><b>DB_HOST:</b> $host</li>";
echo "<li><b>DB_PORT:</b> $port</li>";
echo "<li><b>DB_DATABASE:</b> $database</li>";
echo "<li><b>DB_USERNAME:</b> $username</li>";
echo "<li><b>DB_PASSWORD:</b> $password</li>";
echo "</ul>";

if ($connection === 'not set' || $host === 'not set') {
    echo "<p style='color:red;'><b>Error:</b> Required environment variables are not set in Render. Please check your Environment tab.</p>";
    exit;
}

try {
    echo "<h3>Attempting connection...</h3>";
    $dsn = "pgsql:host=$host;port=$port;dbname=$database";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ];
    
    $pdo = new PDO($dsn, $username, getenv('DB_PASSWORD'), $options);
    echo "<p style='color:green;font-size:20px;'><b>✓ SUCCESS!</b> Successfully connected to the PostgreSQL database.</p>";
} catch (\Throwable $e) {
    echo "<p style='color:red;font-size:20px;'><b>✗ CONNECTION FAILED!</b></p>";
    echo "<pre style='background:#f4f4f4;padding:15px;border:1px solid #ccc;color:red;overflow:auto;'>" . $e->getMessage() . "</pre>";
    
    echo "<h4>Troubleshooting tips:</h4>";
    echo "<ul>";
    echo "<li>Double check if you copied the <b>Internal Hostname</b> instead of the External Hostname.</li>";
    echo "<li>Make sure your <b>DB_PORT</b> is set to <b>5432</b>.</li>";
    echo "<li>Check if the database password was copied correctly with no spaces.</li>";
    echo "</ul>";
}
