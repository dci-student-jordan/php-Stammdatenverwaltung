<?php

if (!isset($_SESSION['is_authenticated']) && basename($_SERVER['PHP_SELF']) != 'login.html') {
    header('Location: login.html');
} else {
    if (!function_exists('connect')) {
        function connect()
        {
            $servername = "localhost";
            $username = "root";
            $dbname = "wohnungen";
            $conn = new mysqli($servername, $username, "", $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            return $conn;
        }
    }
}

?>