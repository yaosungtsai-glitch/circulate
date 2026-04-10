<?php
/**
 * Company:
 * Program: lang-tc.php
 * Author:  Ken Tsai
 * Date:    from 2002.01.25
 * Version: 2.0
 * Description: 系統共用語言定義 - 繁体中文
 */
/*****************************************************/
/* 資料庫語系設定　　　　　　　　　　　　　　        */
/*****************************************************/
define("DBTABLE_FORMAT","collate utf8mb4_general_ci");
define("DB_SET_CHARACTER_SET","utf8");

/*****************************************************/
/* Charset for META Tags                             */
/*****************************************************/
define("_CHARSET","UTF-8");

/**********************************************
共用
**********************************************/
define("_LOGIN","登入");
define("_CREATEUSER","建立人員");
define("_UPDATEUSER","更新人員");
define("_OK","確定");
define("_CLEAR","清除");
define("_PAGE_COUNT","頁次");
define("_FIRST_PAGE","首頁");
define("_PREV_PAGE","前頁");
define("_NEXT_PAGE","次頁");
define("_LAST_PAGE","末頁");
define("_NO_RECORD","目前沒有相關資料！");
define("_EDIT","修改");
define("_DELETE","刪除");
define("_YES","是");
define("_NO","否");
define("_OR","或");
define("_STATUS","狀態");
define("_ENABLE","啟動");
define("_DISABLE","取消");
define("_DETAIL","詳細");
define("_BACKTO","返回");
define("_PREVIOUS","上一頁");
define("_GOBACK","[ <a href=\"javascript:history.go(-1)\">回到上一頁</a> ]");
define("_NOEDIT_GOBACK","<a href=\"javascript:history.go(-1)\">不儲存回上一頁</a> ");
define("_EMPTY"," ***** 資料不齊全 ***** <BR><BR>請回<a href=\"javascript:history.go(-1)\">上一頁</a>重新輸入 ......");
define("_SERVICE_ADMIN_LOGIN","使用者登入");
define("_EMAIL","電子郵件");
define("_PASSWORD","密碼");
define("_WAITINGMSG","資料處理中, 請稍待!");  //2007/12/13
define("_FUNCTIONS","功能");
define("_ID","編號");
define("_SAVE","儲存");
define("_SEARCH", "查詢"); 
define("_GO", "前往"); 
define("_VIEW", "查看"); 
define("_SEND","送出");   
define("_PREVIEW","預覽");
define("_SAVECHANGES","儲存修改");
define("_SUN","星期日");
define("_MON","星期一");
define("_TUE","星期二");
define("_WED","星期三");
define("_THU","星期四");
define("_FRI","星期五");
define("_SAT","星期六");
define("_CREATETIME","建立時間"); 
define("_UPDATETIME","更新時間"); 
define("_REMARK","備註"); 
define("_UPLOAD","上傳"); 
define("_REUPLOAD","重新上傳");
define("_DOWNLOAD","下載"); 
define("_YEAR","年"); 
define("_MONTH","月"); 
define("_DAY","日"); 

define("_FOOTERTXT", "本系統支援HTML5瀏覽器  最佳瀏覽器：<a href='https://www.google.com.tw/chrome/browser/desktop/' target=_blank >Chrome</a>、<a href='https://www.mozilla.org/zh-TW/firefox/new/' target=_blank >Firefox</a>");
define("_NOHAVEPARAMS", "參數不完整");
define("_PARAMSFORMATERROR", "參數格式錯誤");

define("_MEMFLUSHSUCCESS","儲存成功");
define("_MEMFLUSHFAIL","memcache 更新失敗，請重新操作");
/**********************************************
後台 登入畫面
***********************************************/
define("_ADMIN_LOGIN","管理員登入");
define("_ADMIN_AID","管理員登入帳號");
define("_ADMIN_PASSWORD","管理員登入密碼");
define("_ADMINCHECKID","請輸入左方檢查碼");
define("_ADMIN_LOGOUT","登出 / 離開");
define("_YOUARELOGGEDOUT","您已經登出");

/**********************************************
站台 登入畫面
***********************************************/
define("_SAID","登入帳號");
define("_ADCHECK","AD 帳號登入");
/***********************************************
header.php 使用
***********************************************/
define("_TITLE","管理系統");//用於網項TITLE
/***********************************************
footer.php 使用
***********************************************/
define("_POLICY_L","服務及隱私權條款");
define("_POLICY_E","Rule and Privacy Policy");

/**********************************************
後台 功能名稱
***********************************************/
define("_AUTHORSADMIN","管理員管理");
define("_IPMANAGEADMIN","登入IP管理");
define("_STOREADMIN","商店帳號管理");
define("_MERCHANTSADMIN","特約商店管理");
define("_SHORTURL","短網址管理");
define("_MEMBERSADMIN","會友資訊管理");
define("_USERADMIN","會員功能管理");



define("_RULE","會員章程");
define("_VIPUSERDATAEDIT","付費會員管理");
/**********************************************
administrator.php 使用
***********************************************/
define("_HIDDENMENU","隱藏選單列");
define("_OPENMENU","展開選單列");

/**********************************************
22縣市
***********************************************/
define("_CITY01","基隆市");
define("_CITY02","台北市");
define("_CITY03","新北市");
define("_CITY04","桃園市");
define("_CITY05","新竹市");
define("_CITY06","新竹縣");
define("_CITY07","苗栗縣");
define("_CITY08","台中市");
define("_CITY09","彰化縣");
define("_CITY10","南投縣");
define("_CITY11","雲林縣");
define("_CITY12","嘉義市");
define("_CITY13","嘉義縣");
define("_CITY14","台南市");
define("_CITY15","高雄市");
define("_CITY16","屏東縣");
define("_CITY17","台東縣");
define("_CITY18","花蓮縣");
define("_CITY19","宜蘭縣");
define("_CITY20","澎湖縣");
define("_CITY21","金門縣");
define("_CITY22","連江縣");

?>
