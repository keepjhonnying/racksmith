<?php
session_start();
$selectedPage="layout";
include "class/db.class.php";
if(isset($_POST) && isset($_FILES['userfile']) && $_FILES['userfile'])
{
	include "class/upload.fn.php";
        $filename = basename($_FILES['userfile']['name']);
        $ext = substr($filename, strrpos($filename, '.') + 1);
        $ext = strtolower($ext);
	// uploadFile(file,folderPath,fileName,overwriteFlag)
	$val = uploadFile($_FILES['userfile'],"images/uploads/","background_roomID".$_POST['roomID'].".".$ext,1);
	if($val==1)
	{
		// find the size of the uploaded image if possible
		// if its > 700x700 then save its dimentions to the config for the canvas size
		// else default to a 1500x1500 canvas
		$imageSize = getimagesize('images/uploads/background_roomID'.$_POST['roomID'].".".$ext);
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

		header("Location: roomlayout.php?room=".$_POST['roomID']."&upload=complete");
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
elseif(isset($_GET['action']) && $_GET['action'] == 'removeBG' && is_numeric($_GET['room']))
{
	$deleted=0;
	if(file_exists('images/uploads/background_roomID'.$_GET['room'].'.jpg'))
		if(unlink('images/uploads/background_roomID'.$_GET['room'].'.jpg'))
			$deleted=1;
	if(file_exists('images/uploads/background_roomID'.$_GET['room'].'.jpeg'))
		if(unlink('images/uploads/background_roomID'.$_GET['room'].'.jpeg'))
			$deleted=1;
	if(file_exists('images/uploads/background_roomID'.$_GET['room'].'.png'))
		if(unlink('images/uploads/background_roomID'.$_GET['room'].'.png'))
			$deleted=1;
	if(file_exists('images/uploads/background_roomID'.$_GET['room'].'.gif'))
		if(unlink('images/uploads/background_roomID'.$_GET['room'].'.gif'))
			$deleted=1;
	if($deleted)
		header("location: ".$_SERVER['PHP_SELF']."?action=deletedBackground&room=".$_GET['room']);
	else
		header("Location: ".$_SERVER['PHP_SELF']."?error=cannotDelBG&room=".$_GET['room']);
}
else
{
    $room=false; $floor=false; $building=false;
    // check the user was requesting a valid room
    if(isset($_GET['room']) && is_numeric($_GET['room']))
    {
        $room = new room($_GET['room']);
        $floor = new floor($room->floorID);
        $building = new building($floor->buildingID);
    }

    if(!$room && !$floor && !$building)
        header("Location: buildings.php");

$globalTopic="Room Layout";

$floors = new floors;
$rooms = new rooms;
$buildings = new buildings;
$logs = new logs;
$racks = new racks;
$owners = new owners; 

$header = "<link rel='stylesheet' type='text/css' href='theme/room.css' />
<script type='text/javascript'> var roomID = ". $_GET['room'] ."; </script>
<script type='text/javascript' src='theme/js/mapbox.min.js'></script>
<script type='text/javascript' src='theme/js/rotate.js'></script>
<script type='text/javascript' src='theme/roomlayout.js'></script>";

include "theme/" . $theme . "/top.php";
?>
<script type="text/javascript"> 
    $(document).ready(function() {
		resizeCanvas("#viewPortal");
		$('#viewPortal').mapbox({pan: true, zoom: false,defaultX:0,defaultY:0}); 

	<?php 
		$config = new config;
		$lockFloorTiles=$config->returnItem('lockFloorTiles');
		if($lockFloorTiles==1)
		{
                    echo '$.lockedTiles = "locked";';
                    echo 'lockFloorTiles(); ';
		}
	?>
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
	<div id="left">
		<div class="module" id="breadcrumb" >
			<span style="float:left"><strong><a href="buildings.php">Buildings</a> &#187; <?php echo $building->name; ?> &#187; <a href="rooms.php?floor=<?php echo $floor->floorID; ?>">Level: <?php echo $floor->name; ?></a> &#187; <a href="room.php?room=<?php echo $room->roomID; ?>"><?php echo $room->name; ?></a> &#187; Layout</strong></span>
			<span id='links' style='float: right;' ><a href="room.php?room=<?php echo $_GET['room']; ?>" >Back to Room</a> - <a href="http://help.racksmith.net/guide.php?guide=managelayout" target="_help">Help</a></span>
		</div>
		
	<div class="module" id='canvasHolder' >
		<div id="viewPortal" >
			<div>
                            <div class="mapContent" style="height: 2000px;width: 2000px;border: 2px solid #000000;" ></div>
			</div> 
		</div> 

	</div>
		
		<div class="module" id="createRack" style="display: none">
                    <div class="sectionHeader" >
			<strong>Create a new Rack</strong>
			<a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
                    </div>
			<div class="form" id="createRackForm">
				<p>
				<form method="post" > 
				<table width="80%" class="formTable">
                                    <colgroup align="left" class="tblfirstRow"></colgroup>
					<tr>
						<td><label for="name" >Name:</label></td>
						<td colspan="3"><input name="name" type="text" id="name" size="16"/><input type="hidden" id="room" name="room" value="<?php echo $_GET['room']; ?>" /><input name="posx" type="hidden" id="posx" value="" /><input name="posy" type="hidden" id="posy" value="" /></td>
					</tr>
					<tr>
						<td><label for="ownerID" >Owner:</label></td>
						<td colspan="3">
							<select name="ownerID"> 
							<?php foreach($owners->getAll() as $owner) { ?>
							<option value="<?php echo$owner->ownerID; ?>" ><?php echo $owner->name; ?></option>
							<?php } ?>
							</select>
						</td>
					</tr> 
					<tr>
						<td><label for="model" >Model:</label></td>
						<td colspan="3"><input name="model" type="text" id="model" size="30" /></td>
					</tr> 
					<tr>
						<td><label for="RU" >RU:</label></td>
						<td><input name="RU" type="text" id="RU" size="2" /> <i>rack units</i></td>
						<td class="tblfirstRow"><label for="width" >Width:</label></td>
						<td><input name="width" type="text" id="width" value="19" size="3" /> <i>inch</i></td>
					</tr>
						
					<tr>
						<td><label for="depth" >Depth:</label></td><td>
							<select name="depth" id="depth" >
                                                            <option value="450" >450mm</option>
                                                            <option value="600" SELECTED>600mm</option>
                                                            <option value="800" >800mm</option>
                                                            <option value="900" >900mm</option>
                                                            <option value="1000" >1000mm</option>
							</select>
						</td>
						<td class="tblfirstRow"><label for="height" >Height:</label></td>
						<td><input name="height" type="text" id="height" value="" size="3" /> <i>inch</i></td>
					</tr>
                                        <tr><td>Side Mountable Objects:</td>
                                            <td><select name="sideMountable" >
                                                    <option value="0" >None</option>
                                                    <option value="1" >Left</option>
                                                    <option value="2" >Right</option>
                                                    <option value="3" >Both</option>
                                                </select>
                                            </td></tr>
					<tr> 
						<td><label for="notes" >Notes:</label></td>
						<td colspan="3"><textarea name='notes' style='width: 550px;height: 60px;'></textarea></td> 
					</tr> 
					<tr> 
						<td></td>
						<td colspan="3">
						<input type="submit" value="Submit" class="rackCreateSubmit" />
						<div class="error" style="display:none;" ></div>
						</td> 
					</tr>
				</table> 
				</form> 
				
				<div class="success" style="display: none;" >Rack Created</div>
				
				</p>
			</div> 		
		</div>
            <div class="module" id="uploadBackground" style="display:none;">
                <div class="sectionHeader" >
                    <strong>Upload a background</strong>
                    <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
                </div>
			<p>
			<div id="uploadBackgroundForm" class="popupModule">
		<?php
			if(is_writable('images/uploads/'))
			{
		?>
			If you have a room layout or detail drawing you can upload it as a background.<br/>
			This means when using the racks page browsing content will seem more intuitive.
			Images much be in PNG, GIF or JPG format and we suggest having it 1000X1000px or larger.<br/><br/><hr/>
			<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
			<input type="file" name="userfile" size="40" maxlength="100"><hr/>
			<input type="submit" name="submit" value="Start Uploading">
			<input name="name" type="hidden" id="name" value="" />
			<input name="roomID" type="hidden" id="roomID" value="<?php  echo $room->roomID; ?>" />
			<?php
                        if(isset($_GET['room']) && is_numeric($_GET['room']))
                            if(file_exists("images/uploads/background_roomID".$_GET['room'].".jpg"))
                                $filename = "images/uploads/background_roomID".$_GET['room'].".jpg";
                            else if(file_exists("images/uploads/background_roomID".$_GET['room'].".jpeg"))
                                $filename = "images/uploads/background_roomID".$_GET["room"].".jpeg";
                            else if(file_exists("images/uploads/background_roomID".$_GET['room'].".png"))
                                $filename = "images/uploads/background_roomID".$_GET["room"].".png";
                            else if(file_exists("images/uploads/background_roomID".$_GET['room'].".gif"))
                                $filename = "images/uploads/background_roomID".$_GET['room'].".gif";
                            else
                                $filename = "";
			if(file_exists($filename))
				echo '<hr/>
				<a href="#" onclick="if (confirm(\'Are you sure you wish to delete the existing background?\')) { location.href = \''.$_SERVER["PHP_SELF"].'?action=removeBG&room='.$room->roomID.'\'; } "><img src="images/icons/delete_small.gif" /> Remove current background</a>
				<p><center>
					<img src="'.$filename.'" alt="Racksmith Room Background" width="650px" style="border: 4px solid #cccccc" />
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
                    <strong>Views &amp; Controls</strong>
              <p>
                    <table class="dataTable">
                    <tr><td valign="top">
                            <input type="checkbox" checked id="floortiles" /> <label for='floortiles' >Show Floor Tiles</label><br />
                            <input type="checkbox" checked id="racks" /> <label for='racks' >Show Racks</label><br />
                            <input type="checkbox" checked id="cabletrays" /> <label for='cabletrays' >Show Cable Trays</label></td>
                        <td valign="top">
                            <input type="checkbox" id="lockfloortiles" <?php if($lockFloorTiles==1) { echo 'checked'; }?>/> <label for='lockfloortiles' >Lock Floor Tiles</label>
                        </td>
                    </tr>
                    </table>
                </p>


            <strong>Static Items</strong>
            <p>
            <div class="roomInventory inventoryHolder">

                <a href="#" class='inventoryTitle'>Racks (1)</a>
                    <div class="inventory">
			<table style="width:auto;" >
			<tr>
                            <td width="40px"><img class="toolbaritem rack newRack" style='z-index:999;' id="newRack" title="Server Rack" src="images/tiles/rack01.gif" />Generic</td>
			</tr>
			</table>
                    </div>
                <a href="#" class='inventoryTitle'>Floor Tiles (3)</a>
                <div class="inventory" style="height: 60px;">
			<table style="width:auto;" class="tileTbl">
			<tr>
                            <td width="40px"><img class="toolbaritem floortile" id="floortile1" title="Raised Floor" src="images/tiles/floortile2.gif" />Aluminium 600x600</td>
                            <td width="40px"><img class="toolbaritem floortile" id="floortile3" title="Concrete" src="images/tiles/concrete.gif" />Concrete 32x32</td>
                            <td width="40px"><img class="toolbaritem floortile" id="floortile2" title="Unusable" src="images/tiles/floortile3.gif" />Other 600x600</td>
			</tr>
			</table>                    </div>
                <a href="#" class='inventoryTitle'>Cable Trays (3)</a>
                <div class="inventory" >
			<table style="width:auto;">
			<tr>
                            <td width="25"><div class="toolbaritem cabletray" id="cabletray1" title="Cable Tray"></div></td>
                            <td width="25"><div class="toolbaritem cabletray" id="cabletray2" title="Cable Tray"></div></td>
                            <td width="25"><div class="toolbaritem cabletray" id="cabletray3" title="Cable Tray"></div></td>
                        </tr>
                        </table>
                </div>
                <a href="#" class='inventoryTitle'>Misc (3)</a>
                <div class="inventory" >
			<table style="width:auto;">
			<tr>
                            <td width="50px"><div class="toolbaritem floortile hotAisle" id="hotAisle" title="Hot Aisle"></div>Hot Aisle</td>
                            <td width="50px"><div class="toolbaritem floortile coldAisle" id="coldAisle" title="Cold Aisle"></div>Cold Aisle</td>
                            <td width="50px"><img class="toolbaritem floortile" id="floortile4" title="Power" src="images/tiles/power1.jpg" />Power</td>
                            <td width="50px"><img class="toolbaritem floortile" id="floortile5" title="Administration" src="images/tiles/admin1.jpg" />Admin</td>
                        </tr>
                        </table>
                </div>   

                </div></p>
            <?php
            $deviceTypesClass = new deviceTypes();
            $deviceTypes = $deviceTypesClass->getAll();

            
            $templatesClass = new templates;
            $templates = $templatesClass->getByCategory(FLOOR_DEVICE);
            if(is_array($templates))
            {
                echo '            <strong>Floor Devices</strong>
                    <p>
                    <div class="roomInventory inventoryHolder">';
                foreach($templates as $key=>$deviceType)
                { ?>
                        <a href="#" class='inventoryTitle'><?php if(isset($deviceTypes[$key]->name)) { echo $deviceTypes[$key]->name; } else { echo "** error with name**"; } ?> (<?php echo count($deviceType); ?>)</a>
                        <div class="inventory" style="padding: 0px;">
                        <ul>
                        <li class="search"><div>Search: <input class='inventorySearchField' type='text' size='20'/>
                        <img src='images/icons/close_module.gif' style='display:none;' alt='clear search' class='inventoryClearSearch' /></div></li>
                        <?php
                        foreach($deviceType as $template)
                        {
                            if($template->deleted!=1)
                                echo '<li class="item"><div class="toolbaritem newDevice" href="#'.$template->templateID.'"><span>'.ucwords($template->getMeta(12,1)) . ' ' . $template->name.'</span></div></li>';
                        }
                        
                        echo "<li class='inventoryAdd' ><a href='manageTemplates.php?deviceType=".$key."' >Create new template</a></li>
                        </ul></div>";
                }
                ?>
                <a href="#" class='inventoryTitle'>Inventory</a>
                
                <div id="floorPageInventory" class="inventory" >
                    <ul>
                <!-- removed
                        <li class="search"><div>Search: <input class='inventorySearchField' type='text' size='20'/>
                        <img src='images/icons/close_module.gif' style='display:none;' alt='clear search' class='inventoryClearSearch' /></div></li>-->
                <?php
                foreach($racks->getByParent(0, "room") as $rack)
                {
                ?>
                    <li class="item"><div class="item rack rackID<?php echo $rack->rackID; ?>" id="rack<?php echo $rack->rackID; ?>" title="<?php echo $rack->depth; ?>"><?php echo $rack->name; ?></div></li>
                <?php
                }
                $devices=new devices;
                foreach($devices->getByParent(0, "room") as $device)
                    echo "<li class='toolbaritem item inventoryDevice' id='device".$device->deviceID."'>".$device->name."</li>";
                ?>

                    </ul>
                </div><?php

                echo '
                </div></p>';
            }
                ?>

            <hr/>
            <div style="text-align:right;" >
            <strong><a href="#uploadBackground" class="newDOMWindow">Adjust the Background</a></strong><br/>
            <strong><a href="templates.php">Manage Templates</a></strong>
            </div>
        </div>
            <div class="inventoryHelper" style="display:none;height:32px;background-color:#000000"></div>


            <div class="module" >
                <strong>Last Highlighted Item</strong>
                    <div class="layoutItemInfo" >
                    <p>
                        <h5>General</h5>
                        <table class="metaData">
                            <tr><td width="60px" align="right"><em>Name:</em></td><td><span id="highlightedName" ></span></td></tr>
                            <tr><td align="right"><em>Type:</em></td><td><span id="highlightedDevType" ></span></td></tr>
                        </table>
                        <h5>Dimensions</h5>
                        <table class="metaData">
                            <tr><td align="right" width="60px"><em>Width:</em></td><td><span id="resizeWidth" >###</span> <span style="font-style:italic;"> floor tile/s</span></td></tr>
                            <tr><td align="right"><em>Depth:</em></td><td><span id="resizeHeight" >###</span></td></tr>
                        </table>
                    </p>
                </div>
            </div>
	</div>
            
    </div>
    <div id='menus' ></div>
<?php include "theme/" . $theme . "/base.php"; } ?>