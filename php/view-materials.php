<?php

include 'config.php'; //configuration file
session_start();

$selected_semester = isset($_GET['sem']) ?htmlspecialchars($_GET['sem']):'';  //assigns the value of sem either passed through the url or form

$materials = [];
$page_title = $selected_semester ? $selected_semester : "All Study materials"; //assigns the semester selected or else the generic text

if($selected_semester){
    try{
        //query to select data from the database
        $sql ="SELECT subject_name, file_name, file_path, uploaded_at FROM materials WHERE semester = :sem ORDERED BY subject_name ASC, uploaded_at DESC";

        $stmt = $conn-> prepare($sql);

        //parsing value to the parameter
        $stmt->execute([':sem' => $selected_semester]);

        //fetch all data from DB
        $materials = $stmt->fetchAll();
    }catch(PDOException $e){
        $error_message = "Error fetching data.";
    }
}


?>