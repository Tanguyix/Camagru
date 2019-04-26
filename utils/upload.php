<?php
$target_dir = "../pictures/";
$target_file = $target_dir . basename($_FILES["pic"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    if (preg_match("/ +/", $_FILES["pic"]["name"]))  {
        header("Location: ../montage.php?error=error_file");
        return;}
    $check = getimagesize($_FILES["pic"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
    if ($uploadOk == 0) {
        header("Location: ../montage.php?error=error_file");
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["pic"]["tmp_name"], $target_file)) {
            echo "The file ". basename( $_FILES["pic"]["name"]). " has been uploaded.";
            header("Location: ../montage.php?name=" . $_FILES["pic"]["name"]);
        } else {
            header("Location: ../montage.php?error=error_file");
        }
    }
}
