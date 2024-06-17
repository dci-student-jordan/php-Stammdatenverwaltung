<?php
if (!$_SESSION['is_authenticated']) {
    header('Location: login.html');
}
else {
    include_once 'menu.php';
    echo build_menu();
    echo '<h1>Inventar-Typen</h1>';
    include_once 'messages.php';
    include_once 'connection.php';
    $conn = connect();
    $sql = "SELECT * FROM inventar_typ";
    $result = $conn->query($sql);
    $conn->close();
    echo '<div style="display: flex; flex-direction: column; align-items: end">';
    if ($result->num_rows > 0) {
        echo '<table border="1" style="min-width: 350px; margin-bottom: 20px;"><thead>';
        echo '<tr><th>Typ</th><th>Löschen</th></tr></thead><tbody>';
        while($row = $result->fetch_assoc()) {
            $delete = '<form action="delete_inventar_type.php?id='.$row['id'].'" method="post"><button type="submit" style="color:red; background-color:white;" id="delete_inventar_type">X</button></form>';
            echo '<tr><td style="padding:5px;">' .$row["typ"].'</td><td style="padding:5px">' .$delete .'</td></tr>';
        }
        echo '</tbody></table>';
    }
    else {
        echo '<p>Keine Inventar-typen vorhanden.</p>';
    }
    echo '<form action="add_inventar_typ.php" method="post">
        <input type="text" name="typ" placeholder="Elektrizität">
        <button type="submit">Inventar-Typ hinzufügen</button>
    </form></div>';
}

?>