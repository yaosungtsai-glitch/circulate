<?php
/**
 * Company: 
 * Program: authors.php
 * Author:  Ken Tsai
 * Date:    from 2003.06.12
 * Version: 2.0
 */

Header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
//if (!eregi(ADMINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if (!preg_match('/'.ADMINPAGE.'/', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
include_once("lib/dbpager.inc.php");
/**
* 列出管理員
* @param sel 選單種類
*/
function listauthors()
{
	$sel=$_REQUEST['sel'];
	include_once("header.php");
	authors_displaymenu2($sel);
	OpenTable();
	// include_once("lib/mypager.inc");
	echo "<center><font class='undertitle'>"._LISTAUTHORS."</font></center>\n";
	//include_once("lib/dbpager.inc.php");
	$sql = "select id,aid,aname,email from ".ADOPREFIX."_authors where enable=1";
    $colnames= array(_ID,_AUTHORSID,_AUTHORSNAME,_AUTHORSEMAIL,_FUNCTIONS);
    $links[0]['link']="op=authors&op2=UPDATEAUTHORS&sel=$sel&PHPSESSID=".session_id();
    $links[0]['label']=_EDIT;    
    $links[1]['link']="op=authors&op2=DELAUTHORS&sel=$sel&PHPSESSID=".session_id();
    $links[1]['label']=_DELETE;
    $links[2]['link']="op=authors&op2=SETAUTHORSPERMIT&sel=$sel&PHPSESSID=".session_id();
    $links[2]['label']=_SETPERMIT;
    $links[3]['link']="op=authors&op2=SETAUTHORSGROUP&sel=$sel&PHPSESSID=".session_id();
    $links[3]['label']=_SETGROUP;
    $rows=dbpage($GLOBALS['adoconn'],$sql,$colnames,$links);
	CloseTable();
	include_once("footer.php");
}


/**
 * 列出已不使用的管理員列表
 * @param sel 選單種類
 */
function listdelauthors()
{
	$sel=$_REQUEST['sel'];
	include_once("header.php");
	authors_displaymenu2($sel);
	OpenTable();
	// include_once("lib/mypager.inc");
	echo "<center><font class='undertitle'>\n"._LISTDELAUTHORS."</font></center>\n";
	//include_once("lib/dbpager.inc.php");
	$sql = "select id,aid,aname,email from ".ADOPREFIX."_authors where enable=0";
	$colnames= array(_ID,_AUTHORSID,_AUTHORSNAME,_AUTHORSEMAIL,_FUNCTIONS);
    $links[0]['link']="op=authors&op2=SETAUTHORSENABLE&sel=$sel&PHPSESSID=".session_id();
    $links[0]['label']=_SETENABLE;    
    $rows=dbpage($GLOBALS['adoconn'],$sql,$colnames,$links);
	/*
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'function',true);
	$GridHeader = array(_ID,_AUTHORSID,_AUTHORSNAME,_AUTHORSEMAIL,_MANAGE);
	$pager->setRenderGridLayout("width='100%' align='center'",$GridHeader);
	$funcNames = array(_SETENABLE);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=authors&op2=SETAUTHORSENABLE&sel=$sel&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$op = "authors&op2=LISTDELAUTHORS&sel=$sel&PHPSESSID=".session_id();
	$pager->setOp($op);
	$pager->Render_Function($GLOBALS['perpage']);
	*/
	CloseTable();
	include_once("footer.php");
}


/**
 * 新增管理員基本資料的畫面
 * @param sel 選單種類
 */
function addauthors()
{
	$sel=$_REQUEST['sel'];
	$PHPSESSID=$_REQUEST['PHPSESSID'];
	include_once("header.php");
	include_once("admin/includes/authors-js.php");
	authors_displaymenu2($sel);
	OpenTable();
	echo "<form name='authors' action='".$_SERVER['PHP_SELF']."' method='post'>\n"
	."<input type='hidden' name='op' value='authors'>\n"
	."<input type='hidden' name='PHPSESSID' value='$PHPSESSID'>\n"
	."<input type='hidden' name='op2' value='ADDEDAUTHORS'>\n"
	."<input type='hidden' name='sel' value='$sel'>\n"
	."<center><font class='undertitle'>"._ADDAUTHORS."</font></center><br>\n"
	."<table align='center'>\n"
	."<tr>\n"
	."<td class='notabletitle'>"._AUTHORSID.": </td>\n"
	."<td class='notablecontent'><input type='text' name='authors_aid'></td>\n"
	."</tr>\n"
	."<tr>\n"
	."<td class='notabletitle'>"._AUTHORSNAME.": </td>\n"
	."<td class='notablecontent'><input type='text' name='aname'></td>\n"
	."</tr>\n"
	."<tr>\n"
	."<td class='notabletitle'>"._AUTHORSEMAIL.": </td>\n"
	."<td class='notablecontent'><input type='text' name='email'></td>\n"
	."</tr>\n"
	."<tr>\n"
	."<td class='notabletitle'>"._PASSWORD.": </td>\n"
	."<td class='notablecontent'><input type='password' name='pwd1'></td>\n"
	."</tr>\n"
	."<tr>\n"
	."<td class='notabletitle'>"._CONFIRM._PASSWORD.": </td>\n"
	."<td class='notablecontent'><input type='password' name='pwd2'></td>\n"
	."</tr>\n"
	."<tr>\n"
	."<td colspan='2' align='center'><input type='button' value='"._OK."' onclick='check_authorspwd(this.form)'> <input type='reset'></td>\n"
	."</tr>\n"
	."</table>\n"
	."</form>\n";
	CloseTable();
	include_once("footer.php");
}


/**
 * 新增管理員基本資料
 * @param aid 	管理員登入帳號
 * @param aname 管理員名稱
 * @param pwd1 	密碼1
 * @param pwd2 	密碼2
 * @param email 管理員郵件位置
 * @param sel 	選單種類
 */
function px_addauthors()
{
	$authors_aid=$_POST['authors_aid'];
	$aname=$_POST['aname'];
	$email=$_POST['email'];
	$pwd1=$_POST['pwd1'];
	$pwd2=$_POST['pwd2'];
	$sel=$_POST['sel'];
	if ($pwd1==$pwd2)
	{
		if (trim($authors_aid)!="" && trim($aname)!="" && trim($email)!="" && trim($pwd1)!="")
		{
			$rs = $GLOBALS['adoconn']->Execute("select count(*) as count from ".ADOPREFIX."_authors where enable=1 and aid='$authors_aid'");
			if ($rs && !$rs->EOF)
			{
				$count = $rs->fields['count'];
				$rs->Close();
			}
			if ($count==0)
			{
				$aname = addslashes($aname);
				$GLOBALS['adoconn']->Execute("insert into ".ADOPREFIX."_authors(aid,pwd,aname,email,enable) values('$authors_aid','$pwd1', '$email','$aname','$email','1')");
				$formaction = $_SERVER['PHP_SELF'];
				$arr['op'] = "authors";
				$arr['op2'] = "LISTAUTHORS";
				$arr['sel'] = $sel;
				$arr['PHPSESSID'] = session_id();
				POSTFORM($formaction,$arr);
			} else {
				include_once("header.php");
				authors_displaymenu2($sel);
				OpenTable();
				echo _LOGINDUPLICATE."<br>\n";
				echo _BACKTO."<a href='javascript:history.back();'>"._PREVIOUS."</a>"._CONFIRM."<br>\n";
				CloseTable();
				include_once("footer.php");
				exit();
			}
		} else {
			include_once("header.php");
			authors_displaymenu2($sel);
			OpenTable();
			echo _NOTCOMPLETE."<br>\n";
			echo _BACKTO."<a href='javascript:history.back();'>"._PREVIOUS."</a>"._CONFIRM."<br>\n";
			CloseTable();
			include_once("footer.php");
			exit();
		}
	} else {
		include_once("header.php");
		authors_displaymenu2($sel);
		OpenTable();
		echo _PASSWORDDIFFERENT."<br>\n";
		echo _BACKTO."<a href='javascript:history.back();'>"._PREVIOUS."</a>"._CONFIRM."<br>\n";
		CloseTable();
		include_once("footer.php");
		exit();
	}
}


/**
* 確認刪除管理員基本資料的畫面
* @param authorsid	管理員編號
* @param sel 		選單種類
*/
function delauthors()
{
	$authorsid=$_REQUEST['pkid'];
	$sel=$_REQUEST['sel'];
	$rs = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_authors where id=$authorsid");
	if ($rs && !$rs->EOF)
	{
		include_once("header.php");
		authors_displaymenu2($sel);
		OpenTable();
		echo "<center><font class='undertitle'>"._DELAUTHORS."</font></center><br>\n"
	    ."<table align='center'>\n"
		."<tr><td class='notabletitle'>"._AUTHORSID.": </td><td class='notablecontent'>".$rs->fields['aid']."</td></tr>\n"
		."<tr><td class='notabletitle'>"._AUTHORSNAME.": </td><td class='notablecontent'>".stripslashes($rs->fields['aname'])."</td></tr>\n"
		."<tr><td class='notabletitle'>"._AUTHORSEMAIL.": </td><td class='notablecontent'>".$rs->fields['email']."</td></tr>\n"
		."<tr><td class='notabletitle'>"._PASSWORD.": </td><td class='notablecontent'>".$rs->fields['pwd']."</td></tr>\n"
		."<tr><td class='notabletitle'>"._PASSWORD.": </td><td class='notablecontent'>".$rs->fields['pwd']."</td></tr>\n"
		."<tr>\n"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=authors&op2=DELEDAUTHORS&authorsid=$authorsid&sel=$sel&PHPSESSID=".session_id()."' class='funlinktext'>"._YES."</a></td>\n"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=authors&op2=LISTAUTHORS&sel=$sel&PHPSESSID=".session_id()."' class='funlinktext'>"._NO."</a></td>\n"
		."</tr>\n"
		."</table>\n";
		CloseTable();
		include_once("footer.php");
	} else {
		$formaction = $_SERVER['PHP_SELF'];
		$arr['op'] = "authors";
		$arr['op2'] = "LISTAUTHORS";
		$arr['sel'] = $sel;
		$arr['PHPSESSID'] = session_id();
		POSTFORM($formaction,$arr);
	}
	if ($rs)    $rs->Close();
}


/**
 * 刪除管理員基本資料
 * @param authorsid 管理員編號
 * @param sel 		選單種類
 */
function px_delauthors()
{
	$authorsid=$_REQUEST['authorsid'];
	$sel=$_REQUEST['sel'];
	$GLOBALS['adoconn']->StartTrans();
	$GLOBALS['adoconn']->Execute("delete from ".ADOPREFIX."_groupuser where aid=$authorsid");
	$GLOBALS['adoconn']->Execute("delete from ".ADOPREFIX."_permit where aid=$authorsid");
	$GLOBALS['adoconn']->Execute("update ".ADOPREFIX."_authors set enable=0 where id=$authorsid");
	$GLOBALS['adoconn']->CompleteTrans();
	$formaction = $_SERVER['PHP_SELF'];
	$arr['op'] = "authors";
	$arr['op2'] = "LISTAUTHORS";
	$arr['sel'] = $sel;
	$arr['PHPSESSID'] = session_id();
	POSTFORM($formaction,$arr);
}


/**
 * 修改管理員基本資料的畫面
 * @param authorsid	管理員編號
 * @param sel 		選單種類
 */
function updateauthors()
{
	$authorsid=$_REQUEST['pkid'];
	$sel=$_REQUEST['sel'];
	$rs = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_authors where id=$authorsid");
	if ($rs && !$rs->EOF)
	{
		include_once("header.php");
		include_once("admin/includes/authors-js.php");
		authors_displaymenu2($sel);
		OpenTable();
		$PHPSESSID=$_REQUEST['PHPSESSID'];
		echo "<form name='authors' action='".$_SERVER['PHP_SELF']."' method='post'>\n"
		."<input type='hidden' name='PHPSESSID' value='$PHPSESSID'>\n"
		."<input type='hidden' name='op' value='authors'>\n"
		."<input type='hidden' name='op2' value='UPDATEEDAUTHORS'>\n"
		."<input type='hidden' name='sel' value='$sel'>\n"
		."<input type='hidden' name='authorsid' value='".$rs->fields[0]."'>\n"
		."<center><font class='undertitle'>"._EDITADMINS."</font></center><br>\n"
	    ."<table align='center'>\n"
		."<tr>\n"
		."<td class='notabletitle'>"._AUTHORSID.": </td>\n"
		."<td class='notablecontent'>".$rs->fields['aid']."</td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td class='notabletitle'>"._AUTHORSNAME.": </td>\n"
		."<td class='notablecontent'><input type='text' name='aname' value='".stripslashes($rs->fields['aname'])."'></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td class='notabletitle'>"._AUTHORSEMAIL.": </td>\n"
		."<td class='notablecontent'><input type='text' name='email' value='".$rs->fields['email']."'></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td class='notabletitle'>"._PASSWORD.": </td>\n"
	    ."<td class='notablecontent'><input type='password' name='pwd1' value='**********'></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td class='notabletitle'>"._CONFIRM._PASSWORD.": </td>\n"
		."<td class='notablecontent'><input type='password' name='pwd2' value='**********'></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td colspan='2' align='center'><input type='button' value='"._OK."' onclick='check_authorspwd(this.form)'> <input type='reset'></td>\n"
		."</tr>\n"
		."</table>\n"
		."</form>\n";
		CloseTable();
		echo "<br/>\n";
		include_once("footer.php");
	} else {
		$formaction = $_SERVER['PHP_SELF'];
		$arr['op'] = "authors";
		$arr['op2'] = "LISTAUTHORS";
		$arr['sel'] = $sel;
		$arr['PHPSESSID'] = session_id();
		POSTFORM($formaction,$arr);
	}
	if ($rs)    $rs->Close();
}


/**
 * 修改管理員基本資料
 * @param authorsid 管理員編號
 * @param aname 	管理員名稱
 * @param pwd1 		密碼1
 * @param pwd2 		密碼2
 * @param email 	管理員郵件位置
 * @param sel 		選單種類
 */
function px_updateauthors()
{
	$authorsid=$_POST['authorsid'];
	$aname=$_POST['aname'];
	$email=$_POST['email'];
	$pwd1=$_POST['pwd1'];
	$pwd2=$_POST['pwd2'];
	$sel=$_POST['sel'];
	if ($pwd1==$pwd2)
	{
		if (trim($aname)!="" && trim($email)!="" && trim($pwd1)!="")
		{
			$aname = addslashes($aname);
			if(trim($pwd1)!='**********'){
			  $sql="update ".ADOPREFIX."_authors set aname='$aname', email='$email', pwd='$pwd' where id=$authorsid";
			}else{
			  $sql="update ".ADOPREFIX."_authors set aname='$aname', email='$email' where id=$authorsid";
			}

			$GLOBALS['adoconn']->Execute($sql);
			$formaction = $_SERVER['PHP_SELF'];
			$arr['op'] = "authors";
			$arr['op2'] = "LISTAUTHORS";
			$arr['sel'] = $sel;
			$arr['PHPSESSID'] = session_id();
			POSTFORM($formaction,$arr);
		} else {
			include_once("header.php");
			authors_displaymenu2($sel);
			OpenTable();
			echo _NOTCOMPLETE."<br>\n";
			echo _BACKTO."<a href='javascript:history.back();'>"._PREVIOUS."</a>"._CONFIRM."<br>\n";
			CloseTable();
			echo "<br/>\n";
			exit();
		}
	} else {
		include_once("header.php");
		authors_displaymenu2($sel);
		OpenTable();
		echo _PASSWORDDIFFERENT."<br>\n";
		echo _BACKTO."<a href='javascript:history.back();'>"._PREVIOUS."</a>"._CONFIRM."<br>\n";
		CloseTable();
		echo "<br/>\n";
		include_once("footer.php");
		exit();
	}
}


/**
 * 設定管理員權限的畫面
 * @param authorsid	管理員編號
 * @param sel 		選單種類
 */
function setauthorspermit()
{
	$authorsid=$_REQUEST['pkid'];
	$sel=$_REQUEST['sel'];
	$aid=$_REQUEST['PHPSESSID'];
	include_once("header.php");
	authors_displaymenu2($sel);
	OpenTable();
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n"
	."<input type='hidden' name='PHPSESSID' value='$aid'>\n"
	."<input type='hidden' name='op' value='authors'>\n"
	."<input type='hidden' name='op2' value='SETAUTHORSPERMITED'>\n"
	."<input type='hidden' name='sel' value='$sel'>\n"
	."<input type='hidden' name='authorsid' value='$authorsid'>\n"
	."<center><font class='undertitle'>"._SETAUTHORSPERMIT."</font></center><br>\n"
	."<center><font class='fieldtitle'>"._AUTHORSNAME.": ".stripslashes(getaname(getaid($authorsid)))."</font></center><br>\n";

	//顯示權限功能資料
	$function = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_function where enable=1");
	$permit = $GLOBALS['adoconn']->Execute("select fid from ".ADOPREFIX."_permit where aid=$authorsid");
	if ($function && !$function->EOF)
	{
		echo "<TABLE CLASS='title' align='center'>\n"
		."<TR>\n"
		."<TD>"._SET."</TD>\n"
		."<TD>"._FUNCTIONNAME."</TD>\n"
		."</TR>\n";
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
			echo "<tr class='content'>\n"
			."<TD><input type='checkbox' name='function[]' value='".stripslashes($function->fields['id'])."' $chk></TD>\n"
			."<TD>".stripslashes($function->fields['fid'])."( ".stripslashes($function->fields['fname'])." )</td>\n"
			."</tr>\n";
			$function->MoveNext();
		}
	}
	if ($function) $function->Close();
	if ($permit) $permit->Close();
	echo "<TR><TD colspan='2' align='center'><input type='submit'> <input type='reset'></TD></TR>\n"
	."</table>\n"
	."</form>\n";
	CloseTable();
	echo "<br/>\n";
	include_once("footer.php");
}


/**
 * 設定管理員權限
 * @param authorsid 管理員編號
 * @param function  權限功能編號
 * @param sel 		選單種類
 */
function px_setauthorspermit()
{
	$authorsid=$_POST['authorsid'];
	$function=$_POST['function'];
	$sel=$_POST['sel'];
	$aid=$_SESSION['aid'];
	$now = date("Y-m-d H:i:s");
	$GLOBALS['adoconn']->StartTrans();
	$GLOBALS['adoconn']->Execute("delete from ".ADOPREFIX."_permit where aid=$authorsid");
	for ($i=0; $i<count($function); $i++)
		$GLOBALS['adoconn']->Execute("insert into ".ADOPREFIX."_permit(aid,fid,lastupdate,sysadm) values($authorsid,$function[$i],'$now','$aid')");
	$GLOBALS['adoconn']->CompleteTrans();
	$formaction = $_SERVER['PHP_SELF'];
	$arr['op'] = "authors";
	$arr['op2'] = "LISTAUTHORS";
	$arr['sel'] = $sel;
	$arr['PHPSESSID'] = session_id();
	POSTFORM($formaction,$arr);
}


/**
 * 設定管理員所屬群組的畫面
 * @param authorsid	管理員編號
 * @param sel 		選單種類
 */
function setauthorsgroup()
{
	$authorsid=$_REQUEST['pkid'];
	$sel=$_REQUEST['sel'];
    $PHPSESSID=$_REQUEST['PHPSESSID'];

	include_once("header.php");
	authors_displaymenu2($sel);
	OpenTable();
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n"
	."<input type='hidden' name='op' value='authors'>\n"
	."<input type='hidden' name='PHPSESSID' value='$PHPSESSID'>\n"
	."<input type='hidden' name='op2' value='SETAUTHORSGROUPED'>\n"
	."<input type='hidden' name='sel' value='$sel'>\n"
	."<input type='hidden' name='authorsid' value='$authorsid'>\n"
	."<center><font class='undertitle'>"._SETAUTHORSGROUP."</font></center><br>\n"
	."<center><font class='fieldtitle'>"._AUTHORSNAME.": ".stripslashes(getaname(getaid($authorsid)))."</font></center><br>\n"
	."<table align='center'>\n";
	//顯示群組資料
	$group = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_group where enable=1");
	$guser = $GLOBALS['adoconn']->Execute("select gid from ".ADOPREFIX."_groupuser where aid=$authorsid");
	if ($group && !$group->EOF)
	{
		echo "<TABLE align='center'>\n"
		."<TR>\n"
		."<TD>"._SET."</TD>\n"
		."<TD>"._GROUPNAME."</TD>\n"
		."</TR>\n";
		while (!$group->EOF)
		{
			$chk = "";
			if ($guser) $guser->MoveFirst();
			while ($guser && !$guser->EOF)
			{
				if ($guser->fields['gid']==$group->fields['id'])
				{
					$chk = "checked";
					break;
				}
				$guser->MoveNext();
			}
			echo "<tr class='content'>\n"
			."<TD><input type='checkbox' name='group[]' value='".$group->fields['id']."' $chk></TD>\n"
			."<TD>".stripslashes($group->fields['gid'])."( ".stripslashes($group->fields['gname'])." )</td>\n"
			."</tr>\n";
			$group->MoveNext();
		}
	}
	if ($group) $group->Close();
	if ($guser) $guser->Close();
	echo "<TR><TD colspan='2' align='center'><input type='submit'> <input type='reset'></TD></TR>\n"
	."</table>\n"
	."</form>\n";
	CloseTable();
	echo "<br/>\n";
	include_once("footer.php");
}


/**
 * 設定管理員所屬群組
 * @param authorsid 管理員編號
 * @param function  權限功能編號
 * @param sel 		選單種類
 */
function px_setauthorsgroup()
{
	$authorsid=$_POST['authorsid'];
	$group=$_POST['group'];
	$sel=$_POST['sel'];
	$test=$_POST['PHPSESSID'];
	$aid=$_SESSION['aid'];
	$now = date("Y-m-d H:i:s");
	$GLOBALS['adoconn']->StartTrans();
	$GLOBALS['adoconn']->Execute("delete from ".ADOPREFIX."_groupuser where aid=$authorsid");
	for ($i=0; $i<count($group); $i++)
		$GLOBALS['adoconn']->Execute("insert into ".ADOPREFIX."_groupuser(aid,gid,lastupdate,sysadm) values($authorsid,$group[$i],'$now','$aid')");
	$GLOBALS['adoconn']->CompleteTrans();
	$formaction = $_SERVER['PHP_SELF'];
	$arr['op'] = "authors";
	$arr['op2'] = "LISTAUTHORS";
	$arr['sel'] = $sel;
	$arr['PHPSESSID'] = session_id();
	POSTFORM($formaction,$arr);
}


/**
 * 將管理員設定為啟用狀態的畫面
 * @param authorsid	管理員編號
 * @param sel 		選單種類
 */
function setenable()
{
	$authorsid=$_REQUEST['pkid'];
	$sel=$_REQUEST['sel'];
	$rs = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_authors where id=$authorsid");
	if ($rs && !$rs->EOF)
	{
		include_once("header.php");
		authors_displaymenu2($sel);
		OpenTable();
		echo "<center><font class='undertitle'>"._SETENABLE."</font></center><br>\n"
        ."<table align='center'>\n"
        ."<tr><td class='notabletitle'>"._AUTHORSID.": </td><td class='notablecontent'>".$rs->fields['aid']."</td></tr>\n"
	    ."<tr><td class='notabletitle'>"._AUTHORSNAME.": </td><td class='notablecontent'>".stripslashes($rs->fields['aname'])."</td></tr>\n"
	    ."<tr><td class='notabletitle'>"._AUTHORSEMAIL.": </td><td class='notablecontent'>".$rs->fields['email']."</td></tr>\n"
	    ."<tr><td class='notabletitle'>"._PASSWORD.": </td><td class='notablecontent'>".$rs->fields['pwd']."</td></tr>\n"
    	."<tr><td class='notabletitle'>"._PASSWORD.": </td><td class='notablecontent'>".$rs->fields['pwd']."</td></tr>\n"
	    ."<tr>\n"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=authors&op2=SETAUTHORSENABLEED&authorsid=$authorsid&sel=$sel&PHPSESSID=".session_id()."' class='funlinktext'>"._YES."</a></td>\n"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=authors&op2=LISTDELAUTHORS&sel=$sel&PHPSESSID=".session_id()."' class='funlinktext'>"._NO."</a></td>\n"
		."</tr>\n"
		."</table>\n";
		CloseTable();
		echo "<br/>\n";
		include_once("footer.php");
	} else {
		$formaction = $_SERVER['PHP_SELF'];
		$arr['op'] = "authors";
		$arr['op2'] = "LISTAUTHORS";
		$arr['sel'] = $sel;
		$arr['PHPSESSID'] = session_id();
		POSTFORM($formaction,$arr);
	}
	if ($rs)    $rs->Close();
}


/**
 * 將管理員設定為啟用狀態
 * @param authorsid 管理員編號
 * @param sel 		選單種類
 */
function px_setenable()
{
	$authorsid=$_REQUEST['authorsid'];
	$sel=$_REQUEST['sel'];
	$GLOBALS['adoconn']->StartTrans();
	$GLOBALS['adoconn']->Execute("update ".ADOPREFIX."_authors set enable=1 where id=$authorsid");
	$GLOBALS['adoconn']->CompleteTrans();
	$formaction = $_SERVER['PHP_SELF'];
	$arr['op'] = "authors";
	$arr['op2'] = "LISTAUTHORS";
	$arr['sel'] = $sel;
	$arr['PHPSESSID'] = session_id();
	POSTFORM($formaction,$arr);
}

if ($_REQUEST['op']=="authors" && isAuthority($_SESSION['aid'],$_REQUEST['op']))
{
	switch ($_REQUEST['op2'])
	{
		//管理員列表
		case "LISTAUTHORS":
			listauthors();
			break;

		//列出已不使用的管理員列表
		case "LISTDELAUTHORS":
			listdelauthors();
			break;

		//將管理員設定為啟用狀態的畫面
		case "SETAUTHORSENABLE":
			setenable();
			break;

		//將管理員設定為啟用狀態
		case "SETAUTHORSENABLEED":
			px_setenable();
			break;

		//新增管理員基本資料的畫面
		case "ADDAUTHORS":
			addauthors();
			break;

		//新增管理員基本資料
		case "ADDEDAUTHORS":
			px_addauthors();
			break;

		//修改管理員基本資料的畫面
		case "UPDATEAUTHORS":
			updateauthors();
			break;

		//修改管理員基本資料
		case "UPDATEEDAUTHORS":
			px_updateauthors();
			break;

		//確認刪除管理員基本資料的畫面
		case "DELAUTHORS":
			delauthors();
			break;

		//刪除管理員基本資料
		case "DELEDAUTHORS":
			px_delauthors();
			break;

		//設定管理員權限的畫面
		case "SETAUTHORSPERMIT":
			setauthorspermit();
			break;

		//設定管理員權限
		case "SETAUTHORSPERMITED":
			px_setauthorspermit();
			break;

		//設定管理員所屬群組的畫面
		case "SETAUTHORSGROUP":
			setauthorsgroup();
			break;

		//設定管理員所屬群組
		case "SETAUTHORSGROUPED":
			px_setauthorsgroup();
			break;
	}
}
?>
