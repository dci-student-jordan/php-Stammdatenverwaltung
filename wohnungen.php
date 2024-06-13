<?php
session_start();
if (!$_SESSION['is_authenticated']) {
    header('Location: login.html');
}
else {
    include_once 'menu.php';
    echo build_menu('wohnungen.html');
}

function build_table ($validate=false) {
    if ($validate) {
        include 'check_wohnung.php';
    }
    include_once 'messages.php';

    include_once 'connection.php';
    $conn = connect();

    $sql = "SELECT * FROM wohnung";
    $result = $conn->query($sql);

    echo '<h1>Wohnungen</h1>';
    echo '<button><a href="add_wohnung.html">Neue Wohnung hinzufügen</a></button><br><br>';
    echo '<table border="1" id="wohn-table" class="display"><thead>';
    echo '<tr><th>Straße</th><th>Hausnummer</th><th>PLZ</th><th>Stadt</th><th>Bundesland</th><th>Etage</th><th>Größe</th><th></th></tr></thead><tbody>';
    while($row = $result->fetch_assoc()) {
        $strasse = $row['strasse'];
        $hausnummer = $row['hausnummer'];
        $plz = $row['plz'];
        $stadt = $row['stadt'];
        $bundesland = $row['bundesland'];
        $etage = $row['etage'];
        $groesse = $row['groesse'];
        $invalid = false;
        $num_invalid = 0;
        
        if ($validate) {
            // check if fields are valid (if not, add warning to link)
            $check = array("strasse" => $strasse, "hausnummer" => $hausnummer, "plz" => $plz, "stadt" => $stadt, "bundesland" => $bundesland, "etage" => $etage, "groesse" => $groesse);
            $check = check_fields($check, true);
            if ($check != "") {
                $invalid = true;
                $num_invalid++;
            }
        }
        echo '<tr';
        if ($invalid) {
            echo ' title="'.$check.'"';
        }
        echo '><td>';
        if ($invalid) {
            echo '<em style="color:red">!!! </em>';
        }
        echo $strasse.'</td>';
        echo '<td>'.$hausnummer.'</td>';
        echo '<td>'.$plz.'</td>';
        echo '<td>'.$stadt.'</td>';
        echo '<td>'.$bundesland.'</td>';
        echo '<td>'.$etage.'</td>';
        echo '<td>'.$groesse.'</td>';
        echo '<td><a href="update_wohnung.html?id='.$row['id'];
        if ($invalid) {
            echo '">bearbeiten</a><em style="color:red"> !!!</em></td>';
        }
        else {
            echo '">bearbeiten</a></td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '<a href="wohnungen.html?validate=true">Alle Felder validieren (daaaauuuuuuueeeeeerrrrt!)</a>';
    if ($validate) {
        if (!$num_invalid) {
            echo '<h3 style="color:green;">Alle Wohnungensdaten sind i.O!</h3>';
        }
        else {
            echo '<h3 style="color:red;">'.$num_invalid.' Wohnung(en) sind nicht i.O!</h3>';
        }
    }
}
parse_str($_SERVER['QUERY_STRING'], $params);
if ($params) {
    build_table(true);
}
else {
    build_table();
}
?>