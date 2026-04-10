<?php
/*********************************************/
/*Company:                                   */
/*Author :Ken Tsai                           */
/*Date   : from 2002.08.13                           */
/*Description: login管理後台共用函式         */
/*********************************************/


class UserLogin
{
	/*
	var $db;
	var $prefix;
	var $isLogin;
	var $cookieID;
	var $objUser;
	var $tUser;
	var $tPermit;
	var $tFunctions;
    */
	public $db;
	public $prefix;
	public $isLogin;
	public $cookieID;
	public $objUser;
	public $tUser;
	public $tPermit;
	public $tFunctions;
	
	//function UserLogin(&$db,$prefix)
	function __construct(&$adodb,$prefix)
	{
		$this->db = $adodb;
		$this->prefix = $prefix;
		$this->tUser = $this->prefix."_storeadmin";
		$this->tPermit = $this->prefix."_storepermit";
		$this->tFunctions = $this->prefix."_storefunction";
		$this->cookieID = "storeadmin";
		$this->isLogin = FALSE;
		$this->objUser = NULL;
	}

	function login($user,$pass)
	{
		$this->isLogin = FALSE;
		$this->objUser = NULL;
		if(empty($user) or empty($pass)) return FALSE;
		$select = "SELECT * FROM $this->tUser WHERE enable=1"
		." AND said='$user' AND sapw='$pass'";
		$rs = $this->db->Execute($select);
		//$rs = $GLOBALS['adoconn']->Execute($select);
		//比對成功, 將 isLogin 設為 TRUE, 並讀入 User Object
		if($rs and !$rs->EOF) {
			$this->isLogin = TRUE;
			$this->objUser = $rs->FetchObject(FALSE);
			$rs->Close();
			//若 cookie 不存在, 則寫入 cookie
			if(!isset($_SESSION['UID'])) {
//				$cookie = "UID=".$this->objUser->sid;
//				$cookie = base64_encode($cookie);
//				setcookie($this->cookieID,$cookie);
				$_SESSION['UID']=$this->objUser->sid;
			}
		}
		return $this->isLogin;
	}

	function checkLogin()
	{
		$this->isLogin = FALSE;
		$this->objUser = NULL;
		//從 cookie 讀取 userID, 並進行比對
		if(isset($_SESSION['UID'])) {
//			$cookie = base64_decode($_COOKIE[$this->cookieID]);
//    		parse_str($cookie,$userInfo);
    		if(!empty($_SESSION['UID'])) {
    			$selectUser = "SELECT * FROM $this->tUser WHERE enable=1"
    			." AND sid=".$_SESSION['UID'];
    			$rs = $this->db->Execute($selectUser);
    			//比對成功, 將 isLogin 設為 TRUE, 並讀入 User Object
    			if($rs and !$rs->EOF) {
    				$this->isLogin = TRUE;
    				$this->objUser = $rs->FetchObject(FALSE);
    				$rs->Close();
				}
    		} else {
    			$this->printLogout();
    			exit();
    		}
    	}
		return $this->isLogin;
	}
/*
	function writeLog($title)
	{
		if(!empty($title)) {
			$loginname = $this->objUser->said;
			$tLog = $this->prefix."_storeadmin_login";
			$dt = date("Y-m-d H:i:s");
			$ip = getenv("REMOTE_ADDR");
			$insert = "INSERT INTO $tLog (loginname,time,op,ip) VALUES"
			." ('$loginname','$dt','$title','$ip')";
			@$this->db->Execute($insert);
		}
	}
*/
	function logout()
	{
		//setcookie($this->cookieID);
		session_unset();
		$this->isLogin = FALSE;
		$this->objUser = NULL;
	}

	function printLogout()
	{
		echo "<html>\n"
        ."<title>INTRUDER ALERT!!!</title>\n"
        ."<body bgcolor='#FFFFFF' text='#000000'>\n\n<br><br><br>\n\n"
        ."<center><img src='images/eyes.gif' border='0'><br><br>\n"
        ."<font face='Verdana' size='+4'><b>Get Out!</b></font></center>\n"
        ."</body>\n"
        ."</html>\n";
	}

	/**
	 * 取得商店帳號權限
	 * @param sid 商店帳號
	 * @return 權限陣列
	 */
	function getPermit()
	{
		$permit = array();
		$selectPermit = "SELECT sfid,sfname FROM $this->tPermit AS tp,"
		."$this->tFunctions AS tf WHERE tp.sid=".$this->objUser->sid
		." AND tp.fid=tf.id";
		$rs = $this->db->Execute($selectPermit);
		if($rs) {
			while(!$rs->EOF) {
				$permit[] = $rs->fields["sfid"];
				$rs->MoveNext();
			}
			$rs->Close();
		}
		return $permit;
	}

	function getStoreFunction($fid)
	{
		$obj = NULL;
		$selectFunctions = "SELECT * FROM $this->tFunction WHERE"
		." enable=1 AND id=$fid";
		$rs = $this->db->Execute($selectFunctions);
		if($rs) {
			$obj = $rs->FetchObject(FALSE);
			$rs->Close();
		}
		return $obj;
	}
}

?>
