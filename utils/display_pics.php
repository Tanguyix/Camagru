<?php

session_start();
require '../config/setup.php';
//connect to DB
try {
    $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
} catch(PDOException $ex) { exit($ex); };

if (isset($_POST['start'])) {
//get all the photos infos
    try {
        $sql = "SELECT photos.user_id, photos.id AS pid, photos.image, photos.legend, users.id, users.name, users.profile_pic
        FROM photos
        JOIN users ON photos.user_id=users.id
        WHERE users.id > 0
        ORDER BY pid DESC;";
        $check = $pdo->prepare($sql);
        $check->execute();
        $photos = $check->fetchAll();
    } catch(PDOException $ex) { exit($ex); };


    foreach ($photos as $key => $pic) {
        if ($key >= intval($_POST['start']) && $key < intval($_POST['start']) + 5) {
            try {
                $sql = "SELECT photos_id, user_id FROM likes
                WHERE photos_id = ?;";
                $check = $pdo->prepare($sql);
                $check->execute(array($pic['pid']));
                //Put the number of likes for each pic
                $photos[$key]['likes'] = $check->rowCount();
            } catch(PDOException $ex) { exit($ex); };

            $likes = $check->fetchAll();
            $photos[$key]['liked'] = false;
            foreach ($likes as $like) {
                if ($like['user_id'] == $_SESSION['id']) {
                    $photos[$key]['liked'] = true;
                }
            }
            try {
                $sql = "SELECT comments.comment, comments.user_id, comments.photos_id AS pid, users.id, users.name
               FROM `comments`
               JOIN users ON comments.user_id=users.id
               WHERE comments.photos_id = ?;";
                $check = $pdo->prepare($sql);
                 $check->execute(array($pic['pid']));
                 $coms = $check->fetchAll();
            } catch(PDOException $ex) { exit($ex); };
            $photos[$key]['coms'] = $coms;
            if (isset($_SESSION['logged_on_user'])) {
                $photos[$key]['lou'] = $_SESSION['logged_on_user'];
            }
        }
        else {
            unset($photos[$key]);
        }
    }
    echo json_encode($photos);
}