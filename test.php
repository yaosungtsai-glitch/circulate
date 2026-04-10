<?php
$str =  explode("/",$_SERVER['SCRIPT_FILENAME']);
if(!is_null($str)){
	$num=count($str);
	echo $str[$num-1];
}

$dirstr=dirname(__FILE__);
if (is_dir($dirstr)) {
    $files = scandir($dirstr); // 取得目錄下所有檔案和子目錄的名稱
    var_dump($files); // 輸出陣列來查看所有內容
} else {
    echo "目錄不存在！";
}
?>
