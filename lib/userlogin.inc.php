<?php
/*********************************************/
/*Company:                                   */
/*Author :Ken Tsai                           */
/*Date   : from 20080620                           */
/*Description: userlogin管理後台共用函式     */
/*********************************************/
class User_Login
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
	//function User_Login(&$adodb,$prefix)
	function __construct(&$adodb,$prefix)
	{
		$this->db = $adodb;
		$this->prefix = $prefix;
		$this->tUser = $this->prefix."_useradmin";
		$this->tPermit = $this->prefix."_userpermit";
		$this->tFunctions = $this->prefix."_userfunction";
		$this->cookieID = "useradmin";
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
		$rs = $GLOBALS['adoconn']->Execute($select);
		//比對成功, 將 isLogin 設為 TRUE, 並讀入 User Object
		if($rs and !$rs->EOF) {
			$this->isLogin = TRUE;
			$this->objUser = $rs->FetchObject(FALSE);
			$rs->Close();
			//若 cookie 不存在, 則寫入 cookie
			if(!isset($_SESSION['USERID'])) {
				$_SESSION['USERID']=$this->objUser->sid;
			}
		}
		return $this->isLogin;
	}

	function checkLogin()
	{
		$this->isLogin = FALSE;
		$this->objUser = NULL;
		//從 cookie 讀取 userID, 並進行比對
		if(isset($_SESSION['USERID'])) {
    		if(!empty($_SESSION['USERID'])) {
    			$selectUser = "SELECT * FROM $this->tUser WHERE enable=1"
    			." AND sid=".$_SESSION['USERID'];
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

	function writeLog($title)
	{
		if(!empty($title)) {
			$loginname = $this->objUser->said;
			$tLog = $this->prefix."_useradmin_login";
			$dt = date("Y-m-d H:i:s");
			$ip = getenv("REMOTE_ADDR");
			$insert = "INSERT INTO $tLog (loginname,time,op,ip) VALUES"
			." ('$loginname','$dt','$title','$ip')";
			@$this->db->Execute($insert);
		}
	}

	function logout()
	{
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
	 * 取得使用者帳號權限
	 * @param sid 使用者帳號
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
