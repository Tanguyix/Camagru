<?php
    session_start();
    require 'config/setup.php';
     //connect to DB
     try {
        $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $ex) { exit($ex); };

    //get all the photos infos
    $sql = "SELECT photos.user_id, photos.image, photos.legend, users.id, users.name
    FROM photos
    JOIN users ON photos.user_id=users.id
    WHERE users.id < 10000;";
    $check = $pdo->prepare($sql);
    $check->execute();
    $photos = $check->fetchAll();
?>
<HTML>

<HEAD>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/gallery.css">
    <link rel="icon" type="image/png" href="img/Blason_Camargue.png">
    <TITLE>Camargue'U</TITLE>
</HEAD>


<BODY>
    <div class="wrapper">
        <div class="head">
            <a href="index.php"><img class="logo" src="img/Camargue_U.png"></a>
            <a class="montage" href="montage.php">Create</a>
        <?php if ($_SESSION['logged_on_user'] == "") { ?>
            <a class="login" href="login.php">Sign in</a>
            <a class="signup" href="create_account.php">Sign up</a>
        <?php }
         else { ?>
            <form id="logout" action="utils/logout.php" method="get">
                <input class="send" type="submit" name="submit" value="Logout">
            </form>
            <a class="login" href="account.php">My account</a>
        <?php } ?>
        </div>
        <?php foreach ($photos as $pic) {?>
            <div> 
                <p class="creator"> <?php echo $pic['name']; ?> </p>
                <img class="photos" src="<?php echo "pictures/" . $pic['image']; ?>"> </div>
        <?php } ?>
    </div>
    <footer class ="foot">
        <p class="name">© tboissel, 42, 2019</p>
    </footer>
</BODY>
</HTML>