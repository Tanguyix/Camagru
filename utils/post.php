<?php

    require '../config/database.php';
    session_start();

    //connect to DB
    try {
        $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $ex) { exit($ex); };

     //get id from logged user
     try {
         $sql = "SELECT *
                    FROM `users` WHERE `name` = :name";
         $check = $pdo->prepare($sql);
         $check->execute(array(':name' => $_SESSION['logged_on_user']));
         $line = $check->fetch();
     } catch(PDOException $ex) { exit($ex); };
    if ($line === false)
        echo "Error, user doesn't exist";
        var_dump($_GET);
    if (isset($_POST['submit']) && $_POST['submit'] != "") {
        $sql = "INSERT INTO `photos` (user_id, image, legend)
        VALUES (?, ?, ?);";
        $pdo->prepare($sql)->execute(array($line['id'], $_POST['submit'], "on verra plus tard"));
    }

    header("Location: /index.php");