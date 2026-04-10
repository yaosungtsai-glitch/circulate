<?php
/*************************************
 * Company: 
 * Program: tables.php
 * Author:  Ken Tsai
 * Date:    from 2005.03.24
 * Version: 2.0
 * Description:	各式表格樣式
 *************************************/


//原來的表格,細框線
function OpenTable() {
    echo "<table width='95%' border='0' cellspacing='1' cellpadding='0' class='bgcolor2' align='center'><tr><td>\n";
    echo "<table width='100%' border='0' cellspacing='1' cellpadding='8' class='bgcolor1'><tr><td align='center'>\n";
}

function CloseTable() {
    echo "</td></tr></table></td></tr></table>\n";
}


//討論區用到
//上色表頭文字置中,寬95%
function OpenTable1() {
    echo "<table width='95%' border='0' cellspacing='1' cellpadding='0' class='bgcolor2' align='center'><tr><td>\n";
    echo "<table width='100%' border='0' cellspacing='1' cellpadding='8' class='bgcolor1'><tr><td align='center'>\n";
}

function CloseTable1() {
   echo "</td></tr></table></td></tr></table>\n";
}


//內層細框,寬95%
function OpenTable2() {
    echo "<table width='95%' border='0' cellspacing='1' cellpadding='0' class='bgcolor2' align='center'><tr><td class='bgcolor1'>\n";
}


function CloseTable2() {
    echo "</td></tr></table>\n";
}


//討論區、最新消息用到
//上色表頭文字向左對齊,寬95%
function OpenTable3() {
    echo "<table width='95%' border='0' cellspacing='1' cellpadding='1' class='bgcolor2' align='center'><tr><td valign='middle' height='25'>\n";
}

function CloseTable3() {
    echo "</td></tr></table>\n";
}


//隱私條款頁的表格
function OpenTable6() {
    echo "<table align=\"center\" width=\"530\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"policytbstyle02\"><tr><td>\n";
}

function CloseTable6() {
    echo "</td></tr></table>\n";
}


/*目前沒用到
function OpenTable7() {
    echo "<table align=\"center\" width=\"530\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"formtitle13\"><tr><td>\n";
}

function CloseTable7() {
    echo "</td></tr></table>\n";
}


function OpenTable8() {
    echo "<table class=\"tab14\" align=\"center\" width=\"500\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\" bordercolor=\"#448DF0\">\n";
}

function CloseTable8() {
    echo "</table>\n";
}
*/


//首頁簡介表格
function OpenTable10() {
    echo "<table align=\"center\" width=\"100%\" height=\"54\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"indextbstyle01\"><tr><td valign='middle' class=\"indextitle01\">\n";
}

function CloseTable10() {
    echo "</td></tr></table>\n";
}

/*查詢、投票、電子帳單用到
function OpenTable11() {
    echo "<table align=\"center\" width=\"100%\" height=\"54\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"inquiretbstyle01\"><tr><td valign='middle' class=\"inquiretitle01\">\n";
}

function CloseTable11() {
    echo "</td></tr></table>\n";
}
*/


/*義賣產品的新開視窗用到
//內層細框,寬600
function OpenTable12() {
    echo "<table width=\"600\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" class=\"bgcolor2\" align=\"center\"><tr><td class=\"bgcolor1\">\n";
}

function CloseTable12() {
    echo "</td></tr></table>\n";
}
*/


//隱私條款頁表格
function OpenTable13() {
    echo "<table align=\"center\" width=\"530\" height=\"44\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"policytbstyle01\"><tr><td valign='middle'>\n";
}

function CloseTable13() {
    echo "</td></tr></table>\n";
}


//會員專區
function OpenTable14() {
     echo "<table align=\"center\" width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td valign='top'>\n";
}

function MiddleTable14() {
     echo "</td><td valign='top'>\n";
}

function CloseTable14() {
    echo "</td></tr></table>\n";
}

/*
//留言板/最新消息/線上投票/討論版
function OpenTable15() {
     echo "<table align=\"center\" width=\"100%\" height=\"54\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"modulestbstyle01\"><tr><td valign='middle' class=\"modulestitle01\">\n";
}

function CloseTable15() {
    echo "</td></tr></table>\n";
}
*/

?>
