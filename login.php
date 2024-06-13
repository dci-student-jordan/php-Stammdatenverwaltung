<?php

include_once 'connection.php';

if (!isset($_SESSION)) {
    session_start();
}

function login_form($user = "") {
    return '<h1>Bitte einloggen:</h1>
    <form action="login.html" method="post">
        <label for="user">Email-Addresse:</label><br>
        <input type="text" id="user" name="user" placeholder="your@email.address" value="'.$user.'"><br>
        <label for="pw">Password:</label><br>
        <input type="password" id="pw" name="pw"><br><br>
        <button type="submit">Login</button>
    </form>';
}

// if user logged in, redirect to wohnungen
if (isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated']) {
    header('Location: wohnungen.html');
}
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['user'];
    $pw = $_POST['pw'];

    // check if all fields are filled
    if (!($user AND $pw)) {
        echo '<h3 style="color:red;">Bitte alle Felder ausfüllen...</h3>';
        echo login_form($user);
        exit();
    }

    $conn = connect();

    //login
    $sql = "SELECT * FROM user WHERE user = '$user'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pw, $row['pw'])) {
            $_SESSION['username'] = $row['vorname'];
            $_SESSION['is_authenticated'] = true;
            if ($row['is_admin'] == 1) {
                $_SESSION['is_admin'] = true;
            }
            header('Location: wohnungen.html');
        }
        else {
            echo '<h3 style="color:red;">Login failed here!</h3>';
            echo login_form($user);
        }
    }
    else {
        echo '<h3 style="color:red;">Login failed!</h3><br><em>Please log in with your email adress.</em>';
        echo login_form($user);
    }
    $conn->close();
}
else {
    echo login_form();
}
?>