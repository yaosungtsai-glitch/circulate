<?php
/**
 * Company: 
 * Program: user.php
 * Author:  Ken Tsai
 * Date:    2013.11.22
 * Version: 2.0
 * Description: 會員主程式
 */

Header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
//if (!eregi(ADMINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if (!preg_match('/'.ADMINPAGE.'/', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
include_once("admin/language/user-".DEFAULTLANGUAGE.".php");


/**
 * 公司介紹管理功能主畫面
 */
function usermenu()
{
	include_once("header.php");
	GraphicAdmin();
	OpenTable();
	echo "<center class='toptitle'>"._USERADMIN."</center>";
	CloseTable();
	echo "<br>";
	OpenTable();
	echo "<center><a href='".$_SERVER['PHP_SELF']."?op=user&op2=userlist'>"._USERLIST."</a>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='".$_SERVER['PHP_SELF']."?op=user&op2=userattrilist'>"._USERATTRIBUTE."</a>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='".$_SERVER['PHP_SELF']."?op=user&op2=userinsert'>"._USERADD."</a></center>";
	CloseTable();
	echo "<br>";
}

function userlist()
{
	usermenu();	
	users_search2();
	OpenTable();
	
	//查詢request====================================
	$q_sex = '';
	$q_enable = '';
	$q_name = '';
	$q_nickname = '';
	$q_email = '';
	$queryString = '  ';
	$strsearTF=0;//是否有進階查詢
	if ($_REQUEST['sex'] != ''  ){ 
	  $q_sex    = $_REQUEST['sex'];
	  $queryString .= " and sex = '".$q_sex."'  ";	  
	}
	if ($_REQUEST['name'] != '' && $_REQUEST['name'] != _USERADMINQUERYNAME ) {
      $q_name   = $_REQUEST['name'];
	  $queryString .= " and username LIKE '%".$q_name."%' ";	  
	}	
	if ($_REQUEST['email'] != '' && $_REQUEST['email'] != _USERADMINQUERYEMAIL) {
  	  $q_email    = $_REQUEST['email'];
	  $queryString .= " and email LIKE '%".$q_email."%' ";	  
	}
	$searchstr='sex='.$q_sex.'&enable='.$q_enable.'&name='.$q_name.'&email='.$q_email;	 
	
	
	//2012-12-12 add by amy 處理會員動態屬性查詢-------------------
	if($_REQUEST['search2']=='search2'){
		$sqlattribute="select aid,distype,necessary from ".ADOPREFIX."_user_attri where enable='1' order by sort asc";
		$rsa_list=$GLOBALS['adoconn']->Execute($sqlattribute);	
		$searchstr_1="&search2=search2";
		while(!$rsa_list->EOF){
			$ErrorData = 0;//判斷必填欄位未填寫時>$ErrorData =1
			$necessary=$rsa_list->fields[2];//0一般填寫欄 1必填欄位
			switch($rsa_list->fields[1]){			
				case "1"://下拉式選單				
				case "2"://radio
				case "4"://text							
				case "5"://textarea
					if($_REQUEST['aid'][$rsa_list->fields[0]] !=""){
						$strsearTF=1;
						$sql= "select uid from ".ADOPREFIX."_user_attri_record
									where aid='".$rsa_list->fields[0]."' and value like '%".$_REQUEST['aid'][$rsa_list->fields[0]]."%'";								
						$rs_temp=$GLOBALS['adoconn']->Execute($sql);
						while(!$rs_temp->EOF){
							$str_TempUid.="'".$rs_temp->fields[0]."',";
							$rs_temp->MoveNext();							
						}						
						$searchstr_1.="&aid[".$rsa_list->fields[0]."]=".$_REQUEST['aid'][$rsa_list->fields[0]];
					}
				break;
				
				case "3": //checkbox
					$itemsarray=itemsarray($rsa_list->fields[0]);
					foreach ($itemsarray as $id => $itemname ){
						if( $_REQUEST['attritem'][$rsa_list->fields[0]][$id]==$id){	
							$strsearTF=1;
							$sql2="select uid from ".ADOPREFIX."_user_attri_record where aid='".$rsa_list->fields[0]."' and CONVERT(varchar(MAX), value)='".$id."'";
							$rs = $GLOBALS['adoconn']->Execute($sql2);
							while(!$rs->EOF){
								$str_TempUid.="'".$rs->fields[0]."',";
								$rs->MoveNext();
							}							
							$searchstr_1.="&attritem[".$rsa_list->fields[0]."][".$id."]=".$id;
						}						
					}					
				break;
			}
			$rsa_list->MoveNext();			
		}
		$str_TempUid = substr($str_TempUid,0,strlen($str_TempUid)-1);//取$str左邊$n個字符		
		if($strsearTF==1){
			if(!empty($str_TempUid))
				$queryString_1= " and id IN(".$str_TempUid.") ";
			else							
				$queryString_1= " and id IN('') ";
		}
	}
	//end 2012-12-12 add by amy 處理會員動態屬性查詢-------------------
	//end 查詢request================================
	
	
	
	include_once("lib/mypager.inc");
	
	//啟動---------------------
	echo "<center><font class='undertitle'>"._USERLIST."-"._ENABLE."</font></center>\n";
	/*echo "<form name='fm_search' id='fm_search' action='".$_SERVER['PHP_SELF']."' method='POST'>";
	echo "<input type='hidden' id='op' name='op' value='user'>"
		   ."<input type='hidden' id='op2' name='op2' value='downloaddata'>"
		   ."<input type='hidden' id='op3' name='op3' value='1'>"
		   ."<input type='hidden' id='PHPSESSID' name='PHPSESSID' value='".session_id()."'>
		   <input type='submit' name='BT' value='"._BTDOWNLOAD."'></center>\n";
	echo "</form>";	*/
	
	$sql = "select id,username,useremail,birthday,address 
	from ".ADOPREFIX."_user where enable='1' ".$queryString.$queryString_1." order by id asc";
	
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'userlist',true);
	$GridHeader = array(_ID,_USERNAME,_USEREMAIL,_USERBIRTHDAY,_USERADDRESS);
	$pager->setRenderGridLayout("width='100%' align='center'",$GridHeader);
	$funcNames = array(_EDIT);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=user&op2=useredit&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$pager->setOp("user&op2=userlist&$searchstr$searchstr_1&PHPSESSID=".session_id());
	$pager->Render_Function($GLOBALS['perpage']);
	CloseTable();
	echo "<br>";
	
	
	//未認證---------------------
	OpenTable();
	echo "<center><font class='undertitle'>"._USERLIST."-"._USERNOCOFIRM."</font></center>\n";	
	
	$sql = "select id,username,useremail,birthday,address 
	from ".ADOPREFIX."_user where enable='-1' ".$queryString.$queryString_1." order by id asc";
	
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'userlist',true);
	$GridHeader = array(_ID,_USERNAME,_USEREMAIL,_USERBIRTHDAY,_USERADDRESS);	
	$pager->setRenderGridLayout("width='100%' align='center'",$GridHeader);
	$funcNames = array(_EDIT);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=user&op2=useredit&$searchstr$searchstr_1&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$pager->setOp("user&op2=userlist&PHPSESSID=".session_id());
	$pager->Render_Function($GLOBALS['perpage']);
	CloseTable();
	echo "<br>";	
	
	
	//取消---------------------
	OpenTable();
	echo "<center><font class='undertitle'>"._USERLIST."-"._DISABLE."</font></center>\n";	
	
	$sql = "select id,username,useremail,birthday,address 
	from ".ADOPREFIX."_user where enable='0' ".$queryString.$queryString_1." order by id asc";
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'userlist',true);
	$GridHeader = array(_ID,_USERNAME,_USEREMAIL,_USERBIRTHDAY,_USERADDRESS);
	$pager->setRenderGridLayout("width='100%' align='center'",$GridHeader);
	$funcNames = array(_EDIT);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=user&op2=useredit&$searchstr$searchstr_1&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$pager->setOp("user&op2=userlist&PHPSESSID=".session_id());
	$pager->Render_Function($GLOBALS['perpage']);
	CloseTable();
	echo "<br>";
	include_once("footer.php");
}

//查詢功能
function users_search2()
{
	include_once("admin/includes/user-js.php");
	$sel=$_REQUEST['sel'];
	$op2=$_REQUEST['op2'];	
	echo "<form name='frmselect2' id='frmselect2' action='".$_SERVER['PHP_SELF']."' method='GET'>";
	echo   "<input type='hidden' id='op' name='op' value='user'>".
		   "<input type='hidden' id='op2' name='op2' value=$op2>".
		   "<input type='hidden' id='op3' name='op3' value=$op2>".
		   "<input type='hidden' id='search2' name='search2' value='search2'>".
		   "<input type='hidden' id='admin_next_page' name='admin_next_page' value=1>".
		   "<input type='hidden' id='sel' name='sel' value='".$sel."'>".
		   "<input type='hidden' id='PHPSESSID' name='PHPSESSID' value='".session_id()."'>";
	
	echo "<table width=95%  align='center' cellspacing=0 cellpadding=0   bgcolor='#EEF8FF' border='1'  bordercolor='#9C9A9C' style='border-style:solid;border-color:#9C9A9C'>";
	echo "<tr><td align=center >";
	
	//start 一般查詢============================
	echo "<table width=40%  align='center' cellspacing=0 cellpadding=0>";
	////		<td align=center><b>"._USERADMINNICKNAME."</b></td>
	echo "<tr><td align=center><b>"._USERADMINSEX."</b></td>
			<td align=center><b>"._USERADMINNAME."</b></td>	
			<td align=center><b>"._USERADMINEMAIL."</b></td></tr>";			
	echo "		<td align=center>";	
	echo "			<select id='sex' name='sex'>\n".
		 "				<option value=''>"._USERALL."</option>";		
	switch (trim($_GET['sex'])){
		case "1":
			$sel_m = "selected";
			$sel_f = "";
			break;
		case "0":
			$sel_m = "";
			$sel_f = "selected";
			break;
	}
	echo "			<option value='1' ".$sel_m.">"._UERADMINMAN."</option>";
	echo "			<option value='0' ".$sel_f.">"._UERADMINWOMAN."</option>";
	echo "			</select>";	
	echo "		</td>";
	//name:部份比對
 	$value=trim($_GET['name']);						
	if(empty($value))
		$value=_USERADMINQUERYNAME;	
	echo "		<td align=center>".
   		 "			<input type='text' id='name' name='name' value='".$value."' size='20' maxlength='20' ".
		 "				style='width:126px; border: 1px solid #ccc; padding:2px;' onFocus='input_text1(this.id);' >\n";	
	echo "		</td>";	
	//暱稱:部份比對
 	/*$value=trim($_GET['nickname']);
	if(empty($value))
		$value=_USERADMINQUERYNICKNAME;	
	echo "		<td align=center>".
   		 "			<input type='text' id='nickname' name='nickname' value='".$value."' size='20' maxlength='20' ".
		   "				style='width:126px; border: 1px solid #ccc; padding:2px;' onFocus='input_text1(this.id);' >\n";	
	echo "		</td>";	*/		
	
	//E-mail:部份比對
 	$value=trim($_GET['email']);
	if(empty($value))
		$value=_USERADMINQUERYEMAIL;	
	echo "		<td align=center>".  
   		 "			<input type='text' id='email' name='email' value='".$value."' size='18' maxlength='50' ".
		 "			   style='width:179px; border: 1px solid #ccc; padding:2px;' onFocus='input_text1(this.id);' >\n";	
	echo "		</td></tr></table>";
	//end 一般查詢============================
	
	
	
	//start 動態屬性查詢======================
	echo "<table width=99%  align='center' cellspacing=0 cellpadding=0 border='1'  bordercolor='#9C9A9C' style='border-style:solid;border-color:#9C9A9C' bgcolor='#FCFCEE'>";
	echo "<tr><td align=center colspan=2><b>"._USERATTRIBUTE._QUERY."</b></td></tr>";
	//取得設定的動態屬性	
	$rsa=$GLOBALS['adoconn']->Execute("select aid,attriname,distype,necessary from ".ADOPREFIX."_user_attri where enable='1' order by sort asc");
	while(!$rsa->EOF){		
		$aid = $rsa->fields[0];
		$necessary="";
		$STRDynamic="";		
		$trtd="<tr><td align='right' width=200>".$rsa->fields[1]."</td><td align=left width=800>";
		//動態屬性所對應的值
		
		$rsusers_attri_items=$GLOBALS['adoconn']->Execute("select itemid,name from ".ADOPREFIX."_user_attri_items where aid='$aid' AND enable='1' order by sort asc");
		switch($rsa->fields[2]){
			case "1"://下拉式選單
				$selectoption1="";
				$selectoption1=$trtd."<select name='aid[".$aid."]'><option value=''>"._USERALL."</option>";
				while(!$rsusers_attri_items->EOF){
					if($rsusers_attri_items->fields[0] == $_REQUEST['aid'][$rsa->fields[0]])
						$selected = ' selected ';
					else
						$selected = ' ';
					$selectoption1 .= "<option value='".$rsusers_attri_items->fields[0]."' ".$selected.">".$rsusers_attri_items->fields[1]."</option>";
					$rsusers_attri_items->MoveNext();
				}
				$selectoption1 .="</select></td></tr>";
				echo $selectoption1;				
				break;
			case "2"://radio
				$radiooption2="";
				$radiooption2=$trtd;
				if(empty($_REQUEST['aid'][$rsa->fields[0]]))
					$checked12 = ' checked ';
				$radiooption2 .= "<input type=radio name='aid[".$rsa->fields[0]."]' id='aid[".$rsa->fields[0]."]' value='' ".$checked12.">"._USERALL."";
				while(!$rsusers_attri_items->EOF){
					if($rsusers_attri_items->fields[0] == $_REQUEST['aid'][$rsa->fields[0]])
						$checked = ' checked ';
					else
						$checked = ' ';
					$radiooption2 .= "<input type=radio name='aid[".$rsa->fields[0]."]' id='aid[".$rsa->fields[0]."]' value='".$rsusers_attri_items->fields[0]."' ".$checked.">".$rsusers_attri_items->fields[1]."";
					$rsusers_attri_items->MoveNext();
				}
				$radiooption2 .="</td></tr>";
				echo $radiooption2;				
			break;
			
			case "3": //checkbox
				$checkboxoption3="";
				$STRDynamic_item="";
				$checkboxoption3=$trtd;
				$checkboxcount=0;				
				$itemsarray=itemsarray($rsa->fields[0]);
				foreach ($itemsarray as $id => $itemname ){
					if( $_REQUEST['attritem'][$rsa->fields[0]][$id]==$id)
						$checked1 = ' checked ';
					else
						$checked1 = '';
					
					$checkboxoption3 .= "<input type='checkbox' name='attritem[".$aid."][".(string)$id."]'   value='".$id."' ".$checked1.">".$itemname."";
				}				
				$checkboxoption3 .="</td></tr>";
				echo $checkboxoption3;
			break;			
			case "4"://text
				$textoption4=$trtd;
				$textoption4 .= "<input type='text' name='aid[".$aid."]' id='aid[".$aid."]' value='".$_REQUEST['aid'][$rsa->fields[0]]."' size=82 >";
				$textoption4 .="</td></tr>";
				echo $textoption4;				
			break;			
			case "5"://textarea
				$textareaoption5=$trtd;
				$textareaoption5 .= "<textarea cols='86' rows='3' name='aid[".$aid."]' id='aid[".$aid."]'>".$_REQUEST['aid'][$rsa->fields[0]]."</textarea>";
				$textareaoption5 .="</td></tr>";
				echo $textareaoption5;				
			break;
		}	   	 
		$rsa->MoveNext();
	}
	echo "<input type='hidden' name='STRDynamic' id='STRDynamic' value='".$STRDynamic."'>";
	echo "</table>";
	
	echo "</td></tr>";
	echo "<tr><td align=center ><input type='button' name'BT2' value='"._QUERY."' onclick='FUNsearchData(this.form)'>
	<input type='button' name='BT' value='"._BTDOWNLOAD."' onclick='FUNdownloadData(this.form)'>
	</td></tr>";
	echo "</table>";
	
	//end 動態屬性查詢======================
	
    echo "</form><br>";
}


//新增會員=================================
function userinsert()
{
	usermenu();
	//include_once("includes/htmlarea.js");
  OpenTable();
  require_once "HTML/QuickForm.php";
  require_once "HTTP/Upload.php";
  $form = new HTML_QuickForm('userfrm','post',$_SERVER['PHP_SELF']);
  $form->addElement("header","myheader",_USERADD);
  $form->addElement("text","name",_USERNAME);
  $form->addElement("file","image",_USERIMAGE);
  $datearr=array('language'=>'tw',
                 'format'=>'Y-m-d',
                 'minYear'=>"1",
                 'maxYear'=>date("Y")
           );
  $form->addElement("date", "birthday", _USERBIRTHDAY, $datearr);  
  $form->addElement("radio","sex",_USERSEX,_USERMAN,"1"); 
  $form->addElement("radio","sex",null,_USERWOMAN,"0");   
  $form->addElement("text","idno",_USERIDNO);
  $form->addElement("text","email",_USEREMAIL);
  $form->addElement("text","h_phone",_USERHPHONE);
  $form->addElement("text","o_phone",_USEROPHONE);
  $form->addElement("text","c_phone",_USERCPHONE);
  $form->addElement("text","fax",_USERFAX);
  $form->addElement('textarea',"address",_USERADDRESS);
  //$form->addElement('textarea','intro',_USERINTRO,"id='ta',style='width:100%' rows='10' cols='80' ");
  $form->addElement('textarea',"intro",_USERINTRO);
  $form->addElement("radio","enable",_ENABLE,_YES,"1"); 
  $form->addElement("radio","enable",null,_USERNOCOFIRM,"-1");
  $form->addElement("radio","enable",null,_NO,"0");
  $defaultvalue=array('birthday' =>array("Y"=>(string)((int)date("Y")-30),"m"=>'1',"d"=>'1'),
                      'sex'=>'1');  
  $form->setDefaults($defaultvalue);
  $constantvalue['enable'] = "1";
  $form->setConstants($constantvalue); 
  $form->addRule("name",_USERNAMEERR,"required",null,"client");
  $form->addRule("email",_USEREMAILERR,"required",null,"client");
  $form->addRule("email",_USEREMAILFORMAERRT,"email",null,"client");
  $form->addRule("c_phone",_USERCPHONEERR,"required",null,"client");
  $form->addRule("c_phone",_USERCPHONEFORMATERR,"numeric",null,"client");
  $form->addRule("address",_USERADDRESSERR,"required",null,"client");
	$form->addElement("hidden","op","user");
	$form->addElement("hidden","op2","useradd");
  
  //動態屬性
  $sqlattribute="select * from ".ADOPREFIX."_user_attri where enable='1' order by sort asc";
  $rsa=$GLOBALS['adoconn']->Execute($sqlattribute);
  while(!$rsa->EOF)
  {
  	 switch($rsa->fields['distype']) 
  	 { 	 
  	   case "1":
       $itemsarray=itemsarray($rsa->fields['aid']);
  	   $form->addElement('select', "aid[".$rsa->fields['aid']."]", $rsa->fields['attriname'], $itemsarray);
  	   break;
  	 
  	   case "2":
  	   $itemsarray=itemsarray($rsa->fields['aid']);
  	   $display_attriname=0;
  	   foreach ($itemsarray as $itemname => $id )  
  	   {
  	   	 if($display_attriname==0)
  	   	 {
  	   	 	 $display_attriname=1;
  	   	   $form->addElement("radio","aid[".$rsa->fields['aid']."]",$rsa->fields['attriname'],$id,$itemname); 
  	   	 }
  	   	 else
  	   	   $form->addElement("radio","aid[".$rsa->fields['aid']."]",null,$id,$itemname); 
       }
  	   break;

  	   case "3": 
  	   $itemsarray=itemsarray($rsa->fields['aid']);
  	   $display_attriname=0;
  	   foreach ($itemsarray as $id => $itemname ) 
  	   {
  	   	 //echo "Key: $id; Value: $itemname<br />\n";
  	   	 if($display_attriname==0)
  	   	 {
  	   	 	 $display_attriname=1;
  	   	   $form->addElement("checkbox","aid[".$rsa->fields['aid']."][".(string)$id."]",$rsa->fields['attriname'],$itemname,"1"); 
  	   	 }
  	   	 else 
  	   	   $form->addElement("checkbox","aid[".$rsa->fields['aid']."][".(string)$id."]",null,$itemname,"1"); 
  	   }
  	   break;

  	   case "4":
  	   $form->addElement("text","aid[".$rsa->fields['aid']."]",$rsa->fields['attriname']);
  	   break;

  	   case "5":
 	     $form->addElement("textarea","aid[".$rsa->fields['aid']."]",$rsa->fields['attriname']);	   
  	   break;
     }
     if($rsa->fields['necessary']==1)
        $form->addRule("aid[".$rsa->fields['aid']."]",$rsa->fields['attriname']._USERNEEDINPUT,"required",null,"client");  	 
  	 $rsa->MoveNext();
  }
  $form->addElement("submit","btnSubmit",_OK);
  $form->display();
  CloseTable(); 
  echo "<br>";  
  include_once("footer.php");
}

//新增會員至DB =================================
function useradd()
{
  //處理會員圖檔
  if($_FILES["image"]["error"]>0)
	   $image="";
	else
	{
		 $id=time();
	   $image=$id.".".substr($_FILES["image"]["name"],-3);
	   if(!move_uploaded_file($_FILES["image"]["tmp_name"] , "images/user/$image"))
	   	 $image="";   
	}
	//基本資料
	$sql="insert into ".ADOPREFIX."_user(username,image,useridno,sex,useremail,birthday,address,homephone,officephone,cellphone,fax,intro,enable) 
	      values('".$_POST['name']."','$image','".$_POST['idno']."','".$_POST['sex']."','".$_POST['email']."','".$_POST['birthday']['Y']."-".$_POST['birthday']['m']."-".$_POST['birthday']['d']."','".$_POST['address']."','".$_POST['h_phone']."','".$_POST['o_phone']."','".$_POST['c_phone']."','".$_POST['fax']."','".$_POST['intro']."','".$_POST['enable']."')";
  $GLOBALS['adoconn']->Execute($sql);	
  //echo $sql;      
	$uid= $GLOBALS['adoconn']->Insert_ID();
	
	//處理會員動態屬性
  $sqlattribute="select * from ".ADOPREFIX."_user_attri where enable='1' order by sort asc";
  $rsa=$GLOBALS['adoconn']->Execute($sqlattribute);
  while(!$rsa->EOF)
  {
  	 switch($rsa->fields['distype']) 
  	 { 	 
  	   case "1":
  	   $sqlitem= "insert into ".ADOPREFIX."_user_attri_record(aid,uid,value) values( '".$rsa->fields['aid']."','$uid' ,'".$_REQUEST['aid'][$rsa->fields['aid']]."')";
  	   $GLOBALS['adoconn']->Execute($sqlitem);    	    	   
  	   break;
  	 
  	   case "2":
  	   $sqlitem= "insert into ".ADOPREFIX."_user_attri_record(aid,uid,value) values( '".$rsa->fields['aid']."','$uid' ,'".$_REQUEST['aid'][$rsa->fields['aid']]."')";
  	   $GLOBALS['adoconn']->Execute($sqlitem);  
  	   //echo $sqlitem."<br>";   
  	   break;

  	   case "3": 
   	   $itemsarray=itemsarray($rsa->fields['aid']);
  	   foreach ($itemsarray as $id => $itemname ) 
       {
          if($_REQUEST['aid'][$rsa->fields['aid']][$id] =="1")
          { 
             $sqlitem= "insert into ".ADOPREFIX."_user_attri_record(aid,uid,value) values( '".$rsa->fields['aid']."','$uid' ,'$id')";
  	         $GLOBALS['adoconn']->Execute($sqlitem);  	         
  	      }
       }
  	   break;

  	   case "4":
  	   case "5":
  	   $sqlitem= "insert into ".ADOPREFIX."_user_attri_record(aid,uid,value) values( '".$rsa->fields['aid']."','$uid' ,'".$_REQUEST['aid'][$rsa->fields['aid']]."')";
  	   $GLOBALS['adoconn']->Execute($sqlitem);  	   
  	   break;
     }
  	 $rsa->MoveNext();
  }
  header("location:".$_SERVER['PHP_SELF']."?op=user&op2=userlist");
}

//會員資料修改=================================
function useredit()
{
	usermenu();
	$sql="select * from ".ADOPREFIX."_user where id='".$_GET['pkid']."'";
	$rs=$GLOBALS['adoconn']->Execute($sql);
  OpenTable();
  require_once "HTML/QuickForm.php";
  require_once "HTTP/Upload.php";
  $form = new HTML_QuickForm('userfrm','post',$_SERVER['PHP_SELF']);
  $form->addElement("header","myheader",_USERADD);
  $form->addElement("text","name",_USERNAME);
  $form->addElement("header","myheader",_USERADD);
  if(trim($rs->fields['image'])!="" &&  $rs->fields['image']!=null)
    $form->addElement("static","disimg","<img src='images/user/".$rs->fields['image']."'>");
  $form->addElement("file","image",_USERIMAGE);
  $datearr=array('language'=>'tw',
                 'format'=>'Y-m-d',
                 'minYear'=>"1",
                 'maxYear'=>date("Y"));
  $form->addElement("date", "birthday", _USERBIRTHDAY, $datearr);  
  $form->addElement("radio","sex",_USERSEX,_USERMAN,"1"); 
  $form->addElement("radio","sex",null,_USERWOMAN,"0");   
  $form->addElement("text","idno",_USERIDNO);
  $form->addElement("text","email",_USEREMAIL);
  $form->addElement("text","h_phone",_USERHPHONE);
  $form->addElement("text","o_phone",_USEROPHONE);
  $form->addElement("text","c_phone",_USERCPHONE);
  $form->addElement("text","fax",_USERFAX);
  $form->addElement('textarea',"address",_USERADDRESS);
  //$form->addElement('textarea','intro',_USERINTRO,"id='ta',style='width:100%' rows='10' cols='80' ");
  $form->addElement('textarea',"intro",_USERINTRO);
  $form->addElement("radio","enable",_ENABLE,_YES,"1"); 
  $form->addElement("radio","enable",null,_USERNOCOFIRM,"-1");
  $form->addElement("radio","enable",null,_NO,"0");
  $datearray=date_parse($rs->fields['birthday']);
  $defaultvalue=array('birthday' =>array("Y"=> $datearray['year'] ,"m"=> $datearray['month'] ,"d"=> $datearray['day'] ),
                      'sex'=>$rs->fields['sex'],
                      'name'=> $rs->fields['username'],
                      'idno'=> $rs->fields['useridno'],
                      'email'=> $rs->fields['useremail'],
                      'address'=> $rs->fields['address'],
                      'h_phone'=> $rs->fields['homephone'],
                      'o_phone'=> $rs->fields['officephone'],
                      'c_phone'=> $rs->fields['cellphone'],
                      'fax'=> $rs->fields['fax'],
                      'intro'=> $rs->fields['intro']
                      );  
  $constantvalue['enable'] = $rs->fields['enable'];
  $form->addRule("name",_USERNAMEERR,"required",null,"client");
  $form->addRule("email",_USEREMAILERR,"required",null,"client");
  $form->addRule("email",_USEREMAILFORMAERRT,"email",null,"client");
  $form->addRule("c_phone",_USERCPHONEERR,"required",null,"client");
  $form->addRule("c_phone",_USERCPHONEFORMATERR,"numeric",null,"client");
  $form->addRule("address",_USERADDRESSERR,"required",null,"client");
	$form->addElement("hidden","op","user");
	$form->addElement("hidden","op2","userupdate");
	$form->addElement("hidden","uid",$rs->fields['id']);
  $form->addElement("hidden","oldimage",$rs->fields['image']);
  //動態屬性
  $sqlattribute="select * from ".ADOPREFIX."_user_attri where enable='1' order by sort asc";
  $rsa=$GLOBALS['adoconn']->Execute($sqlattribute);
  while($rsa->RecordCount()>0 && !$rsa->EOF)
  {
  	 switch($rsa->fields['distype']) 
  	 { 	 
  	   case "1":
       $itemsarray=itemsarray($rsa->fields['aid']);
  	   $form->addElement('select', "aid[".$rsa->fields['aid']."]", $rsa->fields['attriname'], $itemsarray);
  	   $sqlselitem="select * from ".ADOPREFIX."_user_attri_record where aid='".$rsa->fields['aid']."' and uid='".$rs->fields['id']."'";
  	   $rscount=$GLOBALS['adoconn']->Execute($sqlselitem);
  	   if($rscount->RecordCount()>0 && !$rsconut->EOF)
   	      $defaultvalue['aid'][$rsa->fields['aid']]=$rscount->fields['value'];  	   
  	   break;
  	 
  	   case "2":
  	   $itemsarray=itemsarray($rsa->fields['aid']);
  	   $display_attriname=0;
  	   foreach ($itemsarray as $itemname => $id )  
  	   {
  	   	 if($display_attriname==0)
  	   	 {
  	   	 	 $display_attriname=1;
  	   	   $form->addElement("radio","aid[".$rsa->fields['aid']."]",$rsa->fields['attriname'],$id,$itemname); 
  	   	 }
  	   	 else
  	   	   $form->addElement("radio","aid[".$rsa->fields['aid']."]",null,$id,$itemname); 
       }
       $sqlselitem="select * from ".ADOPREFIX."_user_attri_record where aid='".$rsa->fields['aid']."' and uid='".$rs->fields['id']."'";
  	   $rscount=$GLOBALS['adoconn']->Execute($sqlselitem);
       if($rscount->RecordCount()>0 && !$rsconut->EOF)
   	      $constantvalue['aid'][$rsa->fields['aid']]=$rscount->fields['value'];
  	   break;

  	   case "3": 
  	   $itemsarray=itemsarray($rsa->fields['aid']);
  	   $display_attriname=0;	   
  	   foreach ($itemsarray as $id => $itemname ) 
  	   {
  	   	 //echo "Key: $id; Value: $itemname<br />\n";
  	     $sqlselitem="select * from ".ADOPREFIX."_user_attri_record where aid='".$rsa->fields['aid']."' and value='$id' and uid='".$rs->fields['id']."'";
   	     $rscount=$GLOBALS['adoconn']->Execute($sqlselitem);
  	     if($rscount->RecordCount()>0 && !$rsconut->EOF)
  	       	 $checked="1 checked";
  	     else
  	       	 $checked="1"; 
  	   	 if($display_attriname==0)
  	   	 {
  	   	 	$display_attriname=1;			  
  	   	    $form->addElement("checkbox","aid[".$rsa->fields['aid']."][".(string)$id."]",$rsa->fields['attriname'],$itemname,$checked); 
  	   	 }
  	   	 else {			
  	   	    $form->addElement("checkbox","aid[".$rsa->fields['aid']."][".(string)$id."]",null,$itemname,$checked); 
			}
  	   }
  	   break;

  	   case "4":
  	   $form->addElement("text","aid[".$rsa->fields['aid']."]",$rsa->fields['attriname']);
  	   $sqlselitem="select * from ".ADOPREFIX."_user_attri_record where aid='".$rsa->fields['aid']."' and uid='".$rs->fields['id']."'";
   	   $rscount=$GLOBALS['adoconn']->Execute($sqlselitem);
  	   if($rscount->RecordCount()>0 && !$rsconut->EOF)
  	      $defaultvalue['aid'][$rsa->fields['aid']]=$rscount->fields['value'];  	   
  	   break;

  	   case "5":
  	   $form->addElement("textarea","aid[".$rsa->fields['aid']."]",$rsa->fields['attriname']);	   
  	   $sqlselitem="select * from ".ADOPREFIX."_user_attri_record where aid='".$rsa->fields['aid']."' and uid='".$rs->fields['id']."'";
   	   $rscount=$GLOBALS['adoconn']->Execute($sqlselitem);
  	   if($rscount->RecordCount()>0 && !$rsconut->EOF)
  	      $defaultvalue['aid'][$rsa->fields['aid']]=$rscount->fields['value'];  	   
  	   break;
     }
     if($rsa->fields['necessary']==1)
        $form->addRule("aid[".$rsa->fields['aid']."]",$rsa->fields['attriname']._USERNEEDINPUT,"required",null,"client");  	 
  	 $rsa->MoveNext();
  }
  $form->addElement("submit","btnSubmit",_OK);
  $form->setDefaults($defaultvalue);
  $form->setConstants($constantvalue); 
  $form->display();
  //print_r($defaultvalue);
  //print_r($constantvalue);
  CloseTable(); 
  echo "<br>";  
  include_once("footer.php");		
}

//會員資料修改至DB=================================
function userupdate()
{
	$uid=$_POST['uid'];
  //處理會員圖檔
  if($_FILES["image"]["error"]>0)
	   $image=$_POST['oldimage']; 
	else
	{
		 $id=time();
	   $image=$id.".".substr($_FILES["image"]["name"],-3);
	   if(!move_uploaded_file($_FILES["image"]["tmp_name"] , "images/user/$image"))
	   	 $image=$_POST['oldimage'];   
	}
	//基本資料
	$sqlupdate="update ".ADOPREFIX."_user set username='".$_POST['name']."',image='$image',useridno='".$_POST['idno']."',sex='".$_POST['sex']."',useremail='".$_POST['email']."',birthday='".$_POST['birthday']['Y']."-".$_POST['birthday']['m']."-".$_POST['birthday']['d']."',address='".$_POST['address']."',homephone='".$_POST['h_phone']."',officephone='".$_POST['o_phone']."',cellphone='".$_POST['c_phone']."',fax='".$_POST['fax']."',intro='".$_POST['intro']."',enable='".$_POST['enable']."' where id='$uid'";      
  $GLOBALS['adoconn']->Execute($sqlupdate);	
	
	//處理會員動態屬性
  $sqlattribute="select * from ".ADOPREFIX."_user_attri where enable='1' order by sort asc";
  $rsa=$GLOBALS['adoconn']->Execute($sqlattribute);
  while(!$rsa->EOF)
  {
  	 switch($rsa->fields['distype']) 
  	 { 	 
  	   case "1":
  	   $sqldel="delete from ".ADOPREFIX."_user_attri_record where aid='".$rsa->fields['aid']."' and uid='$uid'";
  	   $GLOBALS['adoconn']->Execute($sqldel);   	   
  	   $sqlitem= "insert into ".ADOPREFIX."_user_attri_record(aid,uid,value) values( '".$rsa->fields['aid']."','$uid' ,'".$_POST['aid'][$rsa->fields['aid']]."')";
  	   $GLOBALS['adoconn']->Execute($sqlitem);    	    	   
  	   break;
  	 
  	   case "2":
  	   $sqldel="delete from ".ADOPREFIX."_user_attri_record where aid='".$rsa->fields['aid']."' and uid='$uid'";
  	   $GLOBALS['adoconn']->Execute($sqldel);     	   
  	   $sqlitem= "insert into ".ADOPREFIX."_user_attri_record(aid,uid,value) values( '".$rsa->fields['aid']."','$uid' ,'".$_POST['aid'][$rsa->fields['aid']]."')";
  	   $GLOBALS['adoconn']->Execute($sqlitem);  
  	   //echo $sqlitem."<br>";   
  	   break;

  	   case "3": 
  	   $sqldel="delete from ".ADOPREFIX."_user_attri_record where aid='".$rsa->fields['aid']."' and uid='$uid'";
  	   $GLOBALS['adoconn']->Execute($ssqldeleldel); 
  	   //echo $sqldel;
   	   $itemsarray=itemsarray($rsa->fields['aid']);
  	   foreach ($itemsarray as $id => $itemname ) 
       {
          if($_POST['aid'][$rsa->fields['aid']][$id] =="1")
          { 
             $sqlitem= "insert into ".ADOPREFIX."_user_attri_record(aid,uid,value) values( '".$rsa->fields['aid']."','$uid' ,'$id')";
  	         $GLOBALS['adoconn']->Execute($sqlitem);  	         
  	      }
       }
  	   break;

  	   case "4":
  	   case "5":
  	   $sqldel="delete from ".ADOPREFIX."_user_attri_record where aid='".$rsa->fields['aid']."' and uid='$uid'";
  	   $GLOBALS['adoconn']->Execute($sqldel);   	    	   
  	   $sqlitem= "insert into ".ADOPREFIX."_user_attri_record(aid,uid,value) values( '".$rsa->fields['aid']."','$uid' ,'".$_POST['aid'][$rsa->fields['aid']]."')";
  	   $GLOBALS['adoconn']->Execute($sqlitem);  	   
  	   break;
     }
  	 $rsa->MoveNext();
  }	
	
	header("location:".$_SERVER['PHP_SELF']."?op=user&op2=userlist");
}

//會員動態屬性列表=================================
function userattrilist()
{
 	usermenu();	
	OpenTable();
	include_once("lib/mypager.inc");
	echo "<center><font class='undertitle'>"._USERATTRIBUTE."-"._ENABLE."</font></center>\n";
	$sql = "select aid,attriname,case when type='1' then '"._USERSELECT."'
	                                  when type='2' then '"._USERDSELECT."'
	                                  when type='3' then '"._USERQA."' end
	                            ,case when distype='1' then '"._USERLISTBOX."'
	                                  when distype='2' then '"._USERRADIO."'
	                                  when distype='3' then '"._USERCHECKBOX."'
	                                  when distype='4' then '"._USERTEXTBOX."'
	                                  when distype='5' then '"._USERTEXTAREA."' end
	                            ,sort                   
	                            ,case when necessary='1' then '"._YES."'	                                 
	                                  when necessary='0' then '"._NO."' end
  from ".ADOPREFIX."_user_attri where enable='1' order by sort asc";
	$pager = new MyPager($GLOBALS['adoconn'],$sql,'userattri',true);
	$GridHeader = array(_ID,_USERATTRINAME,_USERATTRIFORMAT,_USERATTRIDISFORMAT,_USERSORT,_USERNEED);
	$pager->setRenderGridLayout("width='100%' align='center'",$GridHeader);
	$funcNames = array(_EDIT);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=user&op2=userattriedit&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$pager->setOp("user&op2=userlist&PHPSESSID=".session_id());
	$pager->Render_Function($GLOBALS['perpage']);
	CloseTable();
	echo "<br>";
	
	OpenTable();
	include_once("lib/mypager.inc");
	echo "<center><font class='undertitle'>"._USERATTRIBUTE."-"._DISABLE."</font></center>\n";
	$sql = "select aid,attriname,case when type='1' then '"._USERSELECT."'
	                                  when type='2' then '"._USERDSELECT."'
	                                  when type='3' then '"._USERQA."' end
	                            ,case when distype='1' then '"._USERLISTBOX."'
	                                  when distype='2' then '"._USERRADIO."'
	                                  when distype='3' then '"._USERCHECKBOX."'
	                                  when distype='4' then '"._USERTEXTBOX."'
	                                  when distype='5' then '"._USERTEXTAREA."' end
	                            ,sort                   
	                            ,case when necessary='1' then '"._YES."'
	                                  when necessary='0' then '"._NO."' end
  from ".ADOPREFIX."_user_attri where enable='0' order by sort asc";	
  $pager = new MyPager($GLOBALS['adoconn'],$sql,'userattri',true);
	$GridHeader = array(_ID,_USERATTRINAME,_USERATTRIFORMAT,_USERATTRIDISFORMAT,_USERSORT,_USERNEED);
	$pager->setRenderGridLayout("width='100%' align='center'",$GridHeader);
	$funcNames = array(_EDIT);
	$funcUrls = array($_SERVER['PHP_SELF']."?op=user&op2=userattriedit&PHPSESSID=".session_id());
	$pager->setFunctions($funcNames,$funcUrls);
	$pager->setOp("user&op2=userlist&PHPSESSID=".session_id());
	$pager->Render_Function($GLOBALS['perpage']);
	CloseTable();
	echo "<br>";	
	
 	OpenTable();
  require_once "HTML/QuickForm.php";
  require_once "HTTP/Upload.php";
  $form = new HTML_QuickForm('userfrm','post',$_SERVER['PHP_SELF']);
  $form->addElement("header","myheader",_USERATTRIADD);
  $form->addElement("text","name",_USERATTRINAME);
  $form->addElement("radio","type",_USERATTRIFORMAT,_USERSELECT,"1"); 
  $sel1s = array("2"=>_USERRADIO,
                 "1"=>_USERLISTBOX);
  $form->addElement("select", "distype1",'', $sel1s);
  $form->addElement("radio","type",null,_USERDSELECT,"2");
  $sel2s = array("3"=>_USERCHECKBOX);
  $form->addElement("select", "distype2",'', $sel2s);
  $form->addElement("radio","type",null,_USERQA,"3");
  $sel3s = array("4"=>_USERTEXT,
                 "5"=>_USERTEXTAREA); 
  $form->addElement("select", "distype3",'', $sel3s); 
  $sorts = array(
            "1"=>"1",
            "2"=>"2", 
            "3"=>"3", 
            "4"=>"4",
            "5"=>"5",
            "6"=>"6",
            "7"=>"7",
            "8"=>"8",
            "9"=>"9",
			"10"=>"10",
			"11"=>"11",
            "12"=>"12", 
            "13"=>"13", 
            "14"=>"14",
            "15"=>"15",
            "16"=>"16",
            "17"=>"17",
            "18"=>"18",
            "19"=>"19",
			"20"=>"20");
  $form->addElement('select','sort',_USERSORT,$sorts);  
  $form->addElement("radio","need",_USERNEED,_YES,"1"); 
  $form->addElement("radio","need",null,_NO,"0");                 
  $form->addElement("radio","enable",_ENABLE,_YES,"1"); 
  $form->addElement("radio","enable",null,_NO,"0");
  $constantvalue= array('enable'=>"1",
                        'need'=>"1",
                        'type'=>"1");
  $form->setConstants($constantvalue);                    
	$form->addElement("hidden","op","user");
	$form->addElement("hidden","op2","userattriadd");
	$form->addRule("name",_USERATTRINAMEERR,"required",null,"client");
	$form->addElement("submit","btnSubmit",_OK);
  $form->display();
  CloseTable(); 
  echo "<br>";  
  include_once("footer.php");
}

//增加會員動態屬性至DB
function userattriadd()
{
	switch($_POST['type'])
	{
		case "1":
		    $distype=$_POST['distype1'];
		break;
		case "2":
		    $distype=$_POST['distype2'];
		break;
		case "3":
		    $distype=$_POST['distype3'];
		break;					
  }
	
	$sql="insert into ".ADOPREFIX."_user_attri(attriname,type,distype,sort,necessary,enable) values('".$_POST['name']."','".$_POST['type']."','$distype','".$_POST['sort']."','".$_POST['need']."','".$_POST['enable']."')";
	$GLOBALS['adoconn']->Execute($sql);
	//echo $sql;
	header("location:".$_SERVER['PHP_SELF']."?op=user&op2=userattrilist");
}

//會員動態屬性修改
function userattriedit()
{
	$sql="select * from ".ADOPREFIX."_user_attri where aid='".$_GET['pkid']."'";
	$rs=$GLOBALS['adoconn']->Execute($sql);
	usermenu();	
  require_once "HTML/QuickForm.php";
  require_once "HTTP/Upload.php";
 	OpenTable();
  $form = new HTML_QuickForm('userfrm','post',$_SERVER['PHP_SELF']);
  $form->addElement("header","myheader",_USERATTRIEDIT);
  $form->addElement("text","name",_USERATTRINAME);
  $form->addElement("radio","type",_USERATTRIFORMAT,_USERSELECT,"1"); 
  $sel1s = array("2"=>_USERRADIO,
                 "1"=>_USERLISTBOX);
  $form->addElement("select", "distype1",'', $sel1s);
  $form->addElement("radio","type",null,_USERDSELECT,"2");
  $sel2s = array("3"=>_USERCHECKBOX);
  $form->addElement("select", "distype2",'', $sel2s);
  $form->addElement("radio","type",null,_USERQA,"3");
  $sel3s = array("4"=>_USERTEXT,
                 "5"=>_USERTEXTAREA); 
  $form->addElement("select", "distype3",'', $sel3s); 
  $sorts = array(
            "1"=>"1",
            "2"=>"2", 
            "3"=>"3", 
            "4"=>"4",
            "5"=>"5",
            "6"=>"6",
            "7"=>"7",
            "8"=>"8",
            "9"=>"9",
			"10"=>"10",
			"11"=>"11",
            "12"=>"12", 
            "13"=>"13", 
            "14"=>"14",
            "15"=>"15",
            "16"=>"16",
            "17"=>"17",
            "18"=>"18",
            "19"=>"19",
			"20"=>"20");
  $form->addElement('select','sort',_USERSORT,$sorts);  
  $form->addElement("radio","need",_USERNEED,_YES,"1"); 
  $form->addElement("radio","need",null,_NO,"0");                 
  $form->addElement("radio","enable",_ENABLE,_YES,"1"); 
  $form->addElement("radio","enable",null,_NO,"0");
  $defaultvalue= array("name"=> $rs->fields['attriname'],"sort" => $rs->fields['sort']);	
	switch($rs->fields['type'])
	{
		case "1":
		    $defaultvalue['distype1']=$rs->fields['distype'];
		break;
		case "2":
		    $defaultvalue['distype2']=$rs->fields['distype'];
		break;
		case "3":
		    $defaultvalue['distype3']=$rs->fields['distype'];
		break;					
  }  
  $constantvalue= array("enable" => $rs->fields['enable'],
                        "need" => $rs->fields['necessary'],
                        "type" => $rs->fields['type']);
  $form->setDefaults($defaultvalue);                        
  $form->setConstants($constantvalue);                
	$form->addElement("hidden","op","user");
	$form->addElement("hidden","op2","userattriupdate");
	$form->addElement("hidden","aid",$rs->fields['aid']);
	$form->addRule("name",_USERATTRINAMEERR,"required",null,"client");
	$form->addElement("submit","btnSubmit",_OK);
  $form->display();
  CloseTable(); 
  echo "<br>"; 
	if($rs->fields['type']=="1" || $rs->fields['type']=="2")  /// 增加|列表單選及複選動態屬性之選項
	{
	  OpenTable();
    $form = new HTML_QuickForm('useritemfrm','post',$_SERVER['PHP_SELF']);
    $form->addElement("header","itemheader",_USERADDITEM);
    $form->addElement("text","itemname",_USERITEMNAME); 
    $sorts = array(
            "1"=>"1",
            "2"=>"2", 
            "3"=>"3", 
            "4"=>"4",
            "5"=>"5",
            "6"=>"6",
            "7"=>"7",
            "8"=>"8",
            "9"=>"9",
			"10"=>"10",
			"11"=>"11",
            "12"=>"12", 
            "13"=>"13", 
            "14"=>"14",
            "15"=>"15",
            "16"=>"16",
            "17"=>"17",
            "18"=>"18",
            "19"=>"19",
			"20"=>"20");
    $form->addElement('select','sort',_USERSORT,$sorts);       
    $form->addElement("radio","enable",_ENABLE,_YES,"1");   
    $form->addElement("radio","enable",null,_NO,"0");                 
    $form->addRule("item",_USERITEMNAMEERR,"required",null,"client");
    $constantitemvalue= array("enable" => "1");
    $form->setConstants($constantitemvalue);   
    $form->addElement("hidden","aid",$rs->fields['aid']);
    $form->addElement("hidden","op","user");
	$form->addElement("hidden","op2","userattriitemadd");  
	$form->addRule("itemname",_USERITEAMNAMEERR,"required",null,"client");
	$form->addElement("submit","btnSubmit",_OK);
    $form->display();
    CloseTable(); 
    echo "<br>";  		
		OpenTable();
	  include_once("lib/mypager.inc");
	  echo "<center><font class='undertitle'>".$rs->fields['attriname']."-"._USERITEMLIST."-"._ENABLE."</font></center>\n";
	  $sql = "select itemid,name,sort from ".ADOPREFIX."_user_attri_items where aid='".$rs->fields['aid']."' and enable='1' order by sort asc";
	  $pager = new MyPager($GLOBALS['adoconn'],$sql,'useritem',true);
	  $GridHeader = array(_ID,_USERITEMNAME,_USERSORT);
	  $pager->setRenderGridLayout("width='100%' align='center'",$GridHeader);
	  $funcNames = array(_EDIT);
	  $funcUrls = array($_SERVER['PHP_SELF']."?op=user&op2=userattriitemedit&PHPSESSID=".session_id());
	  $pager->setFunctions($funcNames,$funcUrls);
	  $pager->setOp("user&op2=userlist&PHPSESSID=".session_id());
	  $pager->Render_Function($GLOBALS['perpage']);
    echo "</br>";
		echo "</br>";
	  echo "<center><font class='undertitle'>".$rs->fields['attriname']."-"._USERITEMLIST."-"._DISABLE."</font></center>\n";
	  $sql = "select itemid,name,sort from ".ADOPREFIX."_user_attri_items where aid='".$rs->fields['aid']."' and enable='0' order by sort asc";
	  $pager = new MyPager($GLOBALS['adoconn'],$sql,'useritem',true);
	  $GridHeader = array(_ID,_USERITEMNAME,_USERSORT);
	  $pager->setRenderGridLayout("width='100%' align='center'",$GridHeader);
	  $funcNames = array(_EDIT);
	  $funcUrls = array($_SERVER['PHP_SELF']."?op=user&op2=userattriitemedit&PHPSESSID=".session_id());
	  $pager->setFunctions($funcNames,$funcUrls);
	  $pager->setOp("user&op2=userlist&PHPSESSID=".session_id());
	  $pager->Render_Function($GLOBALS['perpage']);		
	  CloseTable(); 
		echo "</br>";    
 	}   
  include_once("footer.php");		
}

//會員動態屬性修改至DB
function userattriupdate() 
{
	switch($_POST['type'])
	{
		case "1":
		    $distype=$_POST['distype1'];
		break;
		case "2":
		    $distype=$_POST['distype2'];
		break;
		case "3":
		    $distype=$_POST['distype3'];
		break;					
  }
			
	$sql="update ".ADOPREFIX."_user_attri set attriname='".$_POST['name']."',type='".$_POST['type']."',distype='$distype',sort='".$_POST['sort']."',necessary='".$_POST['need']."',enable='".$_POST['enable']."' where aid='".$_POST['aid']."'";
	$GLOBALS['adoconn']->Execute($sql);
	//echo $sql;
	header("location:".$_SERVER['PHP_SELF']."?op=user&op2=userattrilist");	
}

//增加會員動態屬性選項至DB
function userattriitemadd()
{
	$sql="insert into ".ADOPREFIX."_user_attri_items(aid,name,sort,enable) values('".$_POST['aid']."','".$_POST['itemname']."','".$_POST['sort']."','".$_POST['enable']."')";
	$GLOBALS['adoconn']->Execute($sql);
	//echo $sql;
	header("location:".$_SERVER['PHP_SELF']."?op=user&op2=userattriedit&pkid=".$_POST['aid']);			
}

//修改會員動態屬性選項
function userattriitemedit()
{
	usermenu();	
  require_once "HTML/QuickForm.php";
  require_once "HTTP/Upload.php";
  $sql="select * from ".ADOPREFIX."_user_attri_items where itemid='".$_GET['pkid']."'";
  $rs=$GLOBALS['adoconn']->Execute($sql);
	OpenTable();
  $form = new HTML_QuickForm('useritemfrm','post',$_SERVER['PHP_SELF']);
  $form->addElement("header","itemheader",_USERADDITEM);
  $form->addElement("text","itemname",_USERITEMNAME); 
  $sorts = array(
            "1"=>"1",
            "2"=>"2", 
            "3"=>"3", 
            "4"=>"4",
            "5"=>"5",
            "6"=>"6",
            "7"=>"7",
            "8"=>"8",
            "9"=>"9",
			"10"=>"10",
			"11"=>"11",
            "12"=>"12", 
            "13"=>"13", 
            "14"=>"14",
            "15"=>"15",
            "16"=>"16",
            "17"=>"17",
            "18"=>"18",
            "19"=>"19",
			"20"=>"20");
  $form->addElement('select','sort',_USERSORT,$sorts);    
  $form->addElement("radio","enable",_ENABLE,_YES,"1");   
  $form->addElement("radio","enable",null,_NO,"0");                 
  $form->addRule("itemname",_USERITEMNAMEERR,"required",null,"client");
  $defaultvalue= array("itemname"=> $rs->fields['name'],"sort" => $rs->fields['sort']);	
  $form->setDefaults($defaultvalue);        
  $constantitemvalue= array("enable" => $rs->fields['enable']);
  $form->setConstants($constantitemvalue);   
  $form->addElement("hidden","itemid",$_GET['pkid']);
  $form->addElement("hidden","aid",$rs->fields['aid']);
  $form->addElement("hidden","op","user");
	$form->addElement("hidden","op2","userattriitemupdate");  
	$form->addElement("submit","btnSubmit",_OK);
  $form->display();
  CloseTable(); 
  echo "<br>";  
}


//修改會員動態屬性選項至DB
function userattriitemupdate()
{
	$sql="update ".ADOPREFIX."_user_attri_items set name='".$_POST['itemname']."' ,enable='".$_POST['enable']."',sort='".$_POST['sort']."' where itemid='".$_POST['itemid']."'";
	$GLOBALS['adoconn']->Execute($sql);
	//echo $sql;
	header("location:".$_SERVER['PHP_SELF']."?op=user&op2=userattriedit&pkid=".$_POST['aid']);			
}

//下載會員資料
function downloaddata(){
	$filename = "admin/modules/user_report.csv";
	//CSV檔程式------
	if (file_exists($filename)){
		$fp = fopen($filename, "w");
	} else {
		$fp = fopen($filename, "w");
		chmod($filename, 0775);
	}
	
	
	//查詢request====================================
	$q_sex = '';
	$q_enable = '';
	$q_name = '';
	$q_nickname = '';
	$q_email = '';
	$queryString = '  ';
	$strsearTF=0;//是否有進階查詢
	if ($_REQUEST['sex'] != ''  ){ 
	  $q_sex    = $_REQUEST['sex'];
	  $queryString .= " and sex = '".$q_sex."'  ";	  
	}
	if ($_REQUEST['name'] != '' && $_REQUEST['name'] != _USERADMINQUERYNAME ) {
      $q_name   = $_REQUEST['name'];
	  $queryString .= " and username LIKE '%".$q_name."%' ";	  
	}	
	if ($_REQUEST['email'] != '' && $_REQUEST['email'] != _USERADMINQUERYEMAIL) {
  	  $q_email    = $_REQUEST['email'];
	  $queryString .= " and email LIKE '%".$q_email."%' ";	  
	}
	$searchstr='sex='.$q_sex.'&enable='.$q_enable.'&name='.$q_name.'&email='.$q_email;
	//=====================================================================
	if($_REQUEST['search2']=='search2'){
		//2012-12-12 add by amy 處理會員動態屬性查詢-------------------		
		$sqlattribute="select aid,distype,necessary from ".ADOPREFIX."_user_attri where enable='1' order by sort asc";
		$rsa_list=$GLOBALS['adoconn']->Execute($sqlattribute);	
		$searchstr_1="&search2=search2";
		while(!$rsa_list->EOF){
			$ErrorData = 0;//判斷必填欄位未填寫時>$ErrorData =1
			$necessary=$rsa_list->fields[2];//0一般填寫欄 1必填欄位
			switch($rsa_list->fields[1]){			
				case "1"://下拉式選單				
				case "2"://radio
				case "4"://text							
				case "5"://textarea
					if($_REQUEST['aid'][$rsa_list->fields[0]] !=""){
						$strsearTF=1;
						$sql= "select uid from ".ADOPREFIX."_user_attri_record
									where aid='".$rsa_list->fields[0]."' and value like '%".$_REQUEST['aid'][$rsa_list->fields[0]]."%'";								
						$rs_temp=$GLOBALS['adoconn']->Execute($sql);
						while(!$rs_temp->EOF){
							$str_TempUid.="'".$rs_temp->fields[0]."',";
							$rs_temp->MoveNext();							
						}						
						$searchstr_1.="&aid[".$rsa_list->fields[0]."]=".$_REQUEST['aid'][$rsa_list->fields[0]];
					}
				break;
				
				case "3": //checkbox
					$itemsarray=itemsarray($rsa_list->fields[0]);
					foreach ($itemsarray as $id => $itemname ){
						if( $_REQUEST['attritem'][$rsa_list->fields[0]][$id]==$id){	
							$strsearTF=1;
							$sql2="select uid from ".ADOPREFIX."_user_attri_record where aid='".$rsa_list->fields[0]."' and CONVERT(varchar(MAX), value)='".$id."'";
							$rs = $GLOBALS['adoconn']->Execute($sql2);
							while(!$rs->EOF){
								$str_TempUid.="'".$rs->fields[0]."',";
								$rs->MoveNext();
							}							
							$searchstr_1.="&attritem[".$rsa_list->fields[0]."][".$id."]=".$id;
						}						
					}					
				break;
			}
			$rsa_list->MoveNext();			
		}
		$str_TempUid = substr($str_TempUid,0,strlen($str_TempUid)-1);//取$str左邊$n個字符		
		if($strsearTF==1){
			if(!empty($str_TempUid))
				$queryString_1= " and id IN(".$str_TempUid.") ";
			else							
				$queryString_1= " and id IN('') ";
		}
		
		//end 2012-12-12 add by amy 處理會員動態屬性查詢-------------------
	}
	//end 查詢request================================
	
	
	
	//動態屬性--------------------------	
	$sql_attri = "select aid,attriname,type,distype from ".ADOPREFIX."_user_attri where enable='1' order by sort asc";
	$stmt_attri = $GLOBALS['adoconn']->Prepare($sql_attri);
	$rsa = $GLOBALS['adoconn']->Execute($stmt_attri);
	if($rsa){
		$head_str_attri=",";
		$rsa->MoveFirst();
		while(!$rsa->EOF){
			if($rsa->fields[3]==3){
				$rs_items = $GLOBALS['adoconn']->Execute("select  name from ".ADOPREFIX."_user_attri_items where aid='".$rsa->fields[0]."' order by sort ");
				while(!$rs_items->EOF){
					$head_str_attri.=$rsa->fields[1]."-".$rs_items->fields[0].",";
					$rs_items->MoveNext();
				}
			}
			else{
				$head_str_attri.=$rsa->fields[1].",";
			}
			$rsa->MoveNext();
		}
	}
	
	
	//會員資料--------------------------
	//enable='".$_REQUEST['op3']."'
	$sql = "select id,username,useridno,
			CASE sex WHEN 0 THEN '"._USERWOMAN."'
					 WHEN 1 THEN '"._USERMAN."'
			END AS sex
			,useremail,birthday,address,homephone,officephone,cellphone,fax,
			CASE enable WHEN 0 THEN '"._DISABLE."'  
						WHEN 1 THEN '"._ENABLE."'
						WHEN -1 THEN '"._USERNOCOFIRM."'
			END AS enable
			from ".ADOPREFIX."_user where 1=1 ".$queryString.$queryString_1." order by id asc";
	$stmt = $GLOBALS['adoconn']->Prepare($sql);
	$rs = $GLOBALS['adoconn']->Execute($stmt);	
	
	//標題-------------------------------
	$head_str = _MEMBERID.","._USERNAME.","._USERIDNO.","._USERSEX.","._USEREMAIL.","._USERBIRTHDAY.
	","._USERADDRESS.","._USERHPHONE.","._USEROPHONE.","._USERCPHONE.","._USERFAX.","._USERSTATUS.$head_str_attri."\n";    

    //內容-------------------------------
	$input_str = "";
	if($rs){ 
		$rs->MoveFirst();
		while(!$rs->EOF){
			$uid = $rs->fields["id"];			
			//取得會員動態屬性之內容============			
			if($rsa){				
				$rsa->MoveFirst();
				$str1="";//會員動態屬性之內容 組合字串
				while(!$rsa->EOF){
					$aid=$rsa->fields["aid"];					
					switch($rsa->fields["distype"]){
						case 1://下拉式選單,
						case 2://radio
							$rsrecord1=$GLOBALS['adoconn']->Execute("select uid,aid,value from ".ADOPREFIX."_user_attri_record where uid='".$uid."' and aid='".$aid."'");
							if(!$rsrecord1->EOF){
								
								while(!$rsrecord1->EOF){								
									$rs1 = $GLOBALS['adoconn']->Execute("select name from ".ADOPREFIX."_user_attri_items where itemid='".$rsrecord1->fields["value"]."'");
									$str1 .= $rs1->fields["name"].",";
									$rsrecord1->MoveNext();
								}
							}
							else
								$str1 .= ",";							
							$rsrecord1->close();
							$rs1->close();
						break;
						case 3://checkbox
							$rs1 = $GLOBALS['adoconn']->Execute("select itemid from ".ADOPREFIX."_user_attri_items where aid='".$aid."' order by sort");							
							$rs1->MoveFirst();
							while(!$rs1->EOF){
								$num1="0";
								$rsrecord1 =$GLOBALS['adoconn']->Execute("select uid,aid,value from ".ADOPREFIX."_user_attri_record where uid='".$uid."' and aid='".$aid."'");
								if(!$rsrecord1->EOF){												
									while (!$rsrecord1->EOF) {										
										if ((string)$rs1->fields[0] == (string)$rsrecord1->fields[2])
											$num1 = "1";									
										$rsrecord1->MoveNext();
									}
								}
								else
									$num1 = "0";								
								$str1.=$num1.",";									
								$rs1->MoveNext();
							}
							$rsrecord1->close();
							$rs1->close();
						break;
						case 4://text
						case 5://textarea
							$rsrecord1=$GLOBALS['adoconn']->Execute("select uid,aid,value from ".ADOPREFIX."_user_attri_record where uid='".$uid."' and aid='".$aid."'");
							$str1_value = str_replace(","," ",$rsrecord1->fields["value"]);
							$str1_value = str_replace("\n"," ",$str1_value);
							$str1_value = mysql_real_escape_string($str1_value);
							$str1.=$str1_value.",";
							$rsrecord1->close();
						break;
					}					
					$rsa->MoveNext();
				}
			}
			//end 取得會員動態屬性之內容============
			
			
			$input_str .= $rs->fields["id"].",".$rs->fields["username"].",".$rs->fields["useridno"].
			",".$rs->fields["sex"].",".$rs->fields["useremail"].",".$rs->fields["birthday"].
			",".$rs->fields["address"].",".$rs->fields["homephone"].
			",".$rs->fields["officephone"].",".$rs->fields["cellphone"].
			",".$rs->fields["fax"].",".$rs->fields["enable"].",".$str1."\n";		
			
			$rs->MoveNext();
		}
	}
	//下載時間
	$my_t=getdate(date("U"));
	$date1= _DOWNLOADTIME. " : ".$my_t[year]."/".$my_t[mon]."/".$my_t[mday]." ".$my_t[hours].":".$my_t[minutes];
	
	$content = $head_str.$input_str."\n\n".$date1;
	
	$content = mb_convert_encoding($content, 'BIG-5', 'UTF-8'); 
	//$content = iconv("utf-8", "big5", $content);
	fwrite($fp, $content, strlen($content));//寫入資料 $content , strlen($content) 長度
	fclose($fp); //關閉檔案         
	header("Content-Type: text/comma-separated-values");
	header("Content-Disposition: attachment; filename=".$filename);
	echo $content;
}


if ($_REQUEST['op']=="user" && isAuthority($_SESSION['aid'],$_REQUEST['op']))
{
	switch ($_REQUEST['op2'])
	{
		case "userlist": //會員列表
		default: 
			userlist();			
		break;
		
		case "useradd": //增加會員至DB
			useradd();			
		break;		
			
		case "userinsert": //增加會員
			userinsert();			
		break;
			
		case "userattrilist":  //會員動態屬性列表
			userattrilist();			
		break;		

		case "userattriadd": //增加會員動態屬性至DB
			userattriadd();			
		break;	
		
		case "userattriedit": //會員動態屬性修改
			userattriedit();			
		break;	

		case "userattriupdate": //會員動態屬性修改至DB
			userattriupdate();			
		break;	

		case "userattriitemadd": //增加會員動態屬性選項至DB
			userattriitemadd();			
		break;	
		
		case "userattriitemedit": //修改會員動態屬性選項
			userattriitemedit();			
		break;	
		
		case "userattriitemupdate": //修改會員動態屬性選項至DB
			userattriitemupdate();			
		break;					
		
		case "useredit": //會員修改資料
			useredit();			
		break;	
		
		case "userupdate": //會員修改資料至DB
			userupdate();			
		break;

		case "downloaddata": //下載會員資料
			downloaddata();			
		break;
					
	}
}

function itemsarray($aid)
{
	  	 $sql="select * from ".ADOPREFIX."_user_attri_items where aid='$aid' order by sort asc";
  	   $rs=$GLOBALS['adoconn']->Execute($sql);
  	   while(!$rs->EOF)
  	   {
  	   	 $items[$rs->fields['itemid']]=$rs->fields['name'];
  	   	 $rs->MoveNext();
  	   }
  	   return $items;  	   
}
?>
