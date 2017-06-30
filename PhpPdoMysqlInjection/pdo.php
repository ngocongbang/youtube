<?php
// Mysql database
$mdatabase = 'sun';
$muser = 'root';
$mpass = 'toor';
$mhost = 'localhost';
$mport = 3306;

class PDO
{
	public $db;
	function __construct()
	{
		// db connection
		$this->db = $this->Conn();
		// clear POST and GET
		$this->Clear();
		$this->CreateTable();
	}

	function CreateTable(){
		$sql = "
		CREATE TABLE IF NOT EXISTS `prod` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `typ` int(11) NOT NULL DEFAULT '1',
		  `nazwa` varchar(250) NOT NULL,
		  `cena` decimal(10,2) NOT NULL DEFAULT '0.00',
		  `opis` text NOT NULL,
		  `time` bigint(20) NOT NULL,
		  `active` int(2) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		";
		$this->db->query($sql);
	}

	// PDO
	function Conn(){
		// load from global variables
		global $mhost,$mport,$muser,$mpass,$mdatabase;
		$con = new PDO('mysql:host='.$mhost.';port='.$mport.';dbname='.$mdatabase.';charset=utf8', $muser, $mpass);
		$con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$con->setAttribute(PDO::ATTR_PERSISTENT, false);
		$con->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
		return $con;
	}

	// Sql injection prevent
	function Clear(){
		foreach ($_GET as $key => $val) { 
	  		$val = $this->clearPHP($val);
	      	if (is_string($val)) { 
	        	$_GET[$key] = htmlentities($val, ENT_QUOTES, 'UTF-8'); 
	      	} else if (is_array($val)) { 
	        	$_GET[$key] = Clear($val); 
	        } 
	  	} 
	  	foreach ($_POST as $key => $val) { 
	  		$val = $this->clearPHP($val);
	      	if (is_string($val)) { 
	        	$_POST[$key] = htmlentities($val, ENT_QUOTES, 'UTF-8'); 
	      	} else if (is_array($val)) { 
	        	$_POST[$key] = Clear($val); 
	        } 
	  	} 
	}

	function clearPHP($php){				
		/* return preg_replace('/^<\?php(.*)(\?>)?$/s', '$1', $php); */
		$s = str_replace('<?php', '', $php);
		$s = str_replace('<?', '', $s);
		$s = str_replace('<%', '', $s);
		$s = str_replace('?>', '', $s);
		return $s = str_replace('<script', '', $s);		
	}

	function addProdukt($nazwa,$cena,$opis){		
		try{
			$id = 0;				
			$r = $this->db->query("INSERT INTO prod(nazwa,cena,opis) VALUES('$nazwa',$cena,'$opis')");
			$id = $this->db->lastInsertId();						
			return $id;
		}catch(Exception $e){
			return 0;
		}		
	}

	function getProdukt(){		
		try{
			$r = $this->db->query("SELECT * FROM prod ORDER BY id DESC LIMIT 10");
			$rows = $r->fetchAll(PDO::FETCH_ASSOC);			
		}catch(Exception $e){
			return $rows;
		}
		return $rows;
	}

	function getProduktID($id){		
		try{
			$id = (int)$id;			
			$r = $this->db->query("SELECT * FROM prod WHERE id = $id AND active = 1");
			$rows = $r->fetchAll(PDO::FETCH_ASSOC);			
		}catch(Exception $e){
			return $rows;
		}
		return $rows;
	}

	function delProduct($id){		
		try{
			$id = (int)$id;
			$r = $this->db->query("UPDATE prod SET active = 0 WHERE id = $id");
			$rows = $r->fetchAll(PDO::FETCH_ASSOC);
			return 1;
		}catch(Exception $e){
			return 0;
		}		
	}

	function validEmail($email){
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return 1;
		} else {
			return 0;
		}
	}

	function mail($to, $from, $subject, $msg, $name)
   	{
   		ini_set("sendmail_from", $from);
      	$name = "=?UTF-8?B?".base64_encode($name)."?=";
      	$subject = "=?UTF-8?B?".base64_encode($subject)."?=";      	
      	$headers = "From: $name <$from>" . "\r\n" . "MIME-Version: 1.0" . "\r\n" . "Content-type: text/html; charset=UTF-8" . "\r\n" . "Reply-to: <$from>" . "\r\n";
    	return mail($to, $subject, $msg , $headers);
   	}

	function IP() {
	    $ipa = '';
	    if (isset($_SERVER['HTTP_CLIENT_IP']))
	        $ipa = $_SERVER['HTTP_CLIENT_IP'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        $ipa = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED']))
	        $ipa = $_SERVER['HTTP_X_FORWARDED'];
	    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	        $ipa = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_FORWARDED']))
	        $ipa = $_SERVER['HTTP_FORWARDED'];
	    else if(isset($_SERVER['REMOTE_ADDR']))
	        $ipa = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipa = $_SERVER['REMOTE_ADDR'];
	    return $ipa;
	}
} // end class
?>
