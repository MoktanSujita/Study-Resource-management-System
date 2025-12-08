<?php 
session_start();
include 'config.php';

if($_SERVER["REQUEST_METHOD"]=="POST"){
  $name = trim($_POST["fullname"]);
  $email = trim($_POST['email']);
  $password = password_hash($_POST['password'],PASSWORD_DEFAULT);

  try{
    $stmt = $conn->prepare("INSERT INTO users(full_name, email, password) VALUES (:name,:email,:password)");
    $stmt->execute([
      ':name'=>$name,
     ':email'=>$email,
     ':password'=>$password
    ]);
      echo "<script>
            alert('Registration Successful!');
            window.location.href = 'login.php';
          </script>";
      exit();
    }
  catch(PDOException $e){
    $error = "Error:" .$e->getMessage();
  }
}
?>

