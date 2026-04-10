<?php
/*******************************************
 * Company: 
 * Program: case.logadmin.php
 * Author:  Ann chen
 * Date:    2019.01.10
 * Version: 1.0
 *******************************************/

//if (!eregi(ADMINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if (!preg_match('/'.ADMINPAGE.'/i', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if ($_REQUEST['op']=="logadmin")
{
	include_once("admin/modules/logadmin.php");
}
?>
