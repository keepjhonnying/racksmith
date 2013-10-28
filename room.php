<?php
session_start();
$selectedPage="layout";
include "class/db.class.php";
$floors = new floors;
$rooms = new rooms;
$buildings = new buildings;
$logs = new logs;

$room=false; $floor=false; $building=false;
// check the user was requesting a valid room
if(isset($_GET['room']) && is_numeric($_GET['room']))
{
    $room = new room($_GET['room']);
    $floor = new floor($room->floorID);
    $building = new building($floor->buildingID);
}
// if invalid send them back to buildings
if(!$room && !$floor && !$building)
    header("Location: buildings.php");

$header = "<script type='text/javascript' > var roomID = " . $_GET['room'] . "; </script>";
$header .= "<script type='text/javascript' src='theme/room.js'></script>";
$header .= "<script type='text/javascript' src='theme/js/mapbox.min.js'></script>";
$header .= "<script type='text/javascript' src='theme/js/rotate.js'></script>";
$header .= "<link rel='stylesheet' type='text/css' href='theme/room.css' /> ";
$header .= "<link rel='stylesheet' type='text/css' href='theme/rack.css' /> ";
$globalTopic="Manage and View Racks";
include "theme/" . $theme . "/top.php";
?>
<script type="text/javascript"> 
    $(document).ready(function() {
        resizeCanvas("#viewPortal");
        $('#viewPortal').mapbox({pan: true, zoom: false,defaultX:0,defaultY:0});
    }); 
</script>
<style>
    .mapContent { <?php
if(isset($_GET['room']) && is_numeric($_GET['room']))
    if(file_exists("images/uploads/background_roomID".$_GET['room'].".jpg"))
        echo " background: #ffffff url('images/uploads/background_roomID".$_GET['room'].".jpg') no-repeat;";
    else if(file_exists("images/uploads/background_roomID".$_GET['room'].".jpeg"))
        echo " background: #ffffff url('images/uploads/background_roomID".$_GET["room"].".jpeg') no-repeat;";
    else if(file_exists("images/uploads/background_roomID".$_GET['room'].".png"))
        echo " background: #ffffff url('images/uploads/background_roomID".$_GET["room"].".png') no-repeat;";
    else if(file_exists("images/uploads/background_roomID".$_GET['room'].".gif"))
        echo " background: #ffffff url('images/uploads/background_roomID".$_GET['room'].".gif') no-repeat;";
    else
        echo " background: #ffffff;";
?> }
</style>
<div id="main"> 

<div id="full">


<div class="module" id="breadcrumb">
 <span style="float:left" ><strong><a href="buildings.php">Buildings</a> &#187; <?php echo $building->name ?> &#187; <a href="rooms.php?floor=<?php echo $floor->floorID ?>">Level: <?php echo $floor->name ?></a> &#187; <?php echo $room->name ?></strong></span>
 <span id='links' style='float: right;' ><a href="roomlayout.php?room=<?php echo $_GET['room'] ?>" >Room Layout</a> </span>
</div>
	<div class="module" id='canvasHolder' >
            <div id="viewPortal" >
                <div>
                    <?php
                    $config = new config;
                    $canvasConfigSizeX=$config->returnItem('room'.$room->roomID.'CanvasX');
                    if(is_numeric($canvasConfigSizeX) && $canvasConfigSizeX!=0)
                            $canvasX=$canvasConfigSizeX;
                    else
                            $canvasX=2000;

                    $canvasConfigSizeY=$config->returnItem('room'.$room->roomID.'CanvasY');
                    if(is_numeric($canvasConfigSizeY) && $canvasConfigSizeY!=0)
                            $canvasY=$canvasConfigSizeY;
                    else
                            $canvasY=2000;
                    ?>
                    <div class="mapContent" id="droppable" style="height: <?php echo $canvasY;?>px;width: <?php echo $canvasX;?>px;border: 2px solid #000000;" >
                    </div>
                </div>
            </div>

		</div> 
		<div id='canvasToolbarSmall'><ul><strong>Toggle Items:</strong>
			<li><input type="checkbox" checked id="toggleFloortiles" onclick="$('.mapContent .floortile').toggle();" /> <label for='toggleFloortiles' >Floor Tiles</label></li>
			<li><input type="checkbox" checked id="toggleRacks" onclick="$('.mapContent .rack1').toggle();" /> <label for='toggleRacks' >Racks</label></li>
			<li><input type="checkbox" checked id="toggleCabletrays" onclick="$('.mapContent .cabletray').toggle();" /><label for='toggleCabletrays' >Cable Trays</label></li>
                        <li><input type="checkbox" checked id="toggleDevices" onclick="$('.mapContent .device').toggle();"/><label for='toggleDevices' >Devices</label></li>
		</ul></div>
	</div>
	<div id="menus" style='display:none;'>
	</div>
	<div id="hoverName" style='display:none;'>
	</div>
</div>

	
<?php
include "theme/" . $theme . "/base.php";
?>