<?php
class cables
{
	var $db;
	var $rows=array();

	function __construct()
	{
		global $db;
		$this->db=$db;
	}
	
	function cacheAll()
	{
		$this->getAll();
	}

	function getAll()
	{
		if (count($this->rows) <= 0)
		{
			$query = $this->db->prepare('SELECT * FROM cables');
			$query->execute();
			$result = $query->fetchAll();
			
			$return = array();
		
			foreach($result as $cable)
			{
				$newCable = new Cable;
				$newCable->CableID=$cable->CableID;
				$newCable->barcode=$cable->barcode;
				$newCable->cableTypeID=$cable->cableTypeID;

				array_push($return, $newCable);
			}
			$this->rows = $return;
			return $return;	
		}
		else
			return $this->rows;
	}

	function getByBarcode($barcode)
	{
		if (count($this->rows) > 0)
		{
			$return = array();
			foreach($this->rows as $cable)
			{
				if ($cable->barcode == $barcode)
				{				
					$newCable = new Cable;
					$newCable->CableID=$cable->CableID;
					$newCable->barcode=$cable->barcode;
					$newCable->cableTypeID=$cable->cableTypeID;

					array_push($return, $newCable);
				}
			}
			return $return;	
		}
		else
		{
			$query = $this->db->prepare('SELECT * FROM cables WHERE barcode = ?');
			$query->execute(array($barcode));
			$result = $query->fetchAll();

			$return = array();
			foreach($result as $cable)
			{
				$newCable = new cable;
				$newCable->cableID=$cable['cableID'];
				$newCable->barcode=$cable['barcode'];
				$newCable->cableTypeID=$cable['cabeTypeID'];
				array_push($return, $newCable);
			}
			return $return;	
		}
	}
	
	// this search will not return the standard object
	// instead it will be an array which can easily be json encoded
	function searchbarcode($barcode)
	{
		$query = $this->db->prepare('SELECT * FROM cables WHERE barcode LIKE ?');
		$this->db->execute(array('%'.$barcode.'%'));
		$results = $query->fetchAll();
	
		$ports = new ports;
	
		if($results)
		{
			$i=0;
			foreach($results as $cable)
			{
				
				
				$cableType=new cableType($cable['cableTypeID']);
				$cablesFound[$i]['cableType']=stripslashes($cableType->name);
				$cablesFound[$i]['barcode']=$cable['barcode'];
				
				// get all ports a cable is attached to
				$attachedDevices = $ports->getBycableID($cable['cableID']);
				// check the first possible endpoint to see if it exists
				if(isset($attachedDevices[0]->deviceID) && $attachedDevices[0]->deviceID >0 )
					$cablesFound[$i]['device1']=$attachedDevices[0]->deviceName." -> " . $attachedDevices[0]->label;
				else
					$cablesFound[$i]['device1']='';
					
				// check a possible second endpoint
				if(isset($attachedDevices[1]->deviceName) && $attachedDevices[1]->deviceName != '')
					$cablesFound[$i]['device2']=$attachedDevices[1]->deviceName." -> " . $attachedDevices[1]->label;
				else
					$cablesFound[$i]['device2']='';
					
				// check if the cable has two endpoints and determine connected
				if($cablesFound[$i]['device2'] && $cablesFound[$i]['device2'])
					$cablesFound[$i]['connected']="Yes";
				else
					$cablesFound[$i]['connected']="No";
				$i++;
			}
			return $cablesFound;
		}
		else
			return array("no_cables");
	}
	
	
	function genBarcode()
	{	
		$barcode = mt_rand(10000, 9999999);
		$checkLimit=0;
		while($this->searchbarcode($barcode) != array("no_cables"))
		{
			$barcode = mt_rand(10000, 9999999);
			$checkLimit++;
			if($checkLimit==5)
			{
				$barcode=0;
				break;
			}
		}	
		return $barcode;
	}

	function insert($new)
	{
		$this->db->prepare("INSERT INTO cables (cableID,barcode,cableTypeID) VALUES ('',?,?);");
		$created = $this->db->execute(array($new->barcode,$new->cableTypeID));
		
		$this->db->query("SELECT LAST_INSERT_ID()");
		$result = $this->db->fetchAll();
		
		if ($created)
			return $result[0]['LAST_INSERT_ID()'];
		else
			return $created;
	}
	


	// NEEDS COMPLETING
	function update($new)
	{
		$this->db->prepare("UPDATE cables SET barcode=? WHERE cableID = ?;");
		return $this->db->execute(array($new->barcode,$new->cableID));
	}



	function delete($cable)
	{
		if(is_numeric($cable))
			$cableID=$cable;
		else
			$cableID=$cable->cableID;
			
		// Delete the cable from the database
		$this->db->prepare("DELETE FROM cables WHERE cableID = ?;");
		$this->db->execute(array($cableID));
	}
}



class cable
{
	var $db;
	var $cableID='';
	var $barcode='';
	var $cableTypeID='';

	function __construct($byCableID='0')
	{
		global $db;
		$this->db=$db;	
		$this->cableID=$byCableID;

		if (is_numeric($this->cableID) && $this->cableID > 0)
		{		
			$query = $this->db->prepare('SELECT * FROM cables WHERE cableID = ?');
			$query->execute(array($this->cableID));
			$result = $query->fetchAll();

			foreach($result as $cable)
			{
				$this->cableID=$cable['cableID'];
				$this->barcode=$cable['barcode'];
				$this->cableTypeID=$cable['cableTypeID'];
			}
		}
	}
	
	
};
?>