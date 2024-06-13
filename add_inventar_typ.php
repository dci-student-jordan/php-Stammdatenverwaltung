<?php
session_start();
if (!$_SESSION['is_authenticated']) {
    header('Location: login.html');
}
else {
    $typ = $_POST['typ'];
    if ($typ == "") {
        $_SESSION['warning'] = 'Bitte geben Sie einen Inventar-Typ ein!';
        header('Location: inventar_typ.html?id='.$id);
        exit();
    }
    include_once 'connection.php';
    $conn = connect();
    $sql = "INSERT INTO inventar_typ VALUES (NULL, '".$typ."')";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Inventar-Typ hinzugefügt.";
    }
    else {
        $_SESSION['error'] = "Fehler beim Hinzufügen des Inventar-Typs: ".$conn->error;
    }
    header('Location: inventar_typ.html?id='.$id);
}
?>