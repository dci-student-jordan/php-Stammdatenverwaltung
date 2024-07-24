<?php
if (!$_SESSION['is_authenticated']) {
    header('Location: login.html');
}
else {
    include_once 'menu.php';
    echo build_menu("angebot.html");
    echo '<div class="splitdiv"><div><h1>Angebotsgenerator</h1>';
    $id = $_GET['raum_id'];
    if (!$id) {
        echo '<h3 style="color:red;">No id given</h3>';
        exit();
    }
    include_once 'messages.php';

    include_once 'connection.php';
    $conn = connect();
    $raum_groesse = 0;
    $sql = "SELECT 
                raum.notiz,
                raum.qm,
                wohnung.id,
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
                raum.id = ".$id;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<button><a href="update_wohnung.html?id='.$row['id'].'">Zurück zur Raumübersicht</a></button><h2>Angebot für Raum "'.$row['notiz'].'"</h2>';
            echo '<h4><em>Wohnung: '.$row['strasse'].' '.$row['hausnummer'].', '.$row['plz'].' '.$row['stadt'].', '.$row['bundesland'].', Etage: '.$row['etage'].'</em></h4>';
            echo '<h3><em>Raum Größe: '.$row['qm'].' m2</em></h3><br>';
        }
        // check if there is already an angebot for this raum
        $sql = "SELECT * FROM angebot WHERE raum_id = ".$id;
        $result = $conn->query($sql);
        // if yes, check if it is already erledigt
        if ($result->num_rows > 0) {
            $angebot = $result->fetch_assoc();
            if ($angebot['erledigt'] == 1) {
                echo '<h2 style="color: magenta;">Angebot wurde bereits erstellt und erledigt.</h2><br>';
            }
        }
    }
    // here we get present inventar and start our calculator
    // first define standard based on raum_groesse
    $standard = array(
        // 0,2 Steckdosen pro m² in einem Raum, aber mindestens 3
        "Steckdose" => max(floor($raum_groesse * 0.2), 3),
        // 0,3 Einbaustrahler pro m² in einem Raum, aber mindestens einen
        "Einbaustrahler" => max(floor($raum_groesse * 0.3), 1),
        // 0,1 Heizkörper pro m² in einem Raum, aber mindestens einen
        "Heizkörper" => max(floor($raum_groesse * 0.1), 1),
        // 0,05 Lichtschalter pro m² in einem Raum, aber mindestens einen
        "Lichtschalter" => max(floor($raum_groesse * 0.05), 1),
        // Fußleisten, wobei wir zunächst immer von einem quadratischen Raum ausgehen und dann 50% Länge aufschlagen
        "Fußleiste" => floor(2 * sqrt($raum_groesse) * 1.5),
    );
    $sql = "SELECT 
                inventar.id,
                inventar.hersteller,
                inventar.produkt,
                inventar.farbe,
                inventar.preis,
                inventar.typ,
                inventar_typ.typ AS typ_name,
                
                raum_inventar.menge,
                raum_inventar.mengen_mass
                FROM 
                    raum_inventar
                LEFT JOIN
                    inventar ON raum_inventar.inventar_id = inventar.id
                LEFT JOIN
                    inventar_typ ON inventar.typ = inventar_typ.id
                WHERE 
                    raum_inventar.raum_id = ".$id;
    $result = $conn->query($sql);
    $conn->close();
    if ($result->num_rows > 0) {
        echo '<h3>Der Raum enthält:</h3>';
        while ($row = $result->fetch_assoc()) {
            // foreach ($row as $key => $value) {
            //     if ($key == "id") {
            //         echo '<br>';
            //     }
            //     echo $key .": ". $value ."<br>";
            // }
            echo '<br><p><strong>'.$row["typ_name"].':</strong> '.$row["menge"].' '.$row["mengen_mass"].'</p>';
            if (array_key_exists($row["typ_name"], $standard)) {
                echo '<p><em>Standard: '.$standard[$row["typ_name"]].'</em></p>';
                if ($row["menge"] > $standard[$row["typ_name"]]) {
                    echo '<p style="color: yellowgreen">Vorhandenes Inventar ist größer als der Standard.</p>';
                    unset($standard[$row["typ_name"]]);
                }
                elseif ($row["menge"] < $standard[$row["typ_name"]]) {
                    echo '<p style="color: red">Vorhandenes Inventar ist kleiner als der Standard.</p>';
                    $standard[$row["typ_name"]] -= $row["menge"];
                }
                else {
                    echo '<p style="color: green">Vorhandenes Inventar entspricht dem Standard.</p>';
                    unset($standard[$row["typ_name"]]);
                }
            }
            else {
                echo '<p style="color: yellow">Kein Standard definiert.</p>';
            }
        }
    }
    else {
        echo '<br><p style="color: yellow">Kein Inventar gefunden.</p><br>';
    }
    echo '</div>';
    if (count($standard) > 0) {
        // check if angebot already exists
        $sql = "SELECT 
                    angebot.*,
                    angebot_inventar.inventar_id,
                    angebot_inventar.anzahl,
                    inventar.produkt,
                    inventar.hersteller,
                    inventar.farbe,
                    inventar.preis
                FROM 
                    angebot
                LEFT JOIN
                    angebot_inventar
                    ON angebot.id = angebot_inventar.angebot_id
                LEFT JOIN
                    inventar
                    ON angebot_inventar.inventar_id = inventar.id
                WHERE 
                    angebot.raum_id = ".$id;
        $conn = connect();
        $result = $conn->query($sql);
        $num_angebot = $result->num_rows;
        if ($num_angebot > 0) {
            $once = TRUE;
            while ($angebot = $result->fetch_assoc()) {
                if ($once) {
                    echo '<div style="margin: 20px; padding: 20px; overflow-x: scroll; max-width: 80vw; background-color: rgb(20, 6, 32); box-shadow: 0 4px 8px 0 rgba(177, 177, 177, 0.2), 2px -6px 20px 0 rgba(189, 149, 199, 0.399); border-radius: 20px;"><h3>Ein Angebot wurde bereits erstellt:</h3>';
                    $once = FALSE;
                }
                $num_angebot--;
                echo '<p>'.$angebot['anzahl'].'x '.$angebot['produkt'].' ('.$angebot['hersteller'].') - '.$angebot['farbe'].', Einzelpreis '.$angebot['preis'].'€</p>';
                if ($num_angebot == 0) {
                    echo '<p><strong>Gesamtpreis: '.$angebot['gesamt_preis'].'€ </strong></p>';
                    // form for updating the erledigt field
                    echo '<form action="angebot_erledigen.php?id='.$angebot['id'].'" method="post">';
                    echo '<label for="erledigt">Status:</label>';
                    echo '<select name="erledigt" id="erledigt">';
                    echo '<option value="0"'.($angebot['erledigt'] == 0 ? ' selected' : '').'>in Arbeit</option>';
                    echo '<option value="1"'.($angebot['erledigt'] == 1 ? ' selected' : '').'>erledigt</option>';
                    echo '<option value="2" style="color: red;">löschen</p></option>';
                    echo '</select>';
                    echo '<input type="hidden" name="raum_id" value="'.$id.'">';
                    echo '<button type="submit" id="submit" hidden>Update</button>';
                    echo '</form></div>';
                }
            }
        }
        else {
            echo '<div><h3 style="color: red;">Für unseren Standard fehlt:</h3>';
            $linkbutton = '<button><a href="add_angebot.html?raum_id='.$id;
            foreach ($standard as $key => $value) {
                echo '<p><strong>'.$key.':</strong> '.$value.'x</p>';
                $linkbutton .= '&'.$key.'='.$value;
            }
            $linkbutton .='">Angebot aufgeben</a></button></div>';
            echo $linkbutton;
        }
        $conn->close();
    }
    echo '</div>';
}  
?>