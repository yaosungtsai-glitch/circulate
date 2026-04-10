<?php
/******************************************
 * Program:mainile.php
 * Function:  短網址 轉址系統 主要 引入檔
 * Author: Ken Tsai
 * Date: 2023/05/12
 * ****************************************/

$f3 = require('fatfree-core-master/base.php');
$f3->config('config.ini');

$GLOBALS['db']=new \DB\SQL($f3->dbtype.":host=".$f3->dbhost.";port=3306;dbname=".$f3->dbname,$f3->dbuser,$f3->dbpassword);

?>