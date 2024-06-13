<?php

session_start();
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

if ($_SESSION['is_authenticated'] != true) {
    header('Location: login.html');
    exit();
}
else {
    function add_inventar_form($typ="", $hersteller="", $produkt="", $farbe="", $preis="") {
        // query for select types
        include 'connection.php';
        $conn = connect();
        $sql = "SELECT * FROM inventar_typ";
        $result = $conn->query($sql);
        $options = "";
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $options .= '<option value="'.$row['id'].'">'.$row['typ'].'</option>';
            }
        }
        return '<h1>Inventar hinzufügen</h1>
        <form action="add_inventar.html" method="post">
            <label for="typ">Typ:</label><br>
                <a href="inventar_typ.html" style="margin-left:20px">Inventar-Typ hinzufügen</a>
            <select id="typ" name="typ" value="'.$typ.'">
                <option value="-1">---bitte wählen---</option>'
                . $options 
                . '</select><br>
            <label for="hersteller">Hersteller:</label><br>
            <input type="text" id="hersteller" name="hersteller" placeholder="Gira" value="'.$hersteller.'"><br>
            <label for="produkt">Produkt:</label><br>
            <input type="text" id="produkt" name="produkt" placeholder="Lichtschalter 010800" value="'.$produkt.'"><br>
            <label for="farbe">Farbe:</label><br>
            <input type="text" id="farbe" name="farbe" placeholder="matt" value="'.$farbe.'"><br>
            <label for="preis">Preis:</label><br>
            <input type="number" id="preis" name="preis" placeholder="5.00" step=".01" value="'.$preis.'" min="0.00" style="width:150px"><br><br>
            <button type="submit" name="post" id="go">Inventar hinzufügen</button>
        </form>';
    }
    include_once 'menu.php';
    echo build_menu('add_inventar.html');
    include_once 'messages.php';
    $fields = array();
    if (isset($_SESSION['fields'])) {
        $fields = $_SESSION['fields'];
        unset($_SESSION['fields']);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!$fields) {
            $typ = $_POST['typ'];
            if ($typ == -1) {
                $_SESSION['warning'] = 'Bitte wählen Sie einen Typ aus!';
                header('Location: add_inventar.html');
                exit();
            }
            $hersteller = $_POST['hersteller'];
            $produkt = $_POST['produkt'];
            $farbe = $_POST['farbe'];
            $preis = $_POST['preis'];

            // create dictionary with all values
            $fields = array("typ" => $typ, "hersteller" => $hersteller, "produkt" => $produkt, "farbe" => $farbe, "preis" => $preis);
        
            // remove empty fields
            if (!($typ AND $hersteller AND $produkt AND $farbe AND $preis)) {
                if (!count(array_filter($fields))) {
                    $_SESSION['warning'] = 'Bitte füllen Sie zumindest ein Feld aus!';
                    header('Location: add_inventar.html');
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
        // include 'check_inventar.php';
        // $verify = check_fields($fields, false);
    
        // if ($verify != "") {
        //     $_SESSION['warning'] = $verify;
        //     $_SESSION['fields'] = $fields;
        //     header('Location: add_inventar.html');
        //     exit();
        // }
    
        // Create connection
        include 'connection.php';
        $conn = connect();
        
        // first check if inventar already exists
        $sql = "SELECT * FROM inventar WHERE ";
        foreach ($fields as $key => $value) {
            $sql .= "$key = '$value' AND ";
        }
        $sql = rtrim($sql, "AND ");
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $_SESSION['warning'] = 'Inventar existiert bereits!<br><em>zumindest eine Eingabe muss anders sein.</em>';
            $_SESSION['fields'] = $fields;
            header('Location: add_invantar.html');
            exit();
        }
    
        // create sql query
        $sql = "INSERT INTO inventar (" . implode(", ", array_keys($fields)) . ") VALUES (" . implode(", ", array_map(function($k, $v) { return ($k == "preis") ? $v : "'" .$v ."'"; }, array_keys($fields), $fields)) . ")";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = 'Inventar erfolgreich hinzugefügt!';
            header("Location: inventar.html");
        }
        else {
            $_SESSION['warning'] = 'Error: '.$sql.'<br>'.$conn->error;
            $_SESSION['fields'] = $fields;
            header('Location: add_inventar.html');
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
        echo add_inventar_form(...$fields);
    }
}

?>