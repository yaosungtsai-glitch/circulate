  <?php
/*************************************
 * Company:
 * Program: dbpager.inc.php
 * Author:  Ken Tsai
 * Date:    from 2018.12.14
 *          2023.05.15 修正PHP 8.x可執行
 * Version: 2.0
 * Description:	DB.php連給include檔案
 *************************************/

include_once("mainfile.php");
include_once("DB.php");
include_once("Pager.php");
include_once("HTML/Table.php");

/*
$DSN_M=ADODATABASE."://".ADOUNAME.":".ADOPASS."@".$ADOHOST_MASTER."/".ADODBNAME;
$GLOBALS['dbh_m']=DB::connect($DSN_M);

for($i=rand(0,count($ADOHOST)-1),$j=1;$j<count($ADOHOST);$i--,$j++){
   $DSN=ADODATABASE."://".ADOUNAME.":".ADOPASS."@".$ADOHOST[$i]."/".ADODBNAME;
   $GLOBALS['dbh']==DB::connect($DSN);
   if(!PEAR::isError($dbh))
  	 break;
   elseif($i<=0) 
  	 $i=count($ADOHOST)-1;
}
*/
/*
** function  dbpage
** param @sql:select 語句, @colnames array 欄位名稱　 
** @fuclinks。當functions=1時才有意義輸入多維array , EX. fuclinks[$i]['label'], fuclinks[$i][link]，為0時傳入可為null
** @function是否有功能欄 1:是 0:否
** return @htmlstr ：DBPage htmlstr
*/
function dbpage(&$conn,$sql, $colnames, $fuclinks,$functions=1){
  
  //當切換模組時，清除session
  if($_GET['op'] != $_SESSION['op'] || $_GET['op2'] != $_SESSION['op2']){
    unset($_SESSION['sql']); //列表sql
    unset($_SESSION['loop']); //此頁面的列表數
    unset($_SESSION['entrant']); //分頁頁碼
  }
  if(!isset($_GET['entrant'])){
    //當重新產生列表時，不會有entrant字串
    //記錄傳入的sql
    $_SESSION['sql'][] = $sql; 
  }else{
    //進入分頁
    //判斷是否有切換分頁
    if($_GET['entrant'] != $_SESSION['entrant']){
      //將此頁頁碼存入session
      $_SESSION['entrant'] = $_GET['entrant'];
      //重新計算列表數，清除session
      unset($_SESSION['loop']);
    }
  }
  //計算此頁面有幾個列表
  $_SESSION['loop']++;
  //判斷此列表sql是否已存在session
  if(isset($_SESSION['sql'][$_SESSION['loop']-1])){
    //存在，使用session存入的sql
    $execsql = $_SESSION['sql'][$_SESSION['loop']-1];
  }else{
    //若不存在，使用傳入的sql
    $execsql = $sql;
    // unset($_SESSION['sql']);
  }

  //設定分頁連結
  //存op值
  if(isset( $_REQUEST['op'])){
    $_SESSION['op']=$_REQUEST['op'];
    $op = 'op='.$_REQUEST['op'];
  }

  if(isset( $_REQUEST['op2'])){
    $_SESSION['op2']=$_REQUEST['op2'];
    $op2 = '&op2='.$_REQUEST['op2'];
  }
  
  //判斷當前網址是否有op值 
  if(isset( $_GET['op'])&&isset( $_GET['op2']) ){
    $pagestr = 'entrant';
  }else{
    $pagestr = $op.$op2.'&entrant';
  }
  // echo '網址＝'.$pagestr.PHP_EOL;

  // $_SESSION['sql'] = $sql;
  // echo 'SQL='.$execsql.PHP_EOL;
  $rows=$conn->getAll($execsql);

  //$para設定值可看Pager.php註解
  $para = array(
    'itemData' => $rows,
    'perPage' => 15, //每頁列數
    'delta' => 10,   //頁碼顯示個數 for 'Jumping'-style a lower number is better
    'append' => true,
    //'separator' => ' | ',
    'clearIfVoid' => true, //只有一頁時不顯示頁碼(boolean) true:不顯示 flase:顯示
    'urlVar' => $pagestr, //分頁連結參數
    'useSessions' => true,
    // 'closeSession' => true,
    // 'mode'  => 'Sliding',    //try switching modes
    'mode'  => 'Jumping',
    'lastPageText' => _LAST_PAGE, //設定[首頁]名稱
    'firstPageText' => _FIRST_PAGE //設定[末頁]名稱
  );

  $pager = & Pager::factory($para);   
  $datas = $pager->getPageData();
 
  $links = $pager->getLinks();
  // var_dump($links);

  $selectBox = $pager->getPerPageSelectBox();
  //$table = new HTML_Table('align="center" class="pagerTable" '); //設定表格樣式
  //$table->addRow($colnames , array('bgcolor = "LightCoral" , align = "center" ')); //設定頂端列樣式
  $addColname=array($colnames);
   //print_r(count($addColname));exit;
  //print_r($addColname);exit;
  $addRows=array();
  foreach ($datas as $key => $value) {
    // $datas[$key][]="<a href='?op=test&$".$datas[$key][0]."'></a>";

    //$datas[$key][]="<a href='op=op'&id=".$datas[$key][0]."'>test</a>";
     
    if($functions==1)   
      $datas[$key][] = addFuncs($fuclinks,$datas[$key][0]);
    
    //Modify by Ken Tsai  
    //$table->addRow($datas[$key]); 
    array_push($addRows,$datas[$key]);
    //print_r($addRows); //exit;
  }
  //判斷是否有資料
  if(empty($rows)){
    //無資料，回應「目前沒有相關資料！」
    echo "<br><center><h3>"._NO_RECORD."</h3></center><br>";
  }else{
    $htmlstr="<p align=center>".$links['all']."</p>\n";
    //$htmlstr.=$table->toHTML()."\n";
    $htmlstr.=html_table($addColname,$addRows);
    $htmlstr.="<p align=center>".$links['all']."</p>\n";

    echo $htmlstr;
  }
  return count($rows);

}
/*
** function  addFuncs
** Description  列表功能顯示
** param @fuclinks: Array,
**          @link:此功能連結 
**          @label:此功能名稱 
**       @pkid:此列ID 
** return @linkstr ：DBPage htmlstr
*/
function addFuncs($fuclinks,$pkid){
  for($i = 0; $i < count($fuclinks); $i++){
        $linkstr .= "<a href='".$_SERVER['PHP_SELF']."?".$fuclinks[$i]['link']."&pkid=".$pkid."' >".$fuclinks[$i]['label']."</a>\n\n";
  }
  return $linkstr;
}


function html_table($cols, $rows, $style=" align='center' class='pagerTable'"){
  //print_r($rows);
  //echo $rows[4];
  $table="<table style=$style>\n";
  $table.="<tr>\n";
  for($i=0;$i<count($cols[0]);$i++){
      $table.="<td>\n";
      $table.=$cols[0][$i];
      $table.="</td>\n";
  }
  $table.="</tr>\n";
  for($i=0;$i<count($rows);$i++) {
    $table.="<tr>\n";
    for($j=0;$j<count($rows[$i]) && isset($rows[$i][$j]);$j++) {
      $table.="<td>\n";
      $table.=$rows[$i][$j];
      $table.="</td>\n";
    }
    $table.="</tr>\n";
  }
  
  $table.="</table>\n";

  return $table;
}




?>