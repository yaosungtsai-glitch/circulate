<?php
/*************************************
 * Company: 
 * Program: image.php
 * Author:  Ken Tsai
 * Date:    from 2002.10.28
 * Version: 2.0
 * Description: 
 *************************************/
session_start();
$strnum = randnum();
$_SESSION['rand'] = $strnum;

Header ("Content-type: image/png");
//Header ("Content-type: image/jpeg");
$im = ImageCreate(70,25) or die ("Cannot Initialize new GD image stream");
$background_color = ImageColorAllocate($im,255,255,255);
$text_color = ImageColorAllocate($im,0,0,255);
$pixel_color = ImageColorAllocate($im,mt_rand(1,127),mt_rand(1,127),mt_rand(1,127));
ImageString($im, 5, 5, 5, $strnum, $text_color);
/*
for ($i = 0; $i < 200; $i++)
{
  $x = mt_rand(0,70);
  $y = mt_rand(0,25);
  imagesetpixel($im,$x,$y,$pixel_color);
}*/
imagepng($im);
//imagejpeg($im);
// Free up memory
imagedestroy($im);

/**
 * 產生亂數數值
 * @return 亂數數值
 */
function randnum()
{
 $type=mt_rand(0,99)%2;
 switch($type)
 {
  case 0:
  default:
  return sprintf("%06d",mt_rand(1,999999));
  break;

  case 1:
  for($i=0,$str="";$i<6;$i++)
  {
     $num=mt_rand(65,90);
     $str.=chr($num);
  }
  return $str;  
  break;

 }
 
}

?>