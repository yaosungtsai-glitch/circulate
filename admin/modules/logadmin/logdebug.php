<?php
/**
 * Company: 
 * Program: logdebug.php
 * Author:  Ann Chen
 * Date:    2019.01.10
 * Version: 1.0
 * Description: LOG偵錯設定
 */

/*
 名稱維護主畫面
*/
function logdebug(){

	logadminMenu();	
	CloseTable();
	//新增名稱
	OpenTable();
	include_once "includes/htmlarea.js";
	require_once "HTML/QuickForm.php";
	// echo "<center><font class='undertitle'>"._UPLOADSTOREIMG."</font></center>\n";
	$form = new HTML_QuickForm('frm','POST',$_SERVER['PHP_SELF']);
	$form->addElement ("header"  ,"myheader"  ,_LOGSEARCHLOG);
	$form->addElement ('text'    ,'name'      ,_LOGENAME);
	$form->addElement ("hidden"  ,"op"        ,"logadmin");
	$form->addElement ("hidden"  ,"op2"       ,"logdebug");
	$form->addElement ("submit"  ,"btnSubmit" ,_SEARCH); 
	//必填規則
	$form->addRule("name"  ,_NOEMPTYNAME  ,"required","","client");  // 名稱不得為空
	$form->display();
	CloseTable();
	echo "<br>";

	//列表
	logquery();
}

/*
 名稱維護列表
*/
function logquery() {
	//參數傳入

	// if(isset( $_POST['name'])){
	  $name = $_POST['name'];
	//   $_SESSION['name']=$_POST['name'];
	// }elseif(isset($_SESSION['name']))
	// 	$name = $_SESSION['name'];
	
	$nameQuery = '';
	if(!empty($name)){
		//若有傳入log名稱，sql要多加篩選條件
		$nameQuery = "where name like '%$name%'";
	}
	$sql = "SELECT id,name,
		(CASE exectype 
			WHEN 1 THEN '"._LOGEXECTYPE1."' 
			WHEN 2 THEN '"._LOGEXECTYPE2."' 
			WHEN 3 THEN '"._LOGEXECTYPE3."' 
			WHEN 4 THEN '"._LOGEXECTYPE4."' 
			ELSE '"._OTHER."' END
		) as exectype,
		(CASE debugmode 
			WHEN 0 THEN '"._LOGDEBUGMODE0."' 
			WHEN 1 THEN '"._LOGDEBUGMODE1."' 
			WHEN 2 THEN '"._LOGDEBUGMODE2."' 
			ELSE '"._OTHER."' END
		) as debugmode,
		(CASE enable
			WHEN 1 THEN 'Y'
			ELSE 'N' END
		) as enable,
		remark 
		FROM log_format ".$nameQuery." ";

	// include_once("lib/mypager.inc");
	OpenTable();
	echo "<center><font class='undertitle'>"._LOGLIST."</font></center>\n";
	// $pager = new MyPager($GLOBALS['adoconnlog'],$sql,'appid',true);
	// $GridHeader = array(_ID,_LOGENAME,_LOGEXECTYPE,_LOGDEBUGMODE,_LOGREMARK,_FUNCTIONS);
	// $pager->setRenderGridLayout("width='100%' align='center'",$GridHeader);
	// $funcNames = array(_EDIT);
	// $funcUrls  = array($_SERVER['PHP_SELF']."?op=logadmin&op2=logdebugEdit");
	// $pager->setFunctions($funcNames,$funcUrls);
	// $pager->setOp("logadmin&op2=logdebug&PHPSESSID=".session_id());
	// $pager->Render_Function($GLOBALS['perpage']);
	include_once("lib/dbpager.inc.php");
	$colnames=array(_ID,_LOGENAME,_LOGEXECTYPE,_LOGDEBUGMODE,_LOGENABLE,_LOGREMARK,_FUNCTIONS);
	$links[0]['link']="op=logadmin&op2=logdebugEdit";
	$links[0]['label']=_EDIT;
	$rows=dbpage($GLOBALS['adoconnlog'],$sql,$colnames,$links);

	CloseTable();
	echo "<br>";
	include_once("footer.php");
}

/*
 名稱維護主畫面
*/
function logdebugEdit(){

	logadminMenu();	
	CloseTable();

	//修改名稱
	OpenTable();
	include_once "includes/htmlarea.js";
	require_once "HTML/QuickForm.php";
	//撈出資料
	$pkid = $_GET['pkid'];
	$sql = "select name,exectype,remark,debugmode,enable from log_format where id = ?";
	$sql_params = array($pkid);
	$rs = $GLOBALS['adoconnlog']->Execute($sql,$sql_params);
	switch ($rs->fields['exectype']) {
		case 1: $exectype = _LOGEXECTYPE1; break;
		case 2: $exectype = _LOGEXECTYPE2; break;
		case 3: $exectype = _LOGEXECTYPE3; break;
		case 4: $exectype = _LOGEXECTYPE4; break;
	}
	//debug mode選單
	$debugmode[0] = _LOGDEBUGMODE0; 
	$debugmode[1] = _LOGDEBUGMODE1; 
	$debugmode[2] = _LOGDEBUGMODE2; 

	$defaultvalue = array
    (
		"name"      => $rs->fields['name'],
		"exectype"  => $exectype, 
		"remark"    => $rs->fields['remark'],
		"debugmode" => $rs->fields['debugmode'],
		"enable"    => $rs->fields['enable']
    );
    // var_dump($rs->fields['debugmode']);
	// echo "<center><font class='undertitle'>"._UPLOADSTOREIMG."</font></center>\n";
	$form = new HTML_QuickForm('frm','POST',$_SERVER['PHP_SELF']);
	$form->addElement ("header"  ,"myheader"  ,_LOGDEBUGEDIT);
	$form->addElement ('static'  ,'name'      ,_LOGENAME);
	$form->addElement ('static'  ,'exectype'  ,_LOGEXECTYPE);
	$form->addElement ('static'  ,'remark'    ,_LOGREMARK);
	$form->addElement ('select'  ,'debugmode' ,_LOGDEBUGMODE,$debugmode);
	$form->addElement ("radio"   ,"enable"    ,_LOGENABLE,_YES   ,'1');
	$form->addElement ("radio"   ,"enable"    ,null      ,_NO    ,'0');
	$form->addElement ("hidden"  ,"id"        ,$pkid);
	$form->addElement ("hidden"  ,"op"        ,"logadmin");
	$form->addElement ("hidden"  ,"op2"       ,"logdebugEdittoDB");
	$form->addElement ("submit"  ,"btnSubmit" ,_SAVE); 
	$form->setDefaults($defaultvalue); //預設值
	//必填規則
	$form->addRule("name"  ,_NOEMPTYNAME  ,"required","","client");  // 名稱不得為空
    $form->setDefaults($defaultvalue);
	$form->display();
	CloseTable();
	echo "<br>";
}
/*
 修改名稱至DB
*/
function logdebugEdittoDB(){
	//表單變數處理
	$id        = trim($_POST['id']);
	$debugmode = trim($_POST['debugmode']);
	$enable    = trim($_POST['enable']);

	//檢查變數是否都有值
	if(isset($id) && isset($debugmode) && isset($enable)){
		$sql = "UPDATE log_format SET debugmode=?,enable=?  WHERE id=?";
		$sql_params = array($debugmode,$enable,$id);
		$stmt = $GLOBALS['adoconnlog']->Prepare($sql);
		$GLOBALS['adoconnlog']->Execute($stmt,$sql_params);
		header("location:".$_SERVER['PHP_SELF']."?op=logadmin&op2=logdebug");
	}else{
		//變數無值錯誤，回上一頁
		include_once("header.php");
        logadminMenu();
        OpenTable();
        echo "<center>"._LOGERROR."<p>"; //回傳「其他錯誤」
        echo "<center>"._GOBACK."<p>";
        CloseTable();
        include_once("footer.php");
	}
}

if ($_REQUEST['op']=="logadmin" && isAuthority($_SESSION['aid'],$_REQUEST['op']))
{
	switch ($_REQUEST['op2'])
	{

		case "logdebug":
		logdebug();
		break; 

		case "logquery":
		logquery();
		break; 

		case "logdebugEdit":
		logdebugEdit();
		break;

		case "logdebugEdittoDB":
		logdebugEdittoDB();
		break;
	}
}


?>