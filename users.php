<?php
if ($_SESSION['is_authenticated'] != true) {
    header('Location: login.html');
    exit();
}
else {
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != true) {
        echo 'Sie haben keine Berechtigung für diese Seite.';
        exit();
    }
    include_once 'menu.php';
    echo build_menu('users.html');

    include_once 'messages.php';
    include_once 'connection.php';
    $sql = "SELECT * FROM user";
    $conn = connect();
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo '<h1>Benutzer</h1>';
        echo '<table border="1" id="user-table" class="display"><thead>';
        echo '<tr><th>Vorname</th><th>Nachname</th><th>Email (Username)</th><th>Admin</th><th></th></tr></thead><tbody>';
        while($row = $result->fetch_assoc()) {
            $vorname = $row['vorname'];
            $nachname = $row['nachname'];
            $email = $row['user'];
            $is_admin = $row['is_admin'];
            echo '<tr>';
            echo '<td>'.$vorname.'</td>';
            echo '<td>'.$nachname.'</td>';
            echo '<td>'.$email.'</td>';
            echo '<td>'.($is_admin ? 'Ja' : 'Nein').'</td>';
            echo '<td><a href="update_user.html?id='.$row['id'].'">bearbeiten</a></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }
    else {
        echo '<br>Keine Benutzer gefunden<br>';
    }
    echo '<br><button><a href="register.html">Benutzer hinzufügen</a></button>';
    $conn->close();

}
?>