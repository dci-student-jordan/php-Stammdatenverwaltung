<?php
if (isset($_SESSION['warning'])) {
    echo '<h3 style="color:red;">'.$_SESSION['warning'].'</h3>';
    unset($_SESSION['warning']);
}
elseif (isset($_SESSION['success'])) {
    echo '<h3 style="color:green;">'.$_SESSION['success'].'</h3>';
    unset($_SESSION['success']);
}
?>