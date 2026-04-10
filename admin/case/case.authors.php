<?php
/*******************************************
 * Company: 
 * Program: case.authors.php
 * Author:  Ken Tsai
 * Date:    from 2004.09.04
 * Version: 2.0
 *******************************************/

//if (!eregi(ADMINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if (!preg_match('/'.ADMINPAGE.'/i', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if ($_REQUEST['op']=="authors")
{
	include_once("admin/modules/authors.php");
}
?>
