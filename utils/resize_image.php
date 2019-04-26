<?php
function resize_image_png($file, $w, $h, $sticker=FALSE) {
    $w = intval($w);
    if ($h == 0) {
        $src = imagecreatefrompng($file);
        return ($src);
    }
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($w/$h > $r) {
        $newwidth = $h*$r;
        $newheight = $h;
    } else {
        $newheight = $w/$r;
        $newwidth = $w;
    }
    $src = imagecreatefrompng($file);
    imagealphablending($src, false);
    imagesavealpha($src, true);
    $color = imagecolorallocatealpha($src, 0, 0, 0, 127);
    imagefill($src, 0, 0, $color);
    $dst = imagecreatetruecolor(intval($newwidth), intval($newheight));
    if ($sticker) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }
    imagecopyresampled($dst, $src, 0, 0, 0, 0, intval($newwidth), intval($newheight), $width, $height);
    return $dst;
}

function resize_image_jpeg($file, $w, $h, $crop=FALSE) {
    $w = intval($w);
    if ($h == 0) {
        $src = imagecreatefrompng($file);
        return ($src);
    }
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    $src = imagecreatefromjpeg($file);
    $color = imagecolorallocatealpha($src, 0, 0, 0, 127);
    imagefill($src, 0, 0, $color);
    $dst = imagecreatetruecolor(intval($newwidth), intval($newheight));
    imagecopyresampled($dst, $src, 0, 0, 0, 0, intval($newwidth), intval($newheight), $width, $height);
    return $dst;
}
