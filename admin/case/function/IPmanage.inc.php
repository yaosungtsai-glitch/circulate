<?php
/*************************************
 * Company: 
 * Program: IPmanage.inc.php
 * Author:  Ken Tsai
 * Date:    from 2005.01.18
 * Version: 2.0
 * Description: 站台登入IP管理共用函式
 *************************************/

include_once("mainfile.php");


/**
 * 取出啟用的站台編號及站台名稱
 * @param id	站台編號
 * @return ResultSet(站台編號,站台名稱)
 */
function getEnableStore($id="")
{
	if ($id!="")	$strsql = "select ".ORGTABLE_FIELD_ID.", ".ORGTABLE_FIELD_NAME." from ".ADOPREFIX.ORGTABLE." where ".ORGTABLE_FIELD_ENABLE."=1 and ".ORGTABLE_FIELD_ID."=$store_id";
	else	$strsql = "select ".ORGTABLE_FIELD_ID.", ".ORGTABLE_FIELD_NAME." from ".ADOPREFIX.ORGTABLE." where ".ORGTABLE_FIELD_ENABLE."=1";
  
	$rs = $GLOBALS['adoconn']->Execute($strsql);
	if ($rs && !$rs->EOF)	return $rs;
	else return null;
	if ($rs) $rs->Close();
}

?>
