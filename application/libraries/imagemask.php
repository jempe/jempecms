<?php

//
// class.imagemask.php
// version 1.0.0, 19th January, 2004
//
// Description
//
// This is a class allows you to apply a mask to an image much like you could
// do in PhotoShop, Gimp, or any other such image manipulation programme.  The
// mask is converted to grayscale so it's best to use black/white patterns.
// If the mask is smaller than the image then the mask can be placed in various
// positions (top left, left, top right, left, centre, right, bottom left,
// bottom, bottom right) or the mask can be resized to the dimensions of the
// image.
//
// Requirements
//
// This class NEEDS GD 2.0.1+ (preferrably the version bundled with PHP)
//
// Notes
//
// This class has to copy an image one pixel at a time.  Please bare in mind
// that this process may take quite some time on large images, so it's probably
// best that it's used on thumbnails and smaller images.
//
// Author
//
// Andrew Collington, 2004
// php@amnuts.com, http://php.amnuts.com/
//
// Feedback
//
// There is message board at the following address:
//
//    http://php.amnuts.com/forums/index.php
//
// Please use that to post up any comments, questions, bug reports, etc.  You
// can also use the board to show off your use of the script.
//
// Support
//
// If you like this script, or any of my others, then please take a moment
// to consider giving a donation.  This will encourage me to make updates and
// create new scripts which I would make available to you.  If you would like
// to donate anything, then there is a link from my website to PayPal.
//
// Example of use
//
//    require 'class.imagemask.php';
//    $im = new imageMask('ffffff');
//    $im->maskOption(mdCENTRE);
//    if ($im->loadImage(dirname(__FILE__) . "/pictures/{$_POST['file']}"))
//    {
//        if ($im->applyMask(dirname(__FILE__) . "/masks/{$_POST['mask']}"))
//        {
//            $im->showImage('png');
//        }
//    }
//


define('mdTOPLEFT',     0);
define('mdTOP',         1);
define('mdTOPRIGHT',    2);
define('mdLEFT',        3);
define('mdCENTRE',      4);
define('mdCENTER',      4);
define('mdRIGHT',       5);
define('mdBOTTOMLEFT',  6);
define('mdBOTTOM',      7);
define('mdBOTTOMRIGHT', 8);
define('mdRESIZE',      9);


class Imagemask
{

function mask( $width , $height , $in , $mask_image , $out=false ){

	$im = imagecreatetruecolor($width, $height);
	imagesavealpha($im, true);
	$trans_colour = imagecolorallocatealpha($im, 0, 0, 0, 127);
	imagefill($im, 0, 0, $trans_colour);
  
	$file_name = explode("." ,$in );

	$extension = strtolower(end($file_name) );

	$mask = imagecreatefromjpeg  ( $mask_image );

	if( $extension == "jpg" )
		$photo = imagecreatefromjpeg  ( $in );
	else if ( $extension == "png" )
		$photo = imagecreatefrompng  ( $in );
	else if($extension == "gif")
		$photo = imagecreatefromgif  ( $in );

	if( isset( $photo ) ){

		for ($x = 0; $x < $width; $x++)
		{
			for ($y = 0; $y < $height; $y++)
			{
				$transp = $this->_pixelAlphaThreshold( $mask , $x , $y);
	
				$transp = floor((255 - $transp["r"]) / 2);
	
				$pixel_color = $this->_pixelAlphaThreshold( $photo , $x , $y) ;
				$color = imagecolorallocatealpha($im, $pixel_color["r"], $pixel_color["g"], $pixel_color["b"],$transp); 
				imagesetpixel($im, $x,$y, $color);
	
			}
		}
	
		if( $out !== false ){
			ImagePng ($im, $out);
		}else{
			header ('Content-type: image/png');
			ImagePng ($im);
		}
	
		ImageDestroy ($im);
		ImageDestroy ($mask);
		ImageDestroy ($photo);

	}


}
    
    
function _pixelAlphaThreshold($img, $x, $y)
{

	$rgb = imagecolorat($img, $x, $y);
	$color["r"]   = ($rgb >> 16) & 0xFF;
	$color["g"]   = ($rgb >> 8) & 0xFF;
	$color["b"]   = $rgb & 0xFF;

	return $color;
}
    
    
}

?>