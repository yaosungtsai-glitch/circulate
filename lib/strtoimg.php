<?php
/*************************************
 * Company: 
 * Program: strtoimg.php
 * Author:  Ken Tsai
 * Date:    from 2021.06.13
 * Version: 2.0
 * Description: 
 *************************************/

class Test_gd {

	public function __construct()
	{
		if(extension_loaded('gd')) {
		    // echo '你可以使用gd<br>';
		    /*foreach(gd_info() as $cate=>$value) {
		        echo "$cate: $value<br>";
		    }*/
		}else {
		    echo '你沒有安裝gd擴充套件';
			return;
		}
	}
        /**
     * 生成一個png圖片,上面字型為楷體
     * @param  [string] $text 中文文字
     * @return [void]   
     */
	public function createPng($text)
	{		
		// Set the content-type
		 header ( 'Content-Type: image/png' );

		 // Create the image
		 $im  =  imagecreatetruecolor ( 30 , 20  );

		 // Create some colors
		 $blue  =  imagecolorallocate ( $im ,  0 ,  0 ,  250 );
		 $grey  =  imagecolorallocate ( $im ,  200 ,  200 ,  200 );  //表示陰影效果
		 $orange  =  imagecolorallocate ( $im ,  255	,  128 ,  0 );  //表示陰影效果
		 $white  =  imagecolorallocate ( $im ,  255 ,  255 ,  255 );
		 //imagefilledrectangle ( $im ,  0 ,  0 ,  120 ,  29 ,  $blue );
		 imagefilledrectangle ( $im ,  0 ,  0 ,  120 ,  29 ,  $white );
		 // Replace path by your own font path
		 //$font  =  '/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans.ttf' ;
		 $font  = '/usr/share/fonts/truetype/cwtex/cwkai.ttf';//楷體		

		 $len = mb_strlen($text);
		 //$posX = (imagesx($im)-20*$len) / 2 - 3*($len-1) - $len*0.5; //字間距和字留白都要去掉
         $posX = (imagesx($im)-20*$len) / 2 - 3*($len-1) - $len*0.5 +12; //字間距和字留白都要去掉
  
		 // Add some shadow to the text
		 //imagettftext ( $im ,  20 ,  0 ,  $posX+1 ,  24 ,  $grey ,  $font ,  $text );

		 // Add the text
		 //imagettftext ( $im ,  15 ,  0 ,  $posX ,  23 ,  $white ,  $font ,  $text );
         //imagettftext ( $im ,  15 ,  0 ,  $posX ,  20 ,  $white ,  $font ,  $text );
         imagettftext ( $im ,  12 ,  0 ,  $posX ,  15 ,  $blue ,  $font ,  $text );
		 // Using imagepng() results in clearer text compared with imagejpeg()
		 imagepng ( $im );
		 imagedestroy ( $im );
	}
      /**
     * 降低圖片質量,減小檔案體積
     * @return [void]
     */
    public function tinyImage()
    {
        $sImage = '/home/www/medicine/SPH00000972/主圖1.jpg';
        $tImage = '/home/www/data/SPH00000972_1.jpg';

        $im = imagecreatefromjpeg($sImage);
        imagejpeg($im,$tImage,50); //quality setting to 50%
        imagedestroy($im);
        echo "complete<br/>";
    }
 }
Header ("Content-type: image/png");
$gdTest = new Test_gd();
$text = $_GET['text'];
$gdTest->createPng($text);
?>
