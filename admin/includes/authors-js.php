<?php
/**
* Company: 
* Program: authors-js.php
* Author:  Ken Tsai
* Date:    from 2004.10.04
* Version: 2.0
* Description: 管理員帳號權限管理的javascript
*/

echo "<SCRIPT type=\"text/javascript\">
<!--

function check_authorspwd(form) {

    if (form.authors_aid != null && form.authors_aid.value.trim() == \"\") {
           	alert(\"["._AUTHORSID."]"._NOTNULL."\");
           	form.authors_aid.focus();
           	return(false);

	}

    if (form.aname.value.trim() == \"\") {
            alert(\"["._AUTHORSNAME."]"._NOTNULL."\");
            form.aname.focus();
            return(false);

    }
    if (form.email.value.trim() == \"\") {
            alert(\"["._AUTHORSEMAIL."]"._NOTNULL."\");
            form.email.focus();
            return(false);

	}
    if (form.pwd1.value.trim() == \"\") {
            alert(\"["._PASSWORD."]"._NOTNULL."\");
            form.pwd1.focus();
            return(false);

	}

    if (form.pwd2.value.trim() == \"\") {
            alert(\"["._CONFIRM._PASSWORD."]"._NOTNULL."\");
            form.pwd2.focus();
            return(false);

	}
    if (form.pwd1.value.trim() != form.pwd2.value.trim()) {
            alert(\""._PWDPWD2."\");
            form.pwd2.focus();
            return(false);
    }

    form.submit();
    return;
}

//-->
</script>";

?>
