<?php
session_start();

header("Content-Type: image/png");

$imageWidth = 290;
$imageHeight = 40;
$image = imagecreatetruecolor($imageWidth, $imageHeight);


$backgroundColor = imagecolorallocate($image, 255, 255, 255); 
$textColor = imagecolorallocate($image, 0, 0, 0); 

imagefill($image, 0, 0, $backgroundColor);

$number1 = rand(1, 9);
$number2 = rand(1, 9);
$captchaText = "$number1 + $number2 = ?";
$_SESSION['captcha_text'] = $number1 + $number2;

imagestring($image, 5, 10, 10, $captchaText, $textColor);

imagepng($image);
imagedestroy($image);
