  <?php
/*************************************
 * Company:
 * Program: upload.inc.php
 * Author:  Ken Tsai
 * Date:    20260326
 * Description:	upload檔案
 *************************************/
function uploadone($processor, $file='file'){
  echo "<form method='post' enctype='multipart/form-data' action='$processor'>".PHP_EOL;
  echo "<input type='file' name='$file'>".PHP_EOL;
  echo "<input type='submit' value='Upload'>".PHP_EOL;
  echo "<input type='hidden' value='file'>".PHP_EOL;
  echo "</form>".PHP_EOL;
}

function uploadonexec($returnurl){
  $err=null;
  if ($_FILES['file']['error'] === UPLOAD_ERR_OK){
    $remark= "Source:". $_FILES['file']['name'] .PHP_EOL
    ."Filetype:" . $_FILES['file']['type'] .PHP_EOL
    ."Filesize:" . ($_FILES['file']['size'] / 1024) . " KB".PHP_EOL
    ."Tempfile:" . $_FILES['file']['tmp_name'] .PHP_EOL;
    # 檢查檔案是否已經存在
    if (file_exists('upload/' . $_FILES['file']['name'])){
      $remark="Error: The file already exists" .PHP_EOL;
    } else {
      $filename = $_FILES['file']['tmp_name'];
      $dest = 'upload/' . $_FILES['file']['name'];
      # 將檔案移至指定位置
      move_uploaded_file($filename, $dest);
    }
  } else {
    $remark='Error:' . $_FILES['file']['error'] .PHP_EOL;
  }
  if(is_null($err))
    header("location:$returnurl&remark=$remark&err=error")
  else
    header("location:$returnurl&remark=$remark")
}




 ?>