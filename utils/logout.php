<?php
    session_start();
    if ($_SESSION['logged_on_user'] != "") {
        $_SESSION['logged_on_user'] = "";
        $_SESSION['id'] = 0;
        $_SESSION['email'] = "";
    }
    header("Location: ../index.php");