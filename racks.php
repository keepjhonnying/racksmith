<?php
session_start();
include "class/db.class.php";
$selectedPage="device";
if(isset($_GET['action']) && $_GET['action'] == "create")
{
	$errors=array();
	if(isset($_GET['name']))
        {
		$name =  preg_replace("/[^a-zA-Z0-9\. -]/", '', $_GET['name']);  
		if(!$name)
			$errors[]="error_name";
	} else 
		$errors[]="error_name";
	

	if(isset($_GET['RU'])) {
		if(!is_numeric($_GET['RU']))
			$errors[]="error_RU";
	} else 
		$errors[]="error_RU";

	if(count($errors) >= 1)
		echo json_encode($errors);
	else
	{
		$racks = new racks;
		$newRack = new rack();

		$newRack->parentID = $_GET['room'];
                $newRack->parentType="room";
		$newRack->ownerID = $_GET['ownerID'];
		$newRack->model = $_GET['model'];
		$newRack->deviceTypeID = 3;
		$newRack->positionX=0;
		$newRack->positionY=0;
		$newRack->width=$_GET['width'];
		$newRack->depth=$_GET['depth'];
		$newRack->height=$_GET['height'];
                $newRack->sideMountable=$_GET['sideMountable'];
		$newRack->RU=$_GET['RU'];
		$newRack->name=$name;
		$newRack->notes=$_GET['notes'];
		
		// Create the new rack and get its ID
		$newRackID = $racks->insert($newRack);
		if($newRackID)
                    echo json_encode(array("created",$newRackID));
		else
                    echo json_encode(array("error_notcreated"));
	}
}
elseif(isset($_GET['action']) && $_GET['action']=="moveDevice")
{
	$racks = new racks;
	$newPosition = preg_replace('[\D]',"",$_GET['newPos']);
	$rackID = preg_replace('[\D]',"",$_GET['rackID']);

        if(isset($_GET['back']) && $_GET['back']==1)
            $back=1;
        else
            $back=0;

        $device = new device($_GET['deviceID']);
        $device->parentID=$rackID;
        $device->position=$newPosition;
        $devices = new devices;
        $devices->update($device);
	//echo $racks->move_device(,$rackID,$newPosition,$back);
}

elseif(isset($_GET['action']) && $_GET['action']=="moveToStock")
{
	$devices = new devices;
	//$rackID = preg_replace('[\D]',"",$_GET['rackID']);
	echo $devices->moveToInventory($_GET['deviceID'],0);
}

elseif(isset($_GET['action']) && $_GET['action']=="loadRack" && is_numeric($_GET['rackID']))
{
        if(!isset($_GET['back']) || (isset($_GET['back']) && $_GET['back']==0))
            $back=false;
        else
            $back="back";
	$devices = new devices;
	$rack = new rack($_GET['rackID']);

        // if we have a cabinet add its name
        if($rack->parentType=='cabinet')
        {
            $cabinet = new cabinet($rack->parentID);
            $rack->name=$cabinet->name.$rack->name;
        }
	
	// add the rack they want into the session
	// allows us to open it by default
	$user = new user($_SESSION['userid']);

        // append a b to the rackID if we are viewing the back of a rack
        if(!$back)
            $user->addRacktoSession($_GET['rackID']);
        else
            $user->addRacktoSession($_GET['rackID']."b");

        // Get a list of all the devices in this rack
	$listDevices = $devices->getByParent($_GET['rackID'],"rack",$back);
        $rackDevices=array();
        foreach($listDevices as &$item)
        {
            // get all the attributes associated with this device that we need
            //$item->fillCategories(1,1,array(GENERIC,RACK_MOUNTABLE));
            
            // move it into an array with the index as the RU it occupies
            if(isset($item->position))
                $rackDevices[$item->position]=$item;
            
        }

	echo '
	<div class="rackHolder" id="rackHolder_'.$rack->rackID; if($back) { echo "_back"; } else { echo "_front"; } echo '">
	<div class="rackTop" ></div>
	<table cellpadding="0" cellspacing="0" id="racktbl_'.$rack->rackID.'" class="racktbl" >
		<thead>
		<tr><th colspan="2"><div class="rackTitle" ><a title="Edit" href="#'.$rack->rackID.'" class="editRack" ><span class="edit" ></span></a>';

       // we only want to rotate a rack if a room item, (excludes cabinets and future holders)
       if($rack->parentType=="room")
            echo '  <a title="Toggle Back/Front View" onclick="rotateRack('.$rack->rackID.')" class="rotateRack" ><span class="rotate" ></span>';
            
        echo '</a>'.$rack->name.'<span class="rackView">'; if($back) { echo ":back view"; } echo '</span>
            <span class="close" >
                <a href="#" onclick=\'loadRack('.$_GET['rackID'].','.$back.');\' >
                    <img src="images/icons/close_rack.gif" border="0" alt="Close Rack" />
                </a>
            </span></div></th></tr>
		</thead>
		<tbody>
	<tr><td class="rackID" >'.$rack->RU.'</td>
	<td valign="top" class="rackContent" rowspan="'.$rack->RU.'">';
	
        echo "\n\n<div class=\"rack rack_".$_GET['rackID']."\" id=\"".$_GET['rackID']."\">\n";
	for($i=$rack->RU;$i>=1;$i--)
	{
            echo "<div class=\"drop d$i "; if($back) { echo "back"; } echo "\" id=\"$i\">";

            // if the position matches the current RU
            if(isset($rackDevices[$i]))
            {
                if(isset($rackDevices[$i]->RUlocation))
                    $RU=$rackDevices[$i]->RUlocation;
                else
                    $RU=0;
                
                // if the back value was passed in the URL and the device is listed as back facing
                // else if back wasn't specified within the URL/DB we must be front facing
                //if(($back && $item->back==1) || (!$back && !$item->back))
                    echo "<div class=\"drag r".$RU." template".$rackDevices[$i]->templateID."\" id=\"".$RU."\" ><span class=\"device".$rackDevices[$i]->deviceID."\">".$rackDevices[$i]->name."</span></div> \n";
            }
            echo "</div>\n";
	}
	echo "	<div class=\"base\" ></div>\n</div>\n\n
	</td></tr>";
	
	for($i=$rack->RU-1;$i>=1;$i--)
		echo "<tr><td class=\"rackID\" >$i</td></tr>";

	echo "</td></tbody></table>
	</div><div class=\"rackBase\" ></div>";

}

elseif(isset($_GET['action']) && $_GET['action']=="closeRack" && is_numeric($_GET['rackID']))
{
    // append a b to the rackID if its the back of a device
    if(isset($_GET['back']) && $_GET['back']==1)
        $back="b";
    else
        $back="";
	
    $user = new user($_SESSION['userid']);
    $user->removeRackFromSession($_GET['rackID'].$back);
}

else
{
	// set the page title and include the header
	$globalTopic="Racks &amp; Devices";
	
	$owners = new owners; 
	$buildings = new buildings;
	$floors = new floors;
	$floors->cacheAll();

	$rooms = new rooms;
	$rooms->cacheAll();

	$racks = new racks();
	$racks->getAll();
	
	// Baseic functions to return parents IDs
	// we'll call these quickly when determining buildingID from rackID
	function floorIDToBuildingID($floorID)
	{
            if(isset($floorID) && is_numeric($floorID))
            {
                    $currentFloor = new floor($floorID);
                    return $currentFloor->buildingID;
            }
	}
	function roomIDToFloorID($roomID)
	{
            if(isset($roomID) && is_numeric($roomID))
            {
                    $currentRoom = new room($roomID);
                    return $currentRoom->floorID;
            }
	}
        
	function rackIDToRoomID($rackID)
	{
            if(isset($rackID) && is_numeric($rackID))
            {
                $currentRack = new rack($rackID);
                return $currentRack->parentID;
            }
	}	
	
	
	/* 
		The user can specify a building/floor/room/rack via $_GET
		based off this we determine that values parentIDs back to
		the building
		This lets us filter the racks shown in the right menu down
		to their requested object
		
		The filtering is down in the listing code below
	*/
	if(isset($_GET['buildingID']) && is_numeric($_GET['buildingID']))
		$buildingID=$_GET['buildingID'];
	else
            if(isset($_GET['floorID']) && is_numeric($_GET['floorID']))
            {
                $floorID=$_GET['floorID'];
                $buildingID=floorIDtoBuildingID($floorID);
            }
            else
                if(isset($_GET['roomID']) && is_numeric($_GET['roomID']) && !isset($_GET['rackID']))
                {
                    $roomID=$_GET['roomID'];
                    $floorID = roomIDToFloorID($roomID);
                    $buildingID=floorIDtoBuildingID($floorID);
                }
                else
                    if(isset($_GET['rackID']) && is_numeric($_GET['rackID']))
                    {
                        $rackID=$_GET['rackID'];
                        $roomID=rackIDToRoomID($rackID);
                        $floorID = roomIDToFloorID($roomID);
                        $buildingID=floorIDtoBuildingID($floorID);

                        // If a rack was specified make it show on load
                        $header = "<script type='text/javascript'> $(document).ready(function (){loadRack(" . $rackID . ");});</script>";
                    }
	include "theme/" . $theme . "/top.php";
?>
        <script src="theme/js/jquery.event.drag-1.5.min.js" type="text/javascript"></script>
        <script src="theme/js/jquery.event.drop-1.2.min.js" type="text/javascript"></script>
        <script src="theme/js/rack_drag.js" type="text/javascript"></script>
        <link rel='stylesheet' href='theme/room.css' type='text/css' />
        <link type="text/css" href="theme/rack.css" rel="stylesheet" />
        <script type='text/javascript' src='theme/racks.js'></script>
        <script type='text/javascript' src='theme/ui.mouse.js'></script>
        <script type='text/javascript' src='theme/ui.widget.js'></script>
	<script type='text/javascript' src='theme/popup.js'></script>
	<script type='text/javascript' src='theme/navigate.js'></script>
	<script type='text/javascript' src='theme/js/mapbox.min.js'></script>

		<script type='text/javascript'>
		$(document).ready(function (){ 
<?php
			// load any racks the user already has in the system
				$user = new user($_SESSION['userid']);
				$loaded=array();
				foreach($user->listSessionItems('rack') as $preload)
				{

                                    // check if the value has a b, if so we are viewing the back
                                    // and we must remove the b so we are left with the rackID
                                    if(strstr($preload['itemID'],'b'))
                                        $back=1;
                                    else
                                        $back=0;

					if((isset($_GET['rackID']) && $_GET['rackID']==$preload['itemID']) || in_array($preload['itemID'],$loaded))
						continue;
					echo "			loadRack(".str_replace("b","",$preload['itemID']).",".$back.");\n";
					$loaded[]=$preload['itemID'];
				}
				
				if (isset($_GET['roomID']))
					echo "			loadRoom2(" . $_GET['roomID'] . ");\n";
				else
					echo "			loadBuildings2();\n";
			?>
			$('#viewPortal').mapbox({pan: true, zoom: false,defaultX:0,defaultY:0});
		});
		</script>
	
<div id="main">

<div id="left" style='padding-top: 5px;'>
        <?php
        $cabinets = new cabinets;
        if (!$buildings->returnCount() && !$cabinets->returnCount())
        {
                ?>
    
    <div class="module" >
        <strong>No Layout Found</strong>

    <p>It looks like you have no buildings to put your systems in, you should head to the layout section and define a basic infrastructure first.</p>

    <p><a href="buildinglayout.php">Layout Manager</a></p>
    </div>

<?php
        }



        ?>


	<div style='margin: 0px 380px 0px 0px;' >
	
	<div id="hoverName" style='display:none;'></div>
	
	<div id="rackPanel" >
            <table><tr id="rackHolderData" >
                    
                    
            </tr></table>
            <div id="draggingRackHolder" ><table></table></div>
	</div>

	</div>
    
        <div class="module" id="createDevice" style="display: none">
            <strong>Create a new Device</strong>
            <a href="#" class="closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
        </div>
	</div>
    <div id="menus" style="display:none;width: 600px;" ></div>
    <div id="right">
            <div class="module" id="sideNavigation">

                <div id="navigation" >
                    <div id="currentArea" align="center"></div>
                    <div id="miniList"></div>
                    <div id="viewPortal" >
                    <div id="miniMap"  align="center" ></div>
					
                    </div>
                    <div id="miniMapHover" style='display:none;'></div>
                    <div id="navigate" style='cursor:pointer;'></div>
                </div>
			
                <div id="mapSpacer"></div>
            </div>
                
            <div class="module" id="rackDeviceList" >
                    
			<div class="racksInventory inventoryHolder">
			<?php
                            // retrieve all items from the inventory & count them
                            $devices = new devices;
                            $listDevices = $devices->getByParent('0','rack');
                            $totalItems=count($listDevices);
			?>
				<a href="#" id='inventoryTitle'>Inventory (<i><?php echo $totalItems; ?></i>)</a>
				<div class="inventory stock" id="stock" style="padding: 0px;"><ul>
					<li class="search" ><div>Search: <input class='inventorySearchField' type='text' size='20' style="padding-left:2px;"/>
					<img src='images/icons/close_module.gif' style='display:none;padding-left:3px;' alt='clear search' class='inventoryClearSearch' /></div></li>
				<?php
				// Loop over each inventory item and add them to panel
				foreach($listDevices as $item)
					echo "		<li class=\"item\"><div class=\"stockItem drag r".$item->RU." template".$item->templateID."\" id=\"".$item->RU."\" ><span class=\"device".$item->deviceID."\">".$item->systemName."</span></div></li> \n";
				echo "				</ul></div>\n";
				
				// find all item types and display a box for them
				$templatesClass = new templates;
                                $deviceTypesClass = new deviceTypes;
                                $deviceTypes=$deviceTypesClass->getAll();
                                $templates = $templatesClass->getByCategory(RACK_MOUNTABLE);
                                if(is_array($templates))
                                {
                                    foreach($templates as $key=>$deviceTypeTemplates)
                                    {
                                    $totalItems=0;
                                    $totalItems=count($deviceTypeTemplates);
                                    ?>
                                        <a href="#" ><?php echo $deviceTypes[$key]->name." <i>($totalItems)</i>"; ?></a>
                                            <div class="inventory" style="padding: 0px;">
                                                <ul>
                                                <li class="search"><div>Search: <input class='inventorySearchField' type='text' size='20' style="padding-left:2px;"/>
                                                <img src='images/icons/close_module.gif' style='display:none;padding-left: 3px;' alt='clear search' class='inventoryClearSearch' /></div></li>
                                <?php
                                        foreach($deviceTypeTemplates as $template)
                                        {
                                            if($template->deleted!=1)
                                            {
                                                // A dirty hack to speed up page display with multiple templates
                                                // issue will exist with very full racks, need cleaner solution
                                                $results=$template->getBriefDetails();
                                                $ru=$results[0]['value'];
                                                if(isset($results[1]['value']))
                                                    $vendorName=$results[1]['value'];
                                                else
                                                    $vendorName="";
                                                
                                                echo '<li class="item"><div class="inventoryItem r'.$ru.'" id="'.$ru.'" href="#'.$template->templateID.'"><span>'.  ucfirst($vendorName). ' ' . $template->name.'</span></div></li>';
                                            }
                                        }
						echo "
					<li class='inventoryAdd'><a href='manageTemplates.php?deviceType=".$key."' >Create new template</a></li>
					</ul></div>";
                                    }
                                }
                                ?>
			</div>
		</div>
        <div class="module" >
            <center><strong><a href="templates.php">Manage Templates</a></strong></center>
        </div>
	</div>
    </div>


</div>
<?php

include "theme/" . $theme . "/base.php";

}

?>