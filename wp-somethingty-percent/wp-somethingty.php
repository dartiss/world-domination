<?php

header("Content-type: image/png");
$im = imagecreate(500,500);
$white = imagecolorallocate($im, 255,255,255);
$black = imagecolorallocate($im, 0,0,0);

$cx = 250; //xorigin
$cy = 250; //yorigin
$cr = 155; //radius
$sp = "   ";

if (isset($_REQUEST["hi"])) {
    $text = $sp . strtoupper($_REQUEST["hi"]) . $sp;
} else {
    $text = $sp . 'ARC WORDS' . $sp;
}

$length = strlen($text);
$degDelta = 185 / $length;

if ($length > 0) {

    $color = $black;

    for ($x = 0; $x < $length; $x++) {

        $AX = $cx - cos(deg2rad($degDelta * $x)) * $cr;
        $AY = $cy - sin(deg2rad($degDelta * $x)) * $cr;

    imagettftext($im, 25, -($degDelta * $x + $degDelta / 2)+90 , $AX, $AY, $color, 'Inconsolata.ttf', $text[$x]);

}
}

if (isset($_REQUEST["lo"])) {
    $text = $sp . strtoupper($_REQUEST["lo"]) . $sp;
} else {
    $text = $sp . 'ARC WORDS' . $sp;
}

$cr = $cr + 20; //radius override for font height

$length = strlen($text);
$degDelta = -185 / $length;

if ($length > 0) {

    $color = $black;

    for ($x = 0; $x < $length; $x++) {

        $AX = $cx - cos(deg2rad($degDelta * $x)) * $cr;
        $AY = $cy - sin(deg2rad($degDelta * $x)) * $cr;

    imagettftext($im, 25, -($degDelta * $x + $degDelta / 2)+270 , $AX, $AY, $color, 'Inconsolata.ttf', $text[$x]);

}
}

//draw W

imagettftext($im, 300, 0, 94, 408, $black, 'wp.ttf', 'w');

imagepng($im);
imagedestroy($im);

?>





