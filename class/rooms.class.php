<?php
class rooms
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
			$query = $this->db->prepare('SELECT * FROM rooms');
			$query->execute();
			$result = $query->fetchAll();
			
			$return = array();
			foreach($result as $room)
			{
                            $newRoom = new room();
                            $newRoom->roomID = $room['roomID'];
                            $newRoom->floorID = $room['floorID'];
                            $newRoom->buildingID = $room['buildingID'];
                            $newRoom->ownerID = $room['ownerID'];
                            $newRoom->name = $room['name'];
                            $newRoom->color = $room['color'];
                            $newRoom->notes = $room['notes'];
                            $newRoom->revisionID = $room['revisionID'];
                            array_push($return, $newRoom);
			}
			$this->rows = $return;
			return $return;	
		}
		else
			return $this->rows;
	}


	function getByID($roomID)
	{
		if (count($this->rows) > 0)
		{
			$return = array();
			foreach($this->rows as $room)
			{
				if ($room->roomID == $roomID)
				{
					array_push($return, $room);
				}
			}
			return $return;	
		}
		else
		{
			$query = $this->db->prepare('SELECT * FROM rooms WHERE roomID = ?');
			$query->execute(array($roomID));
			$result = $query->fetchAll();
			
			$return = array();
			foreach($result as $room)
			{
				$newRoom = new room;
				$newRoom->roomID = $room['roomID'];
				$newRoom->floorID = $room['floorID'];
				$newRoom->buildingID = $room['buildingID'];
				$newRoom->name = $room['name'];
				$newRoom->color = $room['color'];
				$newRoom->ownerID = $room['ownerID'];
				$newRoom->notes = $room['notes'];
				$newRoom->revisionID = $room['revisionID'];
				array_push($return, $newRoom);
			}
			return $return;	
		}
	}

	function getByFloor($floorID)
	{
		if (count($this->rows) > 0)
		{
			$return = array();
			foreach($this->rows as $room)
			{
				if ($room->floorID == $floorID)
				{
					array_push($return, $room);
				}
			}
			return $return;	
		}
		else
		{
			$query = $this->db->prepare('SELECT * FROM rooms WHERE floorID = ?');
			$query->execute(array($floorID));
			$result = $query->fetchAll();
			
			$return = array();
			foreach($result as $room)
			{
				$newRoom = new room;
				$newRoom->roomID = $room['roomID'];
				$newRoom->floorID = $room['floorID'];
				$newRoom->buildingID = $room['buildingID'];
				$newRoom->name = $room['name'];
				$newRoom->ownerID = $room['ownerID'];
				$newRoom->color = $room['color'];
				$newRoom->notes = $room['notes'];
				$newRoom->revisionID = $room['revisionID'];
				array_push($return, $newRoom);
			}
			return $return;	
		}
	}

	function insert($newRoom)
	{
		$this->db->prepare("INSERT INTO rooms (roomID,floorID,buildingID,ownerID,name,color,notes,revisionID) VALUES (?,?,?,?,?,?,?,?);");
		$created = $this->db->execute(array($newRoom->roomID,$newRoom->floorID,$newRoom->buildingID,$newRoom->ownerID,$newRoom->name,$newRoom->color,$newRoom->notes,$newRoom->revisionID));

		$this->db->query("SELECT LAST_INSERT_ID()");
		$result = $this->db->fetchAll();
		
		if ($created)
                    return $result[0]['LAST_INSERT_ID()'];
		else
                    return $created;
	}

	function update($newRoom)
	{
		$layoutItems = new layoutItems;
		$layoutItems->updateNames($newRoom->roomID,$newRoom->name);
		
		$this->db->prepare("UPDATE rooms SET floorID = ?, buildingID = ?, ownerID = ?,name = ?,color = ?,notes = ?,revisionID = ? WHERE roomID = ?;");
		return $this->db->execute(array($newRoom->floorID,$newRoom->buildingID,$newRoom->ownerID,$newRoom->name,$newRoom->color,$newRoom->notes,$newRoom->revisionID,$newRoom->roomID));
	}

	function delete($roomID)
	{
		$this->db->prepare("DELETE FROM rooms WHERE roomID = ?;");
		$this->db->execute(array($roomID));
		
		$layoutItems = new layoutItems;
		$layoutItems->deleteItem($roomID);
	}
}

class room
{
	var $roomID;
	var $floorID;
	var $buildingID;
	var $ownerID;
	var $name;
	var $color;
	var $notes;
	var $revisionID;

	function __construct($ByRoomID='0')
	{
		global $db;
		$this->roomID = $ByRoomID;
		$this->name = "";
		$this->color ="";
		$this->notes = "";
		$this->floorID = 0;
		$this->ownerID = 0;
		$this->revisionID = 0;
		
		if (is_numeric($this->roomID) && $this->roomID > 0)
		{		
			$query = $db->prepare('SELECT * FROM rooms WHERE roomID=?');
			$query->execute(array($this->roomID));
			$result = $query->fetchAll();
			foreach($result as $room)
			{
				$this->roomID = $this->roomID;
				$this->floorID = $room['floorID'];
				$this->buildingID = $room['buildingID'];
				$this->ownerID = $room['ownerID'];
				$this->name = $room['name'];
				$this->color = $room['color'];
				$this->notes = $room['notes'];
				$this->revisionID = $room['revisionID'];
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
