<?php
/**
 * Company: 
 * Program: rule.php
 * Author:  Ken Tsai
 * Date:    2018.08.08
 * Version: 2.0
 * Description: 部門 會員條款和隱私權聲明 設定
 */

Header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
//if (!eregi(SERVICEPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if (!preg_match('/'.SERVICEPAGE.'/', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
include_once("lib/mypager.inc");
include_once("service/language/rule-".DEFAULTLANGUAGE.".php");

/*
*  MENU
*/
function menu()
{
    include_once "header.php";
    GraphicAdmin();

    OpenTable();
    echo "<center><b><h4>"._RULE."</h4></b></center>\n";
    echo "<a href='".$_SERVER['PHP_SELF']."?op=rule&op2=editrule'>"._SETRULE."</a>";
    CloseTable();
    echo "<br/>\n";

}

/*
*   主功能畫面
*/
function main() 
{
   menu(); 
   include("footer.php");
}

/*
*   會員條款和隱私權聲明
*/
function editrule()
{
   menu();  
   OpenTable();/*
   require_once "HTML/QuickForm.php";
   $sql="select * from ".ADOPREFIX."_member_rule_reference where store='".$_SESSION['USER_STOREID']."'";
   $rs = $GLOBALS['adoconn']->Execute($sql);
   
   $form = new HTML_QuickForm('frm','post',$_SERVER['PHP_SELF']);
   $form->addElement("radio","type",_IFCOMMONRULE,_YES,"1"); 
   $form->addElement("radio","type",null,_NO,"0");
   if($rs->fields['type']==1)
   {
     $constantvalue['type'] = "1";
     $sql="select * from ".ADOPREFIX."_member_rule where enable='1' order by id desc";
     $rs1 = $GLOBALS['adoconn']->Execute($sql);
     $form->addElement("static","version",_VERSION,$rs1->fields['version']);
     $form->addElement("static","rule",_RULECONTENT,$rs1->fields['rule']);
     $form->addElement("static","security",_SECURITYCONTENT,$rs1->fields['security']);   
   }
   else
   {
     $constantvalue['type'] = "0";
     $attrtextarea = array( "rows"=>"10","cols"=>"50");
     $form->addElement("textarea","rule",_RULECONTENT,$attrtextarea);
     $form->addElement("textarea","security",_SECURITYCONTENT,$attrtextarea);
     $defaultvalue= array("rule"=>$rs->fields['rule'],"security"=>$rs->fields['security']);
   }
   $form->addElement("hidden","op","rule");
   $form->addElement("hidden","op2","saverule");
   $form->setDefaults($defaultvalue);
   $form->setConstants($constantvalue); //radio預設值需要使用
   $form->addElement("submit","btnSubmit",_OK);
   $form->display();*/
   CloseTable();
   echo "<br/>\n";
   include("footer.php");
}

/*
*   會員條款和隱私權聲明 更新
*/
function saverule()
{
  if($_POST['type']=="1")
    $sql="update ".ADOPREFIX."_member_rule_reference set type='".$_POST['type']."' where storeadmin='".$_SESSION['USER_STOREID']."'";
  else
    $sql="update ".ADOPREFIX."_member_rule_reference set type='".$_POST['type']."', rule='".$_POST['rule']."', sceurity='".$_POST['sceurity']." where storeadmin='".$_SESSION['USER_STOREID']."'";
 
  $GLOBALS['adoconn']->Execute($sql);
  //echo $GLOBALS['adoconn']->errorMsg();
  //echo $sql;
  header("location:".$_SERVER['PHP_SELF']."?op=rule");
}

$op="rule";
if ($_REQUEST['op']==$op)
{
  include_opdir($op);
	switch ($_REQUEST['op2'])
	{
		default:
    case "main":
		     main();
		     break;

   case "editrule":
         editrule();
         break;

   case "saverule":
         saverule();
         break;
	}
}

?>
