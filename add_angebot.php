<?php
if (!$_SESSION['is_authenticated']) {
    header('Location: login.html');
}
else {
    $raum_id = $_GET['raum_id'];
    unset($_GET['raum_id']);
    include_once 'menu.php';
    echo build_menu();
    include_once 'messages.php';
    include 'connection.php';
    $conn = connect();
    echo '<div class="splitdiv"><div><h1>Angebot aufgeben</h1>';    
    $sql = "SELECT 
                raum.notiz,
                wohnung.strasse,
                wohnung.hausnummer,
                wohnung.plz,
                wohnung.stadt,
                wohnung.bundesland,
                wohnung.etage
            FROM 
                raum
            RIGHT JOIN
                wohnung ON raum.wohnung_id = wohnung.id
            WHERE 
                raum.id = ".$raum_id;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<h2>Angebot für Raum "'.$row['notiz'].'"</h2>';
            echo '<h4><em>Wohnung: '.$row['strasse'].' '.$row['hausnummer'].', '.$row['plz'].' '.$row['stadt'].', '.$row['bundesland'].', Etage: '.$row['etage'].'</em></h4></div>';
        }
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $sql = "INSERT INTO angebot (raum_id) VALUES (".$raum_id.")";
        $gesamt_preis = 0;
        $first = true;
        foreach ($_POST as $key => $value) {
            $gesamt_preis += explode(",", $value)[2];
            if ($first) {
                // first create the record in db
                $first = false;
                echo $sql.'<br><br>';
                if ($conn->query($sql) === FALSE) {
                    $_SESSION['warning'] = "Error creating Angebot: " . $sql . "<br>" . $conn->error;
                    header('Location: add_angebot.html?raum_id='.$raum_id);
                    exit();
                }
                else {
                    // get the id of the new record
                    $angebot_id = $conn->insert_id;
                }
            }
            // if not value break
            if ($value == "") {
                break;
            }
            // then for each inventar add the id to angebot_inventar
            $anzahl = isset(explode(",", $value)[1]) ? intval(explode(",", $value)[1]) : 0;
            $inventar_id = explode(",", $value)[0];
            $sql = "INSERT INTO angebot_inventar VALUES (NULL, ".$angebot_id.", ".$inventar_id.",".$anzahl.");";
            echo $sql.'<br>';
            if ($conn->query($sql) === FALSE) {
                $_SESSION['warning'] = "Error adding Inventar to Angebot: " . $sql . "<br>" . $conn->error;
                header('Location: add_angebot.html?raum_id='.$raum_id);
                exit();
            }
            // finally update the gesamt_preis
            $sql = "UPDATE angebot SET gesamt_preis = ".$gesamt_preis." WHERE id = ".$angebot_id;
            echo $sql.'<br>';
            if ($conn->query($sql) === FALSE) {
                $_SESSION['warning'] = "Error updating Gesamtpreis in Angebot: " . $sql . "<br>" . $conn->error;
                header('Location: add_angebot.html?raum_id='.$raum_id);
                exit();
            }
        }
        $_SESSION['success'] = "Angebot erfolgreich erstellt!";
        header('Location: angebot.html?raum_id='.$raum_id);
        exit();
    }
    else {
        echo '<div style="margin: 20px; padding: 20px; overflow-x: scroll; max-width: 80vw; background-color: rgb(20, 6, 32); box-shadow: 0 4px 8px 0 rgba(177, 177, 177, 0.2), 2px -6px 20px 0 rgba(189, 149, 199, 0.399); border-radius: 20px;">';
        // create the form to possibly select inventar
        $form = '<form action="add_angebot.html?raum_id='.$raum_id.'" method="post">';
        foreach ($_GET as $key => $value) {
            // first query all inventar for the given inventar_type
            $sql = "SELECT
                        inventar_typ.id AS type_id,
                        inventar.id AS inventar_id,
                        inventar.produkt,
                        inventar.hersteller,
                        inventar.farbe,
                        inventar.preis
                    FROM
                        inventar_typ
                    LEFT JOIN inventar ON inventar_typ.id = inventar.typ
                    WHERE inventar_typ.typ='".$key."'
                    ORDER BY inventar.preis ASC;";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $form_init = false;
                $form .= '<label for="'.$key.'"><strong style="font-size: 25px;">'.$value.'x '.$key.', ';
                while($row = $result->fetch_assoc()) {
                    $gesamt_preis = $value * floatval($row['preis']);
                    if (!$form_init) {
                        $form.= $gesamt_preis.'€, (Einzelpreis: '.$row['preis'].'€):</strong></label><br>
                            <select id="'.$key.'" name="'.$key.'">';
                        $form_init = true;
                    }
                    $form .= '<option value="'.$row['inventar_id'].', '.$value.', '.$gesamt_preis.'" name="'.$row['inventar_id'].'" preis="'.$row['preis'].'" anzahl="'.$value.'">'
                        .$row['produkt']
                        .($row['hersteller'] != "" ?" (".$row['hersteller'].")" : "")
                        .($row['farbe'] == "" ? "" : ' - '.$row['farbe'])
                        .'</option>';
                }
                $form .= '</select><br>';
            }
        }
        echo $form .'<br><button type="submit" name="post" id="go">Angebot abschicken</button></form></div></div>';
    }
}
?>