<?php

/************************************************************************/
/*Company:                                                              */
/*Author:   Ken Tsai                                                    */
/*Date: from 02/08/2017                                                 */
/************************************************************************/

//if (!eregi(SERVICEPAGE, $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if (!preg_match('/'.SERVICEPAGE.'/i', $_SERVER['PHP_SELF'])) { die ("Access Denied"); }

switch($_REQUEST['op']) {
	case "rule":
		include_once("service/modules/rule.php");
		break;
}

?>