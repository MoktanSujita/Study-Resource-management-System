<?php

session_start();
include 'config.php'; // Includes the $pdo connection object

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    //if the user is not admin

    //error message render
    header("Location: ../templates/login.html?error=forbidden");
    exit();
}

if(!$isset($_GET['id']) || !is_numeric($_GET['id'])){

    //id needs to be present and numeric value
    header("Location: view-materials.php?error=no_id");
    exit();
}

$material_id = (int)$_GET['id'];
$file_path = null;
try{
    //for data integrity purpose
    $conn->beginTransaction();

    //sql query to select all the materials of the material id
        $sql_select =$conn->prepare("SELECT file_path FROM materials where id = :id");
        $stmt->execute([':id' => $material_id]);
        $materials = $stmt->fetchAll();
        if($materials){
            $file_path = $materials['file_path'];

            //checking if the file exists on the server
            if(file_exists($file_path) && !is_dir($file_path)){
                //unlinking with the files on 'uploads' directory
                unlink($file_path);
            }


            $sql = "DELETE FROM materials where id = :id";
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([':id'=>$material_id]);

            $conn->commit();  //finalize the changes incase of success of all previous steps

            header("Location: view-materials.php?success=deleted");
            exit();
        }else{

            //if anything fails no changes has to be implemented
            $conn->rollBack();
            header("Location: view-materials.php?error=material_not_found");
            exit();
        }

    } catch (PDOException $e) {
        //incase of errors -> roll back
        $conn->rollBack();
        header("Location: view-materials.php?error=db-fail");
        exit();    
}

?>