<?php
$username ="root";
$password ="";
$servername ="localhost";
$database ="db_srms";

try{
    $conn = new PDO("mysql:host =$servername;dbname=$database",$username,$password);
    $conn -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    die("Connection failed:" .$e->getMessage());
}

?>