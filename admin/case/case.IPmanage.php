<?php
/*******************************************
 * Company: 
 * Program: case.IPmanage.php
 * Author:  Ken Tsai
 * Date:    from 2005.1.11
 * Version: 2.0
 *******************************************/

//if (!eregi(ADMINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if (!preg_match('/'.ADMINPAGE.'/i', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if ($_REQUEST['op']=="IPmanage")
{
	include_once("admin/modules/IPmanage.php");
}
?>
