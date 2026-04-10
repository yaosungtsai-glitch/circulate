<?php
/**
* Company: 
* Program: members.php
* Author:  Ken Tsai
* Date:    2023.05.25
* Version: 2.0
* Description: 雙證件核對功能
*/

Header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
if (!preg_match('/'.ADMINPAGE.'/', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
include_once("admin/language/members-".DEFAULTLANGUAGE.".php");
include_once("lib/dbpager.inc.php");
/*
系統管理menu
*/
function membersmenu()
{
	include_once("header.php");
	GraphicAdmin();
	OpenTable();
	echo "<center class='toptitle'>"._MEMBERSADMIN."</center>\n";
	echo "<br/>";
	echo "<a href='".$_SERVER['PHP_SELF']."?op=members&op2=membersadd'>"._MEMBERSADD."</a>|";
	echo "<a href='".$_SERVER['PHP_SELF']."?op=members&op2=memberslist'>"._MEMBERSLIST."</a>&nbsp&nbsp";
	CloseTable();
	echo "<br/>";
}

/**
 *  身份證需檢核列表 
 **/

function memberslist(){
	membersmenu();
	OpenTable();
	echo "<center><font class='undertitle'>"._MEMBERSLIST."</font></center>\n";
//會友身份 0:慕道友 1:已入籍會友 2:未入會籍 3:已轉出會籍 4:新朋友(受洗）5:新朋友(未受洗)
	$sql="select id,name,ename, case membership 
								when 0 then '"._MEMBERSHIP0."' 
								when 1 then '"._MEMBERSHIP1."' 
								when 2 then '"._MEMBERSHIP2."' 
								when 3 then '"._MEMBERSHIP3."' 
								when 4 then '"._MEMBERSHIP4."' 
								when 5 then '"._MEMBERSHIP5."' 
								else '"._MEMBERSHIP0."' end 
	from ".ADOPREFIX."_members order by id DESC";
    $colnames= array(_ID,_NAME,_ENAME,_MEMBERSHIP,_FUNCTIONS);
    $links[0]['link']="op=members&op2=membersedit";
    $links[0]['label']=_EDIT;    
  
    $rows=dbpage($GLOBALS['adoconn'],$sql,$colnames,$links);
	CloseTable();
	include_once("footer.php");
	/*
    OpenTable();
   	include_once("lib/dbpager.inc.php");
	echo "<center><font class='undertitle'>"._MEMBERSLIST."</font></center>\n";
	$sql="select id from ".ADOPREFIX."_members order by id DESC";
    echo $sql;
	$colnames= array(_ID,_FUNCTIONS);
	$links[0]['link']  ="op=members&op2=membersedit";
	$links[0]['label'] =_AUDITING;  
    $rows=dbpage($GLOBALS['adoconn_ocr'],$sql,$colnames,$links);
	CloseTable();
	echo "<br>";
	include_once("footer.php");
	*/

}
/*
function membersedit(){

    membersmenu();
    OpenTable();
	$sql="SELECT id ,userNo, userID, mobileNo, name ,createDate from ".ADOPREFIX_OCR."_member where id='".$_GET['pkid']."' and status='2' order by id DESC";
	$rs = $GLOBALS['adoconn_ocr']->Execute($sql);
	$sql="SELECT * from ".ADOPREFIX_OCR."_pic where member_id='".$_GET['pkid']."' order by id DESC";
	$rs_pic = $GLOBALS['adoconn_ocr']->Execute($sql);
	echo "<center><font class='undertitle'>"._AUDITING."</font></center>\n";
	echo "<table>\n";
	echo "<form active='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<input type='hidden' name='op' value='members'>\n";
	echo "<input type='hidden' name='op2' value='memberseditting'>\n";
	echo "<input type='hidden' name='pkid' value='".$rs->fields['id']."'>\n";
	echo "<tr><td>"._USERNAME.":</td><td>".$rs->fields['name']."</td></tr>\n";
	echo "<tr><td>"._CELLPHONE.":</td><td>".$rs->fields['mobileNo']."</td></tr>\n";
	echo "<tr><td>"._USERID.":</td><td>".$rs->fields['userID']."</td></tr>\n";
	echo "<tr><td>"._IDCARDUP.":</td><td>".strtoimg($rs_pic->fields['idCard_front_org'])."</td></tr>\n";
	echo "<tr><td></td><td><input type='submit' value='"._OK."'></td></tr>\n";
    echo "</form>\n";
    echo "</table>\n";

	CloseTable();
	echo "<br>";
	include_once("footer.php");
}

function strtoimg($data){
	$imgtype = array('data:image/jpeg;base64,', 'data:image/png;base64,');
	$imgdata=str_replace($imgtype,'',$data);
	echo $imgdata;
	/*
	$imgdata = base64_decode($imgdata);
	$im = imagecreatefromstring($imgdata);
	if ($im !== false) {
    	header('Content-Type: image/jpeg');
    	imagepng($im);
    	imagedestroy($im);
	}
	else {
    	echo '_IDCARDERR';
	}

}
*/
$op='members';
include_opdir($op);
if ($_REQUEST['op']==$op && isAuthority($_SESSION['aid'],$_REQUEST['op']))
{	
	switch ($_REQUEST['op2'])
	{
		
		
		case "memberslist":	
			memberslist();
		break;
		default:
			 membersmenu();
			 include_once("footer.php");
		break;
		/*
		case "membersedit":
			membersedit();
		break;*/

	}
}
?>