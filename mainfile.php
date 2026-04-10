<?php
/*************************************
 * Company:
 * Program: mainfile.php
 * Author:  Ken Tsai
 * Date:    from 2005.03.24
 * Version: 2.0
 * Description:	站台共用函式庫
                PHP 7.x 以上版本
 *************************************/

if (!headers_sent()) session_start();
include_once("config.php");  //讀取初始設定資訊
include_once("includes/tables.php"); //OpenTble相關定義

/**
 * 使用ADODB連結資料庫
 */
    include_once("adodb5/adodb.inc.php");
    include_once("adodb5/tohtml.inc.php");

// backend
   $GLOBALS['adoconn'] = adoNewConnection(ADODATABASE."://".ADOUNAME.":".ADOPASS."@".ADOHOST_MASTER."/".ADODBNAME."?persist");
// ocrapi
//   $GLOBALS['adoconn_ocr'] = adoNewConnection(ADODATABASE."://".ADOUNAME_API.":".ADOPASS_API."@".ADOHOST_API."/".ADODBNAME_API."?persist");

/**
 * 設定顯示語系
 */
//setcookie("lang",DEFAULTLANGUAGE,time()+31536000);
include("language/lang-".DEFAULTLANGUAGE.".php");

/*
 * 判斷登入是否位置是否在可登入IP位置
 * 如果是要登入至admin管理介面,則要先檢查登入者IP是否在可登入IP區段內
 * 如果是要登入至service管理介面,則先判斷service是否要鎖IP,如果要鎖才檢查登入者IP是否在可登入IP區段內
 * @param store_id	使用者登入商店
 * @return 是否合法 (0.不合法 1.合法)
 */
function chkloginip($store_id=0)
{
	$ip = $_SERVER['REMOTE_ADDR'];
	$isLogin = 0;	//是否可登入
	if (eregi(ADMINPAGE, $_SERVER['PHP_SELF']))
	{
		$table = "_authors_ipmanage";	//登入ADMIN介面
		//ADMIN控管登入IP
		if ($GLOBALS['ADMINIPMANAGE'])
		{
			$sql = "SELECT ipsec1, ipsec2, ipsec3, ipsec4, ipsec5 FROM ".ADOPREFIX.$table;
		} else {
			return 1;
		}
	} else {
		if ($GLOBALS['IPMANAGE']==0 && $store_id==0)	return 1;
		$table = "_ipmanage";	//登入Service介面
		//強制控管商店登入IP
		if ($GLOBALS['IPMANAGE'] && $store_id==0)
		{
			$sql = "SELECT ipsec1, ipsec2, ipsec3, ipsec4, ipsec5 FROM ".ADOPREFIX.$table;
		} else {
			//依各商店自行設定是否控管可登入IP區段
			$rs = $GLOBALS['adoconn']->Execute("select ".ORGTABLE_FIELD_IPMANAGE." from ".ADOPREFIX.ORGTABLE." where ".ORGTABLE_FIELD_ID."='$store_id'");
			if ($rs && !$rs->EOF)	$store_ipmanage = $rs->fields[0];
			else $store_ipmanage = 1;
			if ($rs)	$rs->Close();
			if ($store_ipmanage || $GLOBALS['IPMANAGE'])
			{
				$sql = "SELECT ipsec1, ipsec2, ipsec3, ipsec4, ipsec5 FROM ".ADOPREFIX.$table." where storeid ='$store_id'";
			} else {
				return 1;
			}
		}
	}
	$ipmgr = $GLOBALS['adoconn']->Execute($sql);
	$iparr = explode(".",$ip);
	$ip1 = $iparr[0];
	$ip2 = $iparr[1];
	$ip3 = $iparr[2];
	$ip4 = $iparr[3];
	while ($ipmgr && !$ipmgr->EOF)
	{
		if ($ipmgr->fields[0]==$ip1 && $ipmgr->fields[1]==$ip2 && $ipmgr->fields[2]==$ip3)
		{
			$ipstart = min($ipmgr->fields[3],$ipmgr->fields[4]);
			$ipend = max($ipmgr->fields[3],$ipmgr->fields[4]);
			for ($i=$ipstart; $i<=$ipend; $i++)
			{
				if ($ip4==$i)
				{
					$isLogin = 1;
					break;
				}
			}
		}
		$ipmgr->MoveNext();
	}
	if ($ipmgr)	$ipmgr->Close();
	return $isLogin;
}


/**
 * 判斷是否有後台使用權限
 * @param cadmin 使用者登入後產生的cookie值
 * @return 是否成功登入後台 (1. 成功 0.失敗)
 */
function is_admin($cadmin)
{
	$isAdmin = 0;
	if (!is_array($cadmin))
	{
		$cadmin = base64_decode($cadmin);
		$cadmin = explode(":", $cadmin);
		$aid = $cadmin[0];
		$pwd = $cadmin[1];
		$authors_id = $cadmin[2];
	} else {
		$aid = $cadmin[0];
		$pwd = $cadmin[1];
		$authors_id = $cadmin[2];
	}
	$rs = $GLOBALS['adoconn']->Execute("select pwd from ".ADOPREFIX."_authors where id='$authors_id' and aid='$aid'");
	if ($rs && !$rs->EOF)
	{
		$pass = $rs->fields[0];
		if ($pass==$pwd && $pass!="")	$isAdmin = 1;
		else $isAdmin = 0;
	}
	if ($rs)	$rs->Close();
	return $isAdmin;
}


/**
 * 後台管理者資料初始設定
 */
function init_manage()
{
	$GLOBALS['adoconn']->StartTrans();
	//判斷是否有管理者有 authors 的權限存在
	$count = 0;
	$strsql = "select count(*) as count from "
	.ADOPREFIX."_authors authors, "
	.ADOPREFIX."_function function, "
	.ADOPREFIX."_permit permit "
	."where authors.id=permit.aid and function.id=permit.fid and function.fid='authors'";
	$rs = $GLOBALS['adoconn']->Execute($strsql);
	if ($rs && !$rs->EOF)	$count = $rs->fields['count'];
	if ($rs)	$rs->Close();
	//沒有 authors 的權限存在,將權限指定給 ADMIN
	if ($count==0)
	{
		//取得 authors 權限的編號
		$rs2 = $GLOBALS['adoconn']->Execute("select id from ".ADOPREFIX."_function where fid='authors'");
		if ($rs2 && !$rs2->EOF)
		{
			$fid = $rs2->fields['id'];
			//判斷管理員 ADMIN 是否存在
			$rs_authors = $GLOBALS['adoconn']->Execute("select id, enable from ".ADOPREFIX."_authors where aid='ADMIN'");
			if ($rs_authors && !$rs_authors->EOF)
			{
				$aid = $rs_authors->fields['id'];
				if ($rs_authors->fields['enable']==0)	$GLOBALS['adoconn']->Execute("update ".ADOPREFIX."_authors set enable=1 where id='$aid'");
			} else  {
				$GLOBALS['adoconn']->Execute("insert into ".ADOPREFIX."_authors(aid,aname,pwd,email,enable) values('ADMIN','ADMIN','PASSWORD','admin@domain',1)");
				$aid = $GLOBALS['adoconn']->Insert_ID();
			}
			if ($rs_authors)	$rs_authors->Close();
			$now = date("Y-m-d H:i:s");
			$GLOBALS['adoconn']->Execute("insert into ".ADOPREFIX."_permit(aid,fid,lastupdate,sysadmin) values($aid,$fid,'$now','SYSTEM')");
		}
		if ($rs2)	$rs2->Close();
	}
	$GLOBALS['adoconn']->CompleteTrans();
}


/**
 * 取得權限功能流水號
 * @param fid 權限功能流水號
 * @return 權限功能流水號
 */
function getfunctionid($fid)
{
	$rs = $GLOBALS['adoconn']->Execute("select id from ".ADOPREFIX."_function where enable=1 and fid='$fid'");
	if ($rs && !$rs->EOF)	$functionid = $rs->fields[0];
	if ($rs)	$rs->Close();
	return $functionid;
}


/**
 * 取得權限功能代號
 * @param functionid 權限功能流水號
 * @return 權限功能編號
 */
function getfid($functionid)
{
	$rs = $GLOBALS['adoconn']->Execute("select fid from ".ADOPREFIX."_function where enable=1 and id=$functionid");
	if ($rs && !$rs->EOF)	$fid = $rs->fields[0];
	IF ($rs)	$rs->Close();
	return $fid;
}


/**
 * 取得權限功能名稱
 * @param fid 權限功能代碼
 * @return 權限功能名稱
 */
function getfname($fid)
{
	$rs = $GLOBALS['adoconn']->Execute("select fname from ".ADOPREFIX."_function where enable=1 and fid='$fid'");
	if ($rs && !$rs->EOF)	$fname = $rs->fields[0];
	if ($rs) 	$rs->Close();
	return $fname;
}



/**
 * 取得管理員流水號
 * @param  aid 管理員登入帳號
 * @return 管理員流水號
 */
function getauthorsid($aid)
{
	$rs = $GLOBALS['adoconn']->Execute("select id from ".ADOPREFIX."_authors where enable=1 and aid='$aid'");
	if ($rs && !$rs->EOF)	$authorsid = $rs->fields[0];
	if ($rs)	$rs->Close();
	return $authorsid;
}


/**
 * 取得管理員登入帳號
 * @param authorsid 管理員流水號
 * @return 管理員登入帳號
 */
function getaid($authorsid)
{
	$rs = $GLOBALS['adoconn']->Execute("select aid from ".ADOPREFIX."_authors where enable=1 and id=$authorsid");
	if ($rs && !$rs->EOF)	$aid = $rs->fields[0];
	if ($rs)	$rs->Close();
	return $aid;
}


/**
 * 取得管理員姓名
 * @param aid	管理員代碼
 * @return 管理員姓名
 */
function getaname($aid)
{
	$rs = $GLOBALS['adoconn']->Execute("select aname from ".ADOPREFIX."_authors where enable=1 and aid='$aid'");
	if ($rs && !$rs->EOF)	$aname = $rs->fields[0];
	if ($rs) $rs->Close();
	return $aname;
}


/**
 * 取得群組編號
 * @param groupid 群組流水號
 * @return 群組編號
 */
function getgid($groupid)
{
	$rs = $GLOBALS['adoconn']->Execute("select gid from ".ADOPREFIX."_group where enable=1 and id=$groupid");
	if ($rs && !$rs->EOF)	$gid = $rs->fields[0];
	if ($rs)	$rs->Close();
	return $gid;
}


/**
 * 取得群組名稱
 * @param gid 群組代碼
 * @return 群組名稱
 */
function getgname($gid)
{
	$rs = $GLOBALS['adoconn']->Execute("select gname from ".ADOPREFIX."_group where enable=1 and gid='$gid'");
	if ($rs && !$rs->EOF)	$gname = $rs->fields[0];
	if ($rs)	$rs->Close();
	return $gname;
}


/**
 * 傳入群組編號,傳出此群組內含的管理員編號
 * @param group_id 群組編號
 * @return 管理員編號
 */
function getGroupAuthors($group_id)
{
	$rs = $GLOBALS['adoconn']->Execute("select aid from ".ADOPREFIX."_groupuser where gid=$group_id");
	$i=0;
	while ($rs && !$rs->EOF)
	{
		$authors[$i] = $rs->fields[0];
		$i++;
		$rs->MoveNext();
	}
	if ($rs) $rs->Close();
	return $authors;
}


/**
 * 傳入群組編號,傳出此群組擁有的權限
 * @param group_id 群組編號
 * @return 權限編號
 */
function getGroupPermit($group_id)
{
	$rs = $GLOBALS['adoconn']->Execute("select fid from ".ADOPREFIX."_groupfunction where gid=$group_id");
	$i=0;
	while ($rs && !$rs->EOF)
	{
		$function[$i] = $rs->fields[0];
		$i++;
		$rs->MoveNext();
	}
	if ($rs) $rs->Close();
	return $function;
}


/**
 * 傳入管理員編號,傳出加入那些群組
 * @param authors_id 管理員編號
 * @return 群組編號
 */
function getAuthorsGroup($authors_id)
{
	$rs = $GLOBALS['adoconn']->Execute("select gid from ".ADOPREFIX."_groupuser where aid=$authors_id");
	$i = 0;
	while ($rs && !$rs->EOF)
	{
		$group[$i] = $rs->fields[0];
		$i++;
		$rs->MoveNext();
	}
	if ($rs) $rs->Close();
	return $group;
}


/**
 * 傳入管理員編號,傳出擁有那些權限
 * @param authors_id 管理員編號
 * @return 權限編號
 */
function getAuthorsPermit($authors_id)
{
	/* 取出所屬群組的權限 */
	$rsgroup = $GLOBALS['adoconn']->Execute("select f.fid from ".ADOPREFIX."_groupuser u, ".ADOPREFIX."_groupfunction f where u.gid=f.gid and u.aid=$authors_id");
	$i=0;
	while ($rsgroup && !$rsgroup->EOF)
	{
		$permit[$i] = $rsgroup->fields[0];
		$i++;
		$rsgroup->MoveNext();
	}
	if ($rsgroup) $rsgroup->Close();

	/* 取出本身的權限 */
	$rs = $GLOBALS['adoconn']->Execute("select fid from ".ADOPREFIX."_permit where aid=$authors_id");
	while ($rs && !$rs->EOF)
	{
		if ($i==0)
		{
			$permit[$i]=$rs->fields[0];
			$i++;
		} else {
			array_push($permit,$rs->fields[0]);
		}
		$rs->MoveNext();
	}

	if ($rs) $rs->Close();
	if (is_array($permit)) $permit = array_unique($permit);	//將重覆的值去除
	if (is_array($permit)) sort($permit);	//重新排序
	return $permit;
}


/**
 * 傳入權限編號,傳出有那些管理員擁有該權限
 * @param function_id 權限編號
 * @return 管理員編號
 */
function getPermitAuthors($function_id)
{
	$rs = $GLOBALS['adoconn']->Execute("select aid from ".ADOPREFIX."_permit where fid=$function_id");
	$i = 0;
	while ($rs && !$rs->EOF)
	{
		$authors[$i] = $rs->fields[0];
		$i++;
		$rs->MoveNext();
	}
	if ($rs) $rs->Close();
	return $authors;
}


/**
 * 傳入權限編號,傳出有那些群組擁有該權限
 * @param function_id 權限編號
 * @return 群組編號
 */
function getPermitGroup($function_id)
{
	$rs = $GLOBALS['adoconn']->Execute("select gid from ".ADOPREFIX."_groupfunction where fid=$function_id");
	$i = 0;
	while ($rs && !$rs->EOF)
	{
		$group[$i] = $rs->fields[0];
		$i++;
		$rs->MoveNext();
	}
	if ($rs) $rs->Close();
	return $group;
}


/**
 * 傳入權限編號,傳出有那些群組和管理員擁有該權限
 * @param function_id 權限編號
 * @return 群組和管理員編號(G:group_id,A:authors_id)
 */
function getPermitOwner($function_id)
{
	$authors = getPermitAuthors($function_id);
	$group = getPermitGroup($function_id);
	for ($i=0; $i<count($group); $i++)
		$function[$i] = "G:".$group[$i];
	if ($i>0) $i++;
	for ($j=0; $j<count($authors); $j++)
		$function[$i+$j] = "A:".$authors[$j];
	return $function;
}


/**
 * 傳入管理員編號及群組編號,傳出是否在這個群組內
 * @param authors_id 管理員編號
 * @param group_id   群組編號
 * @return 是否有權限
 */
function isGroupAuthors($authors_id,$group_id)
{
	$rs = $GLOBALS['adoconn']->Execute("select id from ".ADOPREFIX."_groupuser where aid=$authors_id and gid=$group_id");
	$permit = 0;
	if ($rs && !$rs->EOF) $permit = 1;
	if ($rs) $rs->Close();
	return $permit;
}


/**
 * 傳入權限功能編號及群組編號,傳出是否有權限
 * @param group_id   群組編號
 * @param function_id 權限功能編號
 * @return 是否有權限
 */
function isGroupPermit($group_id,$function_id)
{
	$rs = $GLOBALS['adoconn']->Execute("select id from ".ADOPREFIX."_groupfunction where fid=$function_id and gid=$group_id");
	$permit = 0;
	if ($rs && !$rs->EOF) $permit = 1;
	if ($rs) $rs->Close();
	return $permit;
}


/**
 * 傳入管理員編號及權限功能編號,傳出是否有權限
 * @param authors_id  管理員編號
 * @param function_id 權限功能編號
 * @return 是否有權限
 */
function isPermit($authors_id,$function_id)
{
	$permit = getAuthorsPermit($authors_id);
	for ($i=0; $i<count($permit); $i++)
		if ($function_id==$permit[$i])	return 1;
	return 0;
}


/**
 * 傳入管理員登入帳號及權限功能編號,傳出是否有權限
 * @param aid 管理員登入帳號
 * @param fid 權限功能編號
 * @return 是否有權限
 */
function isAuthority($aid,$fid)
{
	$authors_id = getauthorsid($aid);
	$function_id = getfunctionid($fid);
	$isAuthority = isPermit($authors_id,$function_id);
	return $isAuthority;
}

/**
 * XML資訊擷取
 * @param bid	版面區塊編號
 */
/*
function headlines($bid)
{
	$rs = $GLOBALS['adoconn']->Execute("select title, content, url, refresh, time from ".ADOPREFIX."_blocks where bid='$bid'");
	if ($rs && !$rs->EOF)
	{
		$title = $rs->fields['title'];
		$content = $rs->fields['content'];
		$url = $rs->fields['url'];
		$refresh = $rs->fields['refresh'];
		$otime = $rs->fields['time'];
	}
	if ($rs) $rs->Close();
	$past = time()-$refresh;
	if ($otime < $past)
	{
		$btime = time();
		$rdf = parse_url($url);
		$fp = fsockopen($rdf['host'], 80, $errno, $errstr, 15);
		if (!$fp) {
			$content = "";
			//$content = "<font class='content'>"._RSSPROBLEM."</font>";
			$GLOBALS['adoconn']->StartTrans();
			$GLOBALS['adoconn']->Execute("update ".ADOPREFIX."_blocks set content='$content', time='$btime' where bid='$bid'");
			$GLOBALS['adoconn']->CompleteTrans();
			$cont = 0;
			themesidebox($title, $content);
			return;
		}
		if ($fp) {
			fputs($fp, "GET " . $rdf['path'] . "?" . $rdf['query'] . " HTTP/1.0\r\n");
			fputs($fp, "HOST: " . $rdf['host'] . "\r\n\r\n");
			$string	= "";
			while(!feof($fp)) {
				$pagetext = fgets($fp,300);
				$string .= chop($pagetext);
			}
			fputs($fp,"Connection: close\r\n\r\n");
			fclose($fp);

			$items = explode("</item>",$string);

			$content = "<font class='info'>";
			for ($i=0;$i<(count($items)-1);$i++) {
				$link = ereg_replace(".*<link>","",$items[$i]);
				$link = ereg_replace("</link>.*","",$link);
				$title2 = ereg_replace(".*<title>","",$items[$i]);
				$title2 = ereg_replace("</title>.*","",$title2);
				if ($items[$i] == "") {
					$content = "";
					//$content = "<font class='content'>"._RSSPROBLEM."</font>";
					$GLOBALS['adoconn']->Execute("update ".ADOPREFIX."_blocks set content='$content', time='$btime' where bid='$bid'");
					$cont = 0;
					themesidebox($title, $content);
					return;
				}
				if (strcmp($link,$title)) {
					$cont = 1;
					//$content .= "<strong><big>&middot;</big></strong>&nbsp;<a href='$link' target='new'>$title2</a><br>\n";
					$content .= "‧<a href='$link' target='new' class='info'>$title2</a><br>\n";
				}
			}
		}
		$GLOBALS['adoconn']->Execute("update ".ADOPREFIX."_blocks set content='$content', time='$btime' where bid='$bid'");
	}
	themesidebox($title, $content);
}

*/
/**
 * 過濾可能會出錯的文字
 */
/*
function FixQuotes ($what = "") {
    $what = ereg_replace("'","''",$what);
    while (eregi("\\\\'", $what)) {
        $what = ereg_replace("\\\\'","'",$what);
    }
    return $what;
}
*/

/**
 * 產生亂數數值
 * @return 亂數數值
 */
function randnum()
{
	return sprintf("%06d",mt_rand(1,999999));
}


/**
 * 取得所有組織(或單位)資訊
 * @return 組織單位資訊
 */
function getOrgUnit()
{
	$rs = $GLOBALS['adoconn']->Execute("select * from ".ADOPREFIX.ORGTABLE);
	return $rs;
}


/**
 * @return void
 * @param String $formaction	目的位置
 * @param Array	 $fields		傳出值
 * @param String $formname		表單名稱
 * @param String $showaitmsg	是否顯示等待訊息(1:顯示, 0:不顯示)
 * @desc 以HTTP POST方式將值傳出
 * 20060629多加了method參數
 * 20060915多加了showaitmsg參數
 */
function POSTFORM($formaction,$fields,$formname='goto',$method='post',$showaitmsg='1')
{
	echo "<html><head>";
	echo "<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset="._CHARSET."'>\n";
	echo "<title>POSTFORM</title></head>\n<body>\n";
	//顯示請稍待訊息	modify by yushin, 2006-09-15.
	if ($showaitmsg == "1")
	{
		echo "<center><font size='4' color='blue'>"._WAITINGMSG."</font></center>";
	}
	echo "<form name='$formname' id='$formname' action='$formaction' method='$method'>\n"; //modify Ken Tsai 20060629
	foreach($fields as $fieldname => $fieldvalue)
		echo "<input type='hidden' name='".$fieldname."' value='".$fieldvalue."'>\n";
	echo "</form>\n";
	echo "</body></html>";
	echo "<script type='text/javascript'>\n"
	//modify by amy 2006/01/18
	."document.getElementById('$formname').submit();\n"
	."</script>\n";
}


/**
 * 顯示ADMIN介面錯誤訊息
 * @param $op		權限功能代碼
 * @param $errmsg	錯誤訊息內容
 */
function show_errmsg($op,$errmsg)
{
	include_once(SCRIPT_PATH."/header.php");

	GraphicAdmin();	//顯示後台管理功能

	//顯示管理功能名稱
	OpenTable();
	echo "<center><font class='toptitle'>".getfname($op)."</font></center>\n";
	CloseTable();
	echo "<br/>\n";

	OpenTable();
	echo "<center><font class='Warning'>".$errmsg."</font></center>\n";
	echo $errmsg;
	CloseTable();
	echo "<br/>\n";

	include_once(SCRIPT_PATH."/footer.php");
}

/**
 * 取得login權限功能流水號
 * @param fid 權限功能流水號
 * @return 權限功能流水號
 */
function getloginfunctionid($fid)
{
	$rs = $GLOBALS['adoconn']->Execute("select id from ".ADOPREFIX."_storefunction where enable=1 and sfid='$fid'");
	if ($rs && !$rs->EOF)	$functionid = $rs->fields[0];
	if ($rs)	$rs->Close();
	return $functionid;
}


/**
 * 取得login權限功能代號
 * @param functionid 權限功能流水號
 * @return 權限功能編號
 */
function getloginfid($functionid)
{
	$rs = $GLOBALS['adoconn']->Execute("select sfid from ".ADOPREFIX."_storefunction where enable=1 and id=$functionid");
	if ($rs && !$rs->EOF)	$fid = $rs->fields[0];
	IF ($rs)	$rs->Close();
	return $fid;
}


/**
 * 取得login權限功能名稱
 * @param fid 權限功能代碼
 * @return 權限功能名稱
 */
function getloginfname($fid)
{

	$rs = $GLOBALS['adoconn']->Execute("select sfname from ".ADOPREFIX."_storefunction where enable=1 and sfid='$fid'");

	if ($rs && !$rs->EOF)	
		$fname = $rs->fields[0];
	if ($rs) 	$rs->Close();
	return $fname;
}

//獲取使用者IP， 定義一個函數getIP()
function getClientIP(){
	$ip='127.0.0.1';
	if (getenv("HTTP_CLIENT_IP")) {
		$ip = getenv("HTTP_CLIENT_IP");
	}elseif(getenv("HTTP_X_FORWARDED_FOR")) {
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	}elseif(getenv("REMOTE_ADDR")) {
	$ip = getenv("REMOTE_ADDR");
   }
	return $ip;
}

function include_opdir($op){
	$str =  explode("/",$_SERVER['SCRIPT_FILENAME']);
	if(!is_null($str)){
		$num=count($str);
		$pgn = $str[$num-1];
	}else{
		$pgn = 'administrator.php';
	}
	$dirstr=dirname(__FILE__);
	if($str[$num-1]=='administrator.php'){
		$include_opdir ="$dirstr/admin/modules/$op";
	}else{
		$include_opdir ="$dirstr/service/modules/$op";
	}		
	if (is_dir($include_opdir)) {
		$files = scandir("$include_opdir"); 
		for($i=0;$i<sizeof($files);$i++){
			if($files[$i]!='.' && $files[$i]!='..')
				include_once("$include_opdir/".(string)$files[$i]);
		}
	}
}
?>
