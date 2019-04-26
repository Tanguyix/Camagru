<?php
    require "../config/database.php";
    session_start();

    if ($_SESSION['logged_on_user'] == "") {echo "redirect"; exit;}
    try {
        $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $ex) { exit($ex); };

    //Check if photo has been already liked
    try {
        $sql = "SELECT likes.id as likeid, likes.photos_id, likes.user_id, photos.id FROM likes
                INNER JOIN photos
                ON likes.photos_id = photos.id
                WHERE likes.user_id = ?
                AND photos.id = ?;";
        $check = $pdo->prepare($sql);
        $check->execute(array($_SESSION['id'], $_POST['pic_id']));
        $like = $check->fetch();
    } catch(PDOException $ex) { exit($ex); };

    try {
        if (!$like) {        //If empty, photos not liked, insert like
                $sql = "INSERT INTO likes(photos_id, user_id)
                VALUES (?, ?);";
                $check = $pdo->prepare($sql)->execute(array($_POST['pic_id'], $_SESSION['id']));
                echo "added";
            }
            else {          //Else, delete like
                $sql = "DELETE FROM likes
                WHERE id = ?;";
                $check = $pdo->prepare($sql)->execute(array($like['likeid']));
                echo "deleted";
                }
        } catch(PDOException $ex) { exit($ex); };
