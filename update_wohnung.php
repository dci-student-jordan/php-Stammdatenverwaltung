<?php
function update_app_form($id=-1, $strasse="", $hausnummer="", $plz="", $stadt="", $bundesland="", $etage="", $groesse="") {
    if ($id == -1) {
        return '<h3 style="color:red;">No id given</h3>';
    }
    // create fields from given values
    $fields = array("strasse" => $strasse, "hausnummer" => $hausnummer, "plz" => $plz, "stadt" => $stadt, "bundesland" => $bundesland, "etage" => $etage, "groesse" => $groesse);
    // remove empty fields
    $params = "";
    foreach ($fields as $key => $value) {
        if ($value) {
            $params .= (($params == "") ? "?" : "&");
            $params .= $key."=".$value;
            if ($key == "groesse") {
                // share groesse with raeumen.php
                $_SESSION['groesse'] = intval(substr($value, 0, -3));
            }
        }
    }

    return '<h1>Wohnungsdaten ändern</h1>
    <form action="update_wohnung.html?id=' .$id .'" method="post">
        <label for="strasse">Straße:</label><br>
        <input type="text" id="strasse" name="strasse" placeholder="Musterstraße" value="'.$strasse.'"><br>
        <label for="hausnummer">Hausnummer:</label><br>
        <input type="text" id="hausnummer" name="hausnummer" placeholder="123" value="'.$hausnummer.'"><br>
        <label for="plz">PLZ:</label><br>
        <input type="text" id="plz" name="plz" placeholder="12345" value="'.$plz.'"><br>
        <label for="stadt">stadt:</label><br>
        <input type="text" id="stadt" name="stadt" placeholder="Musterstadt" value="'.$stadt.'"><br>
        <label for="bundesland">Bundesland:</label><br>
        <input type="text" id="bundesland" name="bundesland" placeholder="Nordrhein-Westfalen" value="'.$bundesland.'"><br>
        <label for="etage">etage:</label><br>
        <input type="text" id="etage" name="etage" placeholder="5" value="'.$etage.'"><br>
        <label for="groesse">groesse:</label><br>
        <input type="text" id="groesse" name="groesse" placeholder="120" value="'.$groesse.'"><br><br>
        <button type="submit" id="go">Wohnung ändern</button>
    </form>
    <br><br>
    <div style="display:flex; justify-content: space-between; float: right;">
        <button><a href="add_wohnung.html'.$params.'">neu mit diesen Daten</a></button>
        <form action="delete_wohnung.php?id='.$id.'" method="post" id="delete-form" style="margin-left: 20px;">
            <button id="delete">Wohnung löschen</button>
        </form>
    </div>';
}

if ($_SESSION['is_authenticated'] != true) {
    header('Location: login.html');
    exit();
}
else {
        
    $id = $_GET['id'];
    if (!$id) {
        echo '<h3 style="color:red;">No id given</h3>';
        exit();
    }
    $fields = array();
    // check for fields in session
    if (isset($_SESSION['fields'])) {
        $fields = $_SESSION['fields'];
        // we unset after we used it later
    }
    include_once 'messages.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $strasse = $_POST['strasse'];
        $hausnummer = $_POST['hausnummer'];
        $plz = $_POST['plz'];
        $stadt = $_POST['stadt'];
        $bundesland = $_POST['bundesland'];
        $etage = $_POST['etage'];
        $groesse = $_POST['groesse'];
        //add "qm" to groesse if not already there
        if ($groesse AND !str_ends_with(strtolower($groesse), "qm")) {
            $groesse .= " qm";
        }
        // create dictionary with all values
        $fields = array("strasse" => $strasse, "hausnummer" => $hausnummer, "plz" => $plz, "stadt" => $stadt, "bundesland" => $bundesland, "etage" => $etage, "groesse" => $groesse);
    
        // remove empty fields
        if (!($strasse AND $hausnummer AND $plz AND $stadt AND $bundesland AND $etage AND $groesse)) {
            if (!count(array_filter($fields))) {
                $_SESSION["fields"] = $fields;
                $_SESSION['warning'] = 'Bitte füllen Sie zumindest ein Feld aus!';
                header('Location: update_wohnung.html?id='.$id);
                exit();
            }
            else {
                // loop over keys to remove empy values
                foreach ($fields as $key => $value) {
                    if (!$value) {
                        unset($fields[$key]);
                    }
                }
            }
        }
        include 'check_wohnung.php';
        $verify = check_fields($fields, true);
    
        if ($verify != "") {
            $_SESSION['warning'] = $verify;
            $_SESSION['fields'] = $fields;
            header('Location: update_wohnung.html?id='.$id);
            exit();
        }
    
        // Create connection
        include 'connection.php';
        $conn = connect();
    
        // update appartment
        if ($id) {
            $sql = "UPDATE wohnung SET " . implode(", ", array_map(function($k, $v) { return ($k != "etage" && $k != "plz") ? $k." = '".$v."'" : $k." = ".$v; }, array_keys($fields), $fields)) . " WHERE id=" .$id;
            if ($conn->query($sql) === TRUE) {
                $_SESSION['success'] = 'Wohnung erfolgreich geändert!';
                header("Location: wohnungen.html");
            }
            else {
                $_SESSION['warning'] = 'Error: '.$sql.'<br>'.$conn->error;
                $_SESSION['fields'] = $fields;
                header('Location: add_update_wohnung.html?id='.$id);
            }

        }
        else {
            echo '<h3 style="color:red;">No id given</h3>';
        }
        $conn->close();
        exit();
    }
    else {
        parse_str($_SERVER['QUERY_STRING'], $params);
        if ($params) {
            if (isset($params['id'])) {
                include 'connection.php';
                $id = $_GET['id'];
                if (!$fields) {
                    // get fields from db
                    $sql = "SELECT * FROM wohnung WHERE id=" .$id;
                    $conn = connect();
                    $result = $conn->query($sql);
                    $conn->close();
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $fields = array("strasse" => $row['strasse'], "hausnummer" => $row['hausnummer'], "plz" => $row['plz'], "stadt" => $row['stadt'], "bundesland" => $row['bundesland'], "etage" => $row['etage'], "groesse" => $row['groesse']);
                        }
                        echo update_app_form($id, ...$fields);                    
                    }
                }
                else {
                    // fileds given in session
                    echo update_app_form($id, ...$fields);
                    // now we unset the fields
                    unset($_SESSION['fields']);
                }
            }
        }
        else {
            echo '<h3 style="color:red;">No id given</h3>';
        }
    }
}

?>