<?php
session_start();
include "class/db.class.php";
if (isset($_POST['action']))
{

    // returns the portID of the next port in a chain
    function getNextPortID($startID)
    {
        $ports = new ports;
        $return = 0;
        if(strstr($startID,'join'))
        {
            // find the portID we are working with
            $startJoinID = preg_replace("/[^0-9]+/", "", $startID);
            $startJoin = new join((int)$startJoinID,0);
            // if we are checking the front port we want to search from the back
            if(stristr($startID,'prim'))
                $localPortID =  $startJoin->secPort;
            else
                $localPortID =  $startJoin->primPort;

            // we now have the portID associated
            // check that port for a cable and then search that cable for another port
            $localPort = new port($localPortID,0);
            $cableID = $localPort->cableID;
            $otherEnd=$ports->getByCableID($cableID);
            foreach($otherEnd as $port)
            {
                if($port->portID!=$localPortID)
                    $return = $port->portID;
            }
            return $return;

        }
        elseif(is_numeric($startID))
        {
            $return=0;
            $localPort = new port($startID,0);
            if($localPort->joinID)
            {
                $join = new join($localPort->joinID,0);

                if($join->primPort==$startID)
                    $localPortID=$join->secPort;
                else
                    $localPortID=$join->primPort;

                if($localPortID ==0)
                    $return = 0;
                else
                {
                    $localPort = new port($localPortID,0);
                    $cableID = $localPort->cableID;
                    $otherEnd=$ports->getByCableID($cableID);
                    foreach($otherEnd as $port)
                    {
                        if($port->portID!=$localPortID)
                                $return = $port->portID;
                    }
                }
            }
            else
            {
                $cableID = $localPort->cableID;
                $otherEnd=$ports->getByCableID($cableID);
                foreach($otherEnd as $port)
                {
                    if($port->portID!=$startID)
                            $return = $port->portID;
                }
            }
            return $return;
        }
    }



switch($_POST['action'])
{
	case "configuration":
		$config = new config;
				$canvasConfigSizeX=$config->returnItem('buildingCanvasX');
				if(is_numeric($canvasConfigSizeX) && $canvasConfigSizeX!=0)
					$canvasX=$canvasConfigSizeX;
				else
					$canvasX=2000;
					
				$canvasConfigSizeY=$config->returnItem('buildingCanvasY');
				if(is_numeric($canvasConfigSizeY) && $canvasConfigSizeY!=0)
					$canvasY=$canvasConfigSizeY;
				else
					$canvasY=2000;
		echo json_encode(array($canvasX,$canvasY));
		break;
	case "buildings":
		$buildings = new buildings;
		echo json_encode(object_to_array($buildings->getAll()));
		break;
	case "buildingsLevel":
		$buildings = new buildings;
                $cabinetsClass = new cabinets;
                $cabinets=$cabinetsClass->getByParent('0','site');
		echo json_encode(array("buildings"=>object_to_array($buildings->getAll()),"cabinets"=>object_to_array($cabinets)));
		break;
	case "getCabinet":
                $cabinet=new cabinet($_POST['cabinetID']);
                $racksClass = new racks;
                $racks=$racksClass->getByParent($cabinet->cabinetID, 'cabinet');
		echo json_encode(array("cabinet"=>object_to_array($cabinet),"racks"=>object_to_array($racks)));
		break;
        case "attrnames":
                $attrnames = new attrnames;
                echo json_encode(object_to_array($attrnames->getByParent($_POST['attrcategory'],"attrcategory")));
                break;
	case "deviceInfo":
		$newDevice = new device($_POST['deviceID']);
                $newDevice->fillCategories(1,1,GENERIC);
                $newDevice->uploads=$_SESSION['uploads'];
                    echo json_encode(object_to_array($newDevice));
		break;
	case "rackInfo":
		$newRack = new rack($_POST['rackID']);
                //print_r($newRack);
                echo json_encode(object_to_array($newRack));
		break;
	case "roomInfo":
		$newRoom = new room($_POST['roomID']);
                    echo json_encode(object_to_array($newRoom));
		break;	
	case "floorInfo":
		$newFloor = new floor($_POST['floorID']);
			echo json_encode(object_to_array($newFloor));
		break;
	case "portInfo":
		$newPort = new port($_POST['portID']);
                if(isset($_POST['flood']) && $_POST['flood']=="check")
                {
                    $ports = new ports;
                    // counts forward to see if join has available "ports" for flooding
                    if(isset($_POST['existingCable']))
                        $existingCable=1;
                    else
                        $existingCable=0;

                    $flood = $ports->checkFlood($newPort->portID,$existingCable);
                }
                else
                    $flood="0";

                $return = object_to_array($newPort);
                $return['floodIDs']=$flood;
                $return['flood']=count($flood);
		echo json_encode($return);
		break;
        case "deleteCable":
                // check to see if the local end is a join
            $return=array();
            if(strstr($_POST['portID'],'join'))
            {
                // its a join so we need to remember to also delete the port
                // find the portID we are working with
                $startJoinID = preg_replace("/[^0-9]+/", "", $_POST['portID']);
                $startJoin = new join((int)$startJoinID);

                if(stristr($_POST['portID'],'prim'))
                    $localPortID =  $startJoin->primPort;
                else
                    $localPortID =  $startJoin->secPort;

                $ports = new ports;
                $return[]=$ports->delete($localPortID,1,0);

            }
            // the local end is just a port, just delete it
            else
            {
                $ports = new ports;
                $return[] = $ports->delete($_POST['portID'],1,0);
            }

            if(in_array(0,$return))
                echo json_encode(0);
            else
                echo json_encode(1);

            break;

        case "deleteFlood":
            $return=array();
            // if we were passed a join find the actual port ID
            // take into account the focus of the port
            if(strstr($_POST['portID'],"join"))
            {
                $joinID=preg_replace("/[^0-9]+/", "", $_POST['portID']);
                $join = new join($joinID);
                if(strstr($_POST['portID'],"prim"))
                    $_POST['portID']=$join->primPort;
                else
                    $_POST['portID']=$join->secPort;
            }
            // it could come in via the form - join<JOINIreD>focus
            // resolve this to a port before we loop and delete
                $ports = new ports;

                foreach($ports->checkFlood($_POST['portID'],1) as $port)
                    $return[]=$ports->delete($port,1,0);

                if(in_array(0,$return))
                    echo json_encode(0);
                else
                    echo json_encode(1);

                break;
	case "joinInfo":
		$newJoin = new join($_POST['joinID']);
		$device = new device($newJoin->deviceID);
		
		if(isset($_POST['focus']) && $_POST['focus'] == "prim" && $newJoin->primPort)
			$newPort = new port($newJoin->secPort);
		else if(isset($_POST['focus']) && $_POST['focus']=="sec" && $newJoin->secPort)
			$newPort = new port($newJoin->primPort);
		else 
			$newPort = array("");
		
		if(is_object($newPort))
			$return = object_to_array($newPort);
		else
			$return = $newPort;

                    if(isset($_POST['existingCable']))
                        $existingCable=1;
                    else
                        $existingCable=0;

                // if the user requested to check if they can flood a cable
                if(isset($_POST['flood']) && $_POST['flood']=="check")
                {
                    $joins = new joins;
                    // counts forward to see if join has available "ports" for flooding
                    $floodAvailable = $joins->checkFlood($newJoin->joinID,$_POST['focus'],$existingCable);
                    $return['floodIDs']=$floodAvailable;
                    $return['flood']=count($floodAvailable);
                }
                else
                    $return['flood']="0";
		
		$return['patchName']=$device->name;
                $return['deviceID']=$device->deviceID;
		$return['disporder']=$newJoin->disporder;
		echo json_encode($return);
		break;
        // searches on from a portID to determine the end device(non-patch) if any exists
        // extremly basic logic, should adjust given we will probably drop sqlite support
        case "recursivePortSearch":
            $startPortID=$_POST['startPort'];
            // if we start at a join get its portID

            // assume we have an error and no end device found
            $responce="error";
            $foundDevice=0;
            $return=array(); // an array of values to print, used an array so we can array_unique it

            //while we havent found a device keep searching
            $loopLimit=10;
            while(!$foundDevice)
            {
                // only stop if we hit out limit
                $loopLimit--;
                if($loopLimit == 0)
                {
                    $foundDevice=1;

                    // we have stopped the search at this point and need to detect a loop
                    // as there are so many moves, we can detect a loop by making sure
                    // all entries are unique and then comparing the count
                    $originalMoveCount=count($return);
                    $return = array_unique($return) ;
                    $uniqueMoves=count($return);

                    // make the comparision to detect a loop
                    if($originalMoveCount!=$uniqueMoves)
                        $return[]="<em>A loop was found</em>";
                    else
                        $return[]="Over 10 patches found, we stopped looking";
                }
                // we must still be searching for an endpoint
                else
                {
                    // get the ID of the next stop for the cable
                    $startPortID = getNextPortID($startPortID);
                    // there is no further port so get the endpoint (device) details
                    if($startPortID==0)
                    {
                        $foundDevice=true;
                        $device = new device($current->deviceID);
                        $return[] = "Connects to ".$device->name." on port ".$current->label;
                    }
                    // there must be another port to jump through
                    else
                    {
                        // check if this port is a join and display the appropriate message
                        $current = new port($startPortID,0);
                        if(!$current->joinID)
                        {
                            // if its not a join it must be an endpoint so finish here
                            $device = new device($current->deviceID);
                            $return[] = "Connects to ".$device->name." on port ".$current->label;
                            $foundDevice=true;
                        }
                        else
                        {
                            
                            if(!(isset($_POST['onlyShowEndpoint']) && $_POST['onlyShowEndpoint']==1))
                            {
                                $device = new device($current->deviceID);
                                $return[] = "Patch panel ".$device->name." Port #".$current->label;
                            }
                        }
                    }
                }
            }

            // print the list of events one per lne
            foreach($return as $move)
                echo $move."<br/>";

            break;

        case "recursivePortDelete":
            $startPortID=$_POST['portID'];
            // if we start at a join get its portID

            $portsToDelete=array();
            $responce="error";
            $foundDevice=0;
            $loopLimit=10;
            while(!$foundDevice)
            {
                $loopLimit--;
                if($loopLimit == 0)
                {
                    $foundDevice=1;
                }
                else
                {
                    $startPortID = getNextPortID($startPortID);
                    if($startPortID==0)
                    {
                        $foundDevice=true;
                    }
                    else
                    {
                        $current = new port($startPortID,0);
                        if(!$current->joinID)
                        {

                            $device = new device($current->deviceID);
                            $portsToDelete[]=$current->portID;
                            $foundDevice=true;
                        }
                        else
                        {   $device = new device($current->deviceID);

                            $portsToDelete[]=$current->portID;
                        }
                    }
                }

            }
            $ports = new ports;
            foreach($portsToDelete as $del)
            {
                $ports->delete($del,1,0);
            }

            break;

	case "deviceParent":
		$newDevice = new device($_POST['deviceID']);
		echo $newDevice->floorDeviceID;
		break;
	case "rackParent":
		$newRack = new rack($_POST['rackID']);
		echo $newRack->roomID;
		break;
	case "roomParent":
		$newRoom = new room($_POST['roomID']);
		echo $newRoom->floorID;
		break;
	case "updateFloorItemParent":
		$racker = new racks();
                $rack = new rack($_POST['itemID']);
                $rack->parentID=$_POST['parentID'];
                if(isset($_POST['parentType']))
                    $rack->parentType=$_POST['parentType'];
                echo $racker->update($rack);
		break;
	case "updateDeviceParent":
		$class = new devices();
                $device = new device($_POST['itemID']);
                if(isset($_POST['parentID']))
                    $device->parentID=$_POST['parentID'];
                if(isset($_POST['parentType']))
                    $rack->parentType=$_POST['parentType'];
                echo $class->update($device);
		break;
	case "moveFloorRackToInventory":
                // get the items ID so we can work with it, we were only passed layoutItemID
                $layoutItem = new layoutItem($_POST['layoutItemID']);

                // move that item into the inventory (by removing parentID)
		$racker = new racks();
                $rack = new rack($layoutItem->itemID);
                $rack->parentID='0';
                $rack->parentType='room';
                if($racker->update($rack))
                    echo json_encode(array("rackID"=>$rack->rackID,"name"=>$rack->name));
                else
                    echo json_encode(array("rackID"=>1,"name"=>"ERROR: floor_to_inventory"));
		break;
	case "moveFloorDeviceToInventory":
                // get the items ID so we can work with it, we were only passed layoutItemID
                $layoutItem = new layoutItem($_POST['layoutItemID']);

                // move that item into the inventory (by removing parentID)
                $device = new device($layoutItem->itemID);
                $device->parentID='0';
                $device->parentType='room';
                $devices = new devices;
                if($devices->update($device))
                    echo json_encode(array("itemID"=>$device->deviceID,"name"=>$device->name));
                else
                    echo json_encode(array("itemID"=>$device->deviceID,"name"=>"ERROR: Unable to move"));
               
		break;
	case "floorParent":
		$newFloor = new floor($_POST['floorID']);
		echo $newFloor->buildingID;
		break;
	case "portParent":
		$newPort = new port($_POST['portID']);
		echo $newPort->deviceID;
		break;	
	case "insertCable":
                $status = array(); // an array to store insert statuses, 0 = fail
		$ports = new ports;
		$joins = new joins;
                $cables = new cables;

                if(!isset($_POST['floodCount']))
                    $_POST['floodCount']=0;

                // work out the total number of cables we will be making
                $cablesToCreate=$_POST['floodCount']+1;
                // if its > than 1 we must be going to flood
                if($cablesToCreate>1)
                {
                    // if the first cable is a join
                    if(strstr($_POST['end1'],'join'))
                    {
                        // work out its orientation
                        $join1ID = preg_replace("/[^0-9]+/", "", $_POST['end1']);
                        if(stristr($_POST['end1'],'front'))
                                $join1focus = "prim";
                        else
                                $join1focus = "sec";

                        // and retrieve a list of joinIDs we need to work with
                        $firstFloodPorts=$joins->checkFlood($join1ID,$join1Focus);
                    }
                    else
                        $firstFloodPorts=$ports->checkFlood($_POST['end1']);



                    // if the second cable is a join
                    if(strstr($_POST['end2'],'join'))
                    {
                        // work out its orientation
                        $join2ID = preg_replace("/[^0-9]+/", "", $_POST['end2']);
                        if(stristr($_POST['end2'],'front'))
                                $join2focus = "prim";
                        else
                                $join2focus = "sec";

                        // and retrieve a list of joinIDs we need to work with
                        $secondFloodPorts=$joins->checkFlood($join2ID,$join2focus);
                    }
                    else
                        $secondFloodPorts=$ports->checkFlood($_POST['end2']);
                }
                    
                // loop over the number of cables we need to create
                for($i=0;$i<$cablesToCreate;$i++)
                {

                    // if we aren't making the first cable, overwrite the submitted values
                    // this is so the same code can create multiple cables
                    if($i!=0)
                    {
                        if(strstr($firstFloodPorts[($i-1)],'J'))
                        {
                            $jid = preg_replace("/j/","",$firstFloodPorts[($i-1)]);
                            if($join1focus=="prim")
                                $firstFocus="front";
                            else
                                $firstFocus="back";
                            $_POST['end1']="join".$jid.$firstFocus;
                        }
                        else
                            $_POST['end1']=$firstFloodPorts[($i-1)];

                        if(strstr($secondFloodPorts[($i-1)],'J'))
                        {
                            $jid = preg_replace("/j/","",$secondFloodPorts[($i-1)]);
                            if($join2focus=="prim")
                                $secondFocus="front";
                            else
                                $secondFocus="back";
                            $_POST['end2']="join".$jid.$secondFocus;
                        }
                        else
                            $_POST['end2']=$secondFloodPorts[($i-1)];
                    }

                    // make the cable and get its ID#
                    $cable = new cable;
                    $cable->cableTypeID = $_POST['cableTypeID'];
                    $cable->barcode = $_POST['barcode'];
                    $cableID = $cables->insert($cable);


                    // If the first cable is a join we need to do some other work
                    if(strstr($_POST['end1'],'join'))
                    {
                            // find the join were working with
                            $join1ID = preg_replace("/[^0-9]+/", "", $_POST['end1']);
                            $join1 = new join((int)$join1ID);

                            // Create the new port associated with this join
                            $port1 = new port();
                            $port1->cableID=$cableID;
                            $port1->deviceID=$join1->deviceID;
                            $port1->cableTypeID = $cable->cableTypeID;
                            $port1->disporder = $join1->disporder;
                            $port1->label = $join1->disporder;
                            $port1->joinID=$join1ID;
                            // & create it + retrieve its ID to note with the join
                            $newPort1 = $ports->insert($port1);
                            $status[]= $newPort1;

                            // determine if the join is front or back facing
                            if(stristr($_POST['end1'],'front'))
                                    $join1->primPort = $newPort1;
                            else
                                    $join1->secPort = $newPort1;
                            // update the join so it now references the port
                            $status[] = $joins->update($join1);

                    }
                    // we must have a port so list the cableID with it
                    else
                    {
                            $end1 = new port($_POST['end1']);
                            $end1->cableID = $cableID;
                            $status[] = $ports->update($end1);
                    }

                    // If the first cable is a join we need to do some other work
                    if(strstr($_POST['end2'],'join'))
                    {
                            $join2ID = preg_replace("/[^0-9]+/", "", $_POST['end2']);
                            $join2 = new join((int)$join2ID);

                            $port2 = new port();
                            $port2->cableID=$cableID;
                            $port2->deviceID=$join2->deviceID;
                            $port2->cableTypeID = $cable->cableTypeID;
                            $port2->disporder = $join2->disporder;
                            $port2->label = $join2->disporder;
                            $port2->joinID=$join2ID;
                            $newPort2 = $ports->insert($port2);
                            $status[] = $newPort2;

                            if(stristr($_POST['end2'],'front'))
                                    $join2->primPort = $newPort2;
                            else
                                    $join2->secPort = $newPort2;

                            $status[] = $joins->update($join2);
                    }
                    else
                    {
                            $end2 = new port($_POST['end2']);
                            $end2->cableID = $cableID;
                            $status[] = $ports->update($end2);
                    }
                }

                //print_r($firstFloodPorts);
                //print_r($secondFloodPorts);

                // if any of the statuses was fail, return that
                // we should clean this up so do graceful fallback
                if(in_array(0, $status))
                    echo 0;
                else
                    echo 1;
		break;
	case "insertRoom":
		$newRoom = new room;
		$newRoom->name = $_POST['roomName'];
		$newRoom->notes = $_POST['notes'];
		$newRoom->ownerID = $_POST['ownerID'];
		$newRoom->floorID = $_POST['floorID'];
		$newRoom->color = $_POST['color'];
		$newRoom->buildingID = $_POST['buildingID'];
		$rooms = new rooms;
		echo $rooms->insert($newRoom);
		break;
	case "insertFloor":
		$newFloor = new floor;
		$newFloor->name = $_POST['name'];
		$newFloor->notes = $_POST['notes'];
		$newFloor->buildingID = $_POST['building'];
		$floors = new floors;
		$floors->insert($newFloor);
		break;
	case "editFloor":
                if(is_numeric($_POST['floorID']))
                {
                    $newFloor = new floor($_POST['floorID']);
                    $newFloor->name = $_POST['name'];
                    $floors = new floors;
                    $floors->update($newFloor);
                }
		break;
	case "insertBuilding":
		$newBuilding = new building;
		$newBuilding->name = $_POST['name'];
		$newBuilding->description = $_POST['description'];
		$newBuilding->notes = $_POST['notes'];
		$newBuilding->ownerID = $_POST['ownerID'];
		$buildings = new buildings;
		echo $buildings->insert($newBuilding);
		break;
	case "insertCabinet":
                $racks=new racks;
		$newCabinet = new Cabinet;
                $newCabinet->parentType="site";
                $newCabinet->parentID="";
		$newCabinet->name = $_POST['name'];
                $newCabinet->ownerID = $_POST['ownerID'];
		$newCabinet->notes = $_POST['notes'];
		$Cabinets = new Cabinets;
		$cabinetID=$Cabinets->insert($newCabinet);

                // now we've made the cabinet, make the racks within it
                if($cabinetID!=0)
                {
                    $errors=array();
                    if(is_numeric($_POST['racks']) && is_numeric($_POST['RU']))
                    {
                        for($i=0;$i<$_POST['racks'];$i++)
                        {
                            $newRack = new rack();
                            $newRack->parentID=$cabinetID;
                            $newRack->parentType="cabinet";
                            $newRack->ownerID=$_POST['ownerID'];
                            $newRack->RU=$_POST['RU'];
                            $newRack->sideMountable=0;
                            $newRack->name=$_POST['name']. " Rack ".($i+1);
                            $newRack->notes="";
                            if(!$racks->insert($newRack))
                                $errors[]=1;
                        }
                    }
                    if(count($errors)==0)
                        echo $cabinetID;
                    else
                        echo 0;
                }
                else
                    echo 0;
		break;
	case "insertLayoutItem":
		$layoutItem = new layoutItem;

		$layoutItem->height = $_POST['height'];
		$layoutItem->width = $_POST['width'];
		$layoutItem->posX = $_POST['posx'];
		$layoutItem->posY = $_POST['posy'];

		$layoutItem->itemID = $_POST['itemID'];
		$layoutItem->itemName = $_POST['itemName'];
		$layoutItem->itemType = addslashes($_POST['itemType']);

		$layoutItem->parentID = $_POST['parentID'];
		$layoutItem->parentName = $_POST['parentName'];
		$layoutItem->parentType = addslashes($_POST['parentType']);
                if(isset($_POST['zindex']))
                    $layoutItem->zindex=$_POST['zindex'];

		$layoutItems = new layoutItems;
		echo $layoutItems->insert($layoutItem);
		break;
	case "updateLayoutItemPosition":
		$layoutItem = new layoutItem($_POST['layoutItemID']);
		
		$layoutItem->posX = $_POST['posx'];
		$layoutItem->posY = $_POST['posy'];
		
		$layoutItems = new layoutItems;
		echo $layoutItems->update($layoutItem);
		break;
	case "updateLayoutItemSize":

		$layoutItem = new layoutItem($_POST['layoutItemID']);

		$layoutItem->height = $_POST['height'];
		$layoutItem->width = $_POST['width'];
			
		$layoutItems = new layoutItems;
		$layoutItems->update($layoutItem);
		break;
	case "deleteLayoutItem":
		$layoutItems = new layoutItems;
		echo $layoutItems->delete($_POST['layoutItemID']);
		break;
	case "rotateFloorItem":
		$layoutItems = new layoutItems;
		echo $layoutItems->rotate($_POST['layoutItemID'],$_POST['newAngle']);
		break;
	case "lockFloorTiles":
		$config = new config;
		echo $config->setItem('lockFloorTiles',1);
		break;
	case "unlockFloorTiles":
		$config = new config;
		echo $config->setItem('lockFloorTiles',0);
		break;
	case "floors":
		$floors = new floors;
		echo json_encode(object_to_array($floors->getByBuilding($_POST['buildingID'])));
		break;
	case "rooms":
		$rooms = new rooms;
		echo json_encode(object_to_array($rooms->getByFloor($_POST['floorID'])));
		break;
	case "racks":
		$racks = new racks;
		echo json_encode(object_to_array($racks->getByParent($_POST['parentID'],$_POST['parentType'])));
		break;
	case "devices":
		$devices = new devices;
		echo json_encode(object_to_array($devices->getByRack($_POST['rackID'])));
		break;
	case "devicesByParent":
		$devices = new devices;
		echo json_encode(object_to_array($devices->getByParent($_POST['parentID'],$_POST['parentType'])));
		break;
	case "portsByType":
		$ports = new ports;
		echo json_encode(object_to_array($ports->getByType($_POST['deviceID'],$_POST['cableTypeID'])));
		break;
	case "ports":
		$ports = new ports;
		echo json_encode(object_to_array($ports->getByDevice($_POST['deviceID'])));
		break;
	case "joins":
		$joins = new joins;
		echo json_encode(object_to_array($joins->getByDevice($_POST['deviceID'])));
		break;
	case "layoutItems":
		$layoutItems = new layoutItems;
		$result = $layoutItems->getByParent($_POST['parentID'],$_POST['parentType']);
		
		echo json_encode(object_to_array($result));
		break;
	
	case "buildingInfo":
		$building = new building($_POST['buildingID']);
		echo json_encode(object_to_array($building));
		break;
	case "getBuildingMenu":
?>
	<?php
		$floors = new floors;
		$building = new building($_POST['buildingID']);
	?>
		<div class="popupMenu" id="buildingMenu<?php echo $building->buildingID; ?>">
                    <div class="title" >
                        <table>
                            <thead><tr>
                                <th align="left"><?php echo $building->name; ?></th>
                                <th align="right"><span class="closeContextMenu" ><img src="images/icons/close_rack.gif" border="0" alt="Close" title="Close" style="cursor:pointer;" /></span></th>
                            </tr></thead>
                        </table>
                    </div>
                <script>
                $('.launchExternalDOM').openDOMWindow({
                    eventType:'click',
                    width:'800',
                    height: '500',
                    borderSize: 3,
                    windowSource: 'ajax',
                    overlayOpacity: '30',
                    fixedWindowY: 50,
                    borderColor: '#3b4c50',
                    borderSize: 2,
                    windowPadding:0
                    });
                </script>
                <div class="desc" >
                    <table>
                        <tr>
                            <td width="100%"><a href="javascript:void();" onclick="$('#buildingDetails<?php echo $building->buildingID; ?>').slideToggle();">Building Information</a></td>
                            <td><a href="buildings.php?action=building&mode=edit&id=<?php echo $building->buildingID; ?>" class="launchExternalDOM" >[Edit]</a></td>
                        </tr>
                    </table>
                </div>
			
                <div class="desc" id="buildingDetails<?php echo $building->buildingID; ?>" style="display:none;">
                    <strong>Building Owner:</strong> <br/>
                        <a href="owners.php?mode=edit&id=<?php echo $building->owner()->ownerID; ?>"><?php echo $building->owner()->name; ?></a>
                    <?php
                        if(!empty($building->description))
                            echo "<p><strong>Description:</strong><br />".$building->description."</p>";
                        if(!empty($building->notes))
                            echo "<p><strong>Building Notes:</strong><br />".$building->notes."</p>";
                    ?>
                </div>

                <ul class="floors" id="<?php echo $building->buildingID; ?>">
                <?php
                    $listFloors=$floors->getByBuilding($building->buildingID);
                    $numfloors = 0;
                    foreach($listFloors as $floor)
                    {
                        $numfloors += 1;
                ?>
                    <li id="<?php echo $floor->floorID; ?>" >
                        <table><tr>
                            <td width="100%">
                            <div onclick="location.href = 'rooms.php?floor=<?php echo $floor->floorID; ?>';" style="width:100%;padding: 2px 0px; cursor:pointer;">
                                <strong>Level: <span id="buildingMenuFloor<?php echo $floor->floorID; ?>"><?php echo $floor->name;?></span></strong>
                            </div>
                            </td>
                            <td>
                                <img title="Drag to re-order" style="cursor:move;" src="images/icons/drag_list.gif" alt="Sort" onclick="return false;"/>
                            </td></tr></table>
                    </li>
                <?php
                    }
                ?>
                </ul>

			
                <div class="addFloor" >
                    <em><a onclick="$('.addFloor div').slideToggle();"> Add Floor</a></em>
                        <div style="display:none;border-top:0px;">
                            <form method="GET" action="buildings.php" >
                            <input type="hidden" name="buildingID" value="<?php echo $building->buildingID;?>" />
                            <table class="formTable">
                                <colgroup align="left" class="tblfirstRow"></colgroup>
                            <tr>
                                <td><label for="name" >Name:</label></td><td><input name="name" type="text" id="name" value="" /></td>
                            </tr>
                            <tr>
                                <td><label for="notes" >Notes:</label></td><td><textarea name="notes"  id="notes" cols="25" rows="4" ></textarea></td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="button" name="btnSubmit" onclick="AddFloor(<?php echo $building->buildingID ?>);return false;" value="Insert" /></td>
                            </tr>
                            </form>
                            </table>
                        </div>
                        </div>
            </div>
	<?php
	break;


        	case "getCabinetMenu":
?>
	<div class="node">
	<?php
            $cabinet = new cabinet($_POST['cabinetID']);
            $shelves = new shelves;
	?>
		<div class="popupMenu" id="cabinetMenu<?php echo $cabinet->cabinetID; ?>">
                    <div class="title" >
                        <table>
                            <thead><tr>
                                <th align="left">Cabinet: <?php echo $cabinet->name; ?></th>
                                <th align="right" ><span class="closeContextMenu" ><img src="images/icons/close_rack.gif" border="0" alt="Close" title="Close" style="cursor:pointer;" /></span></th>
                            </tr></thead>
                        </table>
                    </div>
                    <script>
                    $('.launchExternalDOM').openDOMWindow({
                        eventType:'click',
                        width:'750',
                        height: '400',
                        borderSize: 3,
                        windowSource: 'ajax',
                        overlayOpacity: '30',
                        fixedWindowY: 50,
                        borderColor: '#3b4c50',
                        borderSize: 2,
                    windowPadding:0
                        });
                    </script>

                    <div class="desc" id="cabinetDetails<?php echo $cabinet->cabinetID; ?>" >
                    <table>
                        <tr>
                            <td width="100%">
                                <strong>Cabinet Owner:</strong> <br/>
                                    <a href="owners.php?mode=edit&id=<?php echo $cabinet->owner()->ownerID; ?>"><?php echo $cabinet->owner()->name; ?></a>
                                <?php
                                    if(!empty($cabinet->notes))
                                        echo "<p><strong>Cabinet Notes:</strong><br />".$cabinet->notes."</p>";
                                ?>
                            </td>
                            <td valign="top">
                                <a href="buildings.php?action=cabinet&mode=edit&id=<?php echo $cabinet->cabinetID; ?>" class="launchExternalDOM" >[Edit]</a>
                            </td>
                        </tr>
                    </table>
                    </div>
                    <?php
                    $racks = new racks;
                    $childRacks = $racks->getByParent($cabinet->cabinetID, "cabinet");
                    if($childRacks)
                    {
                    ?>
                        <div class="subTitle"><table><thead><tr><th align="left">Load Rack within Cabinet</th></tr></thead></table></div>
                        <div class="desc" id="selectCabinetRacks" >
                            <table width="100%" cellspacing="2"><tr>
                            <?php
                            $i=1;
                            foreach($childRacks as $rack)
                            {
                                echo "<td class='individualCabinet' title='Load rack content' onclick='location.href=\"racks.php?rackID=".$rack->rackID."\"' align='center' width='".round(100/count($childRacks))."'>".$i."</td>";
                                $i++;
                            }
                            ?>
                            </tr></table>
                        <?php
                        }
                        ?>
                        </div>
                </div>

	<?php
	break;
}

}
?>