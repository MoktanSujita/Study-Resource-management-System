<?php 
session_start();
session_unset(); //clear all session variables
session_destroy(); //destroy session file

header("Location:../index.php");
exit();
?>