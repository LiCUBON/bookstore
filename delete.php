<?php 

require_once "inc/conectDB.php";


if (!empty($_GET['id_memeber'])) {
    $id = (int) $_GET['id_memeber'];
    $stmt = $conn->prepare("DELETE FROM `books` WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    header('Location: /');
} else {
    echo "<p><a href=\"/\">Back</a><br></p>";
    die("ERROR");
}
