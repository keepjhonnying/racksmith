<?php


class config
{
	var $db;
	var $rows=array();

	function __construct()
	{
		global $db;
		$this->db=$db;
	}


	function insertItem($name,$val)
	{
		$query = $this->db->prepare('SELECT value FROM config WHERE name=? LIMIT 1');
		$query->execute(array($name));
		$result = $query->fetch();
		if(!$result)
		{
                    $insert = $this->db->prepare('INSERT INTO config VALUES (?,?);');
                    $return= $insert->execute(array($name,$val));
                    return $return;
                }
		else
                    return 1;
	}
	
	function returnItem($valueName)
	{
		$query = $this->db->prepare('SELECT value FROM config WHERE name=? LIMIT 1');
		$query->execute(array($valueName));
		$result = $query->fetch();	
		if(!$result)
			return 0;
		else
			return $result['value'];
	}
	
	function setItem($name,$value)
	{
		$query = $this->db->prepare('UPDATE config set value=? WHERE name=?;');
		$query->execute(array($value,$name));
		$result = $query->fetch();	
		if(!$result)
			return 0;
		else
			return 1;
	}
}
?>