<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

function add_app_form($strasse="", $hausnummer="", $plz="", $stadt="", $bundesland="", $etage="", $groesse="") {
    return '<h1>Wohnung hinzufügen</h1>
    <form action="add_wohnung.html" method="post">
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
        <button type="submit" name="post" id="go">Wohnung hinzufügen</button>
    </form>';
}

if ($_SESSION['is_authenticated'] != true) {
    header('Location: login.html');
    exit();
}
else {
    include_once 'menu.php';
    echo build_menu('add_wohnung.html');
    // check for warning in session
    include_once 'messages.php';
    $fields = array();
    if (isset($_SESSION['fields'])) {
        $fields = $_SESSION['fields'];
        echo 'GOT FIELDS<br>';
        unset($_SESSION['fields']);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!$fields) {
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
                    $_SESSION['warning'] = 'Bitte füllen Sie zumindest ein Feld aus!';
                    header('Location: add_wohnung.html');
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
        }
        include 'check_wohnung.php';
        $verify = check_fields($fields, false);
    
        if ($verify != "") {
            $_SESSION['warning'] = $verify;
            $_SESSION['fields'] = $fields;
            header('Location: add_wohnung.html');
            exit();
        }
    
        // Create connection
        include 'connection.php';
        $conn = connect();
        
        // first check if wohnung already exists
        $sql = "SELECT * FROM wohnung WHERE ";
        foreach ($fields as $key => $value) {
            $sql .= "$key = '$value' AND ";
        }
        $sql = rtrim($sql, "AND ");
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $_SESSION['warning'] = 'Wohnung existiert bereits!<br><em>zumindest eine Eingabe muss anders sein.</em>';
            $_SESSION['fields'] = $fields;
            header('Location: add_wohnung.html');
            exit();
        }
    
        // create sql query
        $sql = "INSERT INTO wohnung (" . implode(", ", array_keys($fields)) . ") VALUES (" . implode(", ", array_map(function($k, $v) { return ($k == "etage" || $k == "plz") ? $v : "'" .$v ."'"; }, array_keys($fields), $fields)) . ")";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = 'Wohnung erfolgreich hinzugefügt!';
            header("Location: wohnungen.html");
        }
        else {
            $_SESSION['warning'] = 'Error: '.$sql.'<br>'.$conn->error;
            $_SESSION['fields'] = $fields;
            header('Location: add_wohnung.html');
        }
        $conn->close();
        exit();
    // end POST
    }
    else {        
        parse_str($_SERVER['QUERY_STRING'], $params);
        if ($params) {
            // add params to fields
            foreach ($params as $key => $value) {
                $fields[$key] = $value;
            }
        }
        echo add_app_form(...$fields);
    }
}

?>