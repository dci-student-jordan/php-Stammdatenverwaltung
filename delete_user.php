<?php

session_start();


if (!isset($_SESSION['is_authenticated']) || $_SESSION['is_authenticated'] != true) {
    header('Location: login.html');
    exit();
}
elseif (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true) {
    include_once 'connection.php';
    $conn = connect();
    $id = $_GET['id'];
    if ($id AND $id != $_SESSION['user_id']) {
        $sql = "DELETE FROM user WHERE id=" .$id;
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = "User gelöscht!";
        } else {
            $_SESSION['warning'] = "Error: " . $sql . "<br>" . $conn->error;
        }
        header('Location: users.html');
    }
    else {
        if (!$id) {
            $_SESSION['warning'] = "Keine ID übergeben!";
        }
        else {
            $_SESSION['warning'] = "Du kannst dich nicht selbst löschen!";
        }
        header('Location: users.html');
    }
    $conn->close();
}
else {
    echo 'Du hast keine Berechtigung für diese Seite!';
}
?>