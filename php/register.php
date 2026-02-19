<?php 
session_start();
include 'config.php'; // configuration file

if($_SERVER["REQUEST_METHOD"] == "POST"){
  $name = trim($_POST['fullname']);
  $email = trim($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = trim($_POST['role']);

  try {
         //Inserting to user data to database table
         $stmt = $conn->prepare("INSERT INTO tbl_users (username, email, password, role) VALUES (:name, :email, :password, :role)");
         
         $stmt->execute([
           ':name'     => $name,
           ':email'    => $email,
           ':password' => $password,
           ':role'     => $role
         ]);

         $_SESSION['registered'] = true;
         header("Location: ../templates/login.html");
         exit();
        } 
        catch(PDOException $e)
       {
          //SQL error
         die("Registration Error: " . $e->getMessage());
        }
}
?>