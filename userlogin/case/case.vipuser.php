<?php
/*******************************************
 * Company: Linkuswell Tech Co., Ltd.
 * Program: case.vipuser.php
 * Author:  Yushin Chen
 * Date:    from 2010.01.13
 * Version: 1.0
 *******************************************/

if (!eregi(USERLOGINPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied!"); }

if ($_REQUEST['op']=="vipuser")
{
	include_once("userlogin/modules/vipuser.php");
}
?>