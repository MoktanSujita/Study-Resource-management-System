<?php
$host = '127.0.0.1';
$port = '3307'; 
$db   = 'db_srms';
$user = 'root';
$pass = '';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db"; 
    $conn = new PDO($dsn, $user, $pass);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // echo "Connected successfully"; 
}
catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>