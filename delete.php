<?php

function delete_from_table($table) {
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
            $sql = "DELETE FROM $table WHERE id=" .$id;
            try {
                if ($conn->query($sql) === TRUE) {
                    $_SESSION['success'] = "$table gelöscht!";
                } else {
                    $_SESSION['warning'] = "Error: " . $sql . "<br>" . $conn->error;
                }
            } catch (Exception $e) {
                $_SESSION['warning'] = "Error: " . $e->getMessage();
            }
            $conn->close();
            if ($table == "raum_inventar") {
                header("Location: update_wohnung.html?id=".$_GET['wohnung']."&raum_id=".$_GET['raum']);
                exit();
            }
            header("Location: $table.html");
        }
    }
}
?>