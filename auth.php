<?php 
    session_start();
    if (!isset($_SESSION['user'])) {
        header('Location: ./index.html');
        die();
    }
    $user = &$_SESSION['user'];
    $user_id= "SELECT user_id FROM `users` WHERE user_name= $user";

?>