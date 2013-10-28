<?php
class devices
{
	var $db;
	var $rows=array();

	function __construct()
	{
            global $db;
            $this->db=$db;
	}

	function getByParent($parentID,$parentType,$orientation=false)
	{
            // given the only real time we want to do this we'll be showing it in a rack, grab the rack location at the same time
            // saves making some big queries later
            if($parentType=="rack")
            {
                $query = $this->db->prepare('SELECT devices.*,attrvalues.value FROM devices LEFT JOIN attrvalues ON devices.deviceID=attrvalues.parentID AND attrvalues.parentType="device" WHERE devices.parentID = ? AND devices.parentType = ? AND attrvalues.attrnameid=5 AND devices.orientation=?;');
                $query->execute(array($parentID,$parentType,$orientation));
                $result = $query->fetchAll();
                
                $return = array();
                foreach($result as $device)
                {
                    $newDevice = new device;
                    $newDevice->RUlocation=$device['value'];
                    foreach ($device as $key => $val)
                        $newDevice->$key=$val;
                    array_push($return, $newDevice);
                }
            }
            else
            {
                $query = $this->db->prepare('SELECT * FROM devices WHERE parentID = ? AND parentType = ?;');
                $query->execute(array($parentID,$parentType));
                $result = $query->fetchAll();

                $return = array();
                foreach($result as $device)
                {
                    $newDevice = new device;
                    foreach ($device as $key => $val)
                        $newDevice->$key=$val;
                    array_push($return, $newDevice);
                }
            }
            return $return;
	}
        
	function getByTemplate($templateID)
	{
            $query = $this->db->prepare('SELECT devices.*,attrvalues.value FROM devices LEFT JOIN attrvalues ON devices.deviceID=attrvalues.parentID AND attrvalues.parentType="device" WHERE devices.templateID = ? AND attrvalues.attrnameid=5;');
            $query->execute(array($templateID));
            $result = $query->fetchAll();

            $return = array();
            foreach($result as $device)
            {
                $newDevice = new device;
                $newDevice->RUlocation=$device['value'];
                foreach ($device as $key => $val)
                    $newDevice->$key=$val;
                array_push($return, $newDevice);
            }

            return $return;
	}

	function insert($new)
	{
            $prepared = $this->db->prepare("INSERT INTO devices VALUES (?,?,?,?,?,?,?,?,?,?);");
            if($prepared->execute(array($new->deviceID,$new->parentID,$new->parentType,$new->position,$new->orientation,$new->name,$new->background,$new->deviceTypeID,$new->templateID,$new->ownerID)))
            {
                $this->db->query("SELECT LAST_INSERT_ID()");
                $result = $this->db->fetchAll();
                $itemID = $result[0]['LAST_INSERT_ID()'];

                $deviceType = new deviceType($new->deviceTypeID);

                // Log this event
                $log=new log;
                $log->event = "Added ".$deviceType->name.": ".$new->name;
                $log->eventType="new_device";
                $log->itemID=$itemID;

                $logs = new logs();
                $logs->insert($log);

                return $itemID;
            }
            else
                return 0;
	}
	
    function update($new)
    {
            $deviceType = new deviceType($new->deviceTypeID);

            $log=new log;
            $log->event = "Updated ".$deviceType->name.": ".$new->name;
            $log->eventType="update_device";
            $log->itemID=$new->deviceID;

            $logs = new logs();
            $logs->insert($log);
		
            $this->db->prepare("UPDATE devices SET parentID=?,parentType=?,name=?,background=?,deviceTypeID=?,templateID=?,ownerID=?,position=?,orientation=? WHERE deviceID = ?;");
            return $this->db->execute(array($new->parentID,$new->parentType,$new->name,$new->background,$new->deviceTypeID,$new->templateID,$new->ownerID,$new->position,$new->orientation,$new->deviceID));
    }
	
    function delete($device,$deleteCables=1)
    {
            $status=array();
            // if passed an object get the ID
            if(is_numeric($device))
            {
                $deviceID=$device;
                $device = new device($deviceID);
                $name = $device->name;
            }
            else
            {
                $deviceID=$device->deviceID;
                $name = $device->name;
            }
	
            // loop over each port and delete them, pass the value to leave/remove cables.
            $portsController = new ports;
            $ports = $portsController->getByDevice($deviceID);
            foreach($ports as $port)
                    $status[]=$portsController->delete($port,$deleteCables);

            // loop over the joins to delete
            $joinsController = new joins;
            $joins = $joinsController->getByDevice($deviceID);
            foreach($joins as $port)
                    $status[]=$joinsController->delete($port,$deleteCables);

            $this->db->prepare("DELETE FROM devices WHERE deviceID = ?;");
            $status[]=$this->db->execute(array($deviceID));

            // Move into the categories and optionvalues to delete everything
            $catClass=new attrcategoryvalues();
            $status[]=$catClass->deleteMultiple($deviceID,"device");
            $valClass=new attrvalues();
            $status[]=$valClass->deleteMultiple($deviceID,"device");

            $deviceType = new deviceType($device->deviceTypeID);

            // log this event
            $log=new log;
            $log->event = "Deleted ".$deviceType->name.": ".$name;
            $log->eventType="delete_device";
            $log->itemID=$deviceID;
            $logs = new logs();
            $logs->insert($log);
    }
	
	
    // Default to delete cables
    function moveToInventory($device,$deleteCables=1)
    {
            // if passed an object get the ID
            if(is_numeric($device))
            {
                $deviceID=$device;
                $device = new device($deviceID);
            }
            else
                $deviceID=$device->deviceID;
		
            // log this event
            $log=new log;
            $log->event = "Moved Device ".$device->name." to inventory";
            $log->eventType="move_device";
            $log->itemID=$deviceID;
            $logs = new logs();
            $logs->insert($log);
			
            // loop over each port and delete it. options are
            // portObect/ID, flag to delete cables(DEFAULT: 1), flag to delete port(DEFAULT: 1)
            // flag to delete port exists for when we want to remove cables but leave ports in inventory
            $portsController = new ports;
            $ports = $portsController->getByDevice($deviceID);
            foreach($ports as $port)
                $portsController->delete($port,$deleteCables,0);
		
            $this->db->prepare("UPDATE devices SET parentID=? WHERE deviceID=?;");
            return $this->db->execute(array(0,$deviceID));
    }
	
	
    /* **** Function currrently replaced by searchGeneral **** */
    function searchName($name)
    {
            $deviceTypesClass = new deviceTypes;
            $deviceTypes = $deviceTypesClass->getAll();

            $ownersClass=new owners;
            $owners=$ownersClass->getAll();

            $query = $this->db->prepare("SELECT * FROM devices WHERE name LIKE ? ORDER BY deviceTypeID,name;");
            $query->execute(array('%'.$name.'%'));
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $return=array();
            foreach($result as $deviceData)
            {
               $device = new device;
               foreach ($deviceData as $key => $val)
                    $device->$key=$val;

               $device->deviceTypeName=$deviceTypes[$deviceData['deviceTypeID']]->name;
               $device->ownerName=$owners[$deviceData['ownerID']]->name;
               $device->fillCategories(1,1,array(GENERIC,RACK_MOUNTABLE,FLOOR_DEVICE));
               $return[]=$device;
            }

            return $return;
    }
        
	// Searches both the name and the model number
	function searchGeneral($name,$start=0,$size=30)
	{
            // we sometimes want to display the parent rack name
            $racks=new racks();
            $rackNames=$racks->getAllNames();
            
            $deviceTypesClass = new deviceTypes;
            $deviceTypes = $deviceTypesClass->getAll();

            $ownersClass=new owners;
            $owners=$ownersClass->getAll();

            $query = $this->db->prepare("SELECT devices.*, devices.parentID as devParentID FROM devices LEFT JOIN attrvalues ON attrvalues.parentid=devices.deviceID AND attrvalues.parenttype='device' WHERE (devices.name LIKE :name OR (attrvalues.attrnameid='42' AND attrvalues.value LIKE :name) OR (attrvalues.attrnameid='10' AND attrvalues.value LIKE :name) OR (attrvalues.attrnameid='27' AND attrvalues.value LIKE :name)) GROUP BY devices.deviceID ORDER BY devices.deviceTypeID,devices.name LIMIT :first,:totalResults;");
            $query->bindParam(':first',$start, PDO::PARAM_INT);
            $query->bindParam(':totalResults',$size, PDO::PARAM_INT);
            $searchQuery='%'.$name.'%';
            $query->bindParam(':name',$searchQuery, PDO::PARAM_STR);
            
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $return=array();
            foreach($result as $deviceData)
            {
               $device = new device;
               foreach ($deviceData as $key => $val)
                    $device->$key=$val;
               
               
               $device->deviceTypeName=$deviceTypes[$deviceData['deviceTypeID']]->name;
               $device->ownerName=$owners[$deviceData['ownerID']]->name;
               
               if($deviceData['parentType']=="rack")
                    $device->parentName=$rackNames[$deviceData['devParentID']];

               
               $device->fillCategories(1,1,array(GENERIC,RACK_MOUNTABLE,FLOOR_DEVICE));
               $return[]=$device;
            }

            return $return;
	}

        
        function countTotal()
        {
            $row = $this->db->query("SELECT count(deviceID) FROM devices")->fetch();
            return $row[0];
            
            
        }
}

class device
{
	var $db;
	var $deviceID='';
	var $parentID=0;
        var $parentType='';
        var $parentName=''; 
        var $position=0;
        var $orientation='';
        var $name='';
        var $background='';
        var $deviceTypeID=0;
        var $templateID;
        var $ownerID=0;
        
	function __construct($byDeviceID='0')
	{
		global $db;
		$this->db=$db;
		
		$types=array();
		$deviceTypes=new deviceTypes;
		foreach($deviceTypes->getAll() as $type)
			$types[$type->deviceTypeID]=$type->name;

		if (is_numeric($byDeviceID) && $byDeviceID > 0)
		{
                    $this->deviceID=$byDeviceID;
			$query = $this->db->prepare('SELECT * FROM devices WHERE deviceID = ?;');
			$query->execute(array($this->deviceID));
			$result = $query->fetchAll(PDO::FETCH_ASSOC);

			foreach($result as $device)
			{
			   foreach ($device as $key => $val)
                                $this->$key=$val;

			}
		}
                
                
                /* REVIEW 
                 * use a single method to present parent names over all pages
                 * requires ability to handle all parent types
                 * search results has some of this logic in JS, needs fixing
                 */
                if($this->parentType=='rack')
                {
                    $parentRack=new rack($this->parentID);
                    if(stristr($this->parentName,"rack"))
                        $this->parentName=$parentRack->name;
                    else
                        $this->parentName="Rack ".$parentRack->name;
                }
	}

        function fillMeta($categoryFilter=0)
        {
            if(!$categoryFilter)
                return false;

            $meta=new attrnames;
            $this->attributes = $meta->getByParent($this->deviceID,'device',$categoryFilter,"1");
        }

        function getMeta($nameID,$categoryID)
        {
            if($categoryID)
            {
                if(isset($this->categories[$categoryID][$nameID]))
                    return $this->categories[$categoryID][$nameID]->value;
            }
            else
            {
                if(isset($this->attributes[$nameID]))
                    return $this->attributes[$nameID]->value;
            }
            return "";
        }

        function fillCategories($getNames=0,$getValues=0,$categoryFilter=0)
        {
            $cats=new attrcategoryvalues;
            $this->categories = $cats->getByParent($this->deviceID, 'device',$getNames,$getValues,$categoryFilter);
        }

        function hasCategory($categoryID)
        {
            if(isset($this->categories[$categoryID]))
                return true;
            else
                return false;
        }
};
?>