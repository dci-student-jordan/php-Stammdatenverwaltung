<?php

// function to offer registration form, optionally with values
function register_form($user = "", $name = "", $nachname = "") {
    return '<h1>Benutzer hinzufügen</h1>
    <form action="register.html" method="post">
        <label for="user">Username:</label><br>
        <input type="text" id="user" name="user" placeholder="benutzer@email.addresse" value="'.$user.'"><br>
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" placeholder="Max" value="'.$name.'"><br>
        <label for="nachname">Nachname:</label><br>
        <input type="text" id="nachname" name="nachname" placeholder="Mustermann" value="'.$nachname.'"><br>
        <label for="pw1">Password:</label><br>
        <input type="password" id="pw1" name="pw1"><br>
        <label for="pw2">Password wiederholen:</label><br>
        <input type="password" id="pw2" name="pw2"><br><br>
        <button type="submit">Register</button>
    </form>';
}


if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != true) {
    echo 'Sie haben keine Berechtigung für diese Seite.';
    exit();
}
else {
    include_once 'menu.php';
    echo build_menu();
    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = $_POST['user'];
        $name = $_POST['name'];
        $nachname = $_POST['nachname'];
        $pw1 = $_POST['pw1'];
        $pw2 = $_POST['pw2'];
    
        // check if all fields are filled
        if (!($user AND $name AND $nachname AND $pw1 AND $pw2)) {
            echo '<h3 style="color:red;">Bitte alle Felder ausfüllen...</h3>';
            echo register_form($user, $name, $nachname);
        }
    
        // check if user field is an email and this email is valid
        elseif (!filter_var($user, FILTER_VALIDATE_EMAIL)) {
            echo '<h3 style="color:red;">Bitte eine echte email-Adresse als Username eintragen...</h3>';
            echo register_form($user, $name, $nachname);
        }
    
        // check if passwords match
        elseif (!($pw1 == $pw2)) {
            echo '<h3 style="color:red;">Passwwörter stimmen nicht überein!</h3>';
            echo register_form($user, $name, $nachname);
        }
        else {
            // Create connection
            include 'connection.php';
            $conn = connect();
    
            // Check if user already exists
            $sql = "SELECT * FROM user WHERE user = '$user'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo '<h3 style="color:red;">Diesen Benutzer gibt es schon.</h3>';
                echo register_form($user, $name, $nachname);
            }
            else {
                // hash the password
                $pw1 = password_hash($pw1, PASSWORD_DEFAULT);
                // Insert new user
                $sql = "INSERT INTO user (user, vorname, nachname, pw) VALUES ('$user', '$name', '$nachname', '$pw1')";
                if ($conn->query($sql) === TRUE) {
                    $_SESSION['success'] = 'User successfully added';
                    header('Location: users.html');
                } else {
                    $_SESSION['success'] = 'Error: ' . $sql . ': ' . $conn->error;
                    header('Location: users.html');
                }
            }
            $conn->close();
        }
    }
    // If form is not submitted, show form
    else {
        echo register_form();
    }
}

?>