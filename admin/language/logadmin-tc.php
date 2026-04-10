<?php
/**
 * Company: 
 * Program: logadmin-tc.php
 * Author:  Ann Chen
 * Date:    2019.01.10
 * Version: 1.0
 * Description: LOG管理 語言設定檔
 */

//LOG偵錯設定
define("_LOGDEBUG"     , "LOG偵錯設定");
define("_LOGSEARCHLOG" , "LOG查詢");
define("_LOGENAME"     , "LOG名稱");
define("_LOGDEBUGEDIT" , "修改LOG偵錯設定");

//列表
define("_LOGLIST"      , "LOG列表");
define("_LOGEXECTYPE"  , "執行種類");
define("_LOGDEBUGMODE" , "debug mode");
define("_LOGENABLE"    , "啟用");
define("_LOGREMARK"    , "用途說明");

//erorr
define("_LOGERROR"     , "其他錯誤，請重新設定");

//欄位轉換
//執行種類
define("_LOGEXECTYPE1" , "APP");
define("_LOGEXECTYPE2" , "WEB");
define("_LOGEXECTYPE3" , "CRON");
define("_LOGEXECTYPE4" , "APP/WEB共用"); //Add by Ann 2019.09.11
define("_OTHER" , "其他");
//log記錄
define("_LOGDEBUGMODE0", "input/output皆不記錄");
define("_LOGDEBUGMODE1", "只input記錄");
define("_LOGDEBUGMODE2", "input/output皆記錄");


define("_LOGSETWRITEDATETIME", "發生時間");
define("_LOGSETWRITEFORMAT", "log_format 編號");
define("_LOGSETWRITEFORMATNAME", "log_format 名稱");
define("_LOGSETWRITEPARAMETER", "參數JSON");
define("_LOGSETWRITERETURN", "回傳值JSON");
define("_LOGSETWRITERRMEESSAGE", "錯誤訊息");
define("_LOGSETWRITEREMARK", "備註");
define("_LOGSETWRITEVIWE", "查看詳細資料");
define("_LOGSETWRITELOG" , "logservice錯誤紀錄");//Add by ken. 2019.06.12
define("_LOGSETWRITELOGLIST" , "logservice錯誤紀錄列表");//Add by ken. 2019.06.12

?>