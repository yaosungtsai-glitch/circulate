<?php
/*******************************************
 * Company: 
 * Program: case.auditid.php
 * Author:  Ken Tsai
 * Date:    2023.05.25
 * Version: 2.0
 *******************************************/

if (!preg_match('/'.ADMINPAGE.'/i', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if ($_REQUEST['op']=="members")
{
	include_once("admin/modules/members.php");
}
?>
