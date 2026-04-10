<?php
/*******************************************
 * Company: 
 * Program: case.shorturl.php
 * Author:  Ann Chen
 * Date:    2017.12.20
 * Version: 2.0
 *******************************************/

//if (!eregi(ADMINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if (!preg_match('/'.ADMINPAGE.'/i', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if ($_REQUEST['op']=="shorturl")
{
	include_once("admin/modules/shorturl.php");
}
?>
