<?php
/**
* Company: 
* Program: IPmanage-js.php
* Author:  Ken  Tsai
* Date:    from 2004.09.16
* Version: 2.0
* Description: µn¤JIPºÞ²zjavascript
*/

echo "<SCRIPT type=\"text/javascript\">
<!--

function checkIP()
{
	var error = 0;
	for (i=1; i<=5; i++)
	{
		ipvalue = eval(\"IPManage.ipsec\"+i+\".value\");
		if (!ipvalue.length)
		{
			error = 1;
			eval(\"IPManage.ipsec\"+i).select();
			eval(\"IPManage.ipsec\"+i).focus();
			break;
		}
		if (isNaN(eval(\"IPManage.ipsec\"+i+\".value\")) || eval(\"IPManage.ipsec\"+i+\".value\")<1 || eval(\"IPManage.ipsec\"+i+\".value\")>255)
		{
			error = 2;
			eval(\"IPManage.ipsec\"+i).select();
			eval(\"IPManage.ipsec\"+i).focus();
			break;
		}
	}

	switch (error)
	{
		case 1:
			alert(\""._CANNOTNULL."\");
			break;
		case 2:
			alert(\""._BE1TO255."\");
			break;
		default:
			IPManage.submit();
			break;
	}
}

//-->
</script>";

?>
