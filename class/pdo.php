<?php
class DB
{
	public $instance;
	public $sqlType='mysql';
	private $manualHandle='';	// Used for sqlLite 
	
	private $dbHandle;
	private $statementHandle;

	public function __construct()
	{
		$this->connect();
	}
 
 
	public function connect()
	{
                
                if (file_exists(RACKSMITH_PATH."class/config.inc.php"))
                {
                    include RACKSMITH_PATH."class/config.inc.php";
                }
		if(!isset($host) || !isset($user) || !isset($dbname))
		{
			include "theme/top.php";
			echo "<center><h1 style='color: #2e2e2e;'>Not long now</h1>
			<div style=\"background-color: #c9c9c9;padding: 10px; height: 160px; width: 650px;border: 1px solid #000000;text-align: left;\" >
			<strong>I can't find an installation</strong>
			<br/>
			We couldn't load any usable settings, this could be because the installation isn't complete or there is no access to the config file.
			
			<br/><br/><p>
			<a href='install.php' ><b>Click here to start the installation</b></a>
			</p>or<p>
			<a href='http://www.racksmith.net/forum/' target='_new' >Ask the Racksmith Community</a>
			</p>
			</center>";
			include "theme/base.php";
			exit();
		}
		try 
		{
			if($this->sqlType=='mysql')
				$this->dbHandle = new PDO("mysql:dbname=".$dbname.";host=".$host, $user, $pass);
				
			if($this->sqlType=='sqlite')
				$this->dbHandle = new PDO("sqlite:/www/build/class/test.db");
				

		}
		catch(PDOException $e)
		{
			include "theme/top.php";
			echo "<center><h1>RackSmith Error</h1><b>The application has encounted an error and must exit.<br/>The following 
details were returned.</b><br/><br/>
			<div style=\"background-color: #c9c9c9;padding: 5px; height: 200px; width: 650px;border: 1px solid #000000;\" >
			<B>DB ERROR</b><br/>".$e->getMessage()."</div><br/>Please contact <a href=\"http://www.racksmith.net\" 
target=\"_new\" >http://www.racksmith.net</a> for support.</center>";
			include "theme/base.php";
			exit();
		}
	}
	
	
	public function prepare($sql,$attributes='0')
	{
		unset($this->statementHandle);
		
		if(!$attributes)
			$this->statementHandle = $this->dbHandle->prepare($sql);
		else
			$this->statementHandle = $this->dbHandle->prepare($sql,$attributes);
		return $this->statementHandle;
	}
	
	public function bindParam($id,$value,$attrib='')
	{
		$this->statementHandle->bindParam($id,$value,$attrib);	
	}
	
	
	
	public function execute($params='')
	{
		return $this->statementHandle->execute($params);
	}
	
	public function exec($sql)
	{
		return $this->dbHandle->exec($sql);
		//  or die(print_r($db->errorInfo(), true))
	}
	
	public function query($sql)
	{
		unset($this->statementHandle);
		$this->statementHandle = $this->dbHandle->query($sql);
		return $this->statementHandle;
	}
	
	public function fetchAll()
	{
		return $this->statementHandle->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	public function fetch()
	{
		return $this->statementHandle->fetch(PDO::FETCH_ASSOC);
	}		
	
	public function errorInfo()
	{
		return $this->dbHandle->errorInfo();
	}	
	

}
?>