<?php
/*******************************************
 * Company: 
 * Program: adminstrator.php，可依需求變更檔名
 * Author:  Ken Tsai
 * Date:    from 2002 03 07
 * Version: 2.0
 * Desc:	後台管理程式
 *******************************************/

include_once("mainfile.php");

if($_COOKIE['adminCookie']=="1" || $_GET['op']=='login' || !isset($_GET['op'])){
	setcookie("adminCookie", "1", time()+600);  //閒置超過5mins，Cookie會被清      
}else{ //閒置超過5mins，自動logout
	logout();
	exit;
}
//判斷是否從合法IP登入ADMIN介面
/*
if ($GLOBALS['ADMINIPMANAGE']==1)
{
	if (!(chkloginip()))	exit();
}
*/
/**
** 後台管理者登入畫面
**/
function login()
{
	include_once("header.php");
	setcookie("adminCookie", "1", time()+300);//閒置超過5mins，Cookie會被清      
	echo "<br>";
	OpenTable();
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'> \n"
	."<input type='hidden' name='op' value='px_login'> \n"
	."<div class='logindiv'><table class='logintable' cellspacing='0' cellpadding='0' align='center'> \n"
	."<tr>"
	."<td class='logintext01'>"._ADMIN_AID." : </td> \n"
	."<td class='logintext02'><input type='text' name='aid' maxlength='30'></td></tr> \n"
	."<tr>"
	."<td class='logintext01'>"._ADMIN_PASSWORD." : </td> \n"
	."<td class='logintext02'><input type='password' name='pwd'  maxlength='20'></td></tr> \n"
	."<tr>"
	."<td class='logintext01'><img src='image.php'></td>\n"
	."<td class='logintext02'><input type='text' NAME='pwd2' MAXLENGTH='18'><br/>"._ADMINCHECKID."</td></td></tr>\n"
	."<tr><td colspan='2' align='center'> \n"
	."<input type='submit' value='"._ADMIN_LOGIN."'> \n"
	."</td></tr> \n"
	."<tr><td colspan='2' align='center'>\n"
	//."<input type='checkbox' name='adflag' value='1' checked>"._ADCHECK."\n"
	// ."<input type='checkbox' name='adflag' value='1' >"._ADCHECK."\n"
	."</td></tr> \n"
	."</table></div> \n"
	."</form> \n";
	CloseTable();
	include_once("footer.php");
}


/**
 * 後台管理者登入檢查
 */
function px_login()
{
	if ( $_SESSION['rand']==$_POST['pwd2'])
	{
			foreach ($_POST as $key=>$value)
			if (trim($value)=="")	Header("Location: ".$_SERVER['PHP_SELF']."?op=login");
			init_manage();	//管理員初始化
			$rs = $GLOBALS['adoconn']->Execute("select id, pwd, email from ".ADOPREFIX."_authors where enable=1 and aid='".$_POST['aid']."'");
			if ($rs && !$rs->EOF)
			{
				$authors_id = $rs->fields['id'];
				$authors_pw = $rs->fields['pwd'];
			}
			if ($rs)	$rs->Close();
			//echo $authors_pw; 
			//echo $_POST['pwd'];
			//if ($authors_pw == encrypt($_POST['pwd'],$rs->fields['email'])){
			if ($authors_pw == $_POST['pwd']){
				//$admin = base64_encode($_POST['aid'].":".$_POST['pwd'].":".$authors_id);
				//$admin = base64_encode($_POST['aid'].":".encrypt($_POST['pwd'],$rs->fields['email']).":".$authors_id);
	            $admin = base64_encode($_POST['aid'].":".$_POST['pwd'].":".$authors_id);
				$_SESSION['aid'] = $_POST['aid'];
				$_SESSION['authors_id'] = $authors_id;
				setcookie("admin", $admin);
				Header("Location: ".$_SERVER['PHP_SELF']."?op=adminMain&PHPSESSID=".session_id());
				//webpush_logadmin($authors_id);
			} else {
				Header("Location: ".$_SERVER['PHP_SELF']."?op=login");
			}
		// }
	} else {
		Header("Location: ".$_SERVER['PHP_SELF']."?op=login");
	}
}

/**
 * 後台管理者登入 AD 驗證檢查
 * 先到 cloud_tc_authors 檢查 ad_account 帳號是否在在，存在的話才去呼叫 AD API 去做驗證
 */
/*
function ad_login()
{		
  	$sql="select id, pwd from ".ADOPREFIX."_authors where enable=1 and ad_account='".$_POST['aid']."'";
  	$rs = $GLOBALS['adoconn']->Execute($sql);
  	if ($rs && !$rs->EOF){
  		//呼叫lib checkad(@ad_account,@ad_pwd)驗證ad帳密是否正確
  		$is_login = checkad($_POST['aid'],$_POST['pwd']);
		if($is_login){
			$authors_id = $rs->fields['id'];
			$authors_pw = $rs->fields['pwd'];
			$admin = base64_encode($_POST['aid'].":".$_POST['pwd'].":".$authors_id);
			$_SESSION['aid'] = $_POST['aid'];
			$_SESSION['authors_id'] = $authors_id;
			//setcookie("admin", $admin, 0);
			setcookie("admin", $admin);
			Header("Location: ".$_SERVER['PHP_SELF']."?op=adminMain&PHPSESSID=".session_id());
		} else {
			Header("Location: ".$_SERVER['PHP_SELF']."?op=login");
		}
  	} else {
		Header("Location: ".$_SERVER['PHP_SELF']."?op=login");
	}
}
*/
/**
 * 顯示功能介面
 * @param url 功能連結至
 * @param title 功能名稱
 * @param image 功能圖示
 */
function adminmenu($url, $title, $image)
{
	if (ADMINGRAPHIC == 1)
	{
		$img = "<img src='".ADMINIMG.$image."' border='0' alt='$title'>";
		$funclass = "";
	} else {
		$img = $title;
		$funclass = "AdminFuncMenu";	//文字模式時以此class設定功能文字的呈現樣式
	}
	echo "<td align='center'><a href='$url&PHPSESSID=".session_id()."' class='$funclass'>$img</a><br></td> \n";
}


/**
 * 將管理員的使用記錄寫入log
 * @param title 功能名稱
 */
function writelog($op,$op2='',$title='')
{
	if (trim($op)!="" && trim($op)!="referers")
	{
		$title = addslashes($title);
		if (trim($title)=="")	$title = getfname($op);
		//轉成小寫存入DB
		$op = strtolower($op);
		$op2 = strtolower($op2);
		$sql = "insert into ".ADOPREFIX."_login_log(log_loginname,log_time,log_op,log_op2,log_title,log_ip) values(?,?,?,?,?,?)";
		$stmt = $GLOBALS['adoconn']->Prepare($sql);
		$param = array($_SESSION['aid'],date("Y-m-d H:i:s"),$op,$op2,$title,$_SERVER['REMOTE_ADDR']);
		$GLOBALS['adoconn']->StartTrans();
		$GLOBALS['adoconn']->Execute($stmt, $param);
		$GLOBALS['adoconn']->CompleteTrans();
	}
}


/**
 * 管理者登出
 */
function logout()
{
	setcookie("admin");
	unset($_SESSION);
	unset($_COOKIE['adminCookie']);
	include_once("header.php");
	OpenTable();
	echo "<center><font class='title'><b>"._YOUARELOGGEDOUT."</b></font></center> \n";
	CloseTable();
	echo "<br /> \n";
	OpenTable();
	echo "<center><font class='undertitle'><a href='".$_SERVER['PHP_SELF']."?op=login'>"._ADMIN_LOGIN."</a></font></center> \n";
	CloseTable();
	echo "<br />\n";
	include_once("footer.php");
}


/**
 * 後台管理介面主功能
 */
function adminMain()
{
	include_once("header.php");
	GraphicAdmin();
	include_once("footer.php");
}


/**
 * 顯示後台管理功能
 */
function GraphicAdmin()
{
	//2019.07.26 log錯誤webpush by Ann
	/*
	if(isset($_SESSION['authors_id'])) 
		webpush_logadmin($_SESSION['authors_id']);
*/
	//依照登入帳號,取得權限
	$cookieadmin = $_COOKIE['admin'];
	if (!is_array($cookieadmin))
	{
		$cookieadmin = base64_decode($cookieadmin);
		$cookieadmin = explode(":", $cookieadmin);
	}
	$authors_id = $cookieadmin[2];
	$permit = getAuthorsPermit($authors_id);
	include_once("header.php");
	echo "<br>";
	OpenTable();
	echo "<table align='center' border='0' cellspacing='1' class='AdminFuncTable'> \n"
	."<tr valign='top'> \n";
	if (is_array($permit)) sort($permit);
	for ($i=0; $i<count($permit); $i++)
	$permitsort.=$permit[$i].",";
	while (substr($permitsort, strlen($permitsort)-1, strlen($permitsort))==",")
	{
		$permitsort = substr($permitsort,0,strlen($permitsort)-1);
	}
	$strpermitsort = $permitsort;
	
	$rs = $GLOBALS['adoconn']->Execute("select id from ".ADOPREFIX."_function where id in ($strpermitsort) order by fsort");
	$i=0;
	$spermit = array();
	while ($rs && !$rs->EOF)
	{
		array_push($spermit, $rs->fields[0]);
		$i++;
		$rs->MoveNext();
	}
	
	if ($rs) $rs->Close();
	for ($i=0; $i<count($spermit); $i++)
	{
		$linksdir = dir("admin/links");
		while ($func=$linksdir->read())
		{	
			if (substr($func, 0, 6) == "links.")
			{
				$fname = substr($func,6);
				$fname = substr($fname,0,strlen($fname)-4);
				if (getfid($spermit[$i])==$fname)
				$menulist.="$func ";

			}
		}
	}
	//改移到下面去，因為下面那段程式還有用到$linksdir       by yushin, 2008-04-08.
	//if ($linksdir)  closedir($linksdir->handle);

	$menulist = explode(" ", $menulist);
	$_SESSION['counter'] = 0;   //計算每列個數以判斷是否要換行的參數
	for ($i=0; $i < sizeof($menulist); $i++)
	{
		if ($menulist[$i] != "")
		{
			include_once($linksdir->path."/".$menulist[$i]);
			if ($_SESSION['counter'] == ADMINFUNCPERROW) {
				echo "</tr><tr valign='top'> \n";
				$_SESSION['counter'] = 0;
			} else {
				$_SESSION['counter'] = $_SESSION['counter'] + 1;
			}
		}
	}
    //close $linksdir 改移到此處    by yushin, 2008-04-08.
	if ($linksdir)  closedir($linksdir->handle);    
	adminmenu($_SERVER['PHP_SELF']."?op=logout&title="._ADMIN_LOGOUT, _ADMIN_LOGOUT, "exit.png");	//登出
	//補最後一列剩餘空格    by yushin, 2008-04-22.
	for ($j=0; $j<(ADMINFUNCPERROW-$_SESSION['counter']); $j++)
	{
		echo "<td>&nbsp;</td> \n";
	}
	echo "</tr> \n</table> \n";
	echo "<div id='toggleMenu' class='toggleMenu' onclick='toggleMenu()' >"._HIDDENMENU."</div>"; 
	echo "<script type = 'text/javascript'>"
		."var toggleTable = document.getElementsByClassName('AdminFuncTable');"
		."var menu        = document.getElementById('toggleMenu');"
		."checkMenuStatus();"
		 //檢查紀錄選單狀態
		."function checkMenuStatus(){
			var nowStatus = sessionStorage.getItem('menuStatus')?sessionStorage.getItem('menuStatus'):1;
			if(nowStatus == 0){
				toggleTable[0].style.display = 'none';
				toggleTable[0].style.opacity = '0';
				menu.innerHTML = '"._OPENMENU."';
			}
		 }"
		 //變更選單狀態
		."function toggleMenu() {
			if (toggleTable[0].style.opacity == '0') {
				toggleTable[0].style.display = 'inline-table';
				menu.innerHTML = '"._HIDDENMENU."';
				setTimeout(function(){toggleTable[0].style.opacity = '1';},300);
				sessionStorage.setItem('menuStatus',1);
    		} else {
    			toggleTable[0].style.opacity = '0';
    			menu.innerHTML = '"._OPENMENU."';
        		setTimeout(function(){toggleTable[0].style.display = 'none'},600);
        		sessionStorage.setItem('menuStatus',0);
    		}
		 }</script>";
	CloseTable();
	echo "<br> \n";
}

/*
** webpush_logadmin
** 說明：Log管理最新錯誤紀錄推播
** param @aid: 管理員id 
*/
/*
function webpush_logadmin($aid){
	//取得siteid
	$sql = " select * from ".ADOPREFIX."_store where siteurl = ?";
	$sql_params = array(WEBPUSH_LOG_SITE); 
	$rs = $GLOBALS['adoconn']->Execute($sql,$sql_params);
	if($rs){
		$siteid = $rs->fields['id'];
	}
	//呼叫推播component
	echo "<script type='text/javascript' src='".WEBPUSH_COMPONENT_DOMAIN."component/webpush.js.php?site=".$siteid."&mid=".$aid." '></script>";
}
*/
switch ($_REQUEST['op'])
{
	case "GraphicAdmin":
		GraphicAdmin();
		break;

	case "adminMain":
		adminMain();
		break;

	case "logout":
		logout();
		break;

	case "login";
		unset($_REQUEST['op']);
		login();
		break;

	case "px_login":
		px_login();
		break;

	default:
		if (is_admin($_COOKIE['admin']))
		{
			//依照登入帳號,取得權限
			$functionname = "";
			$cookieadmin = $_COOKIE['admin'];
			if (!is_array($cookieadmin))
			{
				$cookieadmin = base64_decode($cookieadmin);
				$cookieadmin = explode(":", $cookieadmin);
			}
			$authors_id = $cookieadmin[2];
			$permit = getAuthorsPermit($authors_id);
			if (is_array($permit)) sort($permit);
			$casedir = dir("admin/case");
			while ($func = $casedir->read())
			{
				if (substr($func, 0, 5) == "case.")
				{
					for ($i=0; $i<count($permit); $i++)
					{
						$functionname = substr($func,5);
						$functionname = substr($functionname,0,strlen($functionname)-4);
						if (getfid($permit[$i])==$functionname)
						{
							$menulist .= $func." ";
							break;
						}
					}
				}
			}
            //改移到下面去，因為下面那段程式還有用到$casedir       by yushin, 2008-04-22.
			//if ($casedir)	closedir($casedir->handle);
			$menulist = explode(" ", $menulist);
			if (is_array($menulist)) sort($menulist);
			for ($i=0; $i < sizeof($menulist); $i++)
				if ($menulist[$i] != "")	include_once($casedir->path."/".$menulist[$i]);
            //close $casedir 改移到此處    by yushin, 2008-04-22.
			if ($casedir)	closedir($casedir->handle);
		} else {
			//login();
			header("location:".$_SERVER['PHPSESSID']."?op=login");

		}
		break;
}


?>
