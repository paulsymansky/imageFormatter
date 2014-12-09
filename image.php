<?php
// Paul Symansky, (c) 2010
// Created Nov. 21, 2010
// Last modified Nov. 21, 2010
//
// Place this file in the root directory of your image file
// Use:
// 	Add a border to an image
//		image.php5?q=dir/picture.jpg
//	Change the border width
//		image.php5?q=dir/picture.jpg&w=5
//	Resize the image
//		image.php5?q=dir/picture.jpg&x=100
//	Resize the image
//		image.php5?q=dir/picture.jpg&x=100&y=100

// Default border width
define("BORDERWIDTH", 3);

// Get image information
$raw_image = $_GET['q'];
$info = pathinfo($raw_image);

// Get border width
$bwidth = BORDERWIDTH;
if(isset($_GET['w'])){
	$bwidth = (int)$_GET['w'];
	if($bwidth < 0)
		$bwidth = 1;
}

// Make sure file exists and is an image
if(!file_exists($raw_image) || !($info['extension'] == "jpeg" || $info['extension'] == "jpg" || $info['extension'] == "gif" || $info['extension'] == "png")){
	die("File doesn't exist!");
}

// Let browser know what to expect
header ("Content-type: image/" . $info['extension']);

// Resize if necessary
list($orig_width, $orig_height) = getimagesize($raw_image);
$width = $orig_width;
$height = $orig_height;
if(isset($_GET['x']) && !isset($_GET['y'])){
	$width = (int)abs($_GET['x']);
	$height = $orig_height*($width/$orig_width);
}else if(isset($_GET['x']) && isset($_GET['y'])){
	$width = (int)abs($_GET['x']);
	$height = (int)abs($_GET['y']);
}

// Create image resource
switch($info['extension']){
    case "jpeg":
	case "jpg":
        $tmp = imagecreatefromjpeg($raw_image);
        break;
    case "gif":
        $tmp = imagecreatefromgif($raw_image);
        break;
    case "png":
        $tmp = imagecreatefrompng($raw_image);
        break;
}
$im = imagecreatetruecolor($width, $height);
imagecopyresized ($im, $tmp, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);
imagedestroy($tmp);

// Define our border's colors
$border_thin = imagecolorallocate($im, 0, 0, 0);
$border_thick = imagecolorallocate($im, 255, 255, 255);

// Create first outline
imagerectangle ($im, 0, 0, imagesx($im)-1, imagesy($im)-1, $border_thin);
imagerectangle ($im, $bwidth, $bwidth, imagesx($im)-$bwidth-1, imagesy($im)-$bwidth-1, $border_thin);

// Fill the border
for($i=1; $i<$bwidth ; ++$i){
	imagerectangle ($im, $i, $i, imagesx($im)-$i-1, imagesy($im)-$i-1, $border_thick);
}

// Output iamge
switch($info['extension']){
    case "jpeg":
	case "jpg":
		imagejpeg($im);
        break;
    case "gif":
		imagegif($im);
        break;
    case "png":
        imagepng($im);
        break;
}
?>