<?php
/**
 * Company: 
 * Program: loginadmin.php，可依需求變更檔名
 * Author:  Ken Tsai
 * Date:    from 2005.03.25
 * Version: 2.0
 * Description: 商店Service介面登入
 */

include_once("mainfile.php");
include_once("lib/login.inc.php");

$usr = new UserLogin($GLOBALS['adoconn'],ADOPREFIX);

$op = $_REQUEST['op'];

if ($op == "login") {
	if ($_SESSION['rand'] == $_REQUEST['pass2'])  $loginOk = $usr->login($_REQUEST['user'],$_REQUEST['pass']);
	if ($loginOk) unset($op);
} else {
	$loginOk = $usr->checkLogin();
}
$_SESSION['USER_STOREID'] = $usr->objUser->storeid;
$_SESSION['USER_SAID'] = $usr->objUser->said;

if(!isset($op))	
  $op = "adminMain";

  
if(isset($_REQUEST['op2']))	
  $op2=$_REQUEST['op2'];

  
if ($loginOk) {
	$recordSet = &$GLOBALS['adoconn']->Execute("select ".ORGTABLE_FIELD_IPMANAGE." from ".ADOPREFIX.ORGTABLE." where ".ORGTABLE_FIELD_ID."='".$_SESSION['USER_STOREID']."'");
	if ($recordSet && !$recordSet->EOF)
	{
		$store_ipmanage = $recordSet->fields[ORGTABLE_FIELD_IPMANAGE];	//是否控管可登入IP位置
		$recordSet->Close();
	} 
	if (($GLOBALS['IPMANAGE'] || $store_ipmanage) && !chkloginip($_SESSION['USER_STOREID']))
	{
		session_unset();
		exit("Access denid!");
	}
	$permit = $usr->getPermit();
	if(!isset($op2) || empty($op2)) $op2='adminMain';
	//print_r($_SESSION);
	//$usr->writeLog($op,$op2,getloginfname($op));
	writeLog($op,$op2);
	switch ($op)
	{
		case "GraphicAdmin":
		case "adminMain":
			adminMain();
			break;
		
		case "logout":
			$usr->logout();
			logout();
			break;

		case "login";
			unset($_REQUEST['op']);
			login();
			break;

		default:
			//依照登入帳號,取得權限
			$casedir = dir("service/case");
			while ($file = $casedir->read()) {
				$part = explode(".",$file);
				if (in_array($part[1],$GLOBALS['permit'])) {
					$menuList[] = $file;
				}
			}
			if (!empty($menuList)) {
				if (is_array($menuList)) sort($menuList);
				for ($i=0; $i < sizeof($menuList); $i++)
					if ($menuList[$i] != "")	include_once($casedir->path."/".$menuList[$i]);
			}
            //改成跟admin.php同樣的的寫法       by yushin, 2008-04-22.
			//$casedir->close();
			if ($casedir)	closedir($casedir->handle);
			unset($file);
			unset($part);
			unset($casedir);
			break;
	}
} else {
	PrintLogin();
}


/**
* 登入畫面
*/
function PrintLogin()
{
	include_once("header.php");
	echo "<br>";
    OpenTable1();
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n"
	."<div class='logindiv'><table class='logintable' cellspacing='0' cellpadding='0' align='center'>\n"
	."<tr>\n"
	."<td class='logintext01'>"._SAID."&nbsp;:&nbsp;</td>\n"
	."<td class='logintext02'><input class='loginformstyle01' type='text' NAME='user' MAXLENGTH='30'></td>\n"
	."</tr>\n"
	."<tr>\n"
	."<td class='logintext01'>"._PASSWORD."&nbsp;:&nbsp;</td>\n"
	."<td class='logintext02'><input class='loginformstyle01' type='password' NAME='pass' MAXLENGTH='30'></td>\n"
	."</tr>\n"
	."<tr>\n"
	."<td class='logintext01'><img src='image.php'></td>\n"
	."<td class='logintext02'><input type='text' NAME='pass2' MAXLENGTH='18'><br>"._ADMINCHECKID."</td></tr>\n"
	."<tr>\n"
	."<input type='hidden' NAME='op' value='login'>"
	."<tr><td class='tab01' colspan='2' align='center'><input class='loginformstyleword01' type='submit' VALUE='"._LOGIN."'>"
	."</td></tr></table></div></form>\n";
	CloseTable();
	include_once("footer.php");
}


/**
* 顯示功能介面
* @param url 功能連結至
* @param title 功能名稱
* @param image 功能圖示
*/
function service_menu($url, $title, $image)
{
	if (SERVICEGRAPHIC == 1) {
		$img = "<img src='".SERVICEIMG.$image."' border='0' alt='$title'>";
		$funclass = "";
	} else {
		$img = "[$title]";
		$funclass = "LoginFuncMenu";	//文字模式時以此class設定功能文字的呈現樣式
	}
	echo "<td align='center'><a href='$url&PHPSESSID=".session_id()."' class='$funclass'>$img</a><br></td>\n";
}

/**
* 顯示後台管理功能
*/
function GraphicAdmin()
{
	//找出登入帳號所屬站台名稱
	$recordSet = &$GLOBALS['adoconn']->Execute("select ".ORGTABLE_FIELD_NAME." from ".ADOPREFIX.ORGTABLE." where ".ORGTABLE_FIELD_ID."='".$_SESSION['USER_STOREID']."'");
	if ($recordSet && !$recordSet->EOF)
	{
		$storename = $recordSet->fields[ORGTABLE_FIELD_NAME];	//站台名稱
		$recordSet->Close();
	}
	include_once("header_login.php");
	OpenTable1();
	echo "<table align='center'><tr><td class='logintitle01'>$storename</td></tr></table>";
	echo "<br><div class='login-table-div' align='center'><table class='login-table' border='0' align='center' >\r\n"
	."<tr valign='top'>\r\n";
	//依照擁有的權限功能顯示後台管理功能介面
	$linksdir = dir("service/links");

	while ($file = $linksdir->read()) {
		//print_r($file);
		$part = explode(".",$file);
		if ($part[0] == "links") {
			if (in_array($part[1],$GLOBALS['permit'])) {
				$menuList[] = $file;
			}
		}
	}
	$_SESSION['counter'] = 0;   //計算每列個數以判斷是否要換行的參數
	if(!is_null($menuList)){
		for ($i=0; $i < count($menuList); $i++){
			if($menuList[$i] != ""){
				include_once($linksdir->path."/".$menuList[$i]);
				if ($_SESSION['counter'] == SERVICEFUNCPERROW) {
					echo "</tr><tr valign='top'> \n";
					$_SESSION['counter'] = 0;
				}else{
					$_SESSION['counter'] = $_SESSION['counter'] + 1;
				}
			}
		}
	}
	if ($linksdir)  closedir($linksdir->handle);
	service_menu($_SERVER['PHP_SELF']."?op=logout&title="._ADMIN_LOGOUT, _ADMIN_LOGOUT, "exit.gif");	//登出
    //補最後一列剩餘空格
	for ($j=0; $j<(SERVICEFUNCPERROW-$_SESSION['counter']); $j++)
	{
		echo "<td>&nbsp</td>\n";
	}
	echo "</tr></table></div>";
	echo "<br>";
	CloseTable();
  echo "<br>";
}

/**
* 管理者登出
*/
function logout()
{
	include_once("header_login.php");
	OpenTable1();
	echo "<div class='logindiv'><table class='logintable' cellspacing='0' cellpadding='0' align='center'><tr><td  align='center'><div style='margin:20px 0;'>"._YOUARELOGGEDOUT."</td></tr> \n";
	echo "<tr><td align='center'><a href='".$_SERVER['PHP_SELF']."?op=login'>"._SERVICE_ADMIN_LOGIN."</a></td></tr></table></div> \n";
	CloseTable();
	include_once("footer.php");
	
}

/**
* 後台管理介面主功能
*/
function adminMain()
{
	GraphicAdmin();
	include_once("footer.php");
}

function writeLog($op,$op2='adminMain'){

	$username=$_SESSION['USER_SAID'];
	$timestr=date('Y-m-d h:i:s');
	$ipstr=getClientIP();
	$sql="insert into ".ADOPREFIX."_storeadmin_login(loginname, time, op, op2, ip, function) value('$username','$timestr','$op','$op2','$ipstr','$op') ";
  $GLOBALS['adoconn']->Execute($sql);
}

?>
