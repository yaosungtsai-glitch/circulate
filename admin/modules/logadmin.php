<?php
/**
 * Company: 
 * Program: logadmin.php
 * Author:  Ann Chen
            Ken Tsai 增加logserice err記錄(log)顯示功能
 * Date:    2019.01.10
 * Version: 1.0
 * Description: LOG管理
 */

Header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
if (!eregi(ADMINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
include_once("admin/language/logadmin-".DEFAULTLANGUAGE.".php");

/**
 * LOG管理MENU
 */
function logadminMenu()
{
	include_once("header.php");
	GraphicAdmin();
	echo "<style type='text/css'>
			
		</style>";
	OpenTable();
	echo "<center class='toptitle'>"._LOGADMIN."</center>\n";
	echo "<br>";
	echo "<center>\n";
	echo "<a href='".$_SERVER['PHP_SELF']."?op=logadmin&op2=logdebug'>"._LOGDEBUG."</a>&nbsp&nbsp\n"; //LOG偵錯設定
	echo "<a href='".$_SERVER['PHP_SELF']."?op=logadmin&op2=setwrite'>"._LOGSETWRITELOG."</a>&nbsp&nbsp\n"; //logservice 錯誤紀錄
	echo "<center>\n";
	CloseTable();
	echo "<br>";
}

/**
 * LOG管理主畫面
 */
function logAdmin()
{
	logadminMenu();	
	CloseTable();
	echo "<br>";	
}

/**
 * logservice Error記錄(API:setwritelog)
 **/
function setwrite(){
   logadminMenu();
   include_once("lib/dbpager.inc.php");
   dblogconn($conn);
   $colnames=array('id',_LOGSETWRITEFORMAT,_LOGSETWRITEFORMATNAME,_LOGSETWRITEDATETIME,_LOGSETWRITERRMEESSAGE,_LOGSETWRITEREMARK,_FUNCTIONS);
   $sql= "select id,log_formatid,log_formatname,datetime,errmsg,remark from ".ADOPREFIX_LOGSERVICE."_log order by datetime desc";
   $links[0]['link']="op=logadmin&op2=setwritedetail";
   $links[0]['label']=_LOGSETWRITEVIWE;
   OpenTable();
   echo "<center><font class='undertitle'>"._LOGSETWRITELOG."</font></center>\n";
   dbpage($conn,$sql,$colnames,$links);
	
   CloseTable();
   include_once("footer.php");
}

function setwritedetail(){
   	logadminMenu();
   
   	OpenTable();
	include_once "includes/htmlarea.js";
	require_once "HTML/QuickForm.php";
	dblogconn($conn);
   	//撈出資料
	$pkid = $_GET['pkid'];
	$sql = "select id,log_formatid,log_formatname,datetime,param_in,json_out,errmsg,remark from ".ADOPREFIX_LOGSERVICE."_log where id = ?";
	$sql_params = array($pkid);
	$rs = $conn->Execute($sql,$sql_params);
	$defaultvalue = array
    (
		"formatid"   => $rs->fields['log_formatid'],
		"formatname" => $rs->fields['log_formatname'], 
		"datetime"   => $rs->fields['datetime'],
		"param_in"   => $rs->fields['param_in'],
		"json_out"   => $rs->fields['json_out'],
		"errmsg"     => $rs->fields['errmsg'],
		"remark"     => $rs->fields['remark']
    );
    // var_dump($defaultvalue);
	$form = new HTML_QuickForm('frm','POST',$_SERVER['PHP_SELF']);
	$form->addElement ("header"   ,"myheader"   ,_LOGSETWRITELOG);
	$form->addElement ('static'   ,'formatid'   ,_LOGSETWRITEFORMAT);
	$form->addElement ('static'   ,'formatname' ,_LOGSETWRITEFORMATNAME);
	$form->addElement ('static'   ,'datetime'   ,_LOGSETWRITEDATETIME);
	$form->addElement ('textarea' ,'param_in'   ,_LOGSETWRITEPARAMETER ,array("rows"=>"5","cols"=>"100","disabled"=>"disabled"));
	$form->addElement ('textarea' ,'json_out'   ,_LOGSETWRITERETURN    ,array("rows"=>"5","cols"=>"100","disabled"=>"disabled"));
	$form->addElement ('static'   ,'errmsg'     ,_LOGSETWRITERRMEESSAGE);
	$form->addElement ('static'   ,'remark'     ,_LOGSETWRITEREMARK);
	$form->setDefaults($defaultvalue); //預設值
	//必填規則
	$form->display();
   	CloseTable();
	echo "<br>";
   	include_once("footer.php");
}
/*
** 連結 記錄logservice  error 的DB=>/www/commomn/sqlite/setwrotelog.db
** param @adoconn 傳入dbconnection 
*/
function dblogconn(&$adoconn){
    $adoconn = ADOnewConnection(ADODATABASE_LOGSERVICE);
    $adoconn->connect(ADOHOST_LOGSERVICE);							
}


if ($_REQUEST['op']=="logadmin" && isAuthority($_SESSION['aid'],$_REQUEST['op']))
{
	switch ($_REQUEST['op2'])
	{
		case "logadmin": //LOG管理頁面
		default:
			logAdmin();			
		break;
		//LOG偵錯設定
		case 'logdebug': //LOG偵錯主畫面
		case 'logdebugEdit': //修改debug mode
		case 'logdebugEdittoDB': //修改debug mode至DB
			include_once('logadmin/logdebug.php');
		break;

		case "setwrite":
             setwrite();//logservice Error記錄(API:setwritelog)
		break;
		
		case "setwritedetail":
             setwritedetail();//logservice Error記錄詳細資料
		break;
	}
}