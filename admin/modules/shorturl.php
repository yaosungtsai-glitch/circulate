<?php
/**
 * Company: 
 * Program: shorturl.php
 * Author:  Ann Chen
 * Date:    2017.12.20
 * Version: 2.0
 * Description: 短網址設定主程式
 * Log:		2019.03.11 寫入資料庫改為不呼叫sinyiapi/getShorturl.php
 */

Header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
//if (!eregi(ADMINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if (!preg_match('/'.ADMINPAGE.'/', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
include_once("admin/language/shorturl-".DEFAULTLANGUAGE.".php");

/*
系統管理menu
*/
function shorturlmenu()
{
	include_once("header.php");
	GraphicAdmin();
	OpenTable();
	echo "<center class='toptitle'>"._SHORTURL."</center>\n";
	echo "<br/>";
	//短網址設定
	echo "<a href='".$_SERVER['PHP_SELF']."?op=shorturl&op2=shorturl&sel=function'>"._SHORTURL."</a>&nbsp&nbsp";
	CloseTable();
}

/*
短網址設定 新增畫面
*/
function shorturl() 
{
	shorturlmenu();	
	echo "<br>";
	//新增短網址設定
	OpenTable();
	//include_once "includes/htmlarea.js";
	//require_once "HTML/QuickForm.php";
	echo "<center><font class='undertitle'>"._SETSHORTURL."</font></center>\n";
	echo "<table>\n";
	echo "<form active='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<input type='hidden' name='op' value='shorturl'>\n";
	echo "<input type='hidden' name='op2' value='shorturltoDB'>\n";
	echo "<tr><td>"._VDNAME."</td><td><input type='text' name='vd_name' required='required'></td></tr>\n";
	echo "<tr><td>"._ORIGINURL."</td><td><input type='url' name='originurl' required='required'></td></tr>\n";
	echo "<tr><td></td><td><input type='submit' value='"._OK."'></td></tr>\n";
    echo "</form>\n";
    echo "</table>\n";
	/*
	$form = new HTML_QuickForm('frm','POST',$_SERVER['PHP_SELF']);
	$form->addElement ("header"  ,"myheader"  ,_SETSHORTURL.SHOURURL);
	$form->addElement ("header"  ,"myheader"  ,_SETSHORTURL);
	$form->addElement ('text'    ,'originurl' ,_ORIGINURL   ,"id ='t',style = 'width:500px' ");
	$form->addElement ('static'  ,'mystatic'  ,_VDNAMETYPE);
	$form->addElement ('radio'   ,'type'      ,_RANDOMNUMBER  ,null   ,0);
	$form->addElement ('radio'   ,'type'      ,_RANDOMLETTER  ,null   ,1);
	$form->addElement ('radio'   ,'type'      ,_DESIGNATE     ,_DESIGNATEDETAIL  ,2);
	$form->addElement ('text'    ,'designate' ,null   );
	$form->addElement ("hidden"  ,"op"        ,"shorturl");
	$form->addElement ("hidden"  ,"op2"       ,"shorturltoDB");
	$form->addRule    ("originurl" ,_RLUEURL  ,"required"     ,null ,"client");
	$form->addRule    ("designate" ,_RLUETYPE ,"alphanumeric" ,null ,"client");
	$form->addElement ("submit"  ,"btnSubmit" ,_SEND); 
	 $defaultvalue = array
    (
        'type'   => '2'
    );
    $form->setDefaults($defaultvalue);
	$form->display();
	*/
	CloseTable();
	echo "<br>";

  	//短網址列表	
	OpenTable();
	// include_once("lib/mypager.inc");
	include_once("lib/dbpager.inc.php");
	echo "<center><font class='undertitle'>"._SHORTURLLIST._VDNAMEDISPLAY."</font></center>\n";
	$sql="SELECT id,vd_name,urlstr,startime from ".ADOPREFIX."_shorturl where enable = 1 order by id DESC";
    $colnames= array(_ID,_VDNAME,_ORIGINURL,_STARTTIME,_FUNCTIONS);
	$links[0]['link']  ="op=shorturl&op2=shorturltoGO";
	$links[0]['label'] =_SHORTURLGO;    
	$links[1]['link']  ="op=shorturl&op2=shorturldelete";
	$links[1]['label'] =_DELETE;   
    $rows=dbpage($GLOBALS['adoconn'],$sql,$colnames,$links);
	CloseTable();
	echo "<br>";
	include_once("footer.php");
}
/*
短網址設定 新增資料至資料庫
*/
function shorturltoDB()
{
	$originurl =  $_REQUEST['originurl'];
	$vd_name 	   =  $_REQUEST['vd_name'];
	//$designate =  trim($_REQUEST['designate']);
	
	/*
	if(!isset($originurl)){
		echo '
				<script> 
					alert("'._RLUEURL.'");
					location.href = history.go(-1);
				</script>
			 ';
	}else{
		//產生目錄名稱
		if(!empty($designate)) {
		  	$vd_name = $designate;
		  	if(!check_vd_name($vd_name))
		  		return "error";
		} else {
		  	$vd_name=type($type);
		  	while(!check_vd_name($vd_name))
		  	{
		    	$vd_name=type($type);
		  	}
		}
        */
		//寫入資料庫
		if(isset($_REQUEST['startime']) && isset($_REQUEST['endtime']) ) {
		    $sql="insert into ".ADOPREFIX."_shorturl(baseurl,vd_name,urlstr,enable,startime,endtime) "
		        ." values('1',$vd_name','$originurl','1','".$_REQUEST['startime']."','".$_REQUEST['endtime']."')";
		} elseif(isset($_REQUEST['startime']))
		    $sql="insert into ".ADOPREFIX."_shorturl(baseurl,vd_name,urlstr,enable,startime,endtime) values('1','$vd_name','$originurl','1','".$_REQUEST['startime']
		    	."','2999-12-31 00:00:00')";
		else
			$sql="insert into ".ADOPREFIX."_shorturl(baseurl,vd_name,urlstr,enable,startime,endtime) "
		        ." values('1','$vd_name','$originurl','1','".date("Y-m-d H:i:s")."','2999-12-31 00:00:00')";
		        
		$rs = $GLOBALS['adoconn']->Execute($sql);
		header("Location:".$_SERVER['PHP_SELF']."?op=shorturl&op2=shorturl");
		/*
		if($rs){
			//轉址
			wredirect();
			result(SHOURURL.$vd_name);
		}else{
			echo '
				<script> 
					alert("'._SHORTURFAIL.'");
					location.href = history.go(-1);
				</script>
			 ';
		}*/
	//}


}
/**
 * func:    type
 * Author:  Ann Chen
 * Description: 取得目錄名稱
 * Parameter:   @IN     @type:類型 0隨機數字目錄 1隨機英文目錄
 * Return:      @OUT    目錄名稱字串
 */
function type($type)
{
  	if(isset($type))
     	$vd_name=randnumsix($type);
  	elseif(($type)==1 || ($type)==0)
     	$vd_name=randnumsix($type);
  	else
     	$vd_name=randnumsix();

  	return strtolower($vd_name);
}
/**
 * func:    randnumsix
 * Author:  Ann Chen
 * Description: 取得隨機數字、英文
 * Parameter:   @IN     @type:類型 0數字 1英文
 * Return:      @OUT    隨機字串
 */
function randnumsix($type=1)
{

 	if($type!=1 && $type!=0)
   		$type=mt_rand(0,99)%2;

 	switch($type)
 	{
	  	case 0:
	  	default:
	  		return sprintf("%06d",mt_rand(1,999999));
	  	break;

	  	case 1:
		  	for($i=0,$str="";$i<6;$i++)
		  	{
		     	$num=mt_rand(65,90);
		     	$str.=chr($num);
		  	}
	  		return $str;
	  	break;

	}
}
/**
 * func:    check_vd_name
 * Author:  Ann Chen
 * Description: 檢查目錄名稱
 * Parameter:   @IN     @vd_name:目錄名稱
 * Return:      @OUT    隨機字串
 */
function check_vd_name($vd_name)
{
  
 	if(trim($vd_name)!="")
 	{	
    	$sql="SELECT count(*) as count FROM ".ADOPREFIX."_shorturl where vd_name='$vd_name'";
    	//echo $sql;
    	$rs = $GLOBALS['adoconn']->Execute($sql);
    	if($rs->fields['count']>0)
    	{
      		//虛擬目錄重複;
      		echo '
				<script> 
					alert("'._SHORTURLNAMEREPEAT.'");
					location.href = history.go(-1);
				</script>
			 ';
      		exit();
    	}  
    	else
    	{
  	  		return true;
    	}
  	}
   	else
   		return false;
} 

/*
列表功能 前往
*/
function shorturltoGO()
{
	$id  = $_GET['pkid'];
	$sql = "select vd_name from ".ADOPREFIX."_shorturl where id =".$id." ";
	$rs  = $GLOBALS['adoconn']->Execute($sql);
	$url = SHOURURL.$rs->fields['vd_name'];
	$locationurl = $_SERVER['PHP_SELF']."?op=shorturl&op2=shorturl";
	echo "<script language='JavaScript' type='text/javascript'>";
	echo "window.location.href='$locationurl';";
	echo " window.open('".SHOURURL.$rs->fields['vd_name']."','_blank');";
	echo "</script>"; 

}
/*
列表功能 刪除
*/
function shorturldelete()
{
	$id  = $_GET['pkid'];
	$sql = "delete from ".ADOPREFIX."_shorturl where id ='$id'";
	$rs  = $GLOBALS['adoconn']->Execute($sql);
	//轉址
	//wredirect();
	header("location:".$_SERVER['PHP_SELF']."?op=shorturl&op2=shorturl");
}
/*
設定結果
*/
function result($result)
{
	include_once("header.php");
	shorturlmenu();
	echo "<br>";
	OpenTable();
	echo "<center class='toptitle'>"._SHORTURLRESULT."</center>\n";
	echo "<center>".$result."<center><a href='".$result."' target='_blank'>"._SHORTURLGO."</a>";
	CloseTable();
	include_once("footer.php");
}
/*
shorturl 啟動轉址
*/
function wredirect()
{
	//init curl
	$ch = curl_init();
	//curl_setopt可以設定curl參數
	//設定url
	curl_setopt($ch , CURLOPT_URL , SHOURURL."wredirect.php");
	//執行，並將結果存回
	$result = curl_exec($ch);
	//關閉連線
	curl_close($ch);

}


if ($_REQUEST['op']=="shorturl" && isAuthority($_SESSION['aid'],$_REQUEST['op']))
{
	switch ($_REQUEST['op2'])
	{

		default:
		case "admin":
		shorturlmenu();
		break; 

		case "shorturl":
		shorturl();
		break; 

		case "shorturltoDB":
		shorturltoDB();
		break;

		case "shorturltoGO":
		shorturltoGO();
		break;

		case "shorturldelete":
		shorturldelete();
		break;

	}
}
?>