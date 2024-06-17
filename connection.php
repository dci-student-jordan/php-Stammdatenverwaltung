<?php
if (!isset($_SESSION['is_authenticated']) && basename($_SERVER['PHP_SELF']) != 'login.html') {
    header('Location: login.html');
} else {
    if (!function_exists('connect')) {
        function connect()
        {
            $servername = "db";
            $username = "root";
            $password = "root";
            $dbname = "wohnungen";
            $conn = new mysqli($servername, $username, $password, $dbname);
            $conn->set_charset("utf8mb4");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            return $conn;
        }
    }
}
?>