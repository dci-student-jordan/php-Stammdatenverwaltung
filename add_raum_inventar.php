<?php
session_start();
if (!$_SESSION['is_authenticated']) {
    header('Location: login.html');
}
else {
    $id = $_GET['id'];
    $raum_id = $_GET['raum_id'];
    if ($_POST['inventar'] < 0) {
        $_SESSION['raum_warning'] = "Bitte Inventar wählen.";
        header('Location: update_wohnung.html?id='.$id.'&raum_id='.$raum_id);
        exit();
    }
    include_once 'connection.php';
    $conn = connect();
    $sql = "INSERT INTO raum_inventar VALUES (NULL, ".$raum_id.", ".$_POST['inventar'].", ".$_POST['menge'].", '".$_POST['mengen_mass']."')";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['raum_success'] = "Raum Inventar hinzugefügt.";
    }
    else {
        $_SESSION['raum_warning'] = "Fehler beim Hinzufügen des Raumes: ".$conn->error;
    }
    header('Location: update_wohnung.html?id='.$id.'&raum_id='.$raum_id);
}
?>