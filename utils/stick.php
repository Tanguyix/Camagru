<?php
    session_start();
    require_once "../config/database.php";
    if ($_SESSION['logged_on_user'] == "")
        header("Location: ../index.php");

    $sticker = imagecreatefrompng("../stickers/" . $_POST['sticker']);
    $im = imagecreatefrompng("../pictures/" . $_POST['pic']);

    //get the size of the sticker
    $marge_right = 100;
    $marge_bottom = 100;
    $sx = imagesx($sticker);
    $sy = imagesy($sticker);

    imagecopy($im, $sticker, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, $sx, $sy);
    imagepng($im, "../pictures/01.png", 0);


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
    $sql = "INSERT INTO `photos` (user_id, image, legend)
        VALUES (?, ?, ?);";
        $pdo->prepare($sql)->execute(array($_SESSION['id'], "01.png", "on verra plus tard"));

    header("Location: /index.php");