<?php
/******************************************
 * Program:index.php
 * Function:  短網址 轉址主程式
 * Author: Ken Tsai
 * Date: 2023/05/12
 * ****************************************/

include_once('mainfile.php');

$f3->route('PUT /',
   function($f3) {
       //header('Content-Type: application/json');
       echo $f3->get('BODY');
    }
);
/*
$f3->route('GET /',
    function($f3) {
        echo 'Hello, world!';
        #$f3->reroute('https://www.teamplus.tech/');
    }
);

$f3->route('GET /about',
    function($f3) {
        $rows=$GLOBALS['db']->exec("SELECT id,base_url FROM ".$f3->dbprefix."_shorturl_base where enable='1' ORDER BY id DESC");
        echo count($rows); 
        foreach($rows as $row)
        echo $row['base_url'];
    }
);
*/
$f3->route('GET /@querystr',
    function($f3) {
        $sql="SELECT id,target_url FROM ".$f3->dbprefix."_shorturl where enable='1' and querystr='".$f3->PARAMS['querystr']."'";
        $rows=$GLOBALS['db']->exec($sql);
        foreach($rows as $row)
        $f3->reroute($row['target_url']);
    }
);

/*
$f3->route('GET /api/user/@id',
    function($f3) {

    	$id = $f3->get('PARAMS.id');

    	header('Content-Type: application/json');
    	$data = array('id'=>$id, 'name'=>'Taras', 'lastname'=>'Shevchenko');
    	echo json_encode($data);
    }
);
*/

/*
$f3->route('PUT /',
    function($f3) {
        
       $jsonarr=json_decode($f3->get('BODY'));
       $quertstr=$jsonarr['quertstr'];
       $redirecturl=$jsonarr['redirecturl'];
       if(isset($jsonarr['baseurl']))
         $baseurl=$jsonarr['baseurl'];
       else
         $baseurl=1;
       $GLOBALS['db']->exec("insert into ".$f3->dbprefix."_shorturl(baseurl,vd_name,urlstr), values(?,?,?,?)",array('1',$quertstr,$redirecturl,'1'));
       $res['status']='OK';
       $res['code']='100';
       echo json_encode($res);

       $jsonarr=json_decode($f3->get('BODY'));
       echo json_encode($jsonarr);
    }
);*/

$f3->run();

?>