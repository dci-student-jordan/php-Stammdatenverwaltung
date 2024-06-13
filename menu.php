<?php

function build_menu($exclude="") {
    $contents = '<nav><div>Hallo, ' 
    .$_SESSION['username']
    .'! <button><a href="logout.php">Logout</a></button></div>
    <div class="dropdown">
        <button class="dropbtn">MENU</button>
            <div class="dropdown-content">';
    if ($exclude != "wohnungen.html") {
        $contents .= '<a href="wohnungen.html">Wohnungsübersicht</a>';
    }
    if ($exclude != "inventar.html") {
        $contents .= '<a href="inventar.html">Inventarsübersicht</a>';
    }
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] && $exclude != "users.html") {
        $contents .= '<a href="users.html">Benutzerverwaltung</a>';
    }
        
    $contents .= '</div></div></nav>';
    return $contents;
}

?>