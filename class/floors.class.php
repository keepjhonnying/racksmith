<?php
class floors
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
		// MADE A CHANGE, WE WANT TO QUERY WHEN EMPTY NOT WHEN FULL > BECAME <
		if (count($this->rows) <= 0)
		{

			$query = $this->db->prepare('SELECT floorID, name, notes, buildingID FROM floors ORDER BY sort DESC');
			$query->execute();
			$result = $query->fetchAll();
			
			$return = array();
		
			foreach($result as $floor)
			{
				$newFloor = new floor;
				$newFloor->floorID = $floor['floorID'];
				$newFloor->name = $floor['name'];
				$newFloor->notes = $floor['notes'];
				$newFloor->buildingID = $floor['buildingID'];
				array_push($return, $newFloor);
			}
			$this->rows = $return;
			return $return;	
		}
		else
			return $this->rows;

	}

	function getByBuilding($buildingID)
	{
		if (count($this->rows) > 0)
		{
			$return = array();
			foreach($this->rows as $floor)
			{
				if ($floor->buildingID == $buildingID)
				{
					$newFloor = new floor;
					$newFloor->floorID = $floor->floorID;
					$newFloor->name = $floor->name;
					$newFloor->notes = $floor->notes;
					$newFloor->buildingID = $floor->buildingID;
					array_push($return, $newFloor);
				}
			}
			return $return;	
		}
		else
		{
			$query = $this->db->prepare('SELECT floorID, buildingID, name, notes, sort FROM floors WHERE buildingID = ? ORDER BY sort DESC');
			$query->execute(array($buildingID));
			$result = $query->fetchAll();

			$return = array();

			foreach($result as $floor)
			{
				$newFloor = new floor;
				$newFloor->floorID = $floor['floorID'];
				$newFloor->name = $floor['name'];
				$newFloor->notes = $floor['notes'];
				$newFloor->buildingID = $floor['buildingID'];
				array_push($return, $newFloor);
			}
			return $return;	
		}
	}

	function insert($newFloor)
	{
		$this->db->prepare("INSERT INTO floors (name,notes,buildingID,sort) VALUES (?,?,?,'');");
		$this->db->execute(array($newFloor->name,$newFloor->notes,$newFloor->buildingID));

		$this->db->query("SELECT LAST_INSERT_ID()");
		$result = $this->db->fetchAll();

		$floorID = $result[0]['LAST_INSERT_ID()'];

		$building = new building($floorID);
		$roomname="Room 1";

		// A building should automatically generate a ground level.		
		$newRoom = new room;
		$newRoom->name = $roomname;
		$newRoom->ownerID = $building->ownerID;
		$newRoom->floorID = $floorID;
		$newRoom->buildingID = $building->buildingID;
		$newRoom->color='#688bc3';
		$rooms = new rooms;
		$roomID = $rooms->insert($newRoom);
		
		
		$layoutItem = new layoutItem;
		$layoutItem->height = '200';
		$layoutItem->width = '300';
		$layoutItem->posX = '25';
		$layoutItem->posY = '25';

		$layoutItem->itemID = $roomID;
		$layoutItem->itemName = $roomname;;
		$layoutItem->itemType = "Room";

		$layoutItem->parentID = $floorID;
		$layoutItem->parentName = '';
		$layoutItem->parentType = "floor";
		
		$layoutItems = new layoutItems;
		$layoutItems->insert($layoutItem);
	}

	function update($newFloor)
	{
		$this->db->prepare("UPDATE floors SET name = ?,notes = ?,buildingID = ? WHERE floorID = ?;");
		$this->db->execute(array($newFloor->name,$newFloor->notes,$newFloor->buildingID,$newFloor->floorID));
	}

	function delete($FloorID)
	{
		$this->db->prepare("DELETE FROM floors WHERE floorID = ?;");
		$this->db->execute(array($FloorID));
	}
	
	// Update the sort listing for the items, this is used on buildings.php and called through AJAX
	function update_sort($data)
	{
            $saveStatus=array();
            $data = preg_replace("[0-9z]","",$data);
            $data = explode(",",$data);
            $currentPos = count($data)-1;
            foreach($data as $currentItem)
            {
                if(is_numeric($currentItem))
                {
                    $update = $this->db->prepare("UPDATE floors SET sort=? WHERE floorID=?");
                    if($update->execute(array($currentPos,$currentItem)))
                        $saveStatus[]=1;
                    else
                        $saveStatus[]=0;
                }
                $currentPos--;
            }

            if(in_array(0, $saveStatus))
                echo 0;
            else
                echo 1;
	}
}

class floor
{
	var $db;
	var $floorID;
	var $name;
	var $notes;
	var $buildingID;
	var $building;
	var $sort;

	function __construct($ByFloorID='0')
	{
		global $db;
		$this->db=$db;	
		$this->floorID = $ByFloorID;
		$this->name = "";
		$this->notes = "";
		$this->buildingID = 0;
		$this->sort='';

		if (is_numeric($this->floorID) && $this->floorID > 0)
		{		
			$query = $this->db->prepare('SELECT floorID,name,notes,buildingID FROM floors WHERE floorID = ?');
			$query->execute(array($this->floorID));
			$result = $query->fetchAll();

			foreach($result as $floor)
			{
				$this->floorID = $floor['floorID'];
				$this->name = $floor['name'];
				$this->notes = $floor['notes'];
				$this->buildingID = $floor['buildingID'];
			}
		}
	}
};
?>