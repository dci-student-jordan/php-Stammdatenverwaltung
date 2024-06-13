<?php
// if session not yet started, start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!$_SESSION['is_authenticated']) {
    header('Location: login.html');
}
else {
    $header = '';
    // check for warning in session
    if (isset($_SESSION['raum_warning'])) {
        $header .= '<h3 style="color:red;">'.$_SESSION['raum_warning'].'</h3>';
        unset($_SESSION['raum_warning']);
    }
    elseif (isset($_SESSION['raum_success'])) {
        $header .= '<h3 style="color:green;">'.$_SESSION['raum_success'].'</h3>';
        unset($_SESSION['raum_success']);
    }
    include_once 'connection.php';
    $conn = connect();
    // if no raum is is given, list all raeume
    if (!isset($_GET['raum_id'])) {
        echo '<h1>Räume</h1>'.$header;
        $sql = "SELECT * FROM raum WHERE wohnung_id = ".$_GET['id'];
        $result = $conn->query($sql);
        $conn->close();
        $total_qm = 0;
        if ($result->num_rows > 0) {
            echo '<table border="1" id="raum-table" class="display" style="min-width: 250px; margin-top:20px;"><thead>';
            echo '<tr><th>Größe (m2)</th><th>Notiz</th><th></th><th></th><th>Löschen</th></tr></thead><tbody>';
            while($row = $result->fetch_assoc()) {
                $groesse = '<form action="update_raum.php?id='.$row['id'].'" method="post"><input type="number" value="'.$row['qm'].'" step="0.01" name="qm_'.$row['id'].'"><button id="qm_'.$row['id'].'" type="submit" hidden></button> </form>';
                $notiz = '<form action="update_raum.php?id='.$row['id'].'" method="post" title="hit ENTER to submit"><input value="'.$row['notiz'].'" name="notiz_'.$row['id'].'" style="max-width:150px"><button id="notiz_'.$row['id'].'" type="submit" hidden></button> </form>';
                $inventar = '<a href="update_wohnung.html?id='.$_GET['id'].'&raum_id='.$row['id'].'">Inventar</a>';
                $angebot = '<a href="angebot.html?raum_id='.$row['id'].'">Angebotsgenerator</a>';
                $delete = '<form action="delete_raum.php?id='.$row['id'].'" method="post"><button type="submit" style="color:red; background-color:white;" id="delete_raum">X</button></form>';
                echo '<tr><td style="padding:5px;">' .$groesse.'</td><td style="padding:5px;">' .$notiz .'</td"><td style="padding:5px;">' .$inventar .'</td"><td style="padding:5px;">' .$angebot .'</td><td style="padding:5px">' .$delete .'</td></tr>';
                $total_qm += $row['qm'];
            }
        }
        else {
            echo '<p>Keine Raumdaten vorhanden.</p>';
        }
        
        echo '<br><form action="add_raum.php?id='.$_GET['id'].'" method="post">
            <button type="submit">Raum hinzufügen</button>
        </form>';
        echo '</tbody></table><br><p>Insgesamt: '.$result->num_rows.' Räume</p>';
        if (isset($_SESSION['groesse'])) {
            $groesse = $_SESSION['groesse'];
            if ($total_qm != $groesse) {
                if ($total_qm < $groesse) {
                    echo '<p style="color:gold;">Die Summe der Raumgrößen ('.$total_qm.' m2) ist kleiner als die Wohnungsgröße ('.$groesse.' m2).</p>';
                }
            }
        }
    }
    // if raum_id is given, show inventar of this raum
    else {
        // first info about raum
        $raum_id = $_GET['raum_id'];
        $sql = "            
            SELECT 
                raum.*,
                raum_inventar.id AS raum_inventar_id,
                raum_inventar.*,
                inventar.id AS inventar_id, 
                inventar.*
            FROM 
                raum
            LEFT JOIN 
                raum_inventar ON raum.id = raum_inventar.raum_id
            LEFT JOIN 
                inventar ON raum_inventar.inventar_id = inventar.id
            WHERE 
                raum.id = ".$raum_id;
        $inventar_result = $conn->query($sql);
        $sql = "SELECT * FROM inventar";
        $inventar_list = $conn->query($sql);
        $conn->close();

        // inventar list
        if ($inventar_result->num_rows > 0) {
            $row_header = false;
            $table_header = false;
            while($row = $inventar_result->fetch_assoc()) {
                if (!$row_header) {
                    echo '<h2>Raum Inventar für Raum "'.$row['notiz'].'":</h2><p>Raum Größe: '.$row['qm'].' m2</p><button><a href="update_wohnung.html?id='.$_GET['id'].'">Zurück zur Raumübersicht</a></button>';
                    $row_header = true;
                }
                if (!$table_header) {
                    $table_header = true;
                    echo '<table border="1" id="inventar-table" class="display" style="min-width: 250px; margin-top:20px;"><thead>';
                    echo '<tr><th>Inventar</th><th>Menge</th><th>Mengenangabe</th><th>GesamtPreis</th><th>Löschen</th></tr></thead><tbody>';
                }
                // loop over $row keys to get inventar data
                // foreach ($row as $key => $value) {
                //     echo $key .": ". $value ."<br>";
                //     }
                if ($row['inventar_id'] == null) {
                    continue;
                }
                else {
                    echo '<tr>
                            <td>
                                <p>'.$row['produkt'].'('.$row['hersteller'].')</p><p>Farbe: '.$row['farbe'].'</p>
                            </td>
                            <td>'.$row['menge'].'</td>
                            <td>'.$row['mengen_mass'].'</td>
                            <td><p>'.($row['preis'] * $row['menge']).' €</p><p>(Einzelpreis: '.$row['preis'].' €)</p></td>'
                            .'<td><form action="delete_raum_inventar.php?&id='.$row['raum_inventar_id'].'&wohnung='.$_GET['id'].'&raum='.$row['raum_id'].'" method="post"><button type="submit" style="color:red; background-color:white;" id="delete_raum_inventar">X</button></form></td>'
                        .'</tr>';
                }
            }
            echo '</tbody></table>';
        }
        else {
            echo '<h2>Raum nicht gefunden.</h2>';
        }
        // form for adding raum-inventar
        echo '<br><h2>Raum-Inventar hinzufügen</h2>'.$header
            .'<form action="add_raum_inventar.php?id='.$_GET['id'].'&raum_id='.$raum_id.'" method="post">
                <label for="inventar">Inventar:</label><br>
                <select id="inventar" name="inventar">
                    <option value="-1">---bitte Inventar wählen---</option>';
                    while($row = $inventar_list->fetch_assoc()) {
                        echo '<option value="'.$row['id'].'">'.$row['produkt'].'('.$row['hersteller'].'), Farbe: '.$row['farbe'].'</option>';
                    }
                echo '</select><br>
                <label for="menge">Menge:</label><br>
                <input type="number" id="menge" name="menge" placeholder="1" step="0.01" value="1.00" min="1" style="width:150px"><br>
                <label for="mengen_mass">Mengenangabe:</label><br>
                <input type="text" id="mengen_mass" name="mengen_mass" placeholder="Stück" style="width:150px"><br><br>
                <button type="submit">Inventar hinzufügen</button>
            </form>';
    }
}

?>