<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!$_SESSION['is_authenticated']) {
    header('Location: login.html');
}
else {
    include_once 'menu.php';
    echo build_menu("angebot.html");
    echo '<div class="splitdiv"><div><h1>Angebotsgenerator</h1>';
    $id = $_GET['id'];
    if (!$id) {
        echo '<h3 style="color:red;">No id given</h3>';
        exit();
    }
    include_once 'messages.php';

    include_once 'connection.php';
    $conn = connect();
    $erledigt = $_POST['erledigt'];
    $status = ($erledigt == 0) ? "als 'in Arbeit' markiert." : (($erledigt == 1) ? "erledigt." : "gelöscht.");
    if ($erledigt == 0) {
        $sql = "UPDATE angebot SET erledigt = NULL WHERE id = ".$id;
    }
    elseif ($erledigt == 1) {
        // insert the inventar to raum_inventar
        $sql = "SELECT inventar_id, anzahl FROM angebot_inventar WHERE angebot_id = ".$id;
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // first check if the inventar is already in raum_inventar
                $sql = "SELECT * FROM raum_inventar WHERE raum_id = ".$_POST['raum_id']." AND inventar_id = ".$row['inventar_id'];
                $result2 = $conn->query($sql);
                if ($result2->num_rows > 0) {
                    // if already present, add the anzahl to menge
                    $sql = "UPDATE raum_inventar SET menge = menge + ".$row['anzahl'] ." WHERE raum_id = ".$_POST['raum_id']." AND inventar_id = ".$row['inventar_id'];
                }
                else {
                    // if not present, insert the inventar
                    $sql = "INSERT INTO raum_inventar (raum_id, inventar_id, menge) VALUES (".$_POST['raum_id'].", ".$row['inventar_id'].", ".$row['anzahl'].")";
                }
                $conn->query($sql);
            }
            $status .= "<br>Inventar wurde zum Raum hinzugefügt.";
        }
        $sql = "UPDATE angebot SET erledigt = 1 WHERE id = ".$id;
        $conn->query($sql);
    }
    else {
        // delete the angebot
        $sql = "DELETE FROM angebot WHERE id = ".$id;
    }
    $conn->query($sql);
    $_SESSION['success'] = "Angebot erfolgreich ".$status;
    $conn->close();
    header('Location: angebot.html?raum_id='.$_POST['raum_id']);
}
?>