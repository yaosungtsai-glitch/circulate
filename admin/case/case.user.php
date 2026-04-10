<?php
/*******************************************
 * Company: 
 * Program: case.user.php
 * Author:  Ken Tsai
 * Date:    2012.11.21
 * Version: 2.0
 *******************************************/

//if (!eregi(ADMINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if (!preg_match('/'.ADMINPAGE.'/i', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if ($_REQUEST['op']=="user")
{
	include_once("admin/modules/user.php");
}
?>
