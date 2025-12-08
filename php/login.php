<?php
session_start();
include 'config.php';

if($_SERVER["REQUEST_METHOD"]=="POST"){
  $email =trim($_POST['email']);
  $password =trim($_POST['password']);
  
  $stmt =$conn->prepare("SELECT * FROM users WHERE email = :email");
  $result =$stmt->execute([':email'=>$email]);

  if($result){
    if(password_verify($password,$user['password'])){
      $_SESSION['user'] = $user;
      if($result['role'] == 'admin'){
          header("Location:admin-dashboard.php");
        exit();
      }else{
        header("location:student-dashboard.php");
      }
    }else{
      $error = "Invalid password or email";
    }
  }else{
    $error ="No accounts found with the email";
  }

}
?>


