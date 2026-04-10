<?php
/*************************************
 * Company:
 * Program: config.php
 * Author:  Ken Tsai
 * Date:    from 2021.03.31
 * Version: 2.0
 * Description: 站台初始參數
 *************************************/
/**
 * 資料庫連結參數
 */
date_default_timezone_set("Asia/Taipei"); //設定時區

/**
 * 系統路徑
 */
define("SCRIPT_PATH",dirname(__FILE__));    //程式實際放置位置

define("ADODATABASE","mysqli");
define("ADOHOST_MASTER","localhost");
define("ADODBNAME",'circulate');
define("ADOUNAME","root");
define("ADOPASS","");
define("ADOPREFIX","circulate");

/*
 * 記錄子站台基本資訊的資料表相關資訊    by yushin, 2008-04-07.
 */
define("ORGTABLE","_store");                    //記錄子站台基本資訊的資料表名稱，去除掉PREFIX，以"_"開頭
define("ORGTABLE_FIELD_ID","id");                //該資料表記錄"子站台編號"的欄位名稱
define("ORGTABLE_FIELD_NAME","name");            //該資料表記錄"子站台名稱"的欄位名稱
define("ORGTABLE_FIELD_IPMANAGE","ipmanage");    //該資料表記錄"子站台是否控管可登入IP位置"的欄位名稱
define("ORGTABLE_FIELD_ENABLE","enable");        //該資料表記錄"子站台是否啟用"的欄位名稱

/**
 * 記錄USER table基本資訊的資料表相關資訊    by Ken Tsai, 2009-12-07.
 */
define("USER_TABLE","_user");                    //記錄USER table基本資訊的資料表名稱，去除掉PREFIX，以"_"開頭
define("USER_TABLE_FIELD_ID","id");                //該資料表記錄"USER table編號"的欄位名稱
define("USER_TABLE_FIELD_NAME","username");            //該資料表記錄"USER 名稱"的欄位名稱
define("USER_TABLE_FIELD_IPMANAGE","ipmanage");    //該資料表記錄"USER是否控管可登入IP位置"的欄位名稱
define("USER_TABLE_FIELD_ENABLE","enable");        //該資料表記錄"USER帳號是否啟用"的欄位名稱

define("USERLOGIN_TABLE","_user");                    //記錄USER table基本資訊的資料表名稱，去除掉PREFIX，以"_"開頭
define("USERLOGIN_TABLE_FIELD_ID","id");                //該資料表記錄"USER table編號"的欄位名稱
define("USERLOGIN_TABLE_FIELD_NAME","username");            //該資料表記錄"USER 名稱"的欄位名稱
define("USERLOGIN_TABLE_FIELD_IPMANAGE","ipmanage");    //該資料表記錄"USER是否控管可登入IP位置"的欄位名稱
define("USERLOGIN_TABLE_FIELD_ENABLE","enable");  

/**
 * admin及service後台登入程式檔名   
 */
define("ADMINPAGE","administrator.php");        //admin後台登入轉址程式檔名
define("SERVICEPAGE","loginadmin.php");        //service後台登入轉址程式檔名
define("USERLOGINPAGE","useradmin.php");//userlogin後台登入轉址程式檔名

/**
 * 站台用的參數
 */
define("DEFAULTLANGUAGE","tc");

/**
 * 系統版面設定參數
 */
define("DEFAULT_THEME","default");        //預設系統版面
define("ADMINGRAPHIC",0);                //ADMIN介面管理功能是否顯示圖 (1.顯示圖 0.顯示文字)
define("SERVICEGRAPHIC",0);                //SERVICE介面管理功能是否顯示圖 (1.顯示圖 0.顯示文字)
define("USELOGINGRAPHIC",0);                //USERLOGIN介面管理功能是否顯示圖 (1.顯示圖 0.顯示文字)
define("ADMINIMG", "images/admin/");    //ADMIN介面圖檔路徑
define("SERVICEIMG","images/service/");    //SERVICE介面圖檔路徑
define("USERLOGINIMG","images/userlogin/");    //userloginE介面圖檔路徑
define("ADMINFUNCPERROW",3);            //ADMIN介面管理功能每列顯示個數
define("SERVICEFUNCPERROW",3);            //SERVICE介面管理功能每列顯示個數
define("USERLOGINFUNCPERROW",3);            //userlogin介面管理功能每列顯示個數
?>
