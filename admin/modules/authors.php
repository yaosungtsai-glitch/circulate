<?php
/**
* Company: 
* Program: authors.php
* Author:  Ken Tsai
* Date:    from 2004.09.16
* Version: 2.0
* Description: 管理員管理
*/

Header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
if (!preg_match('/'.ADMINPAGE.'/', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
include_once("admin/language/authors-".DEFAULTLANGUAGE.".php");

/**
 * 顯示管理員管理的功能選單
 */
function authors_displaymenu()
{
	include_once("header.php");

	GraphicAdmin();
	OpenTable1();
	echo "<h1 class='toptitle'>"._AUTHORSADMIN."</h1>";
	CloseTable();
  echo "<br>";
	OpenTable1();
	echo "<div class='main-menu2'>
<div class='menu_con'>"
	."<a href='".$_SERVER['PHP_SELF']."?op=authors&op2=SUBMENU&sel=group&PHPSESSID=".session_id()."' class='categorylinktext'>"._MANAGEGROUP."</a><span>|</span>"
	."<a href='".$_SERVER['PHP_SELF']."?op=authors&op2=SUBMENU&sel=authors&PHPSESSID=".session_id()."' class='categorylinktext'>"._MANAGEAUTHORS."</a><span>|</span>"
	."<a href='".$_SERVER['PHP_SELF']."?op=authors&op2=SUBMENU&sel=function&PHPSESSID=".session_id()."' class='categorylinktext'>"._MANAGEFUNCTION."</a><span>|</span>"
	."<a href='".$_SERVER['PHP_SELF']."?op=authors&op2=LOGLIST&sel=function&PHPSESSID=".session_id()."' class='categorylinktext'>"._LOGFUNCTION."</a>"
	."</div>"
			."</div>";
	CloseTable();
	echo "<br>";
}


/**
 * 顯示管理員管理的功能選單2
 * @param sel 選單種類
 */
function authors_displaymenu2($sel)
{
	authors_displaymenu();
	OpenTable1();
	switch ($sel)
	{
		case "group":
			echo "<div class='main-menu3'>
<div class='menu_con'>"
			."<a href='".$_SERVER['PHP_SELF']."?op=authors&op2=LISTGROUP&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._LISTGROUP."</a><span>|</span>"
			."<a href='".$_SERVER['PHP_SELF']."?op=authors&op2=ADDGROUP&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._ADDGROUP."</a><span>|</span>"
			."<a href='".$_SERVER['PHP_SELF']."?op=authors&op2=LISTDELGROUP&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._LISTDELGROUP."</a>"
			."</div>"
			."</div>";
			break;
		case "authors":
			echo "<div class='main-menu3'>
<div class='menu_con'>"
			."<a href='".$_SERVER['PHP_SELF']."?op=authors&op2=LISTAUTHORS&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._LISTAUTHORS."</a><span>|</span>"
			."<a href='".$_SERVER['PHP_SELF']."?op=authors&op2=ADDAUTHORS&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._ADDAUTHORS."</a><span>|</span>"
			."<a href='".$_SERVER['PHP_SELF']."?op=authors&op2=LISTDELAUTHORS&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._LISTDELAUTHORS."</a>"
			."</div>"
			."</div>";
			break;
		case "function":
			echo "<div class='main-menu3'>
<div class='menu_con'>"
			."<a href='".$_SERVER['PHP_SELF']."?op=authors&op2=LISTFUNCTION&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._LISTFUNCTION."</a><span>|</span>"
			."<a href='".$_SERVER['PHP_SELF']."?op=authors&op2=ADDFUNCTION&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._ADDFUNCTION."</a><span>|</span>"
			."<a href='".$_SERVER['PHP_SELF']."?op=authors&op2=LISTDELFUNCTION&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._LISTDELFUNCTION."</a>"
			."</div>"
			."</div>";
			break;
	}
	CloseTable();

}


//log記錄列表
function log_list()
{
  authors_displaymenu();
  OpenTable1();
  echo "<h2 class='title2'>"._LOGFUNCTION."</h2>";
	// CloseTable();
	include_once("lib/dbpager.inc.php");
	$sql = "select log_id, log_loginname, log_time, log_op, log_op2, log_title, log_ip from ".ADOPREFIX."_login_log order by log_id desc";
    $colnames= array(_ID,_LOGIN,_LOGDATEIME,"OP","OP2",_FUNCTIONS,_LOGIP);
    $rows=dbpage($GLOBALS['adoconn'],$sql,$colnames,$links,0);
	/*
	include_once("lib/mypager.inc");	
	$sql = "select log_id, log_loginname, log_time, log_op, log_op2, log_title, log_ip from ".ADOPREFIX."_login_log order by log_id desc";
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'sadmin',true);
	$GridHeader = array(_ID,_LOGIN,_LOGDATEIME,"OP","OP2",_FUNCTIONS,_LOGIP);
	$pager->setRenderGridLayout("width='100%' align='center' class='qqtable'",$GridHeader);
	$pager->setFunctions($funcNames,$funcUrls);
	$pager->setOp("authors&op2=LOGLIST&sel=$sel&PHPSESSID=".session_id());
	$pager->Render_Function($GLOBALS['perpage']);
	*/
	CloseTable();
}

if ($_REQUEST['op']=="authors" && isAuthority($_SESSION['aid'],$_REQUEST['op']))
{
	switch ($_REQUEST['op2'])
	{
		case "SUBMENU":				//列出功能表
			authors_displaymenu2($_REQUEST['sel']);
			include_once("footer.php");
			break;
		case "LOGLIST":				//列出功能表
			log_list();
			include_once("footer.php");
			break;
		case "LISTGROUP":			//群組列表
		case "LISTDELGROUP":		//列出已不使用的群組列表
		case "SETGROUPENABLE":		//將群組設定為啟用狀態的畫面
		case "SETGROUPENABLEED":	//將群組設定為啟用狀態
		case "ADDGROUP":			//新增群組基本資料的畫面
		case "ADDEDGROUP":			//新增群組基本資料
		case "UPDATEGROUP":			//修改群組基本資料的畫面
		case "UPDATEEDGROUP":		//修改群組基本資料
		case "DELGROUP":			//確認刪除群組基本資料的畫面
		case "DELEDGROUP":			//刪除群組基本資料
		case "SETGROUPPERMIT":		//設定群組權限的畫面
		case "SETGROUPPERMITED":	//設定群組權限
		case "SETGROUPUSER":		//設定群組人員的畫面
		case "SETGROUPUSERED":		//設定群組人員
			include_once("admin/modules/authors/group.php");
			break;

		case "LISTAUTHORS":			//管理員列表
		case "LISTDELAUTHORS":		//列出已不使用的管理員列表
		case "SETAUTHORSENABLE":	//將管理員設定為啟用狀態的畫面
		case "SETAUTHORSENABLEED":	//將管理員設定為啟用狀態
		case "ADDAUTHORS":			//新增管理員基本資料的畫面
		case "ADDEDAUTHORS":		//新增管理員基本資料
		case "UPDATEAUTHORS":		//修改管理員基本資料的畫面
		case "UPDATEEDAUTHORS":		//修改管理員基本資料
		case "DELAUTHORS":			//確認刪除管理員基本資料的畫面
		case "DELEDAUTHORS":		//刪除管理員基本資料
		case "SETAUTHORSPERMIT":	//設定管理員權限的畫面
		case "SETAUTHORSPERMITED":	//設定管理員權限
		case "SETAUTHORSGROUP":		//設定管理員所屬群組的畫面
		case "SETAUTHORSGROUPED":	//設定管理員所屬群組
			include_once("admin/modules/authors/authors.php");
			break;

		case "LISTFUNCTION":		//權限功能列表
		case "LISTDELFUNCTION":		//列出已不使用的權限功能列表
		case "SETFUNCTIONENABLE":	//將權限功能設定為啟用狀態的畫面
		case "SETFUNCTIONENABLEED":	//將權限功能設定為啟用狀態
		case "ADDFUNCTION":			//新增權限功能基本資料的畫面
		case "ADDEDFUNCTION":		//新增權限功能基本資料
		case "UPDATEFUNCTION":		//修改權限功能基本資料的畫面
		case "UPDATEEDFUNCTION":	//修改權限功能基本資料
		case "DELFUNCTION":			//刪除權限功能基本資料的畫面
		case "DELEDFUNCTION":		//刪除權限功能基本資料
		case "FUNCTIONSET":			//設定權限給群組或管理員的畫面
		case "FUNCTIONSETED":		//設定權限
		case "FUNCTIONUP":			//權限優先權上升
		case "FUNCTIONDOWN":		//權限優先權下降
			include_once("admin/modules/authors/functions.php");
			break;

		default:
			authors_displaymenu();
			include_once("footer.php");
			break;
	}
}
?>
