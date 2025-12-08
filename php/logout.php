<?php 
session_start();
session_unset(); //clear all session variables
session_destroy(); //destroy session file

echo 
"<script>
alert('You have been logged out!');
windows.location.href ='login.html';
</script>";
exit();
?>