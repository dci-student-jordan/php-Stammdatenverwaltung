<?php
session_start();
if (!$_SESSION['is_authenticated']) {
    header('Location: login.html');
}
else {
    $id = $_GET['id'];
    if (!$id) {
        echo '<h3 style="color:red;">No id given</h3>';
        exit();
    }
    include_once 'connection.php';
    $conn = connect();
    $sql = "INSERT INTO raum (wohnung_id) VALUES (".$id.")";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Raum hinzugefügt.";
    }
    else {
        $_SESSION['warning'] = "Fehler beim Hinzufügen des Raumes: ".$conn->error;
    }
    header('Location: update_wohnung.html?id='.$id);
}
?>