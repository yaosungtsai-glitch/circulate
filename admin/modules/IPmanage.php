<?php
/**
 * Company: 
 * Program: IPmanage.php
 * Author:  Ken Tsai
 * date: from 2021-02-10 
 * Version: 2.0
 * Description: 站台登入IP管理
 */

Header("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
//if (!eregi(ADMINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if (!preg_match('/'.ADMINPAGE.'/', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
include_once("admin/language/IPmanage-".DEFAULTLANGUAGE.".php");
include_once("admin/function/IPmanage.inc.php");


/**
 * 顯示IP管理選單
 */
function displaymenu()
{
	include_once("header.php");
	include_once("admin/includes/IPmanage-js.php");
	GraphicAdmin();
	OpenTable();
	echo "<font class='toptitle'>"._IPMANAGEADMIN."</font>\n";
	CloseTable();
	echo "<br/>\n";
	//顯示
	OpenTable();
	echo "<table class='AdminMenu'>\n"
	."<tr>\n"
	."<td>[ <a href='".$_SERVER['PHP_SELF']."?op=IPmanage&op2=ServiceIPmanage&PHPSESSID=".session_id()."' class='categorylinktext'>"._SETIPMANAGE."</a> ]</td>\n"		//是否強制控管商店可登入IP
	."<td>[ <a href='".$_SERVER['PHP_SELF']."?op=IPmanage&op2=listAdminIP&PHPSESSID=".session_id()."' class='categorylinktext'>"._LISTADMINIP."</a> ]</td>\n"			//ADMIN介面IP區段列表
	."<td>[ <a href='".$_SERVER['PHP_SELF']."?op=IPmanage&op2=listServiceIP&PHPSESSID=".session_id()."' class='categorylinktext'>"._LISTSERVICEIP."</a> ]</td>\n"	//商店IP區段列表
	."</tr>\n"
	."</table>\n";
	CloseTable();
	echo "<br/>\n";
}


/**
 * 顯示ADMIN介面的可登入位置
 */
function listAdminIP()
{
	include_once("header.php");
	include_once("lib/mypager.inc");
	displaymenu();
	OpenTable();
	echo "<font class='toptitle'>"._LISTADMINIP."</font>\n";
	echo "<br/>\n";
	//CASE WHEN,concat(..,..),if(exp,str1,str2)等return string語法加 DBTABLE_FORMAT 作輸出校對 added by summer,2008/01/04
	$strSQL = "SELECT id, "
	." CAST(CONCAT(ipsec1,'.',ipsec2,'.',ipsec3,'.',ipsec4) AS CHAR) ".DBTABLE_FORMAT." as ipstart,"
	." CAST(CONCAT(ipsec1,'.',ipsec2,'.',ipsec3,'.',ipsec5) AS CHAR) ".DBTABLE_FORMAT." as ipend"
	." FROM ".ADOPREFIX."_authors_ipmanage";
	$pager = new MyPager($GLOBALS['adoconn'],$strSQL,'storeipmanage',true);
	$GridHeader = array(_ID,_IPSTART,_IPEND,_MANAGE);
 	$pager->setRenderGridLayout("width='100%' align='center' border='1'",$GridHeader);
	$funcNames = array(_EDIT,_DELETE);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=".$_REQUEST['op']."&op2=editAdminIP&PHPSESSID=".session_id(),
					  $_SERVER['PHP_SELF']."?op=".$_REQUEST['op']."&op2=delAdminIP&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$op = $_REQUEST['op']."op2=".$_REQUEST['op2']."&PHPSESSID=".session_id();
	$pager->setOp($op);
	$pager->Render_Function($GLOBALS['perpage']);
	CloseTable();
	echo "<br/>\n";

	//新增ADMIN介面可登入IP位置的畫面
	OpenTable();
	echo "<font class='undertitle'>"._ADDADMINIP."</font>\n";
	echo "<form name='IPManage' action='".$_SERVER['PHP_SELF']."' method='post'>\n"
	."<input type='hidden' name='op' value='".$_REQUEST['op']."'>\n"
	."<input type='hidden' name='fromop2' value='".$_REQUEST['op2']."'>\n"
	."<input type='hidden' name='op2' value='px_AddAdminIP'>\n"
	."<table align='center'>\n"
	."<tr>\n"
	."<td class='notabletitle'>"._IPSTART."~"._IPEND.":</td>\n"
	."<td class='notablecontent'>\n"
	."<input type='text' name='ipsec1' size='3' maxlength='3'> . "
	."<input type='text' name='ipsec2' size='3' maxlength='3'> . "
	."<input type='text' name='ipsec3' size='3' maxlength='3'> . "
	."<input type='text' name='ipsec4' size='3' maxlength='3'> ~ "
	."<input type='text' name='ipsec5' size='3' maxlength='3'>\n"
	."</td>\n"
	."</tr>\n"
	."<tr>\n"
	."<td><input type='button' onclick='checkIP()' value='"._OK."'></td>\n"
	."<td><input type='reset'></td>\n"
	."</tr>\n"
	."</table>\n"
	."</form>\n";
	CloseTable();
	echo "<br/>\n";
	include_once("footer.php");
}


/**
 * 新增ADMIN介面可登入IP位置
 */
function px_addAdminIP()
{
	$ipsec1 = $_POST['ipsec1'];		//IP位置 class A
	$ipsec2 = $_POST['ipsec2'];     //IP位置 class B
	$ipsec3 = $_POST['ipsec3'];     //IP位置 class C
	$ipsec4 = $_POST['ipsec4'];     //IP位置 class D 開始
	$ipsec5 = $_POST['ipsec5'];		//IP位置 class D 結束
	for ($i=1; $i<=5; $i++)
	{
		$ipsec = "ipsec$i";
		if (!$$ipsec || is_nan($$ipsec))
		{
			$errmsg = _CANNOTNULL._OR._BE1TO255;
			show_errmsg($_POST['op'],$errmsg);
			exit();
		}
	}
	$strsql = "insert into ".ADOPREFIX."_authors_ipmanage(ipsec1,ipsec2,ipsec3,ipsec4,ipsec5) values(?,?,?,?,?)";
	$stmt = $GLOBALS['adoconn']->Prepare($strsql);
	$param = array($ipsec1,$ipsec2,$ipsec3,$ipsec4,$ipsec5);
	$GLOBALS['adoconn']->Execute($stmt,$param);
	$formaction = $_SERVER['PHP_SELF'];
	$fields['op'] = $_POST['op'];
	$fields['op2'] = $_POST['fromop2'];
	$fields['PHPSESSID'] = session_id();
	POSTFORM($formaction,$fields);
}


/**
 * 修改ADMIN介面可登入IP位置的畫面
 */
function editAdminIP()
{
	include_once("header.php");
	displaymenu();
	OpenTable();
	echo "<font class='undertitle'>"._EDITADMINIP."</font>\n";
	$pkid = $_REQUEST['pkid'];
	$ipmanage = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_authors_ipmanage where id=$pkid");
	if ($ipmanage && !$ipmanage->EOF)
	{
		echo "<form name='IPManage' action='".$_SERVER['PHP_SELF']."' method='post'>\n"
		."<input type='hidden' name='op' value='".$_REQUEST['op']."'>\n"
		."<input type='hidden' name='fromop2' value='listAdminIP'>\n"
		."<input type='hidden' name='op2' value='px_editAdminIP'>\n"
		."<input type='hidden' name='pkid' value='$pkid'>\n"
		."<table align='center'>\n"
		."<tr>\n"
		."<td class='notabletitle'>"._IPSTART."~"._IPEND.":</td>\n"
		."<td class='notablecontent'>\n"
		."<input type='text' name='ipsec1' size='3' maxlength='3' value='".$ipmanage->fields['ipsec1']."'> . "
		."<input type='text' name='ipsec2' size='3' maxlength='3' value='".$ipmanage->fields['ipsec2']."'> . "
		."<input type='text' name='ipsec3' size='3' maxlength='3' value='".$ipmanage->fields['ipsec3']."'> . "
		."<input type='text' name='ipsec4' size='3' maxlength='3' value='".$ipmanage->fields['ipsec4']."'> ~ "
		."<input type='text' name='ipsec5' size='3' maxlength='3' value='".$ipmanage->fields['ipsec5']."'>\n"
		."<input type='button' onclick='checkIP()' value='"._OK."'>\n"
		."</td>\n"
		."</table>\n"
		."</form>\n";
	} else {
		$errmsg = _IPID_ERROR;
		show_errmsg($_REQUEST['op'],$errmsg);
		exit();
	}
	if ($ipmanage) $ipmanage->Close();
	CloseTable();
	echo "<br/>\n";
	include_once("footer.php");

}


/**
 * 修改ADMIN介面可登入IP位置
 */
function px_editAdminIP()
{
	$pkid = $_POST['pkid'];		//編號
	$ipsec1 = $_POST['ipsec1']; //IP位置 class A
	$ipsec2 = $_POST['ipsec2']; //IP位置 class B
	$ipsec3 = $_POST['ipsec3']; //IP位置 class C
	$ipsec4 = $_POST['ipsec4']; //IP位置 class D (開始)
	$ipsec5 = $_POST['ipsec5']; //IP位置 class D (結束)

	for ($i=1; $i<=5; $i++)
	{
		$ipsec = "ipsec$i";
		if (!$$ipsec || is_nan($$ipsec))
		{
			$errmsg = _CANNOTNULL._OR._BE1TO255;
			show_errmsg($_REQUEST['op'],$errmsg);
			exit();
		}
	}
	$strsql = "UPDATE ".ADOPREFIX."_authors_ipmanage"
	." SET ipsec1=?, ipsec2=?, ipsec3=?, ipsec4=?, ipsec5=? where id=?";
	$param = array($ipsec1,$ipsec2,$ipsec3,$ipsec4,$ipsec5,$pkid);
	$stmt = $GLOBALS['adoconn']->Prepare($strsql);
	$GLOBALS['adoconn']->Execute($stmt, $param);
	$formaction = $_SERVER['PHP_SELF'];
	$fields['op'] = $_POST['op'];
	$fields['op2'] = $_POST['fromop2'];
	$fields['PHPSESSID'] = session_id();
	POSTFORM($formaction, $fields);
}


/**
 * 刪除ADMIN介面可登入IP位置
 */
function delAdminIP()
{
	$pkid = $_REQUEST['pkid'];
	$strsql = "DELETE FROM ".ADOPREFIX."_authors_ipmanage WHERE id=$pkid";
	$GLOBALS['adoconn']->Execute($strsql);
	$formaction = $_SERVER['PHP_SELF'];
	$fields['op'] = $_REQUEST['op'];
	$fields['op2'] = "listAdminIP";
	$fields['PHPSESSID'] = session_id();
	POSTFORM($formaction, $fields);
}


/**
* 顯示service介面商店的可登入位置
* @param selstore 商店編號
*/
function listServiceIP()
{
	include_once("header.php");
	include_once("lib/mypager.inc");
	displaymenu();
	OpenTable();
	echo "<font class='undertitle'>"._LISTSERVICEIP."</font>\n";
	//CASE WHEN,concat(..,..),if(exp,str1,str2)等return string語法加 DBTABLE_FORMAT 作輸出校對 added by summer,2008/01/04
	$strSQL = "SELECT i.id, o.".ORGTABLE_FIELD_NAME.", "
	." CAST(CONCAT(i.ipsec1,'.',i.ipsec2,'.',i.ipsec3,'.',i.ipsec4) AS CHAR) ".DBTABLE_FORMAT." as ipstart,"
	." CAST(CONCAT(i.ipsec1,'.',i.ipsec2,'.',i.ipsec3,'.',i.ipsec5) AS CHAR) ".DBTABLE_FORMAT." as ipend"
	." FROM ".ADOPREFIX.ORGTABLE." o, ".ADOPREFIX."_ipmanage i"
	." WHERE o.".ORGTABLE_FIELD_ID."=i.storeid"
	." ORDER BY i.storeid, i.ipsec1, i.ipsec2, i.ipsec3, i.ipsec4, i.ipsec5";

	$pager = new MyPager($GLOBALS['adoconn'],$strSQL,'storeipmanage',true);
	$GridHeader = array(_ID,_STORENAME,_IPSTART,_IPEND,_MANAGE);
	$pager->setRenderGridLayout("width='100%' align='center' border='1'",$GridHeader);
	$funcNames = array(_EDIT,_DELETE);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=".$_REQUEST['op']."&op2=editServiceIP&PHPSESSID=".session_id(),
					  $_SERVER['PHP_SELF']."?op=".$_REQUEST['op']."&op2=delServiceIP&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$op = $_REQUEST['op']."op2=".$_REQUEST['op2']."&PHPSESSID=".session_id();
	$pager->setOp($op);
	$pager->Render_Function($GLOBALS['perpage']);
	CloseTable();
	echo "<br/>\n";

	//新增service介面商店可登入IP位置的畫面
	$rs = getEnableStore();
	while ($rs && !$rs->EOF)
	{
		$strselstore .= "<option value='".$rs->fields[0]."'>".$rs->fields[1]."</option>\n";
		$rs->MoveNext();
	}
	if ($rs) $rs->Close();
	
	OpenTable();
	echo "<font class='undertitle'>"._ADDSERVICEIP."</font>\n";
	echo "<form name='IPManage' action='".$_SERVER['PHP_SELF']."' method='post'>\n"
	."<input type='hidden' name='op' value='".$_REQUEST['op']."'>\n"
	."<input type='hidden' name='fromop2' value='".$_REQUEST['op2']."'>\n"
	."<input type='hidden' name='op2' value='PX_ADDSERVICEIP'>\n"
	."<table align='center'>\n";
	//有商店資料才顯示設定畫面
	if (!empty($strselstore))
	{
        echo "<tr>\n"
		."<td class='notabletitle'>"._STORENAME.":</td>\n"
		."<td class='notablecontent'>\n"
		."<select name='selstore'>\n".$strselstore."</select>\n"
		."</td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td class='notabletitle'>"._IPSTART."~"._IPEND.":</td>\n"
		."<td class='notablecontent'>\n"
		."<input type='text' name='ipsec1' size='3' maxlength='3'> . "
		."<input type='text' name='ipsec2' size='3' maxlength='3'> . "
		."<input type='text' name='ipsec3' size='3' maxlength='3'> . "
		."<input type='text' name='ipsec4' size='3' maxlength='3'> ~ "
		."<input type='text' name='ipsec5' size='3' maxlength='3'>\n"
		."</td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td><input type='button' onclick='checkIP()' value='"._OK."'></td>\n"
		."<td><input type='reset'></td>\n"
		."</tr>\n";
	}
	else
	{
		echo "<tr><td>"._NOSTORETOSET."</td></tr>";
	}
	echo "</table>\n"
	."</form>\n";
	CloseTable();
	echo "<br/>\n";
	include_once("footer.php");
}


/**
 * 新增service介面商店的可登入IP位置
 */
function px_addServiceIP()
{
	$selstore = $_POST['selstore'];	//商店編號
	$ipsec1 = $_POST['ipsec1'];     //IP位置 class A
	$ipsec2 = $_POST['ipsec2'];     //IP位置 class B
	$ipsec3 = $_POST['ipsec3'];     //IP位置 class C
	$ipsec4 = $_POST['ipsec4'];     //IP位置 class D 開始
	$ipsec5 = $_POST['ipsec5'];     //IP位置 class D 結束
	for ($i=1; $i<=5; $i++)
	{
		$ipsec = "ipsec$i";
		if (!$$ipsec || is_nan($$ipsec))
		{
			$errmsg = _CANNOTNULL._OR._BE1TO255."<br>\n";
			show_errmsg($_POST['op'],$errmsg);
			exit();
		}
	}
	$sql_insert = "insert into ".ADOPREFIX."_ipmanage"
	."(storeid,ipsec1,ipsec2,ipsec3,ipsec4,ipsec5) values(?,?,?,?,?,?)";
	$param = array($selstore,$ipsec1,$ipsec2,$ipsec3,$ipsec4,$ipsec5);
	$stmt = $GLOBALS['adoconn']->Prepare($sql_insert);
	$GLOBALS['adoconn']->Execute($stmt,$param);
	$formaction = $_SERVER['PHP_SELF'];
	$fields['op'] = $_POST['op'];
	$fields['op2'] = $_POST['fromop2'];
	$fields['PHPSESSID'] = session_id();
	POSTFORM($formaction, $fields);
}


/**
 * 修改SERVICE介面可登入IP位置的畫面
 */
function editServiceIP()
{
	$pkid = $_GET['pkid'];
	$ipmanage = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_ipmanage where id=$pkid");
	if ($ipmanage && !$ipmanage->EOF)
	{
		include_once("header.php");
		displaymenu();
		OpenTable();
		$rs = getEnableStore();
		while ($rs && !$rs->EOF)
		{
			$sel = "";
			if ($ipmanage->fields['storeid']==$rs->fields[0]) $sel = "selected";
			$strselstore.="<option value='".$rs->fields[0]."' $sel>".$rs->fields[1]."</option>\n";
			$rs->MoveNext();
		}
		if ($rs)	$rs->Close();
		echo "<font class='undertitle'>"._EDITSERVICEIP."</font>\n";
		echo "<form name='IPManage' action='".$_SERVER['PHP_SELF']."' method='post'>\n"
		."<input type='hidden' name='op' value='".$_REQUEST['op']."'>\n"
		."<input type='hidden' name='fromop2' value='listServiceIP'>\n"
		."<input type='hidden' name='op2' value='px_editServiceIP'>\n"
		."<input type='hidden' name='pkid' value='$pkid'>\n"
		."<table align='center'>\n"
		."<tr>\n"
		."<td class='notabletitle'>"._STORENAME.":</td>\n"
		."<td class='notablecontent'><select name='selstore'>$strselstore</select></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td class='notabletitle'>"._IPSTART."~"._IPEND.":</td>\n"
		."<td class='notablecontent'>\n"
		."<input type='text' name='ipsec1' size='3' maxlength='3' value='".$ipmanage->fields['ipsec1']."'> . "
		."<input type='text' name='ipsec2' size='3' maxlength='3' value='".$ipmanage->fields['ipsec2']."'> . "
		."<input type='text' name='ipsec3' size='3' maxlength='3' value='".$ipmanage->fields['ipsec3']."'> . "
		."<input type='text' name='ipsec4' size='3' maxlength='3' value='".$ipmanage->fields['ipsec4']."'> ~ "
		."<input type='text' name='ipsec5' size='3' maxlength='3' value='".$ipmanage->fields['ipsec5']."'>\n"
		."<input type='button' onclick='checkIP()' value='"._OK."'>\n"
		."</td>\n"
		."</tr>\n"
		."</table>\n"
		."</form>\n";
		CloseTable();
		echo "<br/>\n";
		include_once("footer.php");
	} else {
		$errmsg = _IPID_ERROR;
		show_errmsg($_REQUEST['op'],$errmsg);
		exit();
	}
	if ($ipmanage) $ipmanage->Close();
}


/**
 * 修改service介面商店可登入IP位置
 */
function px_editServiceIP()
{
	$pkid = $_POST['pkid'];			//IP區段編號
	$selstore = $_POST['selstore'];	//商店編號
	$ipsec1 = $_POST['ipsec1'];		//IP位置 class A
	$ipsec2 = $_POST['ipsec2'];		//IP位置 class B
	$ipsec3 = $_POST['ipsec3'];     //IP位置 class C
	$ipsec4 = $_POST['ipsec4'];     //IP位置 class D 開始
	$ipsec5 = $_POST['ipsec5'];     //IP位置 class D 結束
	for ($i=1; $i<=5; $i++)
	{
		$ipsec = "ipsec$i";
		if (!$$ipsec || is_nan($$ipsec))
		{
			$errmsg = _CANNOTNULL._OR._BE1TO255;
			show_errmsg($_POST['op'],$errmsg);
			exit;
		}
	}
	$strsql = "UPDATE ".ADOPREFIX."_ipmanage"
	." SET storeid=?, ipsec1=?, ipsec2=?, ipsec3=?, ipsec4=?, ipsec5=? WHERE id=?";
	$stmt = $GLOBALS['adoconn']->Prepare($strsql);
	$param= array($selstore,$ipsec1,$ipsec2,$ipsec3,$ipsec4,$ipsec5,$pkid);
	$GLOBALS[adoconn]->Execute($stmt,$param);
	$formaction = $_SERVER['PHP_SELF'];
	$fields['op'] = $_POST['op'];
	$fields['op2'] = $_POST['fromop2'];
	$fields['PHPSESSID'] = session_id();
	POSTFORM($formaction,$fields);
}


/**
 * 刪除SERVICE介面可登入IP位置
 */
function delServiceIP()
{
	$pkid = $_REQUEST['pkid'];
	$strsql = "DELETE FROM ".ADOPREFIX."_ipmanage WHERE id=$pkid";
	$GLOBALS['adoconn']->Execute($strsql);
	$formaction = $_SERVER['PHP_SELF'];
	$fields['op'] = $_REQUEST['op'];
	$fields['op2'] = "listServiceIP";
	$fields['PHPSESSID'] = session_id();
	POSTFORM($formaction, $fields);
}


/**
 * 顯示可登入IP控管設定的畫面
 */
function ServiceIPmanage()
{
	include_once("header.php");
	displaymenu();
	//顯示是否控管ADMIN可登入IP的設定
	if ($GLOBALS['ADMINIPMANAGE'])
	{
		$chkadmin1 = "checked";
		$chkadmin2 = "";
	} else {
		$chkadmin1 = "";
		$chkadmin2 = "checked";
	}
	OpenTable();
	echo "<font class='undertitle'>"._SETADMINIPMANAGE."</font>\n";
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n"
	."<input type='hidden' name='op' value='".$_REQUEST['op']."'>\n"
	."<input type='hidden' name='op2' value='px_AdminIPmanage'>\n"
	."<input type='hidden' name='formop2' value='".$_REQUEST['op2']."'>\n"
	."<table align='center'>\n"
	."</tr>\n"
	."<td>"._YES."<input type='radio' name='config_adminipmanage' value='1' $chkadmin1></td>\n"
	."<td>"._NO ."<input type='radio' name='config_adminipmanage' value='0' $chkadmin2></td>\n"
	."</tr>\n"
	."<tr align='center'><td colspan='2'><input type='submit' value='"._OK."'> &nbsp;&nbsp; <input type='reset'></td></tr>\n"
	."</table>\n"
	."</form>\n";
	CloseTable();
	echo "<br/>\n";

	//顯示是否強制控管商店可登入IP的設定
	if ($GLOBALS['IPMANAGE'])
	{
		$chk1 = "checked";
		$chk2 = "";
	} else {
		$chk1 = "";
		$chk2 = "checked";
	}

	OpenTable();
	echo "<font class='undertitle'>"._SETSERVICEIPMANAGE."</font>\n";
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n"
	."<input type='hidden' name='op' value='".$_REQUEST['op']."'>\n"
	."<input type='hidden' name='op2' value='px_ServiceIPmanage'>\n"
	."<input type='hidden' name='formop2' value='".$_REQUEST['op2']."'>\n"
	."<table align='center'>\n"
	."</tr>\n"
	."<td>"._YES."<input type='radio' name='config_ipmanage' value='1' $chk1></td>\n"
	."<td>"._NO ."<input type='radio' name='config_ipmanage' value='0' $chk2></td>\n"
	."</tr>\n"
	."<tr align='center'><td colspan='2'><input type='submit' value='"._OK."'> &nbsp;&nbsp; <input type='reset'></td></tr>\n"
	."</table>\n"
	."</form>\n";
	CloseTable();
	echo "<br/>\n";

	//顯示是否個別控管商店可登入IP的設定
	OpenTable();
	echo "<font class='undertitle'>"._SETIPMANAGEBYEACH."</font>\n";
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n"
	."<input type='hidden' name='op' value='".$_REQUEST['op']."'>\n"
	."<input type='hidden' name='op2' value='px_setStoreIPmanage'>\n"
	."<input type='hidden' name='formop2' value='".$_REQUEST['op2']."'>\n"
	."<table align='center'>\n";
	$rs = $GLOBALS['adoconn']->Execute("select ".ORGTABLE_FIELD_ID.",".ORGTABLE_FIELD_NAME.",".ORGTABLE_FIELD_IPMANAGE." from ".ADOPREFIX.ORGTABLE." where ".ORGTABLE_FIELD_ENABLE."=1");
	if ($rs && $rs->RecordCount()>0)
	{
        while ($rs && !$rs->EOF)
		{
			$chk = "";
			if ($rs->fields[2]==1)	$chk = "checked";
			echo "<tr>\n"
			."<td><input type='checkbox' name='storeid[]' value='".$rs->fields[0]."' $chk></td><td>".$rs->fields[1]."</td>\n"
			."</tr>\n";
			$rs->MoveNext();
		}
		echo "<tr align='center'><td colspan='2'><input type='submit' value='"._OK."'> <input type='reset'></td></tr>\n";
	}
	else
	{
		echo "<tr><td>"._NOSTORETOSET."</td></tr>";
	}
	if ($rs) $rs->Close();
	echo "</table>\n"
	."</from>\n";
	CloseTable();
	include_once("footer.php");
}


/**
 * 設定是否控管ADMIN可登入IP
 */
function px_AdminIPmanage()
{ 
	$config_adminipmanage = $_POST['config_adminipmanage'];
	$GLOBALS['adoconn']->Execute("update ".ADOPREFIX."_config set adminipmanage='$config_adminipmanage'");
	$formaction = $_SERVER['PHP_SELF'];
	$fields['op'] = $_POST['op'];
	$fields['op2'] = "ServiceIPmanage";
	$fields['PHPSESSID'] = session_id();
	POSTFORM($formaction,$fields);
}


/**
 * 設定是否強制控管全部商店可登入IP
 */
function px_ServiceIPmanage()
{
	$config_ipmanage = $_POST['config_ipmanage'];
	$GLOBALS[adoconn]->Execute("update ".ADOPREFIX."_config set ipmanage='$config_ipmanage'");
	$formaction = $_SERVER['PHP_SELF'];
	$fields['op'] = $_POST['op'];
	$fields['op2'] = $_POST['formop2'];
	$fields['PHPSESSID'] = session_id();
	POSTFORM($formaction,$fields);
}


/**
 * 商店是否控管可登入IP位置
 */
function px_setStoreIPmanage()
{
	$storeid = $_POST['storeid'];
	$GLOBALS['adoconn']->StartTrans();
	$GLOBALS['adoconn']->Execute("update ".ADOPREFIX.ORGTABLE." set ".ORGTABLE_FIELD_IPMANAGE."=0");
	for ($i=0; $i<count($storeid); $i++)
		$GLOBALS['adoconn']->Execute("update ".ADOPREFIX.ORGTABLE." set ".ORGTABLE_FIELD_IPMANAGE."=1 where ".ORGTABLE_FIELD_ID."='".$storeid[$i]."'");
	$GLOBALS['adoconn']->CompleteTrans();
	$formaction = $_SERVER['PHP_SELF'];
	$fields['op'] = $_POST['op'];
	$fields['op2'] = $_POST['formop2'];
	$fields['PHPSESSID'] = session_id();
	POSTFORM($formaction,$fields);
}


if ($_REQUEST['op']=="IPmanage" && isAuthority($_SESSION['aid'],$_REQUEST['op']))
{
	switch ($_REQUEST['op2'])
	{
		//顯示service介面商店的可登入位置
		case "listServiceIP":
			listServiceIP();
			break;

		//顯示ADMIN介面的可登入位置
		case "listAdminIP":
			listAdminIP();
			break;

		//新增service介面商店可登入IP位置的畫面
		case "ADDSERVICEIP":
			addServiceIP();
			break;

		//新增service介面商店的可登入IP位置
		case "PX_ADDSERVICEIP":
			px_addServiceIP();
			break;

		//修改分店的可登入IP位置的畫面
		case "editServiceIP":
			editServiceIP();
			break;

		//修改service介面商店可登入IP位置
		case "px_editServiceIP":
			px_editServiceIP();
			break;

		//刪除分店的可登入IP位置
		case "delServiceIP":
			delServiceIP();
			break;

		//新增ADMIN介面可登入IP位置的畫面
		case "AddAdminIP":
			addAdminIP();
			break;

		//新增ADMIN介面可登入IP位置
		case "px_AddAdminIP":
			px_AddAdminIP();
			break;

		//修改ADMIN介面可登入IP位置的畫面
		case "editAdminIP":
			editAdminIP();
			break;

		//修改ADMIN介面可登入IP位置
		case "px_editAdminIP":
			px_editAdminIP();
			break;

		//刪除ADMIN介面可登入IP位置
		case "delAdminIP":
			delAdminIP();
			break;

		//顯示可登入IP控管設定的畫面
		case "ServiceIPmanage":
			ServiceIPmanage();
			break;

		//設定是否控管ADMIN可登入IP
		case "px_AdminIPmanage":
			px_AdminIPmanage();
			break;

		//設定是否強制控管全部商店可登入IP
		case "px_ServiceIPmanage":
			px_ServiceIPmanage();
			break;

		//設定是否控管商店可登入IP
		case "px_setStoreIPmanage":
			px_setStoreIPmanage();
			break;

		//顯示IP管理選單
		default:
			displaymenu();
			include_once("footer.php");
			break;
	}
}
?>
