<?php
/**
 * Company: 
 * Program: index.php
 * Author:  Ken Tsai
 * Date:    from 2005.03.25
 * Version: 2.0
 * Description: 首頁中央區塊內容
 */

header("location:administrator.php?op=login")

//include_once ("mainfile.php");


/**
 * 顯示首頁中央區塊
 * @back    是否顯示「回上一頁」之連結(1:顯示,0:不顯示)，於footer.php中判斷
 */
/*
function middleblocks($back=0)
{
    include_once("header.php");
    $tablebegin = "<table border='0' width='95%' align='center' cellspacing='0' cellpadding='0'>"
        		 ."<tr valign='top'><td>";
    $tableend = "</td></tr></table>";
    $samerow = "</td><td width='12'>&nbsp;</td><td>";
    $diffrow = $tableend."<br>".$tablebegin;
    echo $tablebegin;

    $f = 1;		//設第一個區塊不用比對是否同一列
    $strSQL = "select url,content,weight,position from ".ADOPREFIX."_blocks where bkey='index' and active=1 order by weight,position";
    $rs = $GLOBALS['adoconn']->Execute($strSQL);
    while ($rs && !$rs->EOF)
	{
        $url = $rs->fields['url'];
        $content = $rs->fields['content'];
        $weight = $rs->fields['weight'];
        $position = $rs->fields['position'];
		if ($url && trim($url) != "") {
	        if (file_exists($url)) {
	            if ($f != 1) {  //第2個區塊後比對是同一列或要換列
	                if ($t == $weight) {
	                    $strhtml = $samerow;
	                } else {
	                    $strhtml = $diffrow;
	                }
	            }
	            if(isset($strhtml)) echo $strhtml;
	            include_once($url);
	            $t = $weight;
	            $f++;
	        }
	    } else if ($content && trim($content) != "") {
	    		if ($f != 1) {  //第2個區塊後比對是同一列或要換列
	                if ($t == $weight) {
	                    $strhtml = $samerow;
	                } else {
	                    $strhtml = $diffrow;
	                }
	            }
	            echo $strhtml.$content;
	            $t = $weight;
	            $f++;
	    }
        $rs->MoveNext();
    }
    if ($rs) $rs->Close();
    echo $tableend;
    include_once("footer.php");
}

middleblocks(0); //傳入0則網頁下方不顯示「回上一頁」之link，於footer.php中判斷
 */
?>
