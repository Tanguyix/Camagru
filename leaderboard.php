<?php
    session_start();
    require 'config/setup.php';
    require 'utils/sortfunctions.php';

    try {
            $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT id from users
                WHERE id > 0";
            $check = $pdo->prepare($sql);
            $check->execute(array());
            $users = $check->fetchAll();

            $infos = [];
        foreach ($users as $user) {
            $key = $user['id'];
            $infos[$key] = [];
            $sql = "SELECT users.name, users.profile_pic, COUNT(photos.id) as nb_photos
                FROM users
                INNER JOIN photos ON photos.user_id = users.id
                WHERE users.id = ?;";
            $check = $pdo->prepare($sql);
            $check->execute(array($key));
            $infos[$key]['photo_infos'] = $check->fetch();

            //get nb of likes
            $sql = "SELECT likes.id, photos.user_id FROM likes
              INNER JOIN photos ON photos.id = likes.photos_id
              WHERE photos.user_id = ?
              AND likes.user_id != ?;";
            $check = $pdo->prepare($sql);
            $check->execute(array($key, $key));
            $likes = $check->fetchAll();
            $infos[$key]['nb_likes'] = count($likes);

            //get nb of comments
            $sql = "SELECT comments.id, photos.user_id FROM comments
                  INNER JOIN photos ON photos.id = comments.photos_id
                  WHERE photos.user_id = ? 
                  AND comments.user_id != ?;";
            $check = $pdo->prepare($sql);
            $check->execute(array($key, $key));
            $comments = $check->fetchAll();
            $infos[$key]['nb_coms'] = count($comments);
            $infos[$key]['id'] = $key;
        }
    } catch(PDOException $ex) { exit($ex); };

if (isset($_GET['order']) && $_GET['order'] == 'alpha') {
        usort($infos, "sortalpha");
    }
    else if (isset($_GET['order']) && $_GET['order'] == 'nb_pic') {
        usort($infos, "sortbypics");
    }
    else if (isset($_GET['order']) && $_GET['order'] == 'nb_coms') {
        usort($infos, "sortbycoms");
    }
    else {
        usort($infos, "sortbylikes");
    }
    ?>
<HTML>

<HEAD>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/leaderboard.css">
    <link rel="icon" type="image/png" href="img/Blason_Camargue.png">
    <TITLE>Camargue'U</TITLE>
</HEAD>

<BODY>
    <div class="wrapper">
        <div class="head">
            <a href="index.php"><img class="logo" src="img/Camargue_U.png"></a>
            <a class="montage" href="montage.php">Post</a>
            <a class="leaderboard" href="leaderboard.php">Leaderboard</a>
        <?php if ($_SESSION['logged_on_user'] == "") { ?>
            <a class="login" href="login.php">Sign in</a>
            <a class="signup" href="create_account.php">Sign up</a>
        <?php }
         else { ?>
            <a class="Logout" href="utils/logout.php">Logout</a>
            <a class="login" href="account.php">My account</a>
        <?php } $i = 1;?>
        </div>
        <div class="leaderboard_wrap">
            <table class="lb">
                    <tr class="header_table">
                        <th>Pos</th>
                        <th>Profile_pic</th>
                        <th><a <?php if (isset($_GET['order']) && $_GET['order'] == 'alpha') {echo "class='selected'";} ?> href="leaderboard.php?order=alpha">Login</a></th>
                        <th><a <?php if (isset($_GET['order']) && $_GET['order'] == 'nb_pic') {echo "class='selected'";} ?>href="leaderboard.php?order=nb_pic">Nb photos</a></th>
                        <th><a <?php if (isset($_GET['order']) && $_GET['order'] == 'nb_coms') {echo "class='selected'";} ?>href="leaderboard.php?order=nb_coms">Nb comments on photos</a></th>
                        <th><a <?php if (!isset($_GET['order']) || $_GET['order'] == '') {echo "class='selected'";} ?>href="leaderboard.php">Nb likes on photos</th>
                    </tr>
                <?php foreach ($infos as $pers) { ?>
                    <tr>
                        <th><?php echo $i; $i++;?></th>
                        <th><img class="minipp" src="<?php echo $pers['photo_infos']['profile_pic'];?>"></th>
                        <th><a href="profile.php?uid=<?php echo $pers['id'];?>"><?php echo $pers['photo_infos']['name'] ?></a></th>
                        <th><?php echo $pers['photo_infos']['nb_photos'];?></th>
                        <th><?php echo $pers['nb_coms'];?></th>
                        <th><?php echo $pers['nb_likes'];?></th>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</BODY>
<script async src="js/comment.js"></script>
<script async src="js/like.js"></script>
</HTML>