<?php
/**
* Company: 
* Program: functions.php
* Author:  Ken Tsai
* Date:    from 2003.03.06.12
* Version: 2.0
*/

Header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
//if (!eregi(ADMINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if (!preg_match('/'.ADMINPAGE.'/', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }

/**
* 列出權限功能
* @param sel 選單種類
*/
function listfunction()
{
	$sel = $_GET['sel'];
	include_once("header.php");
	authors_displaymenu2($sel);
	OpenTable();
	include_once("lib/dbpager.inc.php");
	$sql = "select id,fid,fname from ".ADOPREFIX."_function where enable=1 order by fsort";
    $colnames= array(_ID,_FUNCTIONID,_FUNCTIONNAME,_FUNCTIONS);
    $links[0]['link']="op=authors&op2=UPDATEFUNCTION&sel=$sel&PHPSESSID=".session_id();
    $links[0]['label']=_EDIT;    
    $links[1]['link']="op=authors&op2=DELFUNCTION&sel=$sel&PHPSESSID=".session_id();
    $links[1]['label']=_DELETE;
    $links[2]['link']="op=authors&op2=FUNCTIONSET&sel=$sel&PHPSESSID=".session_id();
    $links[2]['label']=_SETPERMIT;
    $links[3]['link']="op=authors&op2=SETAUTHORSGROUP&sel=$sel&PHPSESSID=".session_id();
    $links[3]['label']="<img src='images/admin/authors/function_up.gif' alt='"._FUNCTIONUP."' border='0'>";
    $links[4]['link']="op=authors&op2=FUNCTIONDOWN&sel=$sel&PHPSESSID=".session_id();
    $links[4]['label']="<img src='images/admin/authors/function_down.gif' alt='"._FUNCTIONDOWN."' border='0'>";
    $rows=dbpage($GLOBALS['adoconn'],$sql,$colnames,$links);
	/*
	include_once("lib/mypager.inc");
	echo "<center><font class='undertitle'>"._LISTFUNCTION."</font></center>";
	$sql = "select id,fid,fname from ".ADOPREFIX."_function where enable=1 order by fsort";
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'function',true);
	$GridHeader = array(_ID,_FUNCTIONID,_FUNCTIONNAME,_MANAGE);
	$pager->setRenderGridLayout("width='100%' align='center'",$GridHeader);
	$funcNames = array(_EDIT,_DELETE,_SETPERMIT,
	"<img src='images/admin/authors/function_up.gif' alt='"._FUNCTIONUP."' border='0'>",
	"<img src='images/admin/authors/function_down.gif' alt='"._FUNCTIONDOWN."' border='0'>");
	$funcUrls = array($_SERVER['PHP_SELF']."?op=authors&op2=UPDATEFUNCTION&sel=$sel&PHPSESSID=".session_id(),
	$_SERVER['PHP_SELF']."?op=authors&op2=DELFUNCTION&sel=$sel&PHPSESSID=".session_id(),
	$_SERVER['PHP_SELF']."?op=authors&op2=FUNCTIONSET&sel=$sel&PHPSESSID=".session_id(),
	$_SERVER['PHP_SELF']."?op=authors&op2=FUNCTIONUP&sel=$sel&PHPSESSID=".session_id(),
	$_SERVER['PHP_SELF']."?op=authors&op2=FUNCTIONDOWN&sel=$sel&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$op = "authors&op2=LISTFUNCTION&sel=$sel&PHPSESSID=".session_id();
	$pager->setOp($op);
	$pager->Render_Function($GLOBALS['perpage']);
	*/
	CloseTable();
	include_once("footer.php");
}


/**
* 列出已不使用的權限功能列表
* @param sel 選單種類
*/
function listdelfunction()
{
	$sel = $_GET['sel'];
	include_once("header.php");
	authors_displaymenu2($sel);
	OpenTable();
	include_once("lib/dbpager.inc.php");
	$sql = "select id,fid,fname from ".ADOPREFIX."_function where enable=0 order by fsort";
    $colnames= array(_ID,_FUNCTIONID,_FUNCTIONNAME,_FUNCTIONS);
    $links[0]['link']="op=authors&op2=UPDATEFUNCTION&sel=$sel&PHPSESSID=".session_id();
    $links[0]['label']=_EDIT;    
    $links[1]['link']="op=authors&op2=DELFUNCTION&sel=$sel&PHPSESSID=".session_id();
    $links[1]['label']=_DELETE;
    $links[2]['link']="op=authors&op2=FUNCTIONSET&sel=$sel&PHPSESSID=".session_id();
    $links[2]['label']=_SETPERMIT;
    $links[3]['link']="op=authors&op2=SETAUTHORSGROUP&sel=$sel&PHPSESSID=".session_id();
    $links[3]['label']="<img src='images/admin/authors/function_up.gif' alt='"._FUNCTIONUP."' border='0'>";
    $links[4]['link']="op=authors&op2=FUNCTIONDOWN&sel=$sel&PHPSESSID=".session_id();
    $links[4]['label']="<img src='images/admin/authors/function_down.gif' alt='"._FUNCTIONDOWN."' border='0'>";
    $rows=dbpage($GLOBALS['adoconn'],$sql,$colnames,$links);	
	/*
	include_once("lib/mypager.inc");
	echo "<center><font class='undertitle'>"._LISTDELFUNCTION."</font></center>";
	$sql = "select id,fid,fname from ".ADOPREFIX."_function where enable=0";
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'function',true);
	$GridHeader = array(_ID,_FUNCTIONID,_FUNCTIONNAME,_MANAGE);
	$pager->setRenderGridLayout("width='100%' align='center'",$GridHeader);
	$funcNames = array(_SETENABLE);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=authors&op2=SETFUNCTIONENABLE&sel=$sel&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$op = "authors&op2=LISTDELFUNCTION&sel=$sel&PHPSESSID=".session_id();
	$pager->setOp($op);
	$pager->Render_Function($GLOBALS['perpage']);
	*/
	CloseTable();
	include_once("footer.php");
}


/**
* 新增權限功能基本資料的畫面
* @param sel 選單種類
*/
function addfunction()
{
	$sel = $_GET['sel'];
	include_once("header.php");
	authors_displaymenu2($sel);
	OpenTable();
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>"
	."<input type='hidden' name='op' value='authors'>"
	."<input type='hidden' name='op2' value='ADDEDFUNCTION'>"
	."<input type='hidden' name='sel' value='$sel'>"
	."<center><font class='undertitle'>"._ADDFUNCTION."</font></center><br>"
	."<table align='center'>"
	."<tr>"
	."<td class='notabletitle'>"._FUNCTIONID.":</td>"
	."<td class='notablecontent'><input type='text' name='fid'></td>"
	."</tr>"
	."<tr>"
	."<td class='notabletitle'>"._FUNCTIONNAME.":</td>"
	."<td class='notablecontent'><input type='text' name='fname'></td>"
	."</tr>"
	."<tr>"
	."<td colspan='2' align='center'><input type='submit' value='"._OK."'> <input type='reset'></td>"
	."</tr>"
	."</table>"
	."</form>";
	CloseTable();
	include_once("footer.php");
}


/**
* 確認刪除權限功能基本資料的畫面
* @param functionid 	權限功能編號
* @param sel 			選單種類
*/
function delfunction()
{
	$functionid = $_GET['pkid'];
	$sel = $_GET['sel'];
	$rs = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_function where id=$functionid");
	if ($rs && !$rs->EOF)
	{
		include_once("header.php");
		authors_displaymenu2($sel);
		OpenTable();
		echo "<center><font class='undertitle'>"._DELETEFUNCTION."</font></center><br>"
		."<table align='center'>"
		."<tr><td class='notabletitle'>"._FUNCTIONID.": </td><td class='notablecontent'>".stripslashes($rs->fields['fid'])."</td></tr>"
		."<tr><td class='notabletitle'>"._FUNCTIONNAME.": </td><td class='notablecontent'>".stripslashes($rs->fields['fname'])."</td></tr>"
		."<tr>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=authors&op2=DELEDFUNCTION&functionid=$functionid&sel=$sel&PHPSESSID=".session_id()."' class='funlinktext'>"._YES."</a></td>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=authors&op2=LISTFUNCTION&sel=$sel&PHPSESSID=".session_id()."' class='funlinktext'>"._NO."</a></td>"
		."</tr>"
		."</table>";
		CloseTable();
		include_once("footer.php");
	} else {
		Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTFUNCTION&sel=$sel&PHPSESSID=".session_id());
	}
	if ($rs)    $rs->Close();
}


/**
* 刪除權限功能基本資料
* @param functionid 	權限功能編號
* @param sel 			選單種類
*/
function px_delfunction()
{
	$functionid = $_GET['functionid'];
	$sel = $_GET['sel'];
	$GLOBALS['adoconn_m']->StartTrans();
	$GLOBALS['adoconn_m']->Execute("delete from ".ADOPREFIX."_permit where fid=$functionid");
	$GLOBALS['adoconn_m']->Execute("delete from ".ADOPREFIX."_groupfunction where fid=$functionid");
	$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_function set enable=0 where id=$functionid");
	$GLOBALS['adoconn_m']->CompleteTrans();
	Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTFUNCTION&sel=$sel&PHPSESSID=".session_id());
}


/**
* 修改權限功能基本資料的畫面
* @param functionid 	權限功能編號
* @param sel 			選單種類
*/
function updatefunction()
{
	$functionid = $_GET['pkid'];
	$sel = $_GET['sel'];
	$rs = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_function where id=$functionid");
	if ($rs && !$rs->EOF)
	{
		include_once("header.php");
		authors_displaymenu2($sel);
		OpenTable();
		echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>"
		."<input type='hidden' name='op' value='authors'>"
		."<input type='hidden' name='PHPSESSID' value='$PHPSESSID'>"
		."<input type='hidden' name='op2' value='UPDATEEDFUNCTION'>"
		."<input type='hidden' name='sel' value='$sel'>"
		."<input type='hidden' name='functionid' value='".$rs->fields[0]."'>"
		."<center><font class='undertitle'>"._UPDATEFUNCTION."</font></center><br>"
		."<table align='center'>"
		."<tr>"
		."<td class='notabletitle'>"._FUNCTIONID.": </td>"
		."<td class='notablecontent'><input type='text' name='fid' value='".stripslashes($rs->fields[1])."' readonly></td>"
		."</tr>"
		."<tr>"
		."<td class='notabletitle'>"._FUNCTIONNAME.": </td>"
		."<td class='notablecontent'><input type='text' name='fname' value='".stripslashes($rs->fields[2])."'></td>"
		."</tr>"
		."<tr>"
		."<td colspan='2' align='center'><input type='submit' value='"._OK."'> <input type='reset'></td>"
		."</tr>"
		."</table>"
		."</form>";
		CloseTable();
		include_once("footer.php");
		$rs->Close();
	} else {
		Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTFUNCTION&sel=$sel&PHPSESSID=".session_id());
	}
}


/**
* 修改權限功能基本資料
* @param fid	權限功能編號
* @param fname	權限功能名稱
* @param sel	選單種類
*/
function px_updatefunction()
{
	$functionid = $_POST['functionid'];
	$fid = $_POST['fid'];
	$fname = $_POST['fname'];
	$sel = $_POST['sel'];
	$fid = addslashes($fid);
	$fname = addslashes($fname);
	$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_function set fname='$fname' where id=$functionid");
	
	Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTFUNCTION&sel=$sel&PHPSESSID=".session_id());
}


/**
* 設定權限給群組或管理員的畫面
* @param functionid 	權限功能編號
* @param sel 			選單種類
*/
function setpermit()
{
	$functionid = $_GET['pkid'];
	$sel = $_GET['sel'];
    $PHPSESSID = $_GET['PHPSESSID'];

	include_once("header.php");
	authors_displaymenu2($sel);
	
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>"
	."<input type='hidden' name='op' value='authors'>"
	."<input type='hidden' name='op2' value='FUNCTIONSETED'>"
	."<input type='hidden' name='PHPSESSID' value='$PHPSESSID'>"
	."<input type='hidden' name='sel' value='$sel'>"
	."<input type='hidden' name='functionid' value='$functionid'>";

	OpenTable();
	echo _SETPERMIT."<br/>\n";
	echo _FUNCTIONNAME.":".stripslashes(getfname(getfid($functionid)))."<br/>\n";
	CloseTable();
	echo "<br/>\n";
	
	OpenTable();
	echo "<table>";
	//顯示群組資料
	$group = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_group where enable=1");
	$groupfunction = $GLOBALS['adoconn']->Execute("select gid from ".ADOPREFIX."_groupfunction where fid=$functionid");
	if ($group && !$group->EOF)
	{
		echo "<tr align='center'><td>"._SET."</td><td>"._GROUPNAME."</td></tr>\n";
		while (!$group->EOF)
		{
			$chk = "";
			if ($groupfunction) $groupfunction->MoveFirst();
			while ($groupfunction && !$groupfunction->EOF)
			{
				if ($groupfunction->fields['gid']==$group->fields['id'])
				{
					$chk = "checked";
					break;
				}
				$groupfunction->MoveNext();
			}
			echo "<tr class='content'>\n"
			."<td align='center'><input type='checkbox' name='group[]' value='".$group->fields['id']."' $chk></td>\n"
			."<td>".stripslashes($group->fields[1])." ( ".stripslashes($group->fields[2])." )</td>\n"
			."</tr>\n";
			$group->MoveNext();
		}
	}
	if ($group) $group->Close();
	if ($groupfunction) $groupfunction->Close();
	
	//顯示使用者資料
	$authors = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_authors where enable=1");
	$permit = $GLOBALS['adoconn']->Execute("select aid from ".ADOPREFIX."_permit where fid=$functionid");
	if ($authors && !$authors->EOF)
	{
		echo "<tr align='center'><td>"._SET."</td><td>"._AUTHORSNAME."</td></tr>";
		while (!$authors->EOF)
		{
			$chk = "";
			if ($permit) $permit->MoveFirst();
			while ($permit && !$permit->EOF)
			{
				if ($permit->fields['aid']==$authors->fields['id'])
				{
					$chk = "checked";
					break;
				}
				$permit->MoveNext();
			}
			echo "<tr class='content'>\n"
			."<td align='center'><input type='checkbox' name='authors[]' value='".$authors->fields['id']."' $chk></td>\n"
			."<td>".$authors->fields['aid']." ( ".$authors->fields['aname']." )</td>\n"
			."</tr>\n";
			$authors->MoveNext();
		}
	}
	if ($authors) $authors->Close();
	if ($permit) $permit->Close();
	echo "<tr><td><input type='submit'></td><td><input type='reset'></td></tr>\n"
	."</table>\n"	
	."</form>\n";
	CloseTable();
	include_once("footer.php");
}


/**
* 設定權限給群組或管理員
* @param functionid	權限功能編號
* @param authors	管理員編號
* @param group		群組編號
* @param sel 		選單種類
* @return
*/
function px_setpermit()
{
	$functionid = $_POST['functionid'];
	$group = $_POST['group'];
	$authors = $_POST['authors'];
	$sel = $_POST['sel'];
	$aid = $_SESSION['aid'];
	$now = date("Y-m-d H:i:s");
	$GLOBALS['adoconn_m']->StartTrans();
	$GLOBALS['adoconn_m']->Execute("delete from ".ADOPREFIX."_permit where fid=$functionid");
	$GLOBALS['adoconn_m']->Execute("delete from ".ADOPREFIX."_groupfunction where fid=$functionid");
	for ($i=0; $i<count($authors); $i++) $GLOBALS['adoconn_m']->Execute("insert into ".ADOPREFIX."_permit(aid,fid,lastupdate,sysadm) values($authors[$i],$functionid,'$now','$aid')");
	for ($i=0; $i<count($group); $i++)   $GLOBALS['adoconn_m']->Execute("insert into ".ADOPREFIX."_groupfunction(gid,fid,lastupdate,sysadm) values($group[$i],$functionid,'$now','$aid')");
	$GLOBALS['adoconn_m']->CompleteTrans();
	Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTFUNCTION&sel=$sel&PHPSESSID=".session_id());
}


/**
* 將權限功能設定為啟用狀態的畫面
* @param functionid 	權限功能編號
* @param sel 			選單種類
*/
function setenable()
{
	$functionid = $_GET['pkid'];
	$sel = $_GET['sel'];
	$rs = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX."_function where id=$functionid");
	if ($rs && !$rs->EOF)
	{
		include_once("header.php");
		authors_displaymenu2($sel);
		OpenTable();
		echo "<center><font class='undertitle'>"._SETENABLE."</font></center><br>"
        ."<table align='center'>"
		."<tr>"
		."<td class='notabletitle'>"._FUNCTIONID.": </td><td class='notablecontent'>".stripslashes($rs->fields['fid'])."</td></tr>"
		."<tr><td class='notabletitle'>"._FUNCTIONNAME.": </td><td class='notablecontent'>".stripslashes($rs->fields['fname'])."</td></tr>"
		."<tr>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=authors&op2=SETFUNCTIONENABLEED&functionid=$functionid&sel=$sel&PHPSESSID=".session_id()."' class='funlinktext'>"._YES."</a></td>"
		."<td><a href='".$_SERVER['PHP_SELF']."?op=authors&op2=LISTDELFUNCTION&sel=$sel&PHPSESSID=".session_id()."' class='funlinktext'>"._NO."</a></td>"
		."</tr>"
		."</table>";
		CloseTable();
		include_once("footer.php");
	} else {
		Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTFUNCTION&sel=$sel&PHPSESSID=".session_id());
	}
	if ($rs)    $rs->Close();
}


/**
* 將權限功能設定為啟用狀態
* @param functionid 	權限功能編號
* @param sel 			選單種類
*/
function px_setenable()
{
	$functionid = $_GET['functionid'];
	$sel = $_GET['sel'];
	$GLOBALS['adoconn_m']->StartTrans();
	$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_function set enable=1 where id=$functionid");
	$GLOBALS['adoconn_m']->CompleteTrans();
	Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTFUNCTION&sel=$sel&PHPSESSID=".session_id());
}


/**
* 新增權限功能模組(上傳打包好的功能模組封裝檔案，含程式及資料結構)
* @param sel 	選單種類
*/
function addfunctionpost()
{
	$sel = $_GET['sel'];
	include_once("header.php");
	authors_displaymenu2($sel);
	OpenTable();
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>"
	."<input type='hidden' name='op' value='authors'>"
	."<input type='hidden' name='op2' value='ADDEDFUNCTION'>"
	."<input type='hidden' name='sel' value='".$_GET['sel']."'>"
	."<table>"
	."<tr>"
	."<td class='notabletitle'>"._FUNCTIONNAME.": </td>"
	."<td class='notablecontent'><input type='file' name='packagefile'></td>"
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
* 將上傳的權限功能模組封裝檔案解開後建立程式及資料表
* @param sel 	選單種類
*/
function px_addfunctionpost()
{
	$sel = $_POST['sel'];
	copy($_FILES['packagefile']['tmp_name'],$_FILES['packagefile']['name']);
	unlink($_FILES['packagefile']['tmp_name']);
	$str = system("unzip -o ".$_FILES['packagefile']['name']." > /dev/null");
	system("chmod 775 . -R");
	unlink($_FILES['packagefile']['name']);
	$sql_file=substr($_FILES['packagefile']['name'],0,-4).".sql";
	if (file_exists($sql_file))
	{
		function_sql($sql_file);
		unlink($sql_file);
    }
	Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTFUNCTION&sel=$sel&PHPSESSID=".session_id());
}


/**
* 在資料庫建立新權限功能需要的資料表
* @param sql_file 	.sql檔案
*/
function function_sql($sql_file)
{
	if(!empty($sql_file))
	{
		$sql_query = addslashes(fread(fopen($sql_file, "r"), filesize($sql_file)));
	}
	$pieces  = split_sql($sql_query);
	if (count($pieces) == 1 && !empty($pieces[0]))
	{
		$sql=stripslashes(trim($pieces[0]));
		$result =  mysql_db_query ($GLOBALS['adodbname'], $sql) or mysql_die();
	} else {
		for ($i=0; $i<count($pieces); $i++)
		{
			$pieces[$i] = stripslashes(trim($pieces[$i]));
			if(!empty($pieces[$i]) && $pieces[$i] != "#")
			{
				$result =  mysql_db_query ($GLOBALS['adodbname'], $pieces[$i]) or mysql_die();
			}
		}
	}
	$sql_query = stripslashes($sql_query);
}


/**
* 將.sql檔案的sql句一句句存入陣列
* @param sql 	.sql檔案的內容
*/
function split_sql($sql)
{
	$sql = trim($sql);
	$sql = ereg_replace("#[^\n]*\n", "", $sql);
	$buffer = array();
	$ret = array();
	$in_string = false;

	for($i=0; $i<strlen($sql)-1; $i++)
	{
		if($sql[$i] == ";" && !$in_string)
		{
			$ret[] = substr($sql, 0, $i);
			$sql = substr($sql, $i + 1);

			$i = 0;
		}

		if($in_string && ($sql[$i] == $in_string) && $buffer[0] != "\\")
		{
			$in_string = false;
		}
		elseif(!$in_string && ($sql[$i] == "\"" || $sql[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\"))
		{
			$in_string = $sql[$i];
		}
		if(isset($buffer[1]))
		{
			$buffer[0] = $buffer[1];
		}
		$buffer[1] = $sql[$i];
	}

	if(!empty($sql))
	{
		$ret[] = $sql;
	}
	return($ret);
}


function mysql_die($error = "")
{
    //global $strError,$strSQLQuery, $strMySQLSaid, $strBack, $sql_query;

    echo "<b> $strError </b><p>";
    if(isset($sql_query) && !empty($sql_query))
    {
        echo "$strSQLQuery: <pre>$sql_query</pre><p>";
    }
    if(empty($error))
        echo $strMySQLSaid.mysql_error();
    else
        echo $strMySQLSaid.$error;
    echo "<br><a href=\"javascript:history.go(-1)\">$strBack</a>";

    exit;
}


######################################## Modify by LiWem Chiang 2005.10.31


/**
* 權限優先權上升
*/
function px_functionup()
{
	$sel=$_GET['sel'];
	$functionid=$_GET['pkid'];
	$fsort = getFsort($functionid);		//原來的權重
	if($fsort == 1){
		$fsort=$newsort=$fid=1;
	}
	else{
		$newsort = $fsort-1;
		$fid = getFsortID($newsort);		//新權重的權限編號
		$GLOBALS['adoconn_m']->StartTrans();
		$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_function set fsort=fsort+1 where id=$fid and enable=1");		//把原權重往下移一位
		$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_function set fsort=fsort-1 where id=$functionid and enable=1");//把自己的權限上升
		$GLOBALS['adoconn_m']->CompleteTrans();
	}
	Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTFUNCTION&sel=$sel&PHPSESSID=".session_id());
}


/**
* 權限優先權下降
*/
function px_functiondown()
{
	$sel=$_GET['sel'];
	$functionid=$_GET['pkid'];
	$fsort = getFsort($functionid);		//原來的權重
	$bigsort=getBsort($fsort);
	if($fsort == $bigsort){
		$fsor=$newsort=$fid=$bigsort;
	}
	else{
		$newsort = $fsort+1;
		$fid = getFsortID($newsort);		//新權重的權限編號
		$GLOBALS['adoconn_m']->StartTrans();
		$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_function set fsort=fsort-1 where id=$fid and enable=1");		//把原權重往下移一位
		$GLOBALS['adoconn_m']->Execute("update ".ADOPREFIX."_function set fsort=fsort+1 where id=$functionid and enable=1");//把自己的權限上升
		$GLOBALS['adoconn_m']->CompleteTrans();
	}
	Header("Location: ".$_SERVER['PHP_SELF']."?op=authors&op2=LISTFUNCTION&sel=$sel&PHPSESSID=".session_id());
}


/**
* 取得優先權的權限編號
*/
function getFsortID($fsort)
{
	$rs = $GLOBALS['adoconn']->Execute("select id from ".ADOPREFIX."_function where fsort=$fsort and enable=1");
	$fid = 0;
	if ($rs && !$rs->EOF)	$fid = $rs->fields[0];
	if ($rs) $rs->Close();
	return $fid;
}


/**
* 取得權限的優先權
*/
function getFsort($functionid)
{
	$rs = $GLOBALS['adoconn']->Execute("select fsort from ".ADOPREFIX."_function where id=$functionid");
	$fsort = 0;
	if ($rs && !$rs->EOF)	$fsort = $rs->fields[0];
	if ($rs) $rs->Close();
	return $fsort;
}


/**
* 取得權限最後一筆的優先權數
*/
function getBsort($fsort)
{
	$rs = $GLOBALS['adoconn']->Execute("select max(fsort) from ".ADOPREFIX."_function where enable=1");
	$bigsort = 0;
	if ($rs && !$rs->EOF)	$bigsort = $rs->fields[0];
	if ($rs) $rs->Close();
	return $bigsort;
}


######################################################


if ($_REQUEST['op']=="authors" && isAuthority($_SESSION['aid'],$_REQUEST['op']))
{
	switch ($_REQUEST['op2'])
	{
		/* 權限功能列表 */
		case "LISTFUNCTION":
		listfunction();
		break;

		/* 列出已不使用的權限功能列表 */
		case "LISTDELFUNCTION":
		listdelfunction();
		break;

		/* 將權限功能設定為啟用狀態的畫面 */
		case "SETFUNCTIONENABLE":
		setenable();
		break;

		/* 將權限功能設定為啟用狀態 */
		case "SETFUNCTIONENABLEED":
		px_setenable();
		break;

		/* 新增權限功能基本資料的畫面 */
		case "ADDFUNCTION":
		addfunctionpost();
		break;

		/* 新增權限功能基本資料 */
		case "ADDEDFUNCTION":
		px_addfunctionpost();
		break;

		/* 修改權限功能基本資料的畫面 */
		case "UPDATEFUNCTION":
		updatefunction();
		break;

		/* 修改權限功能基本資料 */
		case "UPDATEEDFUNCTION":
		px_updatefunction();
		break;

		/* 確認刪除權限功能基本資料的畫面 */
		case "DELFUNCTION":
		delfunction();
		break;

		/* 刪除權限功能基本資料 */
		case "DELEDFUNCTION":
		px_delfunction();
		break;

		/* 設定權限給群組或管理員的畫面 */
		case "FUNCTIONSET":
		setpermit();
		break;

		/* 設定權限 */
		case "FUNCTIONSETED":
		px_setpermit();
		break;

		/* 權限優先權上升 */
		case "FUNCTIONUP":
		px_functionup();
		break;

		/* 權限優先權下降 */
		case "FUNCTIONDOWN":
		px_functiondown();
		break;
	}
}
?>
