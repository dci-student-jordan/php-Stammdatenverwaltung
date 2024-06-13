<?php

session_start();
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

function update_user_form($id=-1, $vorname="", $nachname="", $user="", $is_admin="") {
    if ($id == -1) {
        return '<h3 style="color:red;">No id given</h3>';
    }
    return '<h1>Userdaten ändern</h1>
    <form action="update_user.html?id=' .$id .'" method="post">
        <label for="Vorname">Vorname:</label><br>
        <input type="text" id="vorname" name="vorname" placeholder="Max" value="'.$vorname.'"><br>
        <label for="nachname">Namchname:</label><br>
        <input type="text" id="nachname" name="nachname" placeholder="Mustermann" value="'.$nachname.'"><br>
        <label for="usernam">user:</label><br>
        <input type="text" id="user" name="user" placeholder="email@em.ail" value="'.$user.'"><br>
        <label for="is_admin">Admin:</label><br>
        <input type="checkbox" id="is_admin" name="is_admin"' .(($is_admin == 1) ? 'checked' : '') .'><br><br>
        <button type="submit" id="go">Userdaten ändern</button>
    </form>
    <br><br>
    <form action="delete_user.php?id='.$id.'" method="post" id="delete-form" style="margin-left: 20px;">
        <button id="delete">User löschen</button>
    </form>';
}

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
    echo build_menu();

    $id = $_GET['id'];
    if (!$id) {
        echo '<h3 style="color:red;">No id given</h3>';
        exit();
    }
    $fields = array();
    // check for fields in session
    if (isset($_SESSION['fields'])) {
        $fields = $_SESSION['fields'];
        unset($_SESSION['fields']);
    }
    include_once 'messages.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $vorname = $_POST['vorname'];
        $nachname = $_POST['nachname'];
        $user = $_POST['user'];
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;
        // create dictionary with all values
        $fields = array("vorname" => $vorname, "nachname" => $nachname, "user" => $user, "is_admin" => $is_admin);
    
        // remove empty fields
        if (!($vorname AND $nachname AND $user AND $is_admin)) {
            if (!count(array_filter($fields))) {
                $_SESSION['warning'] = 'Bitte füllen Sie zumindest ein Feld aus!';
                header('Location: update_user.html');
                exit();
            }
            else {
                // loop over keys to remove empy values
                foreach ($fields as $key => $value) {
                    if ($key != "is_admin" && !$value) {
                        unset($fields[$key]);
                    }
                }
            }
        }
        // include 'check_wohnung.php';
        // $verify = check_fields($fields, true);
    
        // if ($verify != "") {
        //     $_SESSION['warning'] = $verify;
        //     $_SESSION['fields'] = $fields;
        //     header('Location: update_user.html?id='.$id);
        //     exit();
        // }
    
        // Create connection
        include 'connection.php';
        $conn = connect();
    
        // update appartment
        $id = $_GET['id'];
        if ($id) {
            $sql = "UPDATE user SET " . implode(", ", array_map(function($k, $v) { return $k." = '".$v."'"; }, array_keys($fields), $fields)) ." WHERE id=" .$id;
            
            if ($conn->query($sql) === TRUE) {
                $_SESSION['success'] = 'User erfolgreich geändert!';
                header("Location: users.html");
            }
            else {
                $_SESSION['warning'] = 'Error: '.$sql.'<br>'.$conn->error;
                $_SESSION['fields'] = $fields;
                header('Location: add_update_wohnung.html?id='.$id);
            }

        }
        $conn->close();
        exit();
    // end POST
    }
    else {
        parse_str($_SERVER['QUERY_STRING'], $params);
        if ($params) {
            if (isset($params['id'])) {
                include 'connection.php';
                $id = $_GET['id'];
                if (!$fields) {
                    $sql = "SELECT * FROM user WHERE id=" .$id;
                    $conn = connect();
                    $result = $conn->query($sql);
                    $conn->close();
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $fields = array("vorname" => $row['vorname'], "nachname" => $row['nachname'], "user" => $row['user'], "is_admin" => $row['is_admin']);
                        }
                    }
                }
                echo update_user_form($id, ...$fields);
            }
        }
        else {
            echo '<h3 style="color:red;">No id given</h3>';
        }
    }
}

?>