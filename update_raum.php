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
    foreach ($_POST as $param => $value) {
        $param = explode("_", $param)[0];
        if ($param == "notiz") {
            // set value in quotes
            $value = "'".$value."'";        
        }
        else {
            // given from update_wohnung.php
            if (isset($_SESSION['groesse'])) {
                // get wohnungs id
                $sql = "SELECT wohnung_id FROM raum WHERE id=".$id;
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $wohnungs_id = $row['wohnung_id'];
                // check total qm
                $sql = "SELECT SUM(qm) AS total_qm FROM raum WHERE wohnung_id=".$wohnungs_id ." AND id !=".$id;
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $total_qm = $row['total_qm'] + $value;
                if ($total_qm > $_SESSION['groesse']) {
                    $_SESSION['raum_warning'] = "Die Summe der Raumgrößen darf nicht größer als die Wohnungsgröße sein!";
                    header('Location: update_wohnung.html?id='.$wohnungs_id);
                    exit();
                }
            }
        }
        $sql = "UPDATE raum SET ".$param ."=" .$value." WHERE id=".$id;
        if ($conn->query($sql) === TRUE) {
            $_SESSION['raum_success'] = "Raumdaten aktualisiert: ".$param ." = " .$value.".";
        } else {
            $_SESSION['raum_warning'] = "Error: " . $sql . "<br>" . $conn->error;
        }
        $result = $conn->query("SELECT wohnung_id FROM raum WHERE id=".$id);
        $row = $result->fetch_assoc();
        header('Location: update_wohnung.html?id='.$row['wohnung_id']);
    }
    $conn->close();
}
?>