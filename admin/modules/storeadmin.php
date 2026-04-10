<?php
/**
 * Company: 
 * Program: storeadmin.php
 * Author:  Ken Tsai
 * Date:    from 2004-02-10
 * Version: 2.0
 */

Header("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
if (!preg_match('/'.ADMINPAGE.'/', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
include_once("admin/language/storeadmin-".DEFAULTLANGUAGE.".php");


/**
 * 顯示主功能選單
 */
function storeadmin_displaymenu()
{
	include_once("header.php");
	GraphicAdmin();
	OpenTable1();
	echo "<h1 class='toptitle'>"._STOREADMIN."</h1>";
	CloseTable();
	echo "<br/>";

	OpenTable1();
	echo "<div class='main-menu2'>
<div class='menu_con'>"
	."<a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=SUBMENU&sel=sadmin&PHPSESSID=".session_id()."' class='categorylinktext'>"._MANAGESADMIN."</a><span>|</span>"
	."<a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=SUBMENU&sel=sfunction&PHPSESSID=".session_id()."' class='categorylinktext'>"._MANAGESFUNCTION."</a><span>|</span>"
	."<a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LOGLIST&sel=sloglist&PHPSESSID=".session_id()."' class='categorylinktext'>"._LOGFUNCTION."</a>"
	."</div>"
	."</div>";
	CloseTable();
	echo "<br/>";

}


/**
 * 顯示子功能選單
 * @param $_REQUEST["sel"] 選單種類
 */
function storeadmin_displaymenu2()
{
	$sel = $_REQUEST["sel"];
	storeadmin_displaymenu();
	OpenTable1();
	switch ($sel)
	{
		case "sadmin":
			echo "<div class='main-menu3'>
<div class='menu_con'>"
			."<a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSADMIN&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._LISTSADMIN."</a><span>|</span>"
			."<a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=ADDSADMIN&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._ADDSADMIN."</a><span>|</span>"
			."<a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTDELSADMIN&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._LISTDELSADMIN."</a>"
			."</div>"
			."</div>";
			break;
		case "sfunction":
			echo "<div class='main-menu3'>
<div class='menu_con'>"
			."<a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSFUNCTION&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._LISTSFUNCTION."</a><span>|</span>"
			."<a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=ADDSFUNCTION&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._ADDSFUNCTION."</a><span>|</span>"
			."<a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTDELSFUNCTION&sel=$sel&PHPSESSID=".session_id()."' class='categorylinktext'>"._LISTDELSFUNCTION."</a>"
			."</div>"
			."</div>";
			break;
	}
	CloseTable();
	echo "<br/>";
}


/**
 * 列出管理員
 * @param $_REQUEST["sel"] 選單種類
 */
function list_sadmin()
{
	$sel = $_REQUEST["sel"];
	include_once("header.php");
	storeadmin_displaymenu2();
	OpenTable1();
	// include_once("lib/mypager.inc");
	include_once("lib/dbpager.inc.php");
	$sql = "select sid,said,".ORGTABLE_FIELD_NAME." from ".ADOPREFIX."_storeadmin,".ADOPREFIX.ORGTABLE." where ".ADOPREFIX."_storeadmin.enable=1 and ".ADOPREFIX."_storeadmin.storeid=".ADOPREFIX.ORGTABLE.".".ORGTABLE_FIELD_ID;
	    $colnames= array(_ID,_SAID,_STORENAME,_FUNCTIONS);
    $links[0]['link']="op=storeadmin&op2=UPDATESADMIN&sel=$sel&PHPSESSID=".session_id();
    $links[0]['label']=_EDIT;    
    $links[1]['link']="op=storeadmin&op2=DELSADMIN&sel=$sel&PHPSESSID=".session_id();
    $links[1]['label']=_DELETE;  
    $links[2]['link']="op=storeadmin&op2=SETSADMINPERMIT&sel=$sel&PHPSESSID=".session_id();
    $links[2]['label']=_SETPERMIT;  
    $rows=dbpage($GLOBALS['adoconn'],$sql,$colnames,$links);
	/*
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'sadmin',true);
	$GridHeader = array(_ID,_SAID,_STORENAME);
	$pager->setRenderGridLayout("width='100%' align='center' class='qqtable'",$GridHeader);
	$funcNames = array(_EDIT,_DELETE,_SETPERMIT);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=storeadmin&op2=UPDATESADMIN&sel=$sel&PHPSESSID=".session_id(),
	                  $_SERVER['PHP_SELF']."?op=storeadmin&op2=DELSADMIN&sel=$sel&PHPSESSID=".session_id(),
					  $_SERVER['PHP_SELF']."?op=storeadmin&op2=SETSADMINPERMIT&sel=$sel&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$pager->setOp("storeadmin&op2=LISTSADMIN&sel=$sel&PHPSESSID=".session_id());
	$pager->Render_Function($GLOBALS['perpage']);
	*/
	CloseTable();
	include_once("footer.php");
}


/**
 * 新增管理員基本資料的畫面
 * @param $_REQUEST["sel"] 選單種類
 */
function add_sadmin()
{
	$sel = $_REQUEST["sel"];
	include_once("header.php");
	storeadmin_displaymenu2();
	$rs = $GLOBALS['adoconn']->Execute("SELECT ".ORGTABLE_FIELD_NAME.",".ORGTABLE_FIELD_ID." FROM ".ADOPREFIX.ORGTABLE);
	//改成用define定義的欄位名稱    by yushin, 2008-04-09.
	//if($rs) $orgidMenu = $rs->GetMenu("id","1",false);
	if ($rs && !$rs->EOF)	$orgidMenu = $rs->GetMenu(ORGTABLE_FIELD_ID,"1",false);
    if ($rs)	$rs->Close();
	//有商店資料才顯示新增帳號畫面
	if (!empty($orgidMenu))
	{
        OpenTable1();
		echo "<form name='storeadmin' action='".$_SERVER['PHP_SELF']."' method='post'>"
		."<input type='hidden' name='op' value='storeadmin'>"
		."<input type='hidden' name='op2' value='ADDEDSADMIN'>"
		."<input type='hidden' name='sel' value='$sel'>"
		."<table>"
		."<tr>"
		."<td>"._SAID.": </td>"
		."<td><input type='text' name='said'></td>"
		."</tr>"
		."<tr>"
		."<td>"._PASSWORD.": </td>"
		."<td><input type='password' name='sapw'></td>"
		."</tr>"
		."<tr>"
		."<td>"._PASSWORD.": </td>"
		."<td><input type='password' name='pw2'></td>"
		."</tr>"
		."<tr>"
		."<td>"._STORENAME.": </td>"
		."<td>$orgidMenu</td>"
		."</tr>"
		."<tr>"
		."<td><input type='submit' value='"._OK."'></td>"
		."<td><input type='reset'></td>"
		."</tr>"
		."</table>"
		."</form>";
		CloseTable();
	}
	else
	{
        OpenTable();
		echo "<center>"._NOSTORETOSET."</center>";
		CloseTable();
	}
	include_once("footer.php");
}


/**
 * 新增管理員基本資料
 * @param $_POST["said"]	管理員登入帳號
 * @param $_POST["sapw"]	密碼
 * @param $_POST["pw2"] 	確認密碼
 * @param $_POST["storeid"] 商店編號
 * @param $_POST["sel"]		選單種類
 */
function db_add_sadmin()
{
	$said = trim($_POST["said"]);
	$sapw = trim($_POST["sapw"]);
	$pw2 = trim($_POST["pw2"]);
	//$storeid = trim($_POST["id"]);
	$storeid = trim($_POST[ORGTABLE_FIELD_ID]);
	$sel = trim($_POST["sel"]);
	if ($sapw==$pw2) {
		if (!empty($said) and !empty($sapw) and !empty($storeid)) {
			$rs = $GLOBALS['adoconn']->Execute("select count(*) as count from ".ADOPREFIX."_storeadmin where said='".$said."'");
			if ($rs and !$rs->EOF) {
				$count = $rs->fields['count'];
			}
			if ($rs)    $rs->Close();
			if($count==0)
			{
				$GLOBALS['adoconn_m']->Execute("insert into ".ADOPREFIX."_storeadmin(said,sapw,storeid,enable) values ('".$said."','".$sapw."','".$storeid."',1)");
				Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSADMIN&sel=$sel&PHPSESSID=".session_id());
			} else {
				include_once("header.php");
				storeadmin_displaymenu2();
				OpenTable();
				echo "<center>"._LOGINDUPLICATE."<p>";
				echo _BACKTO."<a href='javascript:history.back();'>"._PREVIOUS."</a>"._CONFIRM."</center>";
				CloseTable();
				include_once("footer.php");
			}
		} else {
			include_once("header.php");
			storeadmin_displaymenu2();
			OpenTable();
			echo "<center>"._NOTCOMPLETE."<p>";
			echo _BACKTO."<a href='javascript:history.back();'>"._PREVIOUS."</a>"._CONFIRM."</center>";
			CloseTable();
			include_once("footer.php");
		}
	} else {
		include_once("header.php");
		storeadmin_displaymenu2();
		OpenTable();
		echo "<center>"._PASSWORDDIFFERENT."<p>";
		echo _BACKTO."<a href='javascript:history.back();'>"._PREVIOUS."</a>"._CONFIRM."</center>";
		CloseTable();
		include_once("footer.php");
	}
}


/**
 * 列出已不使用的管理員列表
 * @param $_REQUEST["sel"] 選單種類
 */
function list_delete_sadmin()
{
	$sel = $_REQUEST["sel"];
	include_once("header.php");
	storeadmin_displaymenu2();
	OpenTable1();
	// include_once("lib/mypager.inc");
	include_once("lib/dbpager.inc.php");
	$sql = "select sid,said,".ORGTABLE_FIELD_NAME." from ".ADOPREFIX."_storeadmin,".ADOPREFIX.ORGTABLE." where ".ADOPREFIX."_storeadmin.enable=0 and ".ADOPREFIX."_storeadmin.storeid=".ADOPREFIX.ORGTABLE.".".ORGTABLE_FIELD_ID;
	$colnames= array(_ID,_SAID,_STORENAME,_FUNCTIONS);
    $links[0]['link']="op=storeadmin&op2=SETSADMINENABLE&sel=$sel&PHPSESSID=".session_id();
    $links[0]['label']=_SETENABLE;    
    $rows=dbpage($GLOBALS['adoconn'],$sql,$colnames,$links);
	/*
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'deleted_sadmin',true);
	$GridHeader = array(_ID,_SAID,_STORENAME);
	$pager->setRenderGridLayout("width='100%' align='center' class='qqtable'",$GridHeader);
	$funcNames = array(_SETENABLE);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=storeadmin&op2=SETSADMINENABLE&sel=$sel&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$op = "storeadmin&op2=LISTDELSADMIN&sel=$sel&PHPSESSID=".session_id();
	$pager->setOp($op);
	$pager->Render_Function($GLOBALS['perpage']);
	*/
	CloseTable();
	include_once("footer.php");
}


/**
 * 確認刪除管理員基本資料的畫面
 * @param $_REQUEST["pkid"]	管理員編號
 * @param $_REQUEST["sel"]	選單種類
 */
function delete_sadmin()
{
	$sid = $_REQUEST["pkid"];
	$sel = $_REQUEST["sel"];
	$rs = $GLOBALS['adoconn']->Execute("select sid,said,sapw,".ORGTABLE_FIELD_NAME." from ".ADOPREFIX."_storeadmin,".ADOPREFIX.ORGTABLE." where sid=".$sid." and ".ADOPREFIX."_storeadmin.storeid=".ADOPREFIX.ORGTABLE.".".ORGTABLE_FIELD_ID);
	if ($rs and !$rs->EOF) {
		include_once("header.php");
		storeadmin_displaymenu2();
		OpenTable();
		echo "<table>"
		."<tr><td>"._SAID.": </td><td>".$rs->fields['said']."</td></tr>"
		."<tr><td>"._STORENAME.": </td><td>".$rs->fields[ORGTABLE_FIELD_NAME]."</td></tr>"
		."<tr><td>"._PASSWORD.": </td><td>".$rs->fields['sapw']."</td></tr>"
		."<tr>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=DELEDSADMIN&sid=$sid&sel=$sel&PHPSESSID=".session_id()."'>"._YES."</a></td>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSADMIN&sel=$sel&PHPSESSID=".session_id()."'>"._NO."</a></td>"
		."</tr>"
		."</table>";
		CloseTable();
		include_once("footer.php");
	} else {
		Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSADMIN&sel=$sel&PHPSESSID=".session_id());
	}
	if ($rs)	$rs->Close();
}


/**
 * 刪除管理員基本資料
 * @param $_REQUEST["sid"] 管理員編號
 * @param $_REQUEST["sel"] 選單種類
 */
function db_delete_sadmin()
{
	$sid = $_REQUEST["sid"];
	$sel = $_REQUEST["sel"];
	$rs = $GLOBALS['adoconn']->Execute("select said from ".ADOPREFIX."_storeadmin where sid=".$sid);
	if ($rs and !$rs->EOF) {
		$said = $rs->fields["said"];
	}
	if ($rs)	$rs->Close();
	$GLOBALS['adoconn_m']->StartTrans();
	$GLOBALS['adoconn_m']->Execute("delete from ".ADOPREFIX."_storepermit where said=".$said);
	$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_storeadmin set enable=0 where sid=".$sid);
	$GLOBALS['adoconn_m']->CompleteTrans();
	Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSADMIN&sel=$sel&PHPSESSID=".session_id());
}


/**
 * 修改管理員基本資料的畫面
 * @param $_REQUEST["sid"]	管理員編號
 * @param $_REQUEST["sel"]	選單種類
 */
function update_sadmin()
{
	$sid = trim($_REQUEST["pkid"]);
	$sel = trim($_REQUEST["sel"]);
	$rs = $GLOBALS['adoconn']->Execute("select sid,said,sapw,".ORGTABLE_FIELD_NAME." from ".ADOPREFIX."_storeadmin,".ADOPREFIX.ORGTABLE." where sid=".$sid." and ".ADOPREFIX."_storeadmin.storeid=".ADOPREFIX.ORGTABLE.".".ORGTABLE_FIELD_ID);
	if ($rs and !$rs->EOF) {
		include_once("header.php");
		storeadmin_displaymenu2();
		OpenTable();
		echo "<form name='storeadmin' action='".$_SERVER['PHP_SELF']."' method='post'>"
		."<input type='hidden' name='op' value='storeadmin'>"
		."<input type='hidden' name='op2' value='UPDATEEDSADMIN'>"
		."<input type='hidden' name='sel' value='$sel'>"
		."<input type='hidden' name='sid' value='".$rs->fields['sid']."'>"
		."<table>"
		."<tr>"
		."<td>"._SAID.": </td>"
		."<td>".$rs->fields['said']."</td>"
		."</tr>"
		."<tr>"
		."<tr>"
		."<td>"._STORENAME.": </td>"
		."<td>".$rs->fields[ORGTABLE_FIELD_NAME]."</td>"
		."</tr>"
		."<tr>"
		."<td>"._PASSWORD.": </td>"
		."<td><input type='password' name='sapw' value='".$rs->fields['sapw']."'></td>"
		."</tr>"
		."<tr>"
		."<td>"._PASSWORD.": </td>"
		."<td><input type='password' name='pw2' value='".$rs->fields['sapw']."'></td>"
		."</tr>"
		."<tr>"
		."<td><input type='submit' value='"._OK."'></td>"
		."<td><input type='reset'></td>"
		."</tr>"
		."</table>"
		."</form>";
		CloseTable();
		include_once("footer.php");
	} else {
		Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSADMIN&sel=$sel&PHPSESSID=".session_id());
	}
	if ($rs)    $rs->Close();
}


/**
 * 修改管理員基本資料
 * @param $_POST["sid"]  管理員編號
 * @param $_POST["sapw"] 密碼
 * @param $_POST["pw2"]  密碼確認
 * @param $_POST["sel"]  選單種類
 */
function db_update_sadmin()
{
	$sid = trim($_POST["sid"]);
	$sapw = trim($_POST["sapw"]);
	$pw2 = trim($_POST["pw2"]);
	$sel = trim($_POST["sel"]);
	if ($sapw==$pw2) {
		if (!empty($sapw)) {
			$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_storeadmin set sapw='".$sapw."' where sid=".$sid);
			Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSADMIN&sel=$sel&PHPSESSID=".session_id());
		} else {
			include_once("header.php");
			storeadmin_displaymenu2();
			OpenTable();
			echo "<center>"._NOTCOMPLETE."<p>";
			echo _BACKTO."<a href='javascript:history.back();'>"._PREVIOUS."</a>"._CONFIRM."</center>";
			CloseTable();
			include_once("footer.php");
		}
	} else {
		include_once("header.php");
		storeadmin_displaymenu2();
		OpenTable();
		echo "<center>"._PASSWORDDIFFERENT."<p>";
		echo _BACKTO."<a href='javascript:history.back();'>"._PREVIOUS."</a>"._CONFIRM."</center>";
		CloseTable();
		include_once("footer.php");
	}
}


/**
 * 將管理員設定為啟用狀態的畫面
 * @param $_REQUEST["pkid"]	管理員編號
 * @param $_REQUEST["sel"]	選單種類
 */
function enable_sadmin()
{
	$sid = $_REQUEST["pkid"];
	$sel = $_REQUEST["sel"];
	$rs = $GLOBALS['adoconn']->Execute("select sid,said,sapw,".ORGTABLE_FIELD_NAME." from ".ADOPREFIX."_storeadmin,".ADOPREFIX.ORGTABLE." where sid=".$sid." and ".ADOPREFIX."_storeadmin.storeid=".ADOPREFIX.ORGTABLE.".".ORGTABLE_FIELD_ID);
	if ($rs and !$rs->EOF) {
		include_once("header.php");
		storeadmin_displaymenu2();
		OpenTable();
		echo "<table>"
		."<tr><td>"._SAID.": </td><td>".$rs->fields['said']."</td></tr>"
		."<tr><td>"._STORENAME.": </td><td>".$rs->fields[ORGTABLE_FIELD_NAME]."</td></tr>"
		."<tr><td>"._PASSWORD.": </td><td>".$rs->fields['sapw']."</td></tr>"
		."<tr>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=SETSADMINENABLEED&sid=$sid&sel=$sel&PHPSESSID=".session_id()."'>"._YES."</a></td>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTDELSADMIN&sel=$sel&PHPSESSID=".session_id()."'>"._NO."</a></td>"
		."</tr>"
		."</table>";
		CloseTable();
		include_once("footer.php");
	} else {
		Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTDELSADMIN&sel=$sel&PHPSESSID=".session_id());
	}
	if ($rs)    $rs->Close();
}


/**
 * 將管理員設定為啟用狀態
 * @param $_REQUEST["sid"] 管理員編號
 * @param $_REQUEST["sel"] 選單種類
 */
function db_enable_sadmin()
{
	$sid = $_REQUEST["sid"];
	$sel = $_REQUEST["sel"];
	$GLOBALS['adoconn_m']->StartTrans();
	$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_storeadmin set enable=1 where sid=".$sid);
	$GLOBALS['adoconn_m']->CompleteTrans();
	Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSADMIN&sel=$sel&PHPSESSID=".session_id());
}


/**
 * 設定管理員權限的畫面
 * @param $_REQUEST["pkid"]	管理員編號
 * @param $_REQUEST["sel"]	選單種類
 */
function set_sadmin_permit()
{
	$sid = $_REQUEST["pkid"];
	$sel = $_REQUEST["sel"];
	ob_start();
	include_once("header.php");
	storeadmin_displaymenu2();
	OpenTable();
	//OpenTable4();
	$sad = $GLOBALS['adoconn']->Execute("SELECT said FROM ".ADOPREFIX."_storeadmin WHERE enable=1 AND sid=".$sid);
	if ($sad and !$sad->EOF) {
		//echo $sad->fields["said"];
		echo "<center><font class='fieldtitle'>"._SAID.":".stripslashes($sad->fields['said'])."</font></center>";
	}
	if ($sad)    $sad->Close();
	//CloseTable4();
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>"
	."<input type='hidden' name='op' value='storeadmin'>"
	."<input type='hidden' name='op2' value='SETSADMINPERMITED'>"
	."<input type='hidden' name='sel' value='$sel'>"
	."<input type='hidden' name='sid' value='$sid'>";
	/* 顯示權限功能資料 */
	$function = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_storefunction where enable=1");
	$permit = $GLOBALS['adoconn']->Execute("select fid from ".ADOPREFIX."_storepermit where sid=".$sid);
	if ($function and !$function->EOF) {
		echo "<TABLE CLASS='title' align='center'>"
		."<TR>"
		."<TD>"._SET."</TD>"
		."<TD>"._SFUNCTIONNAME."</TD>"
		."</TR>";
		while (!$function->EOF) {
			$chk = "";
			if ($permit) {
				$permit->MoveFirst();
				while (!$permit->EOF) {
					if ($permit->fields['fid']==$function->fields['id']) {
						$chk = "checked";
						break;
					}
					$permit->MoveNext();
				}
			}
			echo "<tr class='content'>"
			."<TD><input type='checkbox' name='function[]' value='".stripslashes($function->fields['id'])."' $chk></TD>"
			."<TD>".stripslashes($function->fields['sfid'])."( ".stripslashes($function->fields['sfname'])." )</td>"
			."</tr>";
			$function->MoveNext();
		}
	}
	if ($function)	$function->Close();
	if ($permit)	$permit->Close();
	echo "<TR><TD colspan='2' align='center'><input type='submit'><input type='reset'></TD></TR>"
	."</table>"
	."</form>";
	CloseTable();
	include_once("footer.php");
	ob_end_flush();
}


/**
 * 設定管理員權限
 * @param $_REQUEST["sid"] 		管理員編號
 * @param $_REQUEST["function"] 權限功能編號
 * @param $_REQUEST["sel"]		選單種類
 */
function db_set_sadmin_permit()
{
	$aid = $_SESSION['aid'];
	$sid = $_REQUEST["sid"];
	$function = $_REQUEST["function"];
	$sel = $_REQUEST["sel"];
	$now = date("Y-m-d H:i:s");
	$GLOBALS['adoconn_m']->StartTrans();
	$GLOBALS['adoconn_m']->Execute("delete from ".ADOPREFIX."_storepermit where sid=".$sid);
	for ($i=0; $i < count($function); $i++)
		$GLOBALS['adoconn_m']->Execute("insert into ".ADOPREFIX."_storepermit (sid,fid,lastmod,admin) values (".$sid.",".$function[$i].",'".$now."','".$aid."')");
	$GLOBALS['adoconn_m']->CompleteTrans();
	Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSADMIN&sel=$sel&PHPSESSID=".session_id());
}


/**
 * 列出權限功能
 * @param $_REQUEST["sel"] 選單種類
 */
function list_sfunction()
{
	$sel = $_REQUEST["sel"];
	include_once("header.php");
	storeadmin_displaymenu2();
	OpenTable();
	// include_once("lib/mypager.inc");
	include_once("lib/dbpager.inc.php");
	$sql = "select id,sfid,sfname from ".ADOPREFIX."_storefunction where enable=1";
	$colnames= array(_ID,_SFUNCTIONID,_SFUNCTIONNAME,_FUNCTIONS);
    $links[0]['link']="op=storeadmin&op2=UPDATESFUNCTION&sel=$sel&PHPSESSID=".session_id();
    $links[0]['label']=_EDIT;    
    $links[1]['link']="op=storeadmin&op2=DELSFUNCTION&sel=$sel&PHPSESSID=".session_id();
    $links[1]['label']=_DELETE; 
    $links[2]['link']="op=storeadmin&op2=SFUNCTIONSET&sel=$sel&PHPSESSID=".session_id();
    $links[2]['label']=_SETPERMIT; 
    $rows=dbpage($GLOBALS['adoconn'],$sql,$colnames,$links);
	/*
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'function',true);
	$GridHeader = array(_ID,_SFUNCTIONID,_SFUNCTIONNAME);
	$pager->setRenderGridLayout("width='100%' align='center' class='qqtable'",$GridHeader);
	$funcNames = array(_EDIT,_DELETE,_SETPERMIT);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=storeadmin&op2=UPDATESFUNCTION&sel=$sel&PHPSESSID=".session_id(),
					  $_SERVER['PHP_SELF']."?op=storeadmin&op2=DELSFUNCTION&sel=$sel&PHPSESSID=".session_id(),
					  $_SERVER['PHP_SELF']."?op=storeadmin&op2=SFUNCTIONSET&sel=$sel&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$op = "storeadmin&op2=LISTSFUNCTION&sel=$sel&PHPSESSID=".session_id();
	$pager->setOp($op);
	$pager->Render_Function($GLOBALS['perpage']);
	*/
	CloseTable();
	echo "<br>";
	include_once("footer.php");
}


/**
 * 列出已不使用的權限功能列表
 * @param $_REQUEST["sel"] 選單種類
 */
function list_delete_sfunction()
{
	$sel = $_REQUEST["sel"];
	include_once("header.php");
	storeadmin_displaymenu2();
	OpenTable();
	// include_once("lib/mypager.inc");
	include_once("lib/dbpager.inc.php");
	$sql = "select id,sfid,sfname from ".ADOPREFIX."_storefunction where enable=0";
	$colnames= array(_ID,_SFUNCTIONID,_SFUNCTIONNAME,_FUNCTIONS);
    $links[0]['link']="op=storeadmin&op2=SETSFUNCTIONENABLE&sel=$sel&PHPSESSID=".session_id();
    $links[0]['label']=_SETENABLE;    
    $rows=dbpage($GLOBALS['adoconn'],$sql,$colnames,$links);
	/*
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'function',true);
	$GridHeader = array(_ID,_SFUNCTIONID,_SFUNCTIONNAME);
	$pager->setRenderGridLayout("width='100%' align='center' class='qqtable'",$GridHeader);
	$funcNames = array(_SETENABLE);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=storeadmin&op2=SETSFUNCTIONENABLE&sel=$sel&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$op = "storeadmin&op2=LISTDELSFUNCTION&sel=$sel&PHPSESSID=".session_id();
	$pager->setOp($op);
	$pager->Render_Function($GLOBALS['perpage']);
	*/
	CloseTable();
	echo "<br>";
	include_once("footer.php");
}


/**
 * 新增權限功能基本資料的畫面
 * @param $_REQUEST["sel"] 選單種類
 */
function add_sfunction()
{
	include_once("header.php");
	$sel = $_REQUEST["sel"];
	storeadmin_displaymenu2();
	OpenTable();
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>"
	."<input type='hidden' name='op' value='storeadmin'>"
	."<input type='hidden' name='op2' value='ADDEDSFUNCTION'>"
	."<input type='hidden' name='sel' value='$sel'>"
	."<table>"
	."<tr>"
	."<td>"._SFUNCTIONID.": </td>"
	."<td><input type='text' name='sfid'></td>"
	."</tr>"
	."<tr>"
	."<td>"._SFUNCTIONNAME.": </td>"
	."<td><input type='text' name='sfname'></td>"
	."</tr>"
	."<tr>"
	."<td><input type='submit'></td>"
	."<td><input type='reset'></td>"
	."</tr>"
	."</table>"
	."</form>";
	CloseTable();
	echo "<br>";
	include_once("footer.php");
}


/**
 * 新增權限功能基本資料
 * @param $_REQUEST["sfid"]   權限功能編號
 * @param $_REQUEST["sfname"] 權限功能名稱
 * @param $_REQUEST["sel"]	  選單種類
 */
function db_add_sfunction()
{
	$sfid = $_REQUEST["sfid"];
	$sfname = $_REQUEST["sfname"];
	$sel = $_REQUEST["sel"];
	if(!empty($sfid) and !empty($sfname)) {
		$sfid = addslashes($sfid);
		$sfname = addslashes($sfname);
		$GLOBALS['adoconn_m']->Execute("INSERT INTO ".ADOPREFIX."_storefunction(sfid,sfname,enable) values('".$sfid."','".$sfname."',1)");
		
	}
	Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSFUNCTION&sel=$sel&PHPSESSID=".session_id());
}


/**
 * 確認刪除權限功能基本資料的畫面
 * @param $_REQUEST["pkid"]	權限功能編號
 * @param $_REQUEST["sel"] 	選單種類
 */
function delete_sfunction()
{
	$pkid = $_REQUEST["pkid"];
	$sel = $_REQUEST["sel"];
	$rs = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_storefunction where id=".$pkid);
	if ($rs and !$rs->EOF)
	{
		include_once("header.php");
		storeadmin_displaymenu2();
		OpenTable();
		echo "<table>"
		."<tr><td>"._SFUNCTIONID.": </td><td>".stripslashes($rs->fields['sfid'])."</td></tr>"
		."<tr><td>"._SFUNCTIONNAME.": </td><td>".stripslashes($rs->fields['sfname'])."</td></tr>"
		."<tr>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=DELEDSFUNCTION&pkid=$pkid&sel=$sel&PHPSESSID=".session_id()."'>"._YES."</a></td>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSFUNCTION&sel=$sel&PHPSESSID=".session_id()."'>"._NO."</a></td>"
		."</tr>"
		."</table>";
		CloseTable();
		include_once("footer.php");
	} else {
		Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSFUNCTION&sel=$sel&PHPSESSID=".session_id());
	}
    if ($rs)    $rs->Close();
}


/**
 * 刪除權限功能基本資料
 * @param $_REQUEST["pkid"]	權限功能編號
 * @param $_REQUEST["sel"]	選單種類
 */
function db_delete_sfunction()
{
	$pkid = $_REQUEST["pkid"];
	$sel = $_REQUEST["sel"];
	$GLOBALS['adoconn_m']->StartTrans();
	$GLOBALS['adoconn_m']->Execute("delete from ".ADOPREFIX."_storepermit where fid=".$pkid);
	$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_storefunction set enable=0 where id=".$pkid);
	$GLOBALS['adoconn_m']->CompleteTrans();
	Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSFUNCTION&sel=$sel&PHPSESSID=".session_id());
}


/**
 * 修改權限功能基本資料的畫面
 * @param $_REQUEST["pkid"] 權限功能編號
 * @param $_REQUEST["sel"]  選單種類
 */
function update_sfunction()
{
	$pkid = $_REQUEST["pkid"];
	$sel = $_REQUEST["sel"];
	$rs = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_storefunction where id=".$pkid);
	if ($rs && !$rs->EOF) {
		include_once("header.php");
		storeadmin_displaymenu2();
		OpenTable();
		echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>"
		."<input type='hidden' name='op' value='storeadmin'>"
		."<input type='hidden' name='op2' value='UPDATEEDSFUNCTION'>"
		."<input type='hidden' name='sel' value='$sel'>"
		."<input type='hidden' name='pkid' value='".$rs->fields['id']."'>"
		."<table>"
		."<tr>"
		."<td>"._SFUNCTIONID.": </td>"
		."<td>&nbsp;".stripslashes($rs->fields['sfid'])."&nbsp;</td>"
		."</tr>"
		."<tr>"
		."<td>"._SFUNCTIONNAME.": </td>"
		."<td><input type='text' name='sfname' value='".stripslashes($rs->fields['sfname'])."'></td>"
		."</tr>"
		."<tr>"
		."<td><input type='submit'></td>"
		."<td><input type='reset'></td>"
		."</tr>"
		."</table>"
		."</form>";
		CloseTable();
		include_once("footer.php");
	} else {
		Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSFUNCTION&sel=$sel&PHPSESSID=".session_id());
	}
	if ($rs)    $rs->Close();
}


/**
 * 修改權限功能基本資料
 * @param $_REQUEST["pkid"]   權限功能編號
 * @param $_REQUEST["sfname"] 權限功能名稱
 * @param $_REQUEST["sel"]    選單種類
 */
function db_update_sfunction()
{
	$id = $_REQUEST["pkid"];
	$sfname = $_REQUEST["sfname"];
	$sel = $_REQUEST["sel"];
	$sfname = addslashes($sfname);
	$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_storefunction set sfname='".$sfname."' where id=".$id);
	Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSFUNCTION&sel=$sel&PHPSESSID=".session_id());
}


/**
 * 設定權限給群組或管理員的畫面
 * @param $_REQUEST["pkid"]	權限功能編號
 * @param $_REQUEST["sel"]	選單種類
 */
function set_permit()
{
	$pkid = $_REQUEST["pkid"];
	$sel = $_REQUEST["sel"];
	include_once("header.php");
	storeadmin_displaymenu2();
	OpenTable();
	//OpenTable4();
	$sfunc = $GLOBALS['adoconn']->Execute("SELECT sfname FROM ".ADOPREFIX."_storefunction WHERE enable=1 AND id=".$pkid);
	if ($sfunc and !$sfunc->EOF) {
		//echo $sfunc->fields["sfname"];
		echo "<center><font class='fieldtitle'>"._SFUNCTIONNAME.":".stripslashes($sfunc->fields['sfname'])."</font></center>";
	}
	if ($sfunc)    $sfunc->Close();
	//CloseTable4();
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>"
	."<input type='hidden' name='op' value='storeadmin'>"
	."<input type='hidden' name='op2' value='SFUNCTIONSETED'>"
	."<input type='hidden' name='sel' value='$sel'>"
	."<input type='hidden' name='pkid' value='$pkid'>";
	// 顯示使用者資料
	$storeadmin = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_storeadmin where enable=1");
	$permit = $GLOBALS['adoconn']->Execute("select sid from ".ADOPREFIX."_storepermit where fid='".$pkid."'");
	if ($storeadmin and !$storeadmin->EOF) {
		echo "<TABLE CLASS='title' align='center'>"
		."<TR>"
		."<TD>"._SET."</TD>"
		."<TD>"._SAID."</TD>"
		."</TR>";
		while (!$storeadmin->EOF) {
			$chk = "";
			if ($permit) $permit->MoveFirst();
			while ($permit and !$permit->EOF) {
				if ($permit->fields['sid']==$storeadmin->fields['sid']) {
					$chk = "checked";
					break;
				}
				$permit->MoveNext();
			}
			echo "<tr class='content'>"
			."<TD><input type='checkbox' name='sids[]' value='".$storeadmin->fields['sid']."' $chk></TD>"
			."<TD>".$storeadmin->fields['said']."</td>"
			."</tr>";
			$storeadmin->MoveNext();
		}
		echo "<TR><TD colspan='2' align='center'><input type='submit'><input type='reset'></TD></TR>"
		."</TABLE>";
	}
	else
	{
        echo "<TABLE align='center'>"
		."<TR><TD>"._NOSADMINTOSET."</TD></TR>"
		."</TABLE>";
	}
	if ($storeadmin)	$storeadmin->Close();
	if ($permit)	$permit->Close();
	echo "</form>";
	CloseTable();
	echo "<br>";
	include_once("footer.php");
}


/**
 * 設定權限給群組或管理員
 * @param functionid	權限功能編號
 * @param storeadmin	管理員編號
 * @param group		群組編號
 * @param sel 		選單種類
 */
function db_set_permit()
{
	$aid = $_SESSION['aid'];
	$pkid = $_REQUEST["pkid"];
	$sel = $_REQUEST["sel"];
	$sids = $_REQUEST["sids"];
	$now = date("Y-m-d H:i:s");
	$GLOBALS['adoconn_m']->StartTrans();
	$GLOBALS['adoconn_m']->Execute("delete from ".ADOPREFIX."_storepermit where fid=".$pkid);
	for($i=0; $i < count($sids); $i++) {
		$GLOBALS['adoconn_m']->Execute("insert into ".ADOPREFIX."_storepermit(sid,fid,lastmod,admin) values('".$sids[$i]."','".$pkid."','".$now."','".$aid."')");
	}
	$GLOBALS['adoconn_m']->CompleteTrans();
	Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSFUNCTION&sel=$sel&PHPSESSID=".session_id());
}


/**
 * 將權限功能設定為啟用狀態的畫面
 * @param $_REQUEST["pkid"]	權限功能編號
 * @param $_REQUEST["sel"]	選單種類
 */
function enable_sfunction()
{
	$pkid = $_REQUEST["pkid"];
	$sel = $_REQUEST["sel"];
	$rs = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_storefunction where id=".$pkid);
	if ($rs && !$rs->EOF)
	{
		include_once("header.php");
		storeadmin_displaymenu2();
		OpenTable();
		echo "<table>"
		."<tr><td>"._SFUNCTIONID.": </td><td>".stripslashes($rs->fields['sfid'])."</td></tr>"
		."<tr><td>"._SFUNCTIONNAME.": </td><td>".stripslashes($rs->fields['sfname'])."</td></tr>"
		."<tr>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=SETSFUNCTIONENABLEED&pkid=$pkid&sel=$sel&PHPSESSID=".session_id()."'>"._YES."</a></td>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTDELSFUNCTION&sel=$sel&PHPSESSID=".session_id()."'>"._NO."</a></td>"
		."</tr>"
		."</table>";
		CloseTable();
		include_once("footer.php");
	} else {
		Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSFUNCTION&sel=$sel&PHPSESSID=".session_id());
	}
	if ($rs)    $rs->Close();
}


/**
 * 將權限功能設定為啟用狀態
 * @param $_REQUEST["pkid"]	權限功能編號
 * @param $_REQUEST["sel"]	選單種類
 */
function db_enable_sfunction()
{
	$pkid = $_REQUEST["pkid"];
	$sel = $_REQUEST["sel"];
	$GLOBALS['adoconn_m']->StartTrans();
	$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_storefunction set enable=1 where id=".$pkid);
	$GLOBALS['adoconn_m']->CompleteTrans();
	Header("Location: ".$_SERVER['PHP_SELF']."?op=storeadmin&op2=LISTSFUNCTION&sel=$sel&PHPSESSID=".session_id());
}

//login log記錄列表
function log_list()
{
	storeadmin_displaymenu();
	OpenTable();
	echo "<h3>"._LOGFUNCTION."</h3>";
	CloseTable();
	echo "<br/>";
	OpenTable();
	// include_once("lib/mypager.inc");	
	include_once("lib/dbpager.inc.php");
	$sql = "select id , loginname, time, op, op2 , function ,ip from ".ADOPREFIX."_storeadmin_login order by id desc";
	$colnames= array(_ID,_LOGIN,_LOGDATEIME,"OP","OP2",_FUNCTIONS,_LOGIP);
    $rows=dbpage($GLOBALS['adoconn'],$sql,$colnames,$links,0);
	/*
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'sadmin',true);
	$GridHeader = array(_ID,_LOGIN,_LOGDATEIME,"OP","OP2",_FUNCTIONS,_LOGIP);
	$pager->setRenderGridLayout("width='100%' align='center' class='qqtable'",$GridHeader);
	$pager->setFunctions($funcNames,$funcUrls);
	$pager->setOp("storeadmin&op2=LOGLIST&sel=$sel&PHPSESSID=".session_id());
	$pager->Render_Function($GLOBALS['perpage']);
	*/
	CloseTable();
}


if ($_REQUEST['op']=="storeadmin" && isAuthority($_SESSION['aid'],$_REQUEST['op']))
{
	if (empty($_REQUEST['op2'])) {
		storeadmin_displaymenu();
		include_once("footer.php");
	}
	switch ($_REQUEST['op2'])
	{
        //列出功能表
		case "SUBMENU":
			storeadmin_displaymenu2();
			include_once("footer.php");
			break;
		
		case "LOGLIST":
			//storeadmin_displaymenu2();			
			log_list();
			include_once("footer.php");
			break;

		//管理員列表
		case "LISTSADMIN":
			list_sadmin();
			break;

		//列出已不使用的管理員列表
		case "LISTDELSADMIN":
			list_delete_sadmin();
			break;

		//新增管理員基本資料的畫面
		case "ADDSADMIN":
			add_sadmin();
			break;

		//新增管理員基本資料
		case "ADDEDSADMIN":
			db_add_sadmin();
			break;

		//確認刪除管理員基本資料的畫面
		case "DELSADMIN":
			delete_sadmin();
			break;

		//刪除管理員基本資料
		case "DELEDSADMIN":
			db_delete_sadmin();
			break;

		//將管理員設定為啟用狀態的畫面
		case "SETSADMINENABLE":
			enable_sadmin();
			break;

		//將管理員設定為啟用狀態
		case "SETSADMINENABLEED":
			db_enable_sadmin();
			break;

		//修改管理員基本資料的畫面
		case "UPDATESADMIN":
			update_sadmin();
			break;

		//修改管理員基本資料
		case "UPDATEEDSADMIN":
			db_update_sadmin();
			break;

		//設定管理員權限的畫面
		case "SETSADMINPERMIT":
			set_sadmin_permit();
			break;

		//設定管理員權限
		case "SETSADMINPERMITED":
			db_set_sadmin_permit();
			break;

		//權限功能列表
		case "LISTSFUNCTION":
			list_sfunction();
			break;

		//列出已不使用的權限功能列表
		case "LISTDELSFUNCTION":
			list_delete_sfunction();
			break;

		//新增權限功能基本資料的畫面
		case "ADDSFUNCTION":
			add_sfunction();
			break;

		//新增權限功能基本資料
		case "ADDEDSFUNCTION":
			db_add_sfunction();
			break;

		//修改權限功能基本資料的畫面
		case "UPDATESFUNCTION":
			update_sfunction();
			break;

		//修改權限功能基本資料
		case "UPDATEEDSFUNCTION":
			db_update_sfunction();
			break;

		//確認刪除權限功能基本資料的畫面
		case "DELSFUNCTION":
			delete_sfunction();
			break;

		//刪除權限功能基本資料
		case "DELEDSFUNCTION":
			db_delete_sfunction();
			break;

		//將權限功能設定為啟用狀態的畫面
		case "SETSFUNCTIONENABLE":
			enable_sfunction();
			break;

		//將權限功能設定為啟用狀態
		case "SETSFUNCTIONENABLEED":
			db_enable_sfunction();
			break;

		//設定權限給群組或管理員的畫面
		case "SFUNCTIONSET":
			set_permit();
			break;

		//設定權限
		case "SFUNCTIONSETED":
			db_set_permit();
			break;
	}
}
?>
