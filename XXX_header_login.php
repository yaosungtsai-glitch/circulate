<?php
/*************************************
 * Company: 
 * Program: header_login.php
 * Author:  Ken Tsai
 * Date:    from 2023.05.15
 * Version: 2.0
 * Description:	網頁共用頁首
 *************************************/
echo "<!DOCTYPE html>".PHP_EOL;
echo "<html>".PHP_EOL;
echo "<head>".PHP_EOL;
echo "<title>"._TITLE."</title>".PHP_EOL;
meta(); 
echo "<LINK REL='StyleSheet' HREF='includes/style/style.css' TYPE='text/css'>".PHP_EOL; 
echo "</head>".PHP_EOL;
themeheader();

function meta(){
    echo "<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset="._CHARSET."'>".PHP_EOL;
    echo "<META HTTP-EQUIV='Pragma' CONTENT='no-cache'>".PHP_EOL;
    echo "<META NAME='AUTHOR' CONTENT='Ken Tsai>".PHP_EOL;
    echo "<META NAME='COPYRIGHT' CONTENT='Copyright (c) ".date("Y")." by teamplus".PHP_EOL;
    echo "<META NAME='DESCRIPTION' CONTENT='teamplus Internal System'>".PHP_EOL;
}

function themeheader()
{
    echo "<table border='0' align='center' width='80%'>";
    echo "<tr><td>\n";      //上方區塊開始
    echo "<img src='images/loginlogo.png'>";
    echo "</td></tr>\n";    //上方區塊結束
    echo "<tr><td>\n";      //中間區塊開始
}

?>
