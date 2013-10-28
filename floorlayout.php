<?php
session_start();
$selectedPage="layout";
include "class/db.class.php";

if (isset($_GET['action']) && $_GET['action'] == 'room')
{	  
	if(isset($_GET['mode']) && $_GET['mode'] == 'edit')
	{
	$currentRoom = new room($_GET['id']);
	
	// if a referrer was set we want to move back there after the submission
	// it could only be the layout page so limit it
	if(isset($_GET['from']) && $_GET['from'] == 'layoutPage')
		$refer='&from=layout';
	else
		$refer='';
?>
 <script type="text/javascript" src="theme/colorwheel/farbtastic.js"></script>
  <script type="text/javascript" charset="utf-8">
 $('#pickeredit').farbtastic('#coloredit');
 </script>
 <link rel="stylesheet" href="theme/colorwheel/farbtastic.css" type="text/css" />
 <div id="editRoom">
<a href="#" class="closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
	<strong>Edit <?php echo $currentRoom->name; ?></strong>
	<p>
		<div class="form" align="center">
		<form method="post" action="floorlayout.php?action=floor<?php echo $refer;?>&mode=edit&id=<?php echo $currentRoom->roomID; ?>&floorID=<?php echo $currentRoom->floorID; ?>" id="editRoom">
		<table width="80%" class="formTable">
                    <colgroup align="left" class="tblfirstRow"></colgroup>
			<tr>
				<td><label for="name" >Name:</label></td><td><input name="editname" type="text" id="editname" value="<?php echo $currentRoom->name; ?>" /></td>
			</tr>
			<tr>
				<td><label for="ownerID" >Owner:</label></td><td><select name="ownerID">
				<?php
				  $owners = new owners;
				  foreach($owners->getAll() as $owner) { ?>
					<option value="<?php echo $owner->ownerID?>" <?php if ($currentRoom->ownerID == $owner->ownerID) echo "selected"; ?>><?php echo $owner->name?></option>
				<?php } ?>
				</select> [<a href="help.php?page=owners">Help</a>]</td>
			</tr>
			<tr>
				<td><label for="notes" >Notes:</label></td><td><textarea name="notes"  id="notes" rows="5" style="width: 600px;" ><?php echo $currentRoom->notes; ?></textarea></td>
			</tr>
			<tr>
				<td><label for="color" >Color:</label></td><td><input onclick='$("#pickeredit").toggle();' size='7' type="text" id="coloredit" name="color" value="<?php echo $currentRoom->color; ?>" style='background-image: none;'/>
				<div id='pickeredit' style='display:none;border: 1px solid #cccccc;'></div></td>
			</tr>
			<tr>
				<td></td><td><input type="submit" value="Update" /> [<a href="help.php?page=updateRoom">Help</a>]</td>
			</tr>
			<tr><td colspan='2' style='text-align: right;'>
				<a href='#' 
				onclick="if (confirm('Are you sure you want to delete this room?')) { location.href = 'floorlayout.php?action=room&mode=delete&from=layoutPage&floorID=<?php echo $currentRoom->floorID; ?>&id=<?php echo $currentRoom->roomID; ?>'; } "
				>[ Delete Room ]</a>
				
			</td></tr>
		</table>
		</form>
		</div>
	</p>
	</div>

<?php
	}

	// delete a room
	else if (isset($_GET['mode']) && $_GET['mode'] == 'delete')
	{
		$newRoom = new rooms();
		$newRoom->delete($_GET['id']);
		header("Location: floorlayout.php?floor=".$_GET['floorID']);
	}	
}

else if(isset($_POST) && isset($_FILES['userfile']) && $_FILES['userfile'])
{
	include "class/upload.fn.php";
        $filename = basename($_FILES['userfile']['name']);
        $ext = substr($filename, strrpos($filename, '.') + 1);
        $ext = strtolower($ext);
	// uploadFile(file,folderPath,fileName,overwriteFlag)
	$val = uploadFile($_FILES['userfile'],"images/uploads/","background_floorID".$_POST['floorID'].".".$ext,1);
	if($val==1)
	{
		// find the size of the uploaded image if possible
		// if its > 700x700 then save its dimentions to the config for the canvas size
		// else default to a 1500x1500 canvas
		$imageSize = getimagesize('images/uploads/background_floorID'.$_POST['floorID'].".".$ext);
		if(is_numeric($imageSize[0]) && $imageSize[0] >= 700)
			$sizeX=$imageSize[0];
		else
			$sizeX=1500;

		if(is_numeric($imageSize[1]) && $imageSize[1] >= 700)
			$sizeY=$imageSize[1];
		else
			$sizeY=1500;
		$config = new config;
		$config->setItem('buildingCanvasX',$sizeX);
		$config->setItem('buildingCanvasY',$sizeY);

		header("Location: ".$_SERVER['PHP_SELF']."?floor=".$_POST['floorID']."&upload=complete");
	}
	else
	{
		echo "<b>Error Uploading</b><br/>";
		switch($val)
		{
			case "error_uploading_moving":
				echo "There was an error while trying to transfer the file on the server. Please check that you have permissions to write the file to the server";
				break;
			case "file_size_or_ext":
				echo "The file was not accepted for upload, please check the size and extension";
				break;
			case "no_file_provided":
				echo "Oops..<br/>We couldn't find the file you wanted to upload, please check the form and try again";
				break;
			default;
				echo $val;
				break;
		};
	}
}

// simple function to delete a building background IMG
elseif(isset($_GET['action']) && $_GET['action'] == 'removeBG' && is_numeric($_GET['floor']))
{
	$deleted=0;
	if(file_exists('images/uploads/background_floorID'.$_GET['floor'].'.jpg'))
		if(unlink('images/uploads/background_floorID'.$_GET['floor'].'.jpg'))
			$deleted=1;
	if(file_exists('images/uploads/background_floorID'.$_GET['floor'].'.jpeg'))
		if(unlink('images/uploads/background_floorID'.$_GET['floor'].'.jpeg'))
			$deleted=1;
	if(file_exists('images/uploads/background_floorID'.$_GET['floor'].'.png'))
		if(unlink('images/uploads/background_floorID'.$_GET['floor'].'.png'))
			$deleted=1;
	if(file_exists('images/uploads/background_floorID'.$_GET['floor'].'.gif'))
		if(unlink('images/uploads/background_floorID'.$_GET['floor'].'.gif'))
			$deleted=1;
	if($deleted)
		header("location: ".$_SERVER['PHP_SELF']."?action=deletedBackground&floor=".$_GET['floor']);
	else
		header("Location: ".$_SERVER['PHP_SELF']."?error=cannotDelBG&floor=".$_GET['floor']);
}


// edit room
else if (isset($_GET['mode']) && $_GET['mode'] == 'edit')
{
	$newRoom = new room($_GET['id']);
	$newRoom->name = $_POST['editname'];
	$newRoom->notes = $_POST['notes'];
	$newRoom->ownerID = $_POST['ownerID'];
	$newRoom->color = $_POST['color'];

	$rooms = new rooms;
	$rooms->update($newRoom);
	header("Location: floorlayout.php?floor=".$_GET['floorID']);
}	

// DEFAULT view room
else
{
	$buildings = new buildings;
	$floors = new floors;
	$rooms = new rooms;
	$logs = new logs;

	if (isset($_GET['floor']) && is_numeric($_GET['floor']))
	{
		$floor = new floor($_GET['floor']);
		$building = new building($floor->buildingID);
		$allRooms = $rooms->getByFloor($floor->floorID);
	}

	if(!isset($_GET['rackID']) && !isset($_GET['unitID']))
		$globalTopic="Floor Layout";
	$header = "<script type='text/javascript'> var floorID = " . $_GET['floor'] . "</script>";
	$header .= "<script type='text/javascript' src='theme/js/mapbox.min.js'></script>";
	$header .= "<script type='text/javascript' src='theme/floorlayout.js'></script>";
	$header .= '<script type="text/javascript" src="theme/colorwheel/farbtastic.js"></script>';
	$header .= '  <link rel="stylesheet" href="theme/colorwheel/farbtastic.css" type="text/css" />';

	// Style For Rooms On Right
	$header .= "<style id='colours'>";
	$header .= ".color0 {-khtml-opacity:.75; -moz-opacity:.75; -ms-filter:'alpha(opacity=75)'; filter:alpha(opacity=75); opacity:.75; background-color:#" . str_pad(dechex(rand(0,255)),2,'0') . str_pad(dechex(rand(0,255)),2,'0') . str_pad(dechex(rand(0,255)),2,'0') . "; width:32px; height:32px; display:block;}	";
	foreach($allRooms as $room)
	{
		if($room->color)
			$color=$room->color;
		else
			$color="#".str_pad(dechex(rand(0,255)),2,"0") . str_pad(dechex(rand(0,255)),2,"0") . str_pad(dechex(rand(0,255)),2,"0");
			
		$header .= ".color" . $room->roomID . " {-khtml-opacity:.75; -moz-opacity:.75; -ms-filter:'alpha(opacity=75)'; filter:alpha(opacity=75); opacity:.75; background-color:" . $color . "; }	";
	}
	$header .= "</style>";
	// End Rooms on Right

	include "theme/" . $theme . "/top.php";
?> 
<script type="text/javascript"> 
    $(document).ready(function() { 		
		resizeCanvas("#viewPortal");
		$('#viewPortal').mapbox({pan: true, zoom: false,defaultX:0,defaultY:0}); 
		$('#picker').farbtastic('#color');
    }); 
</script> 
<style>
    #droppable { width:636px; display:block; height:500px;}
    .mapContent { <?php
if(isset($_GET['floor']) && is_numeric($_GET['floor']))
    if(file_exists("images/uploads/background_floorID".$_GET['floor'].".jpg"))
        echo " background: #CCCCCC url('images/uploads/background_floorID".$_GET['floor'].".jpg') no-repeat;";
    else if(file_exists("images/uploads/background_floorID".$_GET['floor'].".jpeg"))
        echo " background: #CCCCCC url('images/uploads/background_floorID".$_GET["floor"].".jpeg') no-repeat;";
    else if(file_exists("images/uploads/background_floorID".$_GET['floor'].".png"))
        echo " background: #CCCCCC url('images/uploads/background_floorID".$_GET["floor"].".png') no-repeat;";
    else if(file_exists("images/uploads/background_floorID".$_GET['floor'].".gif"))
        echo " background: #CCCCCC url('images/uploads/background_floorID".$_GET['floor'].".gif') no-repeat;";
    else
        echo " background: #CCCCCC;";
?> }
</style>
	
<div id="main"> 
	<div id="left"> 
		<div class="module" id="breadcrumb" >
			<span style="float:left;" ><a href="buildings.php">Buildings</a> &#187; <?php  echo $building->name;?> &#187; <strong>Level: <?php  echo $floor->name;?> - Manage Layout</strong></span>
			<span id='links' style='float: right;' ><a href='rooms.php?floor=<?php  echo $floor->floorID; ?>' >Back to Floor Layout</a> - <a href="http://help.racksmith.net/guide.php?guide=managelayout" target="_help">Help</a></span>
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
			
		<div class="module" id="createRoom" style="display: none"> 
			<strong>Create Room</strong>
			<a href="#" class="closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
			<p>
				<div class="form" align="center" id="createRoomForm">
				<form method="post">
				<table width="80%" class="formTable">
                                    <colgroup align="left" class="tblfirstRow"></colgroup>
					<tr>
						<td><label for="roomName" >Name:</label></td>
						<td>
							<input name="roomName" type="text" id="roomName" value="" />
							<input name="posx" type="hidden" id="posx" value="" />
							<input name="posy" type="hidden" id="posy" value="" />
							<input name="action" type="hidden" id="action" value="insertRoom" />
							<input name="floorID" type="hidden" id="floorID" value="<?php  echo $floor->floorID; ?>" />
							<input name="buildingID" type="hidden" id="buildingID" value="<?php  echo $floor->buildingID; ?>" />
						</td>
					</tr>
					<tr>
						<td><label for="ownerID" >Owner:</label></td><td><select name="ownerID">
						<?php						  $owners = new owners;
						  foreach($owners->getAll() as $owner) { 
						?>
							<option value="<?php  echo $owner->ownerID?>"><?php  echo $owner->name?></option>
						<?php } ?>
						</select> [<a href="#" title="This is the building in which the floor exists" class="tooltip">Help</a>]</td>
					</tr>
					<tr>
						<td><label for="notes" >Notes:</label></td>
						<td><textarea name="notes" id="notes" rows="6" style="width: 350px;" ></textarea></td>
					</tr>
					<tr>
						<?php $color="#".str_pad(dechex(rand(0,255)),2,"0") . str_pad(dechex(rand(0,255)),2,"0") . str_pad(dechex(rand(0,255)),2,"0");?>
						<td><label for="color" >Color:</label></td><td><input onclick='$("#picker").toggle();' size='3' type="text" id="color" name="color" value="<?php echo $color;?>" style='background-image: none;'/>
						<div id='picker' style='display:none;border: 1px solid #cccccc;'></div></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" id="btnSubmit" name="btnSubmit" value="Create" /></td>
					</tr>
				</table>
				</form>
				</div>
			</p>
		</div>
		<div class="module" id="uploadBackground" style="display: none">
                    <div class="sectionHeader" >
			<strong>Upload a background</strong>
			<a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
                    </div>
			<p>
			<div class="popupModule" id="uploadBackgroundForm" >
		<?php
			if(is_writable('images/uploads/'))
			{ 
		?>
			If you have a floor plan or detail drawing you can upload it as a background.<br/>
			This means when using the racks page browsing content will seem more intuitive.
			Images much be in PNG, GIF or JPG format and we suggest having it 1000X1000px or larger.<br/><br/><br />
			<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
			<input type="file" name="userfile" size="40" maxlength="100"><br />
			<input type="submit" name="submit" value="Start Uploading">
			<input name="name" type="hidden" id="name" value="" />
			<input name="floorID" type="hidden" id="floorID" value="<?php  echo $floor->floorID; ?>" />
			<?php
                        if(isset($_GET['floor']) && is_numeric($_GET['floor']))
                            if(file_exists("images/uploads/background_floorID".$_GET['floor'].".jpg"))
                                $filename = "images/uploads/background_floorID".$_GET['floor'].".jpg";
                            else if(file_exists("images/uploads/background_floorID".$_GET['floor'].".jpeg"))
                                $filename = "images/uploads/background_floorID".$_GET["floor"].".jpeg";
                            else if(file_exists("images/uploads/background_floorID".$_GET['floor'].".png"))
                                $filename = "images/uploads/background_floorID".$_GET["floor"].".png";
                            else if(file_exists("images/uploads/background_floorID".$_GET['floor'].".gif"))
                                $filename = "images/uploads/background_floorID".$_GET['floor'].".gif";
                            else
                                $filename = "";
			if(file_exists($filename))
				echo '<br />
				<a href="#" onclick="if (confirm(\'Are you sure you wish to delete the existing background?\')) { location.href = \''.$_SERVER["PHP_SELF"].'?action=removeBG&floor='.$floor->floorID.'\'; } "><img src="images/icons/delete_small.gif" /> Remove current background</a>
				<p><center>
					<img src="'.$filename.'" alt="Racksmith Floor Layout" width="650px" style="border: 4px solid #cccccc" />
				</center></p>';
			?>
			</form>
		<?php
			}
			else
				echo "<div class='notice' >/images/uploads/ is not writable<br/>A background image cannot be uploaded in this state.
					Please contact your system administrator or read more at <a href='http://help.racksmith.net/guide.php?search=upload+background' target='_help' >help.racksmith.net</a></div>
					<br/><br/><a href='#' class='closeDOMWindow'><img src='images/icons/close_module.gif' border='0' alt='Close' />Close this window</a><br/>";
		?>
			</div>
			</p>
		</div>
	</div>

	<div id="right">
		<div class="module">		
                    <strong>Rooms</strong>
                        <p>Drag this tiles onto the panel to represent the area a room takes up. Feel free to use many tiles combined to make more specific shapes.

                        <ul class='itemList' id="roomList">
                            <li class="addNew toolbaritem" id="room0"><span>Drag to add new Room</span></li>
                            <?php
                            foreach($allRooms as $room)
                            {
                            ?>
                                <li class="toolbaritem roomTile" style="background-color:<?php echo $room->color;?>;" id="room<?php echo $room->roomID ?>" onmouseover="$('.room<?php echo $room->roomID;?>').addClass('roomHover');" onmouseout="$('.room<?php echo $room->roomID;?>').removeClass('roomHover');">
                                    <span class="title" ><?php echo $room->name;?></span>
                                    <span class="edit roomedit" ></span>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                        </p>

                </div>
            <div class="module" style="text-align: center;">
                <strong><a href="#uploadBackground" class="newDOMWindow">Adjust the Background</a></strong>
            </div>
	</div>
</div>
<div class="roomHelper" style="display:none;"></div>
<?php 
	include "theme/" . $theme . "/base.php";
} ?>
