<?php
/**
* Company: Linkuswell Tech Co., Ltd.
* Program: vipuser.php
* Author:  Yushin Chen
* Date:    from 2010.01.13
* Version: 1.1
* Description: VIP User Data Edit
*/

Header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
if (!eregi(USERLOGINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
include_once("userlogin/language/vipuser-".DEFAULTLANGUAGE.".php");


/**
 * Main Menu
 */
function MainMenu()
{
	GraphicAdmin();
	OpenTable();
	echo "<center><font class='toptitle'>"._VIPUSERDATAEDIT."</font></center>";
	CloseTable();
	echo "<br>\n";
}


/**
 * User Data Edit
 */
function userdata_edit($showmsg="")
{
	
  if ($_REQUEST['op2'] == "px_userdata_edit")
		$introduction = $_REQUEST['introduction'];
	else
	{
		$sql = "select introduction from ".ADOPREFIX."_useradmin where sid=? and enable=?";
		$stmt = $GLOBALS['adoconn']->Prepare($sql);
		$rs = $GLOBALS['adoconn']->Execute($stmt,array($_SESSION['USERLOGIN_SID'],"1"));
		if ($rs && !$rs->EOF)
			$introduction = $rs->fields['introduction'];
		if ($rs)	$rs->Close();
	}

	include_once("header.php");
	//include_once("userlogin/includes/vipuser-js.php");

	MainMenu();

	OpenTable();
	echo "<center><font class='undertitle'>"._VIPUSER_DATAEDIT."</font></center><br>";
	if (trim($showmsg) != "")
		echo "<center><font class='Warning'>".$showmsg."</font></center><br>";
	echo "<form id='vipuser' action='".USERLOGINPAGE."' method='post'>";
	echo "<table>"
	."<tr><td>"._VIPUSER_INTRODUCTION.":</td><td><textarea name='introduction' rows='8' cols='40'>".stripslashes($introduction)."</textarea><br><font class='pstext'>"._VIPUSER_INTRODUCTION_PS."</font></td></tr>"
	."<tr><td colspan='2'>&nbsp;</td></tr>"
	."<tr><td colspan='2'><font color='red'>"._VIPUSER_MODIFYPASSWORD."</font></td></tr>"
	."<tr><td>"._VIPUSER_NEWPASSWORD.":</td><td><input type='password' name='newpassword'></td></tr>"
	."<tr><td>"._VIPUSER_NEWPASSWORDRETYPE.":</td><td><input type='password' name='retypepassword'></td></tr>"
	."</table>"
	."<input type='hidden' name='op' value='vipuser'>"
	."<input type='hidden' name='op2' value='px_userdata_edit'>"
	."<br><center><input type='submit' name='submitbtn' value='"._OK."'>"
	."</center>"
	."</form>";

	CloseTable();
	echo "<br>\n";
	include_once("footer.php");
}


/**
 * User Data Edit Save
 */
function px_userdata_edit()
{
	
  $err = 0;
	$errmsg = "";
	$introduction = trim($_REQUEST['introduction']);
	$newpassword = $_REQUEST['newpassword'];
	$retypepassword = $_REQUEST['retypepassword'];
	if ($newpassword != "" && $retypepassword != "")
	{
		if ($newpassword != $retypepassword)
		{
			userdata_edit(_CHECK_CONSISTENT);
			exit();
		}
		else
		{
			$sql = "update ".ADOPREFIX."_useradmin set sapw=?,introduction=? "
			." where sid=?";
			$params = array();
			$params[] = $newpassword;
			$params[] = $introduction;
			$params[] = $_SESSION['USERLOGIN_SID'];
			$stmt = $GLOBALS['adoconn']->Prepare($sql);
			$ok = $GLOBALS['adoconn']->Execute($stmt,$params);
			if ($ok)
				$updmsg = _VIPUSER_DATACHANGED." "._VIPUSER_NEXTLOGINPLSUSENEWPW;
		}
	}
	else
	{
		$sql = "update ".ADOPREFIX."_useradmin set introduction=? where sid=?";
		$params = array();
		$params[] = $introduction;
		$params[] = $_SESSION['USERLOGIN_SID'];
		$stmt = $GLOBALS['adoconn']->Prepare($sql);
		$ok = $GLOBALS['adoconn']->Execute($stmt,$params);
		if ($ok)
			$updmsg = _VIPUSER_DATACHANGED;
	}
	$formaction = USERLOGINPAGE;
	$arr['op'] = "vipuser";
	$arr['op2'] = "userdata_edit";
	$arr['showmsg'] = $updmsg;
	POSTFORM($formaction,$arr);
}


if ($_REQUEST['op']=="vipuser")
{
	switch ($_REQUEST['op2'])
	{
		case "userdata_edit":			//User Data Edit
			userdata_edit($_REQUEST['showmsg']);
			break;

		case "px_userdata_edit":	//User Data Edit Save
			px_userdata_edit();
			break;

		default:									//User Data Edit
			userdata_edit();
			break;
	}
}
?>