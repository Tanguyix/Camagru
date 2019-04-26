<?php
    require "../config/database.php";
    session_start();

    try {
        $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $ex) { exit($ex); };

    if (isset($_POST['id'])) {
            //get photo_name and delete it from local files
            try {
                $sql = "SELECT image, user_id FROM photos
                    WHERE id =:id;";
                $check = $pdo->prepare($sql);
                $check->execute(array(':id' => $_POST['id']));
                $photo_path = $check->fetch();

                if (!$photo_path || ($photo_path['user_id'] != $_SESSION['id']))

                    unlink("../pictures/" . $photo_path['image']);

                //delete photo
                $sql = "DELETE FROM `photos`
                    WHERE `id` =:id;";
                $pdo->prepare($sql)->execute(array(':id' => $_POST['id']));
                //delete likes for this photo
                $sql = "DELETE FROM `likes`
                    WHERE `photos_id` =:id;";
                $pdo->prepare($sql)->execute(array(':id' => $_POST['id']));
                //delete comments for this photo
                $sql = "DELETE FROM `comments`
                    WHERE `photos_id` =:id;";
                $pdo->prepare($sql)->execute(array(':id' => $_POST['id']));
            } catch (PDOException $ex) {
                exit($ex);
            };
    }