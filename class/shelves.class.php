<?php
class shelves
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

			$query = $this->db->prepare('SELECT * FROM shelves ORDER BY vieworder DESC');
			$query->execute();
			$result = $query->fetchAll();
			
			$return = array();
		
			foreach($result as $shelf)
			{
                            $newshelf = new shelf;
                            $newshelf->cabinetID = $shelf['cabinetID'];
                            $newshelf->shelfID = $shelf['shelfID'];
                            $newshelf->name = $shelf['name'];
                            $newshelf->vieworder = $shelf['vieworder'];
                            $newshelf->trays = $shelf['trays'];
                            $newshelf->notes = $shelf['notes'];
                            array_push($return, $newshelf);
			}
			$this->rows = $return;
			return $return;	
		}
		else
			return $this->rows;

	}

	function getByCabinet($cabinetID)
	{
		if (count($this->rows) > 0)
		{
                    $return = array();
                    foreach($this->rows as $shelf)
                    {
                        if ($shelf->cabinetID == $cabinetID)
                        {
                            $newshelf = new shelf;
                            $newshelf = new shelf;
                            $newshelf->cabinetID = $shelf->cabinetID;
                            $newshelf->shelfID = $shelf->shelfID;
                            $newshelf->name = $shelf->name;
                            $newshelf->vieworder = $shelf->vieworder;
                            $newshelf->trays = $shelf->trays;
                            $newshelf->notes = $shelf->notes;
                            array_push($return, $newshelf);
                        }
                    }
                    return $return;
		}
		else
		{
                    $query = $this->db->prepare('SELECT * FROM shelves WHERE cabinetID = ? ORDER BY sort DESC');
                    $query->execute(array($cabinetID));
                    $result = $query->fetchAll();

                    $return = array();

                    foreach($result as $shelf)
                    {
                        $newshelf = new shelf;
                        $newshelf->cabinetID = $shelf['cabinetID'];
                        $newshelf->shelfID = $shelf['shelfID'];
                        $newshelf->name = $shelf['name'];
                        $newshelf->vieworder = $shelf['vieworder'];
                        $newshelf->trays = $shelf['trays'];
                        $newshelf->notes = $shelf['notes'];
                        array_push($return, $newshelf);
                    }
                    return $return;
		}
	}

	function insert($newshelf)
	{
		$this->db->prepare("INSERT INTO shelves  VALUES (?,?,?,?,?,?);");
		$this->db->execute(array($newshelf->cabinetID,$newshelf->shelfID,$newshelf->name,$newshelf->vieworder,$newshelf->trays,$newshelf->notes));

		$this->db->query("SELECT LAST_INSERT_ID()");
		$result = $this->db->fetchAll();

		$shelfID = $result[0]['LAST_INSERT_ID()'];

		$building = new building($shelfID);
		$roomname="Room 1";

		// A building should automatically generate a ground level.		
		$newRoom = new room;
		$newRoom->name = $roomname;
		$newRoom->ownerID = $building->ownerID;
		$newRoom->shelfID = $shelfID;
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

		$layoutItem->parentID = $shelfID;
		$layoutItem->parentName = '';
		$layoutItem->parentType = "shelf";
		
		$layoutItems = new layoutItems;
		$layoutItems->insert($layoutItem);
	}

	function update($newshelf)
	{
		$this->db->prepare("UPDATE shelves SET name = ?,notes = ?,buildingID = ? WHERE shelfID = ?;");
		$this->db->execute(array($newshelf->name,$newshelf->notes,$newshelf->buildingID,$newshelf->shelfID));
	}

	function delete($shelfID)
	{
		$this->db->prepare("DELETE FROM shelves WHERE shelfID = ?;");
		$this->db->execute(array($shelfID));
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
                    $update = $this->db->prepare("UPDATE shelves SET sort=? WHERE shelfID=?");
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

class shelf
{
	var $db;
	var $shelfID;
	var $name;
	var $notes;
	var $buildingID;
	var $building;
	var $sort;

	function __construct($ByshelfID='0')
	{
		global $db;
		$this->db=$db;	
		$this->shelfID = $ByshelfID;
		$this->name = "";
		$this->notes = "";
		$this->buildingID = 0;
		$this->sort='';

		if (is_numeric($this->shelfID) && $this->shelfID > 0)
		{		
			$query = $this->db->prepare('SELECT shelfID,name,notes,buildingID FROM shelves WHERE shelfID = ?');
			$query->execute(array($this->shelfID));
			$result = $query->fetchAll();

			foreach($result as $shelf)
			{
				$this->shelfID = $shelf['shelfID'];
				$this->name = $shelf['name'];
				$this->notes = $shelf['notes'];
				$this->buildingID = $shelf['buildingID'];
			}
		}
	}
};
?>