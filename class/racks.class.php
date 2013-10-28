<?php
class racks
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

        $this->db->query('SELECT * FROM racks ORDER BY name');
        $result = $this->db->fetchAll();

        foreach($result as $item)
        {
            $newrack = new rack(0);
            foreach($item as $key=>$val)
                $newrack->$key=$val;

            array_push($return, $newrack);
        }
        $this->rows = $return;
        return $return;
    }
    
    function getAllNames()
    {
        $return = array();

        $this->db->query('SELECT rackID,name FROM racks ORDER BY rackID');
        $result = $this->db->fetchAll();

        foreach($result as $item)
        {
            $return[$item['rackID']]=$item['name'];
        }
        return $return;
    }

    function insert($newrack)
    {
        $created=0;
        $this->db->prepare("INSERT INTO racks VALUES ('',?,?,?,?,?,?,?,?,?,?,?,?)");
        $created = $this->db->execute(array($newrack->parentID,$newrack->parentType,$newrack->ownerID,$newrack->model,$newrack->deviceTypeID,$newrack->sideMountable,$newrack->width,$newrack->depth,$newrack->height,$newrack->RU,$newrack->name,$newrack->notes));

        // Select and return the ID of the last created item
        $this->db->query("SELECT LAST_INSERT_ID()");
        $result = $this->db->fetchAll();

        if($created)
        {
            // when in a cabinet only the cabinet creation event gets logged
            if($newrack->parentType!='cabinet')
            {
                // Log this event
                $log=new log;
                $log->event = "Added Rack ".$newrack->name.": RU".$newrack->RU;
                $log->eventType="new_device";
                $log->itemID=$created;

                $logs = new logs();
                $logs->insert($log);
            }
            return $result[0]['LAST_INSERT_ID()'];
        }
        else
            return 0;
    }

    function update($rack)
    {
        $status=array();
        $this->db->prepare("UPDATE racks SET parentID=?,parentType=?,sideMountable=?,name=?,model=?,notes=?,ownerID=?,width=?,height=?,RU=?,depth=? WHERE rackID=?;");
        $status[]=$this->db->execute(array($rack->parentID,$rack->parentType,$rack->sideMountable,$rack->name,$rack->model,$rack->notes,$rack->ownerID,$rack->width,$rack->height,$rack->RU,$rack->depth,$rack->rackID));

        $this->db->prepare("UPDATE layoutitems SET itemName=? WHERE itemID=? AND itemType='rack1';");
        $status[]=$this->db->execute(array($rack->name,$rack->rackID));

        if(!in_array('0', $status))
            return 1;
        else
            return 0;
    }

    function delete($rackID)
    {
        $this->db->prepare("DELETE FROM racks WHERE rackID=? LIMIT 1;");
        return $this->db->execute(array($rackID));
    }

    function getByParent($parentID,$parentType='room')
    {
        $query = $this->db->prepare('SELECT * FROM racks WHERE parentID=? AND parentType=? ORDER BY name;');
        $query->execute(array($parentID,$parentType));
        $result = $query->fetchAll();

        $return = array();

        foreach($result as $item)
        {
            $newrack = new rack(0);
            foreach($item as $key=>$val)
                $newrack->$key=$val;

            array_push($return, $newrack);
        }
        return $return;
    }


    function move_device($device,$newRack,$newPosition,$back=0)
    {
        // if passed an object get the ID
        if(is_numeric($device))
        {
            $deviceID = $device;
            $device = new device($device);
        }
        else
            $deviceID = $device->deviceID;

        $device->parentID=$newRack;
        $device->position=$newPosition;
        
        $devices = new devices;
        $devices->update($device);
        
        /*$rack = new rack($newRack);
        // log this event
        $log = new log;
        $log->event = "Moved Device ".$device->systemName." to rack ".$rack->name ." RU ".$newPosition;
        $log->eventType="move_device";
        $log->itemID=$deviceID;
        $logs = new logs;
        $logs->insert($log);

        */
    }
};


class rack
{
	var $db;
	
	var $rackID=0;
	var $parentID=0;
	var $parentType=0;
        var $ownerID=0;
	var $model=0;
	var $deviceTypeID=0;
        var $sideMountable=0;
	var $width=0;
	var $depth=0;
	var $height=0;
	var $RU=0;
	var $name=0;
	var $notes=0;

	function __construct($ByrackID='0')
	{
            global $db;
            $this->db=$db;

            $this->rackID = $ByrackID;
            if (is_numeric($this->rackID) && $this->rackID > 0)
            {
                $query = $this->db->prepare('SELECT * FROM racks WHERE rackID=? LIMIT 1;');
                $query->execute(array($this->rackID));
                $rack = $query->fetch();
                
                foreach ($rack as $key => $val)
                    $this->$key=$val;
            }
	}
	
	function owner()
	{
		$newOwner = new owner;
		if ($this->ownerID > 0)
			$newOwner = new owner($this->ownerID);
		return $newOwner;
	}
};
?>