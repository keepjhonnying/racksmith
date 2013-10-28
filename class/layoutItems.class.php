<?php
class layoutItems
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
			$this->db->prepare("SELECT * FROM layoutitems");
			$this->db->execute(array());

			$result = $this->db->fetchAll();
			
			$return = array();
			foreach($result as $Item)
			{
				$newItem = new ItemItem();
				$newItem->layoutItemID = $Item['layoutItemID'];
				$newItem->parentID = $Item['parentID'];
				$newItem->itemID = $Item['itemID'];
				$newItem->parentName = $Item['parentName'];
				$newItem->itemName = $Item['itemName'];
				$newItem->parentType = $Item['parentType'];
				$newItem->itemType = $Item['itemType'];
				$newItem->width = $Item['width'];
				$newItem->height = $Item['height'];	
				$newItem->posX = $Item['posX'];
				$newItem->posY = $Item['posY'];
				$newItem->rotation = $Item['rotation'];
                                $newItem->zindex=$Item['zindex'];

				array_push($return, $newItem);
			}
			$this->rows = $return;
			return $return;	
		}
		else
			return $this->rows;
	}

	function getByParent($ParentID,$ParentType)
	{
		if (count($this->rows) > 0)
		{
			$return = array();
			foreach($this->rows as $ItemItem)
			{
				if ($ItemItem->ItemID == $ItemID)
				{
					array_push($return, $ItemItem);
				}
			}
			return $return;	
		}
		else
		{
			$query = $this->db->prepare('SELECT * FROM layoutitems WHERE parentID = ? AND parentType = ?');
			$query->execute(array($ParentID,$ParentType));
			$result = $query->fetchAll();
			
			$return = array();
			$building = new buildings();
			foreach($result as $Item)
			{
				$newItem = new layoutItem;
				
				$newItem->layoutItemID = $Item['layoutItemID'];
				$newItem->parentID = $Item['parentID'];
				$newItem->itemID = $Item['itemID'];
				$newItem->parentName = $Item['parentName'];
				
				/*
				Its a bit of a hack for now, really we need to pull in the items name here
				we initially stored this in the layoutitems table but leads to issues */
				if($Item['itemType'] == "Building")
					$newItem->itemName = $building->returnName($Item['itemID']);
				else
					$newItem->itemName = $Item['itemName'];
					
				$newItem->parentType = $Item['parentType'];
				$newItem->itemType = $Item['itemType'];
				$newItem->width = $Item['width'];
				$newItem->height = $Item['height'];	
				$newItem->posX = $Item['posX'];
				$newItem->posY = $Item['posY'];
				$newItem->rotation = $Item['rotation'];
                                $newItem->zindex=$Item['zindex'];

				array_push($return, $newItem);
			}
			return $return;	
		}
	}

	function getByType($itemID,$itemType)
	{
            $newItem = new layoutItem;
            $query = $this->db->prepare('SELECT * FROM layoutitems WHERE itemID = ? AND itemType = ? LIMIT 1;');
            $query->execute(array($itemID,$itemType));
            $Item = $query->fetch();

            $newItem->layoutItemID = $Item['layoutItemID'];
            $newItem->parentID = $Item['parentID'];
            $newItem->itemID = $Item['itemID'];
            $newItem->parentName = $Item['parentName'];

            /*
            Its a bit of a hack for now, really we need to pull in the items name here
            we initially stored this in the layoutitems table but leads to issues */
            if($Item['itemType'] == "Building")
            {
                $building = new buildings();
                $newItem->itemName = $building->returnName($Item['itemID']);
            }
            else
                    $newItem->itemName = $Item['itemName'];

            $newItem->parentType = $Item['parentType'];
            $newItem->itemType = $Item['itemType'];
            $newItem->width = $Item['width'];
            $newItem->height = $Item['height'];
            $newItem->posX = $Item['posX'];
            $newItem->posY = $Item['posY'];
            $newItem->rotation = $Item['rotation'];
            $newItem->zindex=$Item['zindex'];

            return $newItem;
	}

	function insert($newItem)
	{
		$this->db->prepare("INSERT INTO layoutitems ( `parentID`,`itemID`,`parentName`,`itemName`,`parentType`,`itemType`,`posX`,`posY`,`rotation`,`width`,`height`,`zindex`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?);");
		$created = $this->db->execute(array($newItem->parentID,$newItem->itemID,$newItem->parentName,$newItem->itemName,$newItem->parentType,$newItem->itemType,$newItem->posX,$newItem->posY,$newItem->rotation,$newItem->width,$newItem->height,$newItem->zindex));
		
		$this->db->query("SELECT LAST_INSERT_ID()");
		$result = $this->db->fetchAll();
		
		if ($created)
                    return $result[0]['LAST_INSERT_ID()'];
		else
                    return $created;
	}

	function update($newItem)
	{
		$this->db->prepare("UPDATE layoutitems SET `parentID`=?,`itemID`=?,`parentName`=?,`itemName`=?,`parentType`=?,`itemType`=?,`width`=?,`height`=?,`posX`=?,`posY`=?,`zindex`=?,`rotation`=? WHERE layoutItemID = ?;");
		$this->db->execute(array($newItem->parentID,$newItem->itemID,$newItem->parentName,$newItem->itemName,$newItem->parentType,$newItem->itemType,$newItem->width,$newItem->height,$newItem->posX,$newItem->posY,$newItem->zindex,$newItem->rotation,$newItem->layoutItemID));
	}
	
	function updateNames($itemID,$newName)
	{
		$this->db->prepare("UPDATE layoutitems SET `itemName`=? WHERE itemID = ?;");
		$this->db->execute(array($newName,$itemID));
	}
	
	function rotate($layoutItemID,$newAngle)
	{
	/*
		$query = $this->db->prepare('SELECT rotation FROM layoutitems WHERE layoutItemID = ?;');
		$query->execute(array($layoutItemID));
		$result = $query->fetch();
		$newAngle = ($result['rotation']+$newAngle)%360;
	*/
		$this->db->prepare("UPDATE layoutitems SET `rotation`=? WHERE layoutItemID = ?;");
		$this->db->execute(array($newAngle,$layoutItemID));
		
		return $newAngle;
	}
	
	function delete($LayoutItemID)
	{	
		$this->db->prepare("DELETE FROM layoutitems WHERE layoutItemID = ?;");
		$this->db->execute(array($LayoutItemID));
	}

	function deleteByDevice($itemID,$itemType)
	{
		$this->db->prepare("DELETE FROM layoutitems WHERE itemID = ? AND itemType=? LIMIT 1;");
		$this->db->execute(array($itemID,$itemType));
	}
	
	function deleteItem($itemID)
	{	
		$this->db->prepare("DELETE FROM layoutitems WHERE itemID = ?;");
		$this->db->execute(array($itemID));
	}
};

class layoutItem
{
	var $layoutItemID;
	var $parentID;
	var $itemID;
	var $parentName;
	var $itemName;
	var $parentType;
	var $itemType;
	var $width;
	var $height;
	var $posX;
	var $posY;
        var $zindex;
	var $rotation;
	
	var $db;

	function __construct($ByLayoutItemID='0')
	{
		global $db;
		$this->db = $db;	

		$this->layoutItemID = $ByLayoutItemID;
		$this->parentID = 0;
		$this->itemID = 0;
		$this->parentName = "";
		$this->itemName = "";
		$this->parentType = "";
		$this->itemType = "";
		$this->width = 0;
		$this->height = 0;
		$this->posX = 0;
		$this->posY = 0;
                $this->zindex=1;
		$this->rotation =0;

		if (is_numeric($this->layoutItemID) && $this->layoutItemID > 0)
		{		
			$this->db->prepare('SELECT * FROM layoutitems WHERE layoutItemID = ?');
			$this->db->execute(array($this->layoutItemID));
			$result = $this->db->fetchAll();
			
			foreach($result as $Item)
			{
				$this->layoutItemID = $Item['layoutItemID'];
				$this->parentID = $Item['parentID'];
				$this->itemID = $Item['itemID'];
				$this->parentName = $Item['parentName'];
				$this->itemName = $Item['itemName'];
				$this->parentType = $Item['parentType'];
				$this->itemType = $Item['itemType'];
				$this->width = $Item['width'];
				$this->height = $Item['height'];	
				$this->posX = $Item['posX'];
				$this->posY = $Item['posY'];
                                $this->zindex=$Item['zindex'];
				$this->rotation = $Item['rotation'];
			}
		}
	}
};
?>