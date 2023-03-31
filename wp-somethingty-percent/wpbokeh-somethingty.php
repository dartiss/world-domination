<?php
/**
 * Let's draw some circles based on an argument input
 * Then we'll spin some text round a circle, again from arg
 * The we'll use an absurdist but effective workaround so we don't have to draw the WP logo ;)
 * Let's go!
 */

// define path to cache images and base filename for them 
$path = ''; //trail a slash tho why don't ya
$basename = 'somethingty';
$defaultargument = 'pressword';

//grab url args if any 
if (isset($_REQUEST['hash'])) {
    $input = $_REQUEST['hash'];
} else {
    $input = $defaultargument;
}

$sp = "   ";

if (isset($_REQUEST["hi"])) {
    $texthi = $sp . strtoupper($_REQUEST["hi"]) . $sp;
} else {
    $texthi = '';
}

if (isset($_REQUEST["lo"])) {
    $textlo = $sp . strtoupper($_REQUEST["lo"]) . $sp;
} else {
    $textlo = '';
}

    //RENDER SOMETHING

    //let's bokeh
    
    //hash up a fixed length integer from argument
    $hashlength = 25;
    $hashynumber = substr(hexdec(substr(sha1($input), 0, 10)), 0, $hashlength);

    /**
     * reusable function to call hash string slices like a looping thru a lookup table
     * inspired by Doom's LUT mechanism to fetch repeatable pseudorandom numbers, lol
     * best of the length of the LUT and the chunks you'll lookup from it are evens vs odds
     */
    function getnum($digits)
    {
        global $hashynumber, $hashlength;
        static $hashiterator = 0;
        $hashiterator++;
        if ($hashiterator > $hashlength) {
            $hashiterator = $hashiterator - $hashlength;
        }
        return (int)(substr($hashynumber, $hashiterator, $digits));
    }

    //set up image
    $widthx = 1000;
    $heighty = 600;
    $centrex = $widthx/2;
    $centrey = $heighty/2;
    $my_img = imagecreatetruecolor(1000, 600);
 
    //background
    $givemeahue = imagecolorallocatealpha($my_img, (getnum(2)+50), (getnum(2)+50), (getnum(2)+50), 100);
    imagefilledrectangle($my_img, 0, 0, $widthx, $heighty, $givemeahue);
    imagefilledrectangle($my_img, 0, 0, $widthx, $heighty, $givemeahue);


    //bokeh
    for ($x=1; $x<=15; $x++) {
        $circletone = imagecolorallocatealpha($my_img, (getnum(2)/2+150), (getnum(2)/2+150), (getnum(2)/2+150), getnum(2)/5+100);
        $circlesize = getnum(3)/2;
        $xposit = getnum(2)*($widthx/100);
        $yposit = getnum(2)*($heighty/100);
        imagefilledellipse($my_img, $xposit, $yposit, $circlesize, $circlesize, $circletone);
    }

    //best blur GD gots
    for ($x=1; $x<=15; $x++) {
        ImageFilter($my_img, IMG_FILTER_SCATTER, 4, 6);
        imageflip($my_img, IMG_FLIP_VERTICAL);
        ImageFilter($my_img, IMG_FILTER_SCATTER, 4, 6);

        //smooth, but slowww
        ImageFilter($my_img, IMG_FILTER_GAUSSIAN_BLUR);
    }
        //a bit more just because
        for ($x=1; $x<=5; $x++) {
            ImageFilter($my_img, IMG_FILTER_GAUSSIAN_BLUR);
        }

        //add colour with background again
        imagefilledrectangle($my_img, 0, 0, $widthx, $heighty, $givemeahue);

        //bokeh again
        for ($x=1; $x<=50; $x++) {
            $circletone = imagecolorallocatealpha($my_img, (getnum(2)/2+150), (getnum(2)/2+150), (getnum(2)/2+150), (getnum(1)/2+123));
            $circlesize = getnum(3)/2;
            $xposit = getnum(2)*($widthx/100);
            $yposit = getnum(2)*($heighty/100);
            imagefilledellipse($my_img, $xposit, $yposit, $circlesize, $circlesize, $circletone);
        }

        //throw another tone over it
        imagefilledrectangle($my_img, 0, 0, $widthx, $heighty, $givemeahue);

        //filter
        ImageFilter($my_img, IMG_FILTER_BRIGHTNESS, 25);
        ImageFilter($my_img, IMG_FILTER_CONTRAST, -25);

        //TEXT DRAWING TIME

        //set up b/w
        $white = imagecolorallocatealpha($my_img, 255, 255, 255, 25);
        $black = imagecolorallocatealpha($my_img, 0, 0, 0, 100);

        //hand adjust offset from origin for logo/dingbat font use
        $logofontsize = 300;
        $fontoffsetx = 156;
        $fontoffsety = 158;

        //draw a big dingbat
        imagettftext($my_img, $logofontsize, 0, $centrex-$fontoffsetx, $centrey+$fontoffsety, $white, 'wp.ttf', 'w');


        //let's spinny some text

        //circle radius
        $cr = 155;

        //set up upper text
        $length = strlen($texthi);
        $degDelta = 185 / $length;

        if ($length > 0) {
            $textcolor = $white;
            for ($x = 0; $x < $length; $x++) {
                $AX = $centrex - cos(deg2rad($degDelta * $x)) * $cr;
                $AY = $centrey - sin(deg2rad($degDelta * $x)) * $cr;
                //draw a glyph
                imagettftext($my_img, 25, -($degDelta * $x + $degDelta / 2)+90, $AX, $AY, $textcolor, 'Inconsolata.ttf', $texthi[$x]);
            }
        }


        $cr = $cr + 20; //radius override for font height

        //set up lower text
        $length = strlen($textlo);
        $degDelta = -185 / $length;

        if ($length > 0) {
            $textcolor = $white;
            for ($x = 0; $x < $length; $x++) {
                $AX = $centrex - cos(deg2rad($degDelta * $x)) * $cr;
                $AY = $centrey - sin(deg2rad($degDelta * $x)) * $cr;
                //draw a glyph
                imagettftext($my_img, 25, -($degDelta * $x + $degDelta / 2)+270, $AX, $AY, $textcolor, 'Inconsolata.ttf', $textlo[$x]);
            }
        }


        // save & serve results

        // lol i'm now a png even tho i'm .php
        header("Content-type: image/png");

        //write image file to cachepath
        //imagepng($my_img, $path . $basename . '_' . $input . '_' . str_replace(' ', '', $texthi) . str_replace(' ', '', $textlo) . '.png');

        //serve it 
        imagepng($my_img);

        //clean up the mess
        imagecolordeallocate($givemeahue);
        imagecolordeallocate($circletone);
        imagecolordeallocate($black);
        imagecolordeallocate($white);
        imagedestroy($my_img);

?>
