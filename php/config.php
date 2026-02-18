<?php
$host = '127.0.0.1';
$port = '3307'; 
$db   = 'db_srms';
$user = 'root';
$pass = '';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db"; 
    $conn = new PDO($dsn, $user, $pass);
    
    // CHANGE THIS LINE: $conn -> $pdo
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // echo "Connected successfully"; // Uncomment to test
}
catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>