<?php
class cabinets
{
    var $db;

    function __construct()
    {
        global $db;
        $this->db=$db;
    }

    var $rows;

    function getAll()
    {
        $return = array();

        $this->db->query('SELECT * FROM cabinets ORDER BY cabinetID ASC');
        $result = $this->db->fetchAll();

        foreach($result as $cabinet)
        {
            $newCabinet = new cabinet;
            $newCabinet->cabinetID = $cabinet['cabinetID'];
            $newCabinet->parentType = $cabinet['parentType'];
            $newCabinet->parentID = $cabinet['parentID'];
            $newCabinet->name = $cabinet['name'];
            $newCabinet->ownerID = $cabinet['ownerID'];
            $newCabinet->notes = $cabinet['notes'];
            array_push($return, $newCabinet);
        }
        $this->rows = $return;
        return $return;
    }

    function getByParent($parentID,$parentType)
    {
        $return = array();

        $find=$this->db->prepare('SELECT * FROM cabinets WHERE parentID=? AND parentType=? ORDER BY cabinetID ASC;');
        $this->db->execute(array($parentID,$parentType));
        $result = $this->db->fetchAll();

        foreach($result as $cabinet)
        {
            $newCabinet = new cabinet;
            $newCabinet->cabinetID = $cabinet['cabinetID'];
            $newCabinet->parentType = $cabinet['parentType'];
            $newCabinet->parentID = $cabinet['parentID'];
            $newCabinet->name = $cabinet['name'];
            $newCabinet->ownerID = $cabinet['ownerID'];
            $newCabinet->notes = $cabinet['notes'];
            array_push($return, $newCabinet);
        }
        $this->rows = $return;
        return $return;
    }

    function insert($newCabinet)
    {
        $this->db->prepare("INSERT INTO cabinets VALUES ('',?,?,?,?,?);");
        if($this->db->execute(array($newCabinet->parentType,$newCabinet->parentID, $newCabinet->name,$newCabinet->ownerID,$newCabinet->notes)))
        {
            $this->db->query("SELECT LAST_INSERT_ID()");
            $result = $this->db->fetch();
            if($result)
            {
                $createdID=$result['LAST_INSERT_ID()'];
                // Log this event
                $log=new log;
                $log->event = "Added Cabinet ".$newCabinet->name;
                $log->eventType="new_device";
                $log->itemID=$createdID;

                $logs = new logs();
                $logs->insert($log);
                
                return $createdID;
            }
            else
                return 0;
        }
        else
            return 0;
    }

    function update($newCabinet)
    {
        $this->db->prepare("UPDATE layoutitems SET itemName=? WHERE itemID=? AND itemType='cabinet' LIMIT 1;");
        $this->db->execute(array($newCabinet->name,$newCabinet->cabinetID));

        $this->db->prepare("UPDATE cabinets SET parentType=?,parentID=?,name=?,ownerID=?,notes=? WHERE cabinetID=?;");
        $this->db->execute(array($newCabinet->parentType,$newCabinet->parentID,$newCabinet->name,$newCabinet->ownerID,$newCabinet->notes,$newCabinet->cabinetID));
    }
    
    function returnCount()
    {
            if (count($this->rows) <= 0)
            {
                    $return = array();
                    $this->db->query('SELECT count(cabinetID) FROM cabinets');
                    $result = $this->db->fetchAll();
                    return $result[0]['count(cabinetID)'];	
            }
            else
                    return count($this->rows);
    }

    function delete($cabinetID)
    {
        $status=array();
        $delItem = $this->db->prepare("DELETE FROM cabinets WHERE cabinetID = ? LIMIT 1;");
        $status[]=$delItem->execute(array($cabinetID));
        
        $delLayout = $this->db->prepare("DELETE FROM layoutitems WHERE itemID = ? AND itemType='cabinet' LIMIT 1;");
        $status[]=$delLayout->execute(array($cabinetID));

        
        $racks = new racks;
        $childRacks = $racks->getByParent($cabinetID, "cabinet");
        foreach($childRacks as $rack)
            $status[]=$racks->delete($rack->rackID);

        if(in_array('0',$status))
            return 0;
        else
            return 1;
    }
};

class cabinet
{
	var $cabinetID;
	var $parentType;
	var $parentID;
	var $name;
	var $ownerID;
	var $notes;

	var $db;

	function __construct($cabinetID='0')
	{
		global $db;
		$this->db=$db;	

		$this->cabinetID = $cabinetID;
		$this->parentType = "";
		$this->parentID = "";
		$this->name = "";
		$this->ownerID = "";
		$this->notes = "";

		if(is_numeric($this->cabinetID) && $this->cabinetID > 0)
		{		
                    $query = $this->db->prepare('SELECT * FROM cabinets WHERE cabinetID = ? LIMIT 1;');
                    $query->execute(array($this->cabinetID));
                    $result = $query->fetch();

                    $this->cabinetID = $result['cabinetID'];
                    $this->parentType = $result['parentType'];
                    $this->parentID = $result['parentID'];
                    $this->name = $result['name'];
                    $this->ownerID = $result['ownerID'];
                    $this->notes = $result['notes'];
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