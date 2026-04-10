<?php
include_once("mainfile.php");
include_once("lib/upload.inc.php");
include_once("header.php");
OpenTable();

function uploadfinal(){
    

}

switch($_REQUEST['op']){
    default:
    case "uploadone":
        uploadone($_SERVER['PHP_SELF']."?op=uploadonexec", 'file');
        break;
    case "uploadonexec":
        uploadonexec();
        break;
    case "uploadfinal":
        uploadfinal();
        break;
}
CloseTable();
echo "<br/>";
include_once("footer.php");
?>