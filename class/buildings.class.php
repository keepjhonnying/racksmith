<?php
class buildings
{
	var $db;
	
	function __construct()
	{
		global $db;
		$this->db=$db;		
	}

	var $rows;
	
	function cacheAll()
	{
		$this->getAll();
	}

	function getAll()
	{
		if (count($this->rows) <= 0)
		{
			$return = array();
			
			$this->db->query('SELECT buildingID,name,description,notes,ownerID FROM buildings ORDER BY name');
			$result = $this->db->fetchAll();
			
			foreach($result as $building)
			{
				$newBuilding = new building;
				$newBuilding->buildingID = $building['buildingID'];
				$newBuilding->name = $building['name'];
				$newBuilding->description = $building['description'];
				$newBuilding->notes = $building['notes'];
				$newBuilding->ownerID = $building['ownerID'];
				array_push($return, $newBuilding);
			}
			$this->rows = $return;
			return $return;		
		}
		else
			return $this->rows;
	}
	
	function returnCount()
	{
		if (count($this->rows) <= 0)
		{
			$return = array();
			$this->db->query('SELECT count(buildingID) FROM buildings');
			$result = $this->db->fetchAll();
			return $result[0]['count(buildingID)'];	
		}
		else
			return count($this->rows);
	}
	
	function getByID($BuildingID)
	{
		if (count($this->rows) <= 0)
		{
			$return = array();
			
			$query = $this->db->prepare('SELECT buildingID,name,description,notes,ownerID FROM buildings WHERE buildingID = ?');
			$query->execute(array($this->buildingID));
			$result = $query->fetchAll();
			
			foreach($result as $building)
			{
				$newBuilding = new building;
				$newBuilding->buildingID = $building['buildingID'];
				$newBuilding->name = $building['name'];
				$newBuilding->description = $building['description'];
				$newBuilding->notes = $building['notes'];
				$newBuilding->ownerID = $building['ownerID'];
				array_push($return, $newBuilding);
			}
			return $newBuilding;		
		}
		else
		{
			foreach ($this->rows as $building)
			{
				if ($building->buildingID == $BuildingID)
					return $building;
			}
		}
	}

	function insert($newBuilding)
	{
		$this->db->prepare("INSERT INTO buildings (name,description,notes,ownerID) VALUES (?,?,?,?)");
		$created = $this->db->execute(array($newBuilding->name,$newBuilding->description,$newBuilding->notes,$newBuilding->ownerID));
		
		$this->db->query("SELECT LAST_INSERT_ID()");
		$result = $this->db->fetchAll();

		// A building should automatically generate a ground level.		
		foreach($result as $building)
		{
			$newFloor = new floor;
			$newFloor->name = "G";
			$newFloor->ownerID = 1;
			$newFloor->buildingID = $building['LAST_INSERT_ID()'];
			$floors = new floors;
			$floors->insert($newFloor);
		}

		if ($created)
			return $result[0]['LAST_INSERT_ID()'];
		else
			return $created;
	}

	function update($newBuilding)
	{
		$this->db->prepare("UPDATE buildings SET name = ?,description = ?,notes = ?,ownerID = ? WHERE buildingID = ?;");
		$this->db->execute(array($newBuilding->name,$newBuilding->description,$newBuilding->notes,$newBuilding->ownerID,$newBuilding->buildingID));
	}

	function delete($buildingID)
	{
		$this->db->prepare("DELETE FROM layoutitems WHERE itemID = ? AND parentType = 'Building';");
		$this->db->execute(array($buildingID));
		
		$this->db->prepare("DELETE FROM buildings WHERE buildingID=?;");
		$this->db->execute(array($buildingID));
	}

	
	/* A "ligher" function to return a name for the hover effect on the main page */
	function returnName($buildingID)
	{
		$this->db->prepare("SELECT name from buildings WHERE buildingID = ?");
		$this->db->execute(array($buildingID));
		$result = $this->db->fetch();
		return $result['name'];
	
	}
};


class building
{
	var $db;
	
	var $buildingID;
	var $name;
	var $description;
	var $notes;
	var $ownerID;
	
	function __construct($ByBuildingID='0')
	{
		global $db;
		$this->db=$db;	

		$this->buildingID = $ByBuildingID;
		$this->name = "";
		$this->description = "";
		$this->notes = "";
		$this->ownerID = 0;

		if (is_numeric($this->buildingID) && $this->buildingID > 0)
		{		
			$query = $this->db->prepare('SELECT buildingID,name,description,notes,ownerID FROM buildings WHERE buildingID = ?');
			$query->execute(array($this->buildingID));
			$result = $query->fetchAll();

			foreach($result as $building)
			{
				$this->buildingID = $building['buildingID'];
				$this->name = $building['name'];
				$this->description = $building['description'];
				$this->notes = $building['notes'];
				$this->ownerID = $building['ownerID'];
			}
		}
	}


	function owner()
	{
		$newOwner = new owner;
		if ($this->ownerID > 0)
		{	
			$newOwner = new owner($this->ownerID);
		}
		return $newOwner;
	}
};
?>