<?php
    session_start();
    if ($_SESSION['logged_on_user'] == '')
        header("Location: login.php");
?>
<HTML>
<HEAD>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" media="screen" href="css/global.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/montage.css" />
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
    <div class="photo">
        <div class="camera">
    <?php if (isset($_GET['name']) && $_GET['name'] != "") {
        ?>  <img id="uploaded" src="pictures/<?php echo $_GET['name'];?>">
    <?php } else { ?>
          <video autoplay="true" id="videoElement"></video>
    <?php } ?>
        </div>
        <div class="stickers">
            <form action="/utils/post.php" method="post">
            <input type="submit" value="<?php echo $_GET['name']; ?>" name="submit">
            </form>
        </div>
        <form action="utils/upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="pic" accept="image/*">
            <input type="submit" value="Upload image" name="submit">
        </form>
    </div>
    <div class="pastpics">
    </div>
</div>
<footer class ="foot">
    <p class="name">© tboissel, 42, 2019</p>
</footer>
</BODY>

<script async src="js/camera.js"></script>
</HTML>