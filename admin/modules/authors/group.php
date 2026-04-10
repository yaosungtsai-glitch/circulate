<?php
/**
* Company: 
* Program: group.php
* Author:  Ken Tsai
* Date:    from 2003.06.12
* Version: 2.0
*/

Header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
//if (!eregi(ADMINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if (!preg_match('/'.ADMINPAGE.'/', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }

/**
* 列出群組
* @param sel 選單選項
*/
function listgroup()
{
	$sel=$_GET['sel'];
	include_once("header.php");
	authors_displaymenu2($sel);
	OpenTable();
	echo "<center><font class='undertitle'>"._LISTGROUP."</font></center>";
	include_once("lib/dbpager.inc.php");
	$sql = "select id,gid,gname from ".ADOPREFIX."_group where enable=1";
    $colnames= array(_ID,_GROUPID,_GROUPNAME,_FUNCTIONS);
    $links[0]['link']="op=authors&op2=UPDATEGROUP&sel=$sel&PHPSESSID=".session_id();
    $links[0]['label']=_EDIT;    
    $links[1]['link']="op=authors&op2=DELGROUP&sel=$sel&PHPSESSID=".session_id();
    $links[1]['label']=_DELETE;
    $links[2]['link']="op=authors&op2=FUNCTIONSET&sel=$sel&PHPSESSID=".session_id();
    $links[2]['label']=_SETPERMIT;
    $links[3]['link']="op=authors&op2=SETGROUPUSER&sel=$sel&PHPSESSID=".session_id();
    $links[3]['label']=_SETUSER;
    $rows=dbpage($GLOBALS['adoconn'],$sql,$colnames,$links);	
	/*
	include_once("lib/mypager.inc");
	$sql = "select id,gid,gname from ".ADOPREFIX."_group where enable=1";
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'group',true);
	$GridHeader = array(_ID,_GROUPID,_GROUPNAME,_MANAGE);
	$pager->setRenderGridLayout("width='100%' align='center'",$GridHeader);
	$funcNames = array(_EDIT,_DELETE,_SETPERMIT,_SETUSER);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=authors&op2=UPDATEGROUP&sel=$sel&PHPSESSID=".session_id(),
	$_SERVER['PHP_SELF']."?op=authors&op2=DELGROUP&sel=$sel&PHPSESSID=".session_id(),
	$_SERVER['PHP_SELF']."?op=authors&op2=SETGROUPPERMIT&sel=$sel&PHPSESSID=".session_id(),
	$_SERVER['PHP_SELF']."?op=authors&op2=SETGROUPUSER&sel=$sel&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$pager->setOp("authors&op2=LISTGROUP&sel=$sel&PHPSESSID=".session_id());
	$pager->Render_Function($GLOBALS['perpage']);
	*/
	CloseTable();
	echo "<br>";
	include_once("footer.php");
}


/**
* 列出已不使用的群組
* @param sel 選單種類
*/
function listdelgroup()
{
	$sel=$_GET['sel'];
	include_once("header.php");
	authors_displaymenu2($sel);
	OpenTable();
	include_once("lib/dbpager.inc.php");
	$sql = "select id,gid,gname from ".ADOPREFIX."_group where enable=0	";
    $colnames= array(_ID,_GROUPID,_GROUPNAME,_FUNCTIONS);
    $links[0]['link']="op=authors&op2=UPDATEGROUP&sel=$sel&PHPSESSID=".session_id();
    $links[0]['label']=_EDIT;    
    $links[1]['link']="op=authors&op2=DELGROUP&sel=$sel&PHPSESSID=".session_id();
    $links[1]['label']=_DELETE;
    $links[2]['link']="op=authors&op2=FUNCTIONSET&sel=$sel&PHPSESSID=".session_id();
    $links[2]['label']=_SETPERMIT;
    $links[3]['link']="op=authors&op2=SETGROUPUSER&sel=$sel&PHPSESSID=".session_id();
    $links[3]['label']=_SETUSER;
    $rows=dbpage($GLOBALS['adoconn'],$sql,$colnames,$links);		
	/*
	include_once("lib/mypager.inc");
	echo "<center><font class='undertitle'>"._LISTDELGROUP."</font></center>";
	$sql = "select id,gid,gname from ".ADOPREFIX."_group where enable=0";
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'function',true);
	$GridHeader = array(_ID,_GROUPID,_GROUPNAME,_MANAGE);
	$pager->setRenderGridLayout("width='100%' align='center'",$GridHeader);
	$funcNames = array(_SETENABLE);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=authors&op2=SETGROUPENABLE&sel=$sel&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$op = "authors&op2=LISTDELGROUP&sel=$sel&PHPSESSID=".session_id();
	$pager->setOp($op);
	$pager->Render_Function($GLOBALS['perpage']);
	CloseTable();
	echo "<br>";
	*/
	include_once("footer.php");
}


/**
* 新增群組基本資料的畫面
* @param sel 選單種類
*/
function addgroup()
{
	$sel=$_GET['sel'];
	include_once("header.php");
	authors_displaymenu2($sel);
	OpenTable();
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>"
	."<input type='hidden' name='op' value='authors'>"
	."<input type='hidden' name='op2' value='ADDEDGROUP'>"
	."<input type='hidden' name='sel' value='$sel'>"
	."<center><font class='undertitle'>"._ADDGROUP."</font></center><br>"
	."<table>"
	."<tr>"
	."<td>"._GROUPID.": </td>"
	."<td><input type='text' name='groupid'></td>"
	."</tr>"
	."<tr>"
	."<td>"._GROUPNAME.": </td>"
	."<td><input type='text' name='groupname'></td>"
	."</tr>"
	."<tr>"
	."<td><input type='submit'></td>"
	."<td><input type='reset'></td>"
	."</tr>"
	."</table>"
	."</form>";
	CloseTable();
	include_once("footer.php");
}


/**
* 新增群組基本資料
* @param gid 群組編號
* @param gname 群組名稱
* @param sel 選單種類
*/
function px_addgroup()
{
	$gid=$_POST['groupid'];
	$gname=$_POST['groupname'];
	$sel=$_POST['sel'];
	if (trim($gid)!="" && trim($gname)!="")
	{
		$gid = addslashes($gid);
		$gname = addslashes($gname);
		$GLOBALS['adoconn_m']->Execute("insert into ".ADOPREFIX."_group(gid,gname,enable) values('$gid','$gname',1)");
	}
	Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTGROUP&sel=$sel&PHPSESSID=".session_id());
}


/**
* 確認刪除群組基本資料的畫面
* @param groupid 群組編號
* @param sel 選單種類
*/
function delgroup()
{
	$groupid=$_GET['pkid'];
	$sel=$_GET['sel'];
	$rs = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_group where id=$groupid");
	if ($rs && !$rs->EOF)
	{
		include_once("header.php");
		authors_displaymenu2($sel);
		OpenTable();
		echo "<center><font class='undertitle'>"._DELGROUP."</font></center><br>"
	    ."<table align='center'>"
		."<tr><td class='notabletitle'>"._GROUPID.": </td><td class='notablecontent'>".stripslashes($rs->fields['gid'])."</td></tr>"
		."<tr><td class='notabletitle'>"._GROUPNAME.": </td><td class='notablecontent'>".stripslashes($rs->fields['gname'])."</td></tr>"
		."<tr>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=authors&op2=DELEDGROUP&groupid=$groupid&sel=$sel&PHPSESSID=".session_id()."' class='funlinktext'>"._YES."</a></td>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=authors&op2=LISTGROUP&sel=$sel&PHPSESSID=".session_id()."' class='funlinktext'>"._NO."</a></td>"
		."</tr>"
		."</table>";
		CloseTable();
	} else {
		Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTGROUP&sel=$sel&PHPSESSID=".session_id());
	}
	if ($rs)    $rs->Close();
}


/**
* 刪除群組基本資料
* @param groupid 群組編號
* @param sel 選單種類
*/
function px_delgroup()
{
	$groupid=$_GET['groupid'];
	$sel=$_GET['sel'];
	$GLOBALS['adoconn_m']->StartTrans();
	$GLOBALS['adoconn_m']->Execute("delete from ".ADOPREFIX."_groupuser where gid=$groupid");
	$GLOBALS['adoconn_m']->Execute("delete from ".ADOPREFIX."_groupfunction where gid=$groupid");
	$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_group set enable=0 where id=$groupid");
	$GLOBALS['adoconn_m']->CompleteTrans();
	Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTGROUP&sel=$sel&PHPSESSID=".session_id());
}


/**
* 修改群組基本資料的畫面
* @param groupid 群組編號
* @param sel 選單種類
*/
function updategroup()
{
	$groupid=$_GET['pkid'];
	$sel=$_GET['sel'];
	$rs = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_group where id=$groupid");
	if ($rs && !$rs->EOF)
	{
		include_once("header.php");
		authors_displaymenu2($sel);
		OpenTable();
		echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>"
		."<input type='hidden' name='op' value='authors'>"
		."<input type='hidden' name='op2' value='UPDATEEDGROUP'>"
		."<input type='hidden' name='sel' value='$sel'>"
		."<input type='hidden' name='groupid' value='".$rs->fields[0]."'>"
		."<center><font class='undertitle'>"._UPDATEGROUP."</font></center><br>"
		."<table align='center'>"
		."<tr>"
		."<td class='notabletitle'>"._GROUPID.": </td>"
		."<td class='notablecontent'><input type='text' name='gid' value='".stripslashes($rs->fields[1])."' readonly></td>"
		."</tr>"
		."<tr>"
		."<td class='notabletitle'>"._GROUPNAME.": </td>"
		."<td class='notablecontent'><input type='text' name='gname' value='".stripslashes($rs->fields[2])."'></td>"
		."</tr>"
		."<tr>"
		."<td colspan='2' align='center'><input type='submit' value='"._OK."'> <input type='reset'></td>"
		."</tr>"
		."</table>"
		."</form>";
		CloseTable();
	} else {
		Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTGROUP&sel=$sel");
	}
	if ($rs)    $rs->Close();
}


/**
* 修改群組基本資料
* @param gid 群組編號
* @param gname 群組名稱
* @param sel 選單種類
*/
function px_updategroup()
{
	$groupid=$_POST['groupid'];
	$gid=$_POST['gid'];
	$gname=$_POST['gname'];
	$sel=$_POST['sel'];
	$gname = addslashes($gname);
	$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_group set gname='$gname' where id=$groupid");
	Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTGROUP&sel=$sel&PHPSESSID=".session_id());
}


/**
* 設定群組權限的畫面
* @param groupid 群組流水號
* @param sel 選單種類
*/
function setgrouppermit()
{
	$groupid=$_GET['pkid'];
	$sel=$_GET['sel'];
	include_once("header.php");
	authors_displaymenu2($sel);
	OpenTable();
	echo "<center><font class='undertitle'>"._SETGROUPPERMIT."</font></center><br>";
	echo "<center class='undertitle'>"._GROUPNAME.": ".stripslashes(getgname(getgid($groupid)))."</center>";
	$PHPSESSID=$_GET['PHPSESSID'];
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>"
	."<input type='hidden' name='op' value='authors'>"
	."<input type='hidden' name='op2' value='SETGROUPPERMITED'>"
	."<input type='hidden' name='PHPSESSID' value='$PHPSESSID'>"
	."<input type='hidden' name='sel' value='$sel'>"
	."<input type='hidden' name='groupid' value='$groupid'>";
	//顯示權限功能資料
	$function = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_function where enable=1");
	$permit = $GLOBALS['adoconn']->Execute("select fid from ".ADOPREFIX."_groupfunction where gid=$groupid");
	if ($function && !$function->EOF)
	{
		echo "<TABLE align='center'>"
		."<TR>"
		."<TD>"._SET."</TD>"
		."<TD>"._FUNCTIONNAME."</TD>"
		."</TR>";
		while (!$function->EOF)
		{
			$chk = "";
			if ($permit) $permit->MoveFirst();
			while ($permit && !$permit->EOF)
			{
				if ($permit->fields['fid']==$function->fields['id'])
				{
					$chk = "checked";
					break;
				}
				$permit->MoveNext();
			}
			echo "<tr class='content'>"
			."<TD><input type='checkbox' name='function[]' value='".stripslashes($function->fields['id'])."' $chk></TD>"
			."<TD>".stripslashes($function->fields['fid'])."( ".stripslashes($function->fields['fname'])." )</td>"
			."</tr>";
			$function->MoveNext();
		}
	}
	if ($function) $function->Close();
	if ($permit) $permit->Close();
	echo "<TR><TD colspan='2' align='center'><input type='submit'> <input type='reset'></TD></TR>"
	."</table>"
	."</form>";
	CloseTable();
	include_once("footer.php");
}


/**
* 設定群組權限
* @param groupid   群組流水編號
* @param function  權限功能編號
* @param sel 		選單種類
*/
function px_setgrouppermit()
{
	$groupid=$_POST['groupid'];
	$function=$_POST['function'];
	$sel=$_POST['sel'];
	$aid=$_SESSION['aid'];
	$now = date("Y-m-d H:i:s");
	$GLOBALS['adoconn_m']->StartTrans();
	$GLOBALS['adoconn_m']->Execute("delete from ".ADOPREFIX."_groupfunction where gid=$groupid");
	for ($i=0; $i<count($function); $i++)
	$GLOBALS['adoconn_m']->Execute("insert into ".ADOPREFIX."_groupfunction(gid,fid,lastupdate,sysadm) values($groupid,$function[$i],'$now','$aid')");
	$GLOBALS['adoconn_m']->CompleteTrans();
	Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTGROUP&sel=$sel&PHPSESSID=".session_id());
}


/**
* 設定群組人員的畫面
* @param groupid 群組流水號
* @param sel 選單種類
*/
function setgroupuser()
{
	$groupid=$_GET['pkid'];
	$sel=$_GET['sel'];
    $PHPSESSID=$_GET['PHPSESSID'];
	include_once("header.php");
	authors_displaymenu2($sel);
	OpenTable();
	echo "<center><font class='undertitle'>"._SETGROUPUSER."</font></center><br>"
	."<center><font class='fieldtitle'>"._GROUPNAME.": ".stripslashes(getgname(getgid($groupid)))."</font></center><br>";
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>"
	."<input type='hidden' name='op' value='authors'>"
	."<input type='hidden' name='PHPSESSID' value='$PHPSESSID'>"
	."<input type='hidden' name='op2' value='SETGROUPUSERED'>"
	."<input type='hidden' name='sel' value='$sel'>"
	."<input type='hidden' name='groupid' value='$groupid'>";
	//顯示管理員基本資料
	$authors = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_authors where enable=1");
	$groupuser = $GLOBALS['adoconn']->Execute("select aid from ".ADOPREFIX."_groupuser where gid=$groupid");
	if ($authors && !$authors->EOF)
	{
		echo "<table class='title' align='center'>\n"
		."<tr><td>"._SET."</td><td>"._AUTHORSNAME."</td></tr>\n";
		while (!$authors->EOF)
		{
			$chk = "";
			if ($groupuser) $groupuser->MoveFirst();
			while ($groupuser && !$groupuser->EOF)
			{
				if ($groupuser->fields['aid']==$authors->fields['id'])
				{
					$chk = "checked";
					break;
				}
				$groupuser->MoveNext();
			}
			echo "<tr class='content'>\n"
			."<td><input type='checkbox' name='authors[]' value='".$authors->fields['id']."' $chk></td>\n"
			."<td>".stripslashes($authors->fields['aid'])."( ".stripslashes($authors->fields['aname'])." )</td>\n"
			."</tr>\n";
			$authors->MoveNext();
		}
	}
	if ($authors) $authors->Close();
	if ($groupuser) $groupuser->Close();
	echo "<TR><TD colspan='2' align='center'><input type='submit'> <input type='reset'></TD></TR>"
	."</table>"
	."</form>";
	CloseTable();
	include_once("footer.php");
}


/**
* 設定群組人員
* @param groupid   群組流水編號
* @param authors  	權限功能編號
* @param sel 		選單種類
*/
function px_setgroupuser()
{
	$groupid=$_POST['groupid'];
	$authors=$_POST['authors'];
	$sel=$_POST['sel'];
	$aid=$_SESSION['aid'];
	$now = date("Y-m-d H:i:s");
	$GLOBALS['adoconn_m']->StartTrans();
	$GLOBALS['adoconn_m']->Execute("delete from ".ADOPREFIX."_groupuser where gid=$groupid");
	for ($i=0; $i<count($authors); $i++)
	$GLOBALS['adoconn_m']->Execute("insert into ".ADOPREFIX."_groupuser(gid,aid,lastupdate,sysadm) values($groupid,$authors[$i],'$now','$aid')");
	$GLOBALS['adoconn_m']->CompleteTrans();
	Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTGROUP&sel=$sel&PHPSESSID=".session_id());
}


/**
* 將群組設定為啟用狀態的畫面
* @param authorsid	管理員編號
* @param sel 		選單種類
*/
function setenable()
{
	$groupid=$_GET['pkid'];
	$sel=$_GET['sel'];
	$rs = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_group where id=$groupid");
	if ($rs && !$rs->EOF)
	{
		include_once("header.php");
		authors_displaymenu2($sel);
		OpenTable();
		echo "<center><font class='undertitle'>"._SETENABLE."</font></center><br>";
		echo "<table align='center'>"
		."<tr><td class='notabletitle'>"._GROUPID.": </td><td class='notablecontent'>".stripslashes($rs->fields[1])."</td></tr>"
		."<tr><td class='notabletitle'>"._GROUPNAME.": </td><td class='notablecontent'>".stripslashes($rs->fields[2])."</td></tr>"
		."<tr>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=authors&op2=SETGROUPENABLEED&groupid=$groupid&sel=$sel&PHPSESSID=".session_id()."' class='funlinktext'>"._YES."</a></td>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=authors&op2=LISTDELGROUP&sel=$sel&PHPSESSID=".session_id()."' class='funlinktext'>"._NO."</a></td>"
		."</tr>"
		."</table>";
		CloseTable();
		include_once("footer.php");
	} else {
		Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTGROUP&sel=$sel&PHPSESSID=".session_id());
	}
	if ($rs)    $rs->Close();
}


/**
* 將群組設定為啟用狀態
* @param authorsid 管理員編號
* @param sel 		選單種類
*/
function px_setenable()
{
	$groupid=$_GET['groupid'];
	$sel=$_GET['sel'];
	$GLOBALS['adoconn_m']->StartTrans();
	$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_group set enable=1 where id=$groupid");
	$GLOBALS['adoconn_m']->CompleteTrans();
	Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTGROUP&sel=$sel&PHPSESSID=".session_id());
}


if ($_REQUEST['op']=="authors" && isAuthority($_SESSION['aid'],$_REQUEST['op']))
{
	switch ($_REQUEST['op2'])
	{
		/* 群組列表 */
		case "LISTGROUP":
		listgroup();
		break;

		/* 列出已不使用的群組列表 */
		case "LISTDELGROUP":
		listdelgroup();
		break;

		/* 將群組設定為啟用狀態的畫面 */
		case "SETGROUPENABLE":
		setenable();
		break;

		/* 將群組設定為啟用狀態 */
		case "SETGROUPENABLEED":
		px_setenable();
		break;

		/* 新增群組基本資料的畫面 */
		case "ADDGROUP":
		addgroup();
		break;

		/* 新增群組基本資料 */
		case "ADDEDGROUP":
		px_addgroup();
		break;

		/* 修改群組基本資料的畫面 */
		case "UPDATEGROUP":
		updategroup();
		break;

		/* 修改群組基本資料 */
		case "UPDATEEDGROUP":
		px_updategroup();
		break;

		/* 確認刪除群組基本資料的畫面 */
		case "DELGROUP":
		delgroup();
		break;

		/* 刪除群組基本資料 */
		case "DELEDGROUP":
		px_delgroup();
		break;

		/* 設定群組權限的畫面 */
		case "SETGROUPPERMIT":
		setgrouppermit();
		break;

		/* 設定群組權限 */
		case "SETGROUPPERMITED":
		px_setgrouppermit();
		break;

		/* 設定群組人員的畫面 */
		case "SETGROUPUSER":
		setgroupuser();
		break;

		/* 設定群組人員 */
		case "SETGROUPUSERED":
		px_setgroupuser();
		break;
	}
}
?>
