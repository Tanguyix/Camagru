<?php
    session_start();
    require 'config/setup.php';
    if (!isset($_GET['uid'])) {header("Location: index.php");}
    //connect to DB
     try {
        $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);

    //get all the photos infos
    $sql = "SELECT users.name, users.profile_pic, COUNT(photos.id) as nb_photos
            FROM users
            INNER JOIN photos ON photos.user_id = users.id
            WHERE users.id = ?;";
    $check = $pdo->prepare($sql);
    $check->execute(array($_GET['uid']));
    $infos = $check->fetch();
    if ($infos['name'] == NULL)
        header("Location: index.php");

    //get nb of likes
    $sql = "SELECT likes.id, photos.user_id FROM likes
          INNER JOIN photos ON photos.id = likes.photos_id
          WHERE photos.user_id = ?;";
    $check = $pdo->prepare($sql);
    $check->execute(array($_GET['uid']));
    $likes = $check->fetchAll();
    $nb_likes = count($likes);

    //get nb of comments
    $sql = "SELECT comments.id, photos.user_id FROM comments
              INNER JOIN photos ON photos.id = comments.photos_id
              WHERE photos.user_id = ?;";
    $check = $pdo->prepare($sql);
    $check->execute(array($_GET['uid']));
    $comments = $check->fetchAll();
    $nb_coms = count($comments);


//get photos infos
    $sql = "SELECT photos.user_id, photos.id AS pid, photos.image, photos.legend, users.id, users.name
    FROM photos
    JOIN users ON photos.user_id=users.id
    WHERE users.id = ?;";
    $check = $pdo->prepare($sql);
    $check->execute(array($_GET['uid']));
    $photos = $check->fetchAll();
     } catch(PDOException $ex) { exit($ex); };

    foreach ($photos as $key => $pic) {
        try {
            $sql = "SELECT photos_id, user_id FROM likes
                    WHERE photos_id = ?;";
            $check = $pdo->prepare($sql);
            $check->execute(array($pic['pid']));
            //Put the number of likes for each pic
            $photos[$key]['likes'] = $check->rowCount();

            $likes = $check->fetchAll();
        } catch(PDOException $ex) { exit($ex); };
        $photos[$key]['liked'] = false;
        foreach ($likes as $like) {
            if ($like['user_id'] == $_SESSION['id']) { $photos[$key]['liked'] = true;}
        }
    }
?>
<HTML>

<HEAD>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/gallery.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="icon" type="image/png" href="img/Blason_Camargue.png">
    <TITLE>Camargue'U</TITLE>
</HEAD>

<BODY>
    <div class="wrapper">
        <div class="head">
            <a href="index.php"><img class="logo" src="img/Camargue_U.png"></a>
            <a class="montage" href="montage.php">Post</a>
            <a class="leaderboard" href="leaderboard.php?order=alpha">Leaderboard</a>
        <?php if ($_SESSION['logged_on_user'] == "") { ?>
            <a class="login" href="login.php">Sign in</a>
            <a class="signup" href="create_account.php">Sign up</a>
        <?php }
         else { ?>
            <a class="Logout" href="utils/logout.php">Logout</a>
            <a class="login" href="account.php">My account</a>
        <?php } ?>
        </div>
        <div class="center">
            <div class="infos">
                <p><?php echo $infos['name'];?></p>
                <img id="bigger_pic" src="<?php echo $infos['profile_pic'];?>">
                <p><?php echo $infos['nb_photos'] . " photo" . ($infos['nb_photos'] > 1 ? "s " : " ") . "published";?></p>
                <p><?php echo $nb_likes . " like" . ($nb_likes > 1 ? "s " : " ") . "on your photos";?></p>
                <p><?php echo $nb_coms . " comment" . ($nb_coms > 1 ? "s " : " ") . "on your photos";?></p>
            </div>
            <div class="gallery">
                <?php foreach ($photos as $key => $pic) { ?>
                <div class="post">
                        <img class="photos" src="<?php echo "pictures/" . $pic['image']; ?>">
                </div>
           <?php } ?>
            </div>
        </div>
        <footer><p>Camagru, projet ecole 42</p>by Tanguy BOISSEL-DALLIER</footer>
    </div>
</BODY>
<script async src="js/comment.js"></script>
<script async src="js/like.js"></script>
</HTML>