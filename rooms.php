<?php
session_start();
$selectedPage="layout";
include "class/db.class.php";
$floors = new floors;
$rooms = new rooms;
$buildings = new buildings;
$logs = new logs;

if (isset($_GET['floor']) && is_numeric($_GET['floor']))
{
	$floor = new floor($_GET['floor']);
	$building = new building($floor->buildingID);
	$allRooms = $rooms->getByFloor($floor->floorID);
}


// If submit was posted check what happened
if(isset($_POST) && $_POST)
{

// for edits create an object and populate with existing data
// for new item create object and set from new
// pass the object back to the class to edit/create
	if (isset($_GET['action']) && $_GET['action'] == 'room')
	{
		if (isset($_GET['mode']) && $_GET['mode'] == 'edit')
		{
	  		$newRoom = new room($_GET['id']);
			$newRoom->name = $_POST['name'];
			$newRoom->notes = $_POST['notes'];
			$newRoom->ownerID = $_POST['ownerID'];

			$rooms->update($newRoom);	
			header("Location: rooms.php?floor=".$_GET['floor']);
		}	
		else if(isset($_GET['mode']) && $_GET['mode'] == 'insert')
		{
			$newRoom = new room;
			$newRoom->name = $_POST['name'];
			$newRoom->notes = $_POST['notes'];
			$newRoom->buildingID = $floor->buildingID;
			$newRoom->floorID = $floor->floorID;
			$newRoom->color = "#8b96b0";
			$newRoom->ownerID = $_POST['ownerID'];
		
			$rooms->insert($newRoom);
			
			$user=$_SESSION['userid'];

			$newLog = new log;
			$newLog->event = "Create room "." ".$_POST['name'];
			$newLog->eventType = "";
			$newLog->previous = "";
			$newLog->comment = $_POST['notes'];
			$newLog->userID = $user;
			$logs->insert($newLog);
		
			header("Location: rooms.php?floor=" . $_GET['floor']);
		}
	}
	elseif(isset($_GET['action']) && $_GET['action'] == 'floor')
	{
		if (isset($_GET['mode']) && $_GET['mode'] == 'edit')
		{
			$floors=new floors;
	  		$newFloor = new floor($_GET['id']);
			$newFloor->name = $_POST['name'];
			$newFloor->notes = $_POST['notes'];

			$floors->update($newFloor);	
			header("Location: rooms.php?floor=".$_GET['id']);
			exit(0);
		}	
	}

}

if(isset($_GET['action']) && $_GET['action'] == 'room' && isset($_GET['mode']) && $_GET['mode'] == 'delete' && is_numeric($_GET['id']))
{
	$rooms->delete($_GET['id']);
	header("Location: rooms.php?floor=".$_GET['floor']);

}

$globalTopic="Manage and View Buildings";
$header = "<script> var floorID = " . $_GET['floor'] . "; </script>";
$header .= "<script type='text/javascript' src='theme/floor.js'></script>";
$header .= "<script type='text/javascript' src='theme/js/mapbox.min.js'></script>";
$header .= "<style id='colours'>";
$header .= ".color0 {-khtml-opacity:.75; -moz-opacity:.75; -ms-filter:'alpha(opacity=75)'; filter:alpha(opacity=75); opacity:.75; background-color:#" . str_pad(dechex(rand(0,255)),2,'0') . str_pad(dechex(rand(0,255)),2,'0') . str_pad(dechex(rand(0,255)),2,'0') . "; width:32px; height:32px; display:block;}	";
foreach($allRooms as $room)
{
	$header .= ".color" . $room->roomID . " {-khtml-opacity:.75; -moz-opacity:.75; -ms-filter:'alpha(opacity=75)'; filter:alpha(opacity=75); opacity:.75; background-color:" . $room->color . "; width:32px; height:32px; display:block;}	";
}
$header .= "</style>";
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
if(isset($_GET['floor']) && is_numeric($_GET['floor']))
    if(file_exists("images/uploads/background_floorID".$_GET['floor'].".jpg"))
        echo " background: #ffffff url('images/uploads/background_floorID".$_GET['floor'].".jpg') no-repeat;";
    else if(file_exists("images/uploads/background_floorID".$_GET['floor'].".jpeg"))
        echo " background: #ffffff url('images/uploads/background_floorID".$_GET["floor"].".jpeg') no-repeat;";
    else if(file_exists("images/uploads/background_floorID".$_GET['floor'].".png"))
        echo " background: #ffffff url('images/uploads/background_floorID".$_GET["floor"].".png') no-repeat;";
    else if(file_exists("images/uploads/background_floorID".$_GET['floor'].".gif"))
        echo " background: #ffffff url('images/uploads/background_floorID".$_GET['floor'].".gif') no-repeat;";
    else
        echo " background: #ffffff;";
?> }
</style>
<div id="main"> 

	<div id="full">
	
		<div class="module" id="breadcrumb" >
			<span style="float:left;" ><strong><a href="buildings.php">Buildings</a> &#187; <?php echo $building->name ?> &#187; Level: <?php echo $floor->name ?> [<a href="#editFloor" class="defaultDOMWindow">edit</a>]</strong></span>
			<span id='links' style='float: right;' ><a href="floorlayout.php?floor=<?php echo $_GET['floor']; ?>" >Manage Floor Layout</a></span>
		</div>

		<div class="module" id='canvasHolder' >
			<div id="viewPortal" >
				<div>
					<div class="mapContent" style="height: 1500px;width: 2000px;border: 2px solid #000000;" ></div>
				</div> 
			</div> 
		</div>

		<div class="module" id="createRoom" style="display: none"> 
		<strong>Create Room</strong>
		<a href="#" class="closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
		<p>
			<div class="form" align="center">
			<form method="post" action="?action=room&mode=insert&floor=<?php echo $_GET['floor'] ?>" id="createRoom">
			<table width="80%">
				<tr>
                                    <td><label for="name" >Name:</label></td><td><input name="name" type="text" id="name" value="" /></td>
				</tr>
				<tr>
                                    <td><label for="ownerID" >Owner:</label></td><td><select name="ownerID">
                                    <?php
                                      $owners = new owners;
                                      foreach($owners->getAll() as $owner) {
                                    ?>
                                            <option value="<?php echo $owner->ownerID?>"><?php echo $owner->name?></option>
                                    <?php } ?>
                                    </select> [<a href="help.php?page=owners">Help</a>]</td>
				</tr>
				<tr>
                                    <td><label for="notes" >Notes:</label></td><td><input name="notes" type="text" id="notes" value="" /></td>
				</tr>
			<tr>
				<td><label for="color" >Color:</label></td><td><input onclick='$("#picker").toggle();' size='3' type="text" id="color" name="color" style='background-image: none;'/>
				<div id='picker' style='display:none;border: 1px solid #cccccc;'></div></td>
			</tr>
				<tr>
					<td></td><td><input type="submit" name="btnSubmit" value="Create" /></td>
				</tr>
			</table>
			</form>
			</div>
		</p>
		</div>
		
		<div class="module" id="editFloor" style="display: none"> 
		<strong>Edit floor <u><?php echo $floor->name;?></u> in <?php echo $building->name ?></strong>
		<a href="#" class="closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
		<p>
			<div class="form" align="center">
			<form method="post" action="?action=floor&mode=edit&id=<?php echo $_GET['floor'] ?>" id="editFloor">
			<table width="80%">
				<tr>
					<td><label for="name" >Name:</label></td><td><input name="name" type="text" id="name" value="<?php echo $floor->name;?>" /></td>
				</tr>
				<tr>
					<td><label for="notes" >Notes:</label></td><td><textarea name="notes" id="notes" style='width: 600px;height: 100px;' ><?php echo $floor->notes;?></textarea></td>
				</tr>
				<tr>
					<td></td><td><input type="submit" name="btnSubmit" value="Update" /></td>
				</tr>
			</table>
			</form>
			</div>
		</p>
		</div>

	</div>

</div>

<?php
include "theme/" . $theme . "/base.php";
?>
