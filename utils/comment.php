<?php
    require '../config/database.php';
    session_start();

    if ($_SESSION['logged_on_user'] == "") {echo "redirect"; exit;}
    //connect to DB
    try {
        $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $ex) { exit($ex); };

    if (isset($_POST['comment']) && isset($_POST['id'])) {
        try {
            $sql = "INSERT INTO `comments` (photos_id, user_id, comment)
              VALUES (?, ?, ?);";
             $pdo->prepare($sql)->execute(array($_POST['id'], $_SESSION['id'], $_POST['comment']));
        } catch(PDOException $ex) { exit($ex); };
    }

    //Verify if user has notif_on and send him an email if he does
    try {
        $sql = "SELECT photos.user_id, settings.notif_on, users.email, users.name
        FROM settings
        INNER JOIN photos ON settings.user_id = photos.user_id 
        INNER JOIN users ON settings.user_id = users.id
        WHERE photos.id = ?;";
        $check = $pdo->prepare($sql);
        $check->execute(array($_POST['id']));
        $res = $check->fetch();
    } catch(PDOException $ex) { exit($ex); };

    if ($res['notif_on'] == 1) {
        $message = "Hello " . $res['name'] . ", " . $_SESSION['logged_on_user'] . " has commented one of your pictures.";
        mail($res['email'], "Someone commented your picture", $message);
    }

    echo "<div id='cont'><a href='profile.php?uid=" . $_SESSION['id'] . "' id='commentator'> " . $_POST['username'] . " : </a><p class='com'>" . htmlspecialchars($_POST['comment']) . "</div>";