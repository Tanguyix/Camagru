<?php
    session_start();
    require("resize_image.php");
    require_once "../config/database.php";
    if ($_SESSION['logged_on_user'] == "")
        header("Location: ../index.php");
    list($width, $height) = getimagesize($_POST['sticker']);
    $sticker = resize_image_png($_POST['sticker'], $_POST['stick_width'], ($height * $_POST['stick_width']) / $width, true);

    if ($_POST['pic'] == "" || $_POST['sticker'] == "") {exit;}
    if (strstr($_POST['pic'], "base64")) {
        $im_str = base64_decode(str_replace(" ", "+", str_replace("data:image/png;base64,", "", $_POST['pic'])));
        $im = imagecreatefromstring($im_str);
        imageflip($im, IMG_FLIP_HORIZONTAL);
    }
    else {
        $infos = getimagesize($_POST['pic']);
        if ($infos['mime'] == "image/png") {
            $im = resize_image_png($_POST['pic'], 640, 480);
        } else if ($infos['mime'] == "image/jpeg") {
            $im = resize_image_jpeg($_POST['pic'], 640, 480);
        } else {
            echo "img_problem";
            return;
        }
    }

    //get the size of the sticker
    $marge_left = intval($_POST['stick_left']);
    $marge_top = intval($_POST['stick_top']);
    $sx = imagesx($sticker);
    $sy = imagesy($sticker);

    try {
        $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $ex) { exit($ex); };

    $timestamp = time();
    imagecopy($im, $sticker, $marge_left, $marge_top, 0, 0, $sx, $sy);
    imagepng($im, "../pictures/$timestamp.png", 0);

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
        $pdo->prepare($sql)->execute(array($_SESSION['id'], "$timestamp.png", $_POST['legend']));



    //SElect max id to get the id for the new pic
    try {
        $sql = "SELECT MAX(id) FROM photos;";
        $check = $pdo->prepare($sql);
        $check->execute();
        $max = $check->fetch();
    } catch(PDOException $ex) { exit($ex); };

    echo "<img class='pho' id='$max[0]' onclick='erase_pic($max[0])' src='pictures/$timestamp.png'>";