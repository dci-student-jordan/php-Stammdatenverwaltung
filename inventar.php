<?php
if ($_SESSION['is_authenticated'] != true) {
    header('Location: login.html');
    exit();
} else {
    include_once 'menu.php';
    echo build_menu('inventar.html');

    include_once 'messages.php';
    include_once 'connection.php';

    $sql = "SELECT inventar.*, inventar_typ.typ, COUNT(raum_inventar.inventar_id) AS anzahl
        FROM inventar 
        JOIN inventar_typ ON inventar.typ = inventar_typ.id
        LEFT JOIN raum_inventar ON inventar.id = raum_inventar.inventar_id
        GROUP BY inventar.id";
    $conn = connect();
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<h1>Inventar</h1>';
        echo '<table border="1" id="inventar-table" class="display"><thead>';
        echo '<tr><th>Verbaut</th><th>Typ</th><th>Hersteller</th><th>Produkt</th><th>Farbe</th><th>Preis</th><th>Löschen</th></tr></thead><tbody>';

        while ($row = $result->fetch_assoc()) {
            $verbaut = $row['anzahl'];
            $typ = $row['typ'];
            $hersteller = $row['hersteller'];
            $produkt = $row['produkt'];
            $farbe = $row['farbe'];
            $preis = $row['preis'];

            echo '<tr>';
            echo '<td>' . $verbaut . ' mal</td>';
            echo '<td>' . $typ . '</td>';
            echo '<td>' . $hersteller . '</td>';
            echo '<td>' . $produkt . '</td>';
            echo '<td>' . $farbe . '</td>';
            echo '<td>' . $preis . ' €</td>';
            echo '<td><form action="delete_inventar.php?id=' . $row['id'] . '" method="post"><button type="submit"  style="color:red; background-color:white;" id="delete_inventar">X</button></form>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<br>Kein Inventar gefunden.<br>';
    }

    echo '<br>
    <div style="display: flex; justify-content: space-between;">
        <button>
            <a href="inventar_typ.html">Inventar-Typen</a>
        </button>
        <button style="margin-left:20px">
            <a href="add_inventar.html">Inventar hinzufügen</a>
        </button>
    </div>';

    $conn->close();
}
?>
