<?php
/*******************************************
 * Company: 
 * Program: case.storeadmin.php
 * Author:  Ken Tsai
 * Date:    from 2001.12.14
 * Version: 2.0
 *******************************************/

//if (!eregi(ADMINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if (!preg_match('/'.ADMINPAGE.'/i', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if ($_REQUEST['op']=="storeadmin")
{
	include_once("admin/modules/storeadmin.php");
}
?>
