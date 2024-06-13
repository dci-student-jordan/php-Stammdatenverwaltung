<?php

session_start();


if ($_SESSION['is_authenticated'] != true) {
    header('Location: login.html');
    exit();
}
else {
    include_once 'connection.php';
    $conn = connect();
    $id = $_GET['id'];
    if ($id) {
        // first get the wohnung id
        $sql = "SELECT wohnung_id FROM raum WHERE id=" .$id;
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $wohnung_id = $row['wohnung_id'];
        // then delete the raum
        $sql = "DELETE FROM raum WHERE id=" .$id;
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = "Raum gelöscht!";
        } else {
            $_SESSION['warning'] = "Error: " . $sql . "<br>" . $conn->error;
        }
        $conn->close();
        header('Location: update_wohnung.html?id='.$wohnung_id);
    }
}
?>