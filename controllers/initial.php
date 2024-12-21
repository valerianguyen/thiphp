<?php

require_once __DIR__ . '/../vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); 
$dotenv->load();





$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];

$sqlFile = 'sieuthi_db.sql';
define('BASE_URL', '/sieuthi/');
ob_start();

if (!file_exists('verified.txt')) {
    $conn = new mysqli($host, $user, $password);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Drop database if exists
    $conn->query("DROP DATABASE IF EXISTS $dbname");

    // Create database
    if ($conn->query("CREATE DATABASE $dbname") === TRUE) {
        // Select the database
        $conn->select_db($dbname);

        // Read and execute SQL file
        if (file_exists($sqlFile)) {
       
            $sql = file_get_contents($sqlFile);
            
            // Remove SQL comments
            $sql = preg_replace('/^--.*$/m', '', $sql);
            $sql = preg_replace('/^\s*#.*$/m', '', $sql);
            
            // Split the SQL file into individual statements
            $queries = array_filter(array_map('trim', explode(';', $sql)));
            
            // Execute each query
            foreach ($queries as $query) {
                if (!empty($query)) {
                    if ($conn->query($query) === FALSE) {
                        echo "Error executing query: " . $conn->error . "<br>";
                        exit;
                    }
                }
            }
        }
    } else {
        echo "Error creating database: " . $conn->error . "<br>";
        exit;
    }

    // Create verification file
    file_put_contents('verified.txt', 'Done.');

    $conn->close();

    // Redirect to index
    header("Location: " . BASE_URL . "index.php");
    exit;
} else {
   
}