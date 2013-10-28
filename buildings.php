<?php
session_start();
$selectedPage="layout";
include "class/db.class.php";

// When a floor has been moved we send a ajax request to this page and move it
// the data value is a , seperated string of floorIDs starting from top to bottom.
if(isset($_GET['action']) && $_GET['action']=="savefloors")
{
    $floors = new floors();
    $floors->update_sort($_GET['data']);
    exit();
}

$buildings = new buildings;
$cabinets = new cabinets;
$logs = new logs;
$floors = new floors;
$floors->cacheAll();

$rooms = new rooms;
$rooms->cacheAll();

    if (isset($_GET['action']) && $_GET['action'] == 'floor')
	{
            if (isset($_GET['mode']) && $_GET['mode'] == 'edit' && is_numeric($_GET['id']))
            {
                $newFloor = new floor($_GET['id']);
                $newFloor->name = $_POST['name'];
                $newFloor->notes = $_POST['notes'];
                $newFloor->buildingID = $_POST['buildingID'];

                $floors->update($newFloor);

                header("Location: buildings.php");
            }
            else if(isset($_GET['mode']) && $_GET['mode'] == 'insert')
            {
                $newFloor = new floor;
                $newFloor->name = $_POST['name'];
                $newFloor->notes = $_POST['notes'];
                $newFloor->buildingID = $_POST['buildingID'];
                $floors->insert($newFloor);

                header("Location: buildings.php");
            }
            else if (isset($_GET['mode']) && $_GET['mode'] == 'delete' && is_numeric($_GET['id']))
            {
                $floors->delete($_GET['id']);
                if(isset($_GET['from']) && $_GET['from'] == 'layout')
                    header("Location: buildinglayout.php");
                else
                    header("Location: buildings.php");
            }
}

else if (isset($_GET['action']) && $_GET['action'] == 'building')
{
    if (isset($_GET['mode']) && $_GET['mode'] == 'editSave' && is_numeric($_GET['id']))
    {
        $newBuilding = new building($_GET['id']);
        $newBuilding->name = $_POST['name'];
        $newBuilding->description = $_POST['description'];
        $newBuilding->notes = $_POST['notes'];
        $newBuilding->ownerID = $_POST['ownerID'];

        $buildings->update($newBuilding);
        if(isset($_GET['from']) && $_GET['from'] == 'layout')
            header("Location: buildinglayout.php");
        else
            header("Location: buildings.php");
    }
    else if(isset($_GET['mode']) && $_GET['mode'] == 'insert')
    {
        $newBuilding = new building;
        $newBuilding->name = $_POST['name'];
        $newBuilding->description = $_POST['description'];
        $newBuilding->notes = $_POST['notes'];
        $newBuilding->ownerID = $_POST['ownerID'];

        $buildings->insert($newBuilding);

        header("Location: buildings.php");
    }
    else if (isset($_GET['mode']) && $_GET['mode'] == 'delete' && is_numeric($_GET['id']))
    {
        $buildings->delete($_GET['id']);
        if(isset($_GET['from']) && $_GET['from'] == 'layout')
            header("Location: buildinglayout.php");
        else
            header("Location: buildings.php");
    }
    else if(isset($_GET['mode']) && $_GET['mode'] == 'edit' && is_numeric($_GET['id']))
    {
    $currentBuilding = new building($_GET['id']);

    // if a referrer was set we want to move back there after the submission
    // it could only be the layout page so limit it
    if(isset($_GET['from']) && $_GET['from'] == 'layout')
        $refer='&from=layout';
    else
        $refer='';
	
?><div id="editBuilding">
    <div class="sectionHeader" >
	<strong><em>edit:</em> <?php echo $currentBuilding->name; ?></strong>
        <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
    </div>
	<p>
		<div class="form" align="center">
		<form method="post" action="buildings.php?action=building<?php echo $refer;?>&mode=editSave&id=<?php echo $currentBuilding->buildingID; ?>" id="editBuilding">
		<table width="80%" class="formTable">
                    <colgroup align="left" class="tblfirstRow"></colgroup>
			<tr>
				<td><label for="name" >Name:</label></td><td><input name="name" type="text" id="name" value="<?php echo $currentBuilding->name; ?>" /></td>
			</tr>
			<tr>
				<td><label for="ownerID" >Owner:</label></td><td><select name="ownerID">
				<?php
				  $owners = new owners;
				  foreach($owners->getAll() as $owner) { 
				?>
					<option value="<?php echo $owner->ownerID?>" <?php if ($currentBuilding->ownerID == $owner->ownerID) echo "selected"; ?>><?php echo $owner->name?></option>
				<?php } ?>
				</select></td>
			</tr>
			<tr>
				<td><label for="description" >Description:</label></td><td><input name="description" type="text" id="description" value="<?php echo $currentBuilding->description; ?>" /></td>
			</tr>
			<tr>
				<td><label for="notes" >Notes:</label></td><td><textarea name="notes"  id="notes" rows="5" style="width: 600px;" ><?php echo $currentBuilding->notes; ?></textarea></td>
			</tr>
			<tr>
				<td></td><td><input type="submit" value="Update" /></td>
			</tr>
                        <tr>
                            <td></td><td><a style="cursor:pointer;" onclick="$('#delbuild').toggle();" ><em>Delete this building</em></a></td>
                        </tr>
                        <tr id="delbuild" style="display:none;"><td colspan="2" >All content within this building will be lost, Are you sure? <a href="buildings.php?mode=delete&action=building&id=<?php echo $currentBuilding->buildingID; if(isset($_GET['from']) && $_GET['from'] == 'layout') { echo $refer; } ?>" style="cursor:pointer;">Yes</a> - <a onclick="$('#delbuild').toggle();"  style="cursor:pointer;">No</a></td></tr>
		</table>
		</form>
		</div>
	</p>

<strong>Floors</strong>
<br />
<table class="formTable">
    <colgroup align="left" class="tblfirstRow"></colgroup>
<thead> 
<tr> 
    <th scope="col">Floor Name</th>
    <th align="center" scope="col" width="100" >Manage</th>
</tr> 
</thead> 
<?php 
   foreach($floors->getByBuilding($_GET['id']) as $floor) 
   { 
?>
<tr> 
	<td>Level: <span id="floor<?php echo $floor->floorID; ?>"><?php echo $floor->name; ?></span></td>
	<td align="center">
            <a href="#<?php echo $floor->floorID ?>" class="floorEdit" >Edit</a> -
            <a onclick="if (confirm('Are you sure you want to delete this item?')) { return true; } else { return false;}" href="buildings.php?mode=delete&action=floor&id=<?php echo $floor->floorID ?>">Delete</a></td>
</tr>
<?php } ?> 
</table>

	</div>

<?php
	}
}



else if (isset($_GET['action']) && $_GET['action'] == 'cabinet')
{

    if (isset($_GET['mode']) && $_GET['mode'] == 'editSave' && is_numeric($_GET['id']))
    {
        $cabinet = new cabinet($_GET['id']);
        $cabinet->name = $_POST['name'];
        $cabinet->notes = $_POST['notes'];
        $cabinet->ownerID = $_POST['ownerID'];

        $cabinets->update($cabinet);

        if(isset($_GET['from']) && $_GET['from'] == 'layout')
            header("Location: buildinglayout.php");
        else
            header("Location: buildings.php");
    }
    else if (isset($_GET['mode']) && $_GET['mode'] == 'delete' && is_numeric($_GET['id']))
    {
        $cabinets->delete($_GET['id']);
        if(isset($_GET['from']) && $_GET['from'] == 'layout')
            header("Location: buildinglayout.php");
        else
            header("Location: buildings.php");
    }

    else if(isset($_GET['mode']) && $_GET['mode'] == 'edit' && is_numeric($_GET['id']))
    {
    $cabinet = new cabinet($_GET['id']);

    // if a referrer was set we want to move back there after the submission
    // it could only be the layout page so limit it
    if(isset($_GET['from']) && $_GET['from'] == 'layout')
        $refer='&from=layout';
    else
        $refer='';

?><div id="editCabinet">
    <div class="sectionHeader" >
	<strong><em>edit:</em> <?php echo $cabinet->name; ?></strong>
        <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
    </div>
	<p>
		<div class="form" align="center">
		<form method="post" action="buildings.php?action=cabinet<?php echo $refer;?>&mode=editSave&id=<?php echo $cabinet->cabinetID; ?>" id="editCabinet">
		<table width="80%" class="formTable">
                    <colgroup align="left" class="tblfirstRow"></colgroup>
			<tr>
                            <td><label for="name" >Name:</label></td><td><input name="name" type="text" id="name" value="<?php echo $cabinet->name; ?>" /></td>
			</tr>
			<tr>
                            <td><label for="ownerID" >Owner:</label></td><td><select name="ownerID">
                            <?php
                              $owners = new owners;
                              foreach($owners->getAll() as $owner) {
                            ?>
                                    <option value="<?php echo $owner->ownerID?>" <?php if ($cabinet->ownerID == $owner->ownerID) echo "selected"; ?>><?php echo $owner->name?></option>
                            <?php } ?>
                            </select></td>
			</tr>
			<tr>
				<td><label for="notes" >Notes:</label></td><td><textarea name="notes"  id="notes" rows="5" style="width: 600px;" ><?php echo $cabinet->notes; ?></textarea></td>
			</tr>
			<tr>
				<td></td><td><input type="submit" value="Update" /></td>
			</tr>
                        <tr>
                            <td></td><td><a style="cursor:pointer;" onclick="$('#cabdel').toggle();" >Delete this Cabinet</a></td>
                        </tr>
                        <tr id="cabdel" style="display:none;"><td colspan="2" >All content within this cabinet will be lost, Are you sure? <a href="buildings.php?mode=delete&action=cabinet&id=<?php echo $cabinet->cabinetID; if(isset($_GET['from']) && $_GET['from'] == 'layout') { echo $refer; } ?>" style="cursor:pointer;">Yes</a> - <a onclick="$('#cabdel').toggle();"  style="cursor:pointer;">No</a></td></tr>
		</table>
		</form>
		</div>
            <br/>
            <strong>Racks within this cabinet</strong>
                <div class="desc" id="selectCabinetRacks" >
                    <table width="100%" height="85px" cellspacing="2" cellpadding="2"><tr>
                    <?php
                    $i=1;
                    $racksClass=new racks;
                    $racks=$racksClass->getByParent($cabinet->cabinetID, "cabinet");
                    foreach($racks as $rack)
                    {
                        /* FOR FUTURE USE?
                        $devices = new devices;
                        $devicesInRack=$devices->getByParent($rack->rackID,"rack");
                        count($devicesInRack)*/
                        echo "<td valign='top' class='individualCabinet' align='left' onclick='location.href=\"racks.php?rackID=".$rack->rackID."\"' align='center' width='".round(100/count($racks))."'>";
                        echo "Rack $i";

                        echo "</td>";
                        $i++;
                    }
                    ?>
                    </tr></table>
                    
                </div>
	</p>


	</div>

<?php
	}
}

else 
{
    $header = "<script type='text/javascript' src='theme/building.js'></script>
    <script type='text/javascript' src='theme/js/mapbox.min.js'></script>";

$globalTopic="Manage and View Buildings";
include "theme/" . $theme . "/top.php";
?>
<script type="text/javascript"> 
    $(document).ready(function() {
        resizeCanvas("#viewPortal");
        $('#viewPortal').mapbox({pan: true, zoom: false,defaultX:0,defaultY:0});
        //$("#menus").draggable({handle: '.floortitle'});
    }); 
</script> 

<style> 
.mapContent { background: #ffffff url('images/uploads/buildings.jpg') no-repeat;}
</style> 


<div id="main"> 
	<div id='full' >
	
		<div class="module" id="breadcrumb" >
			<strong style='float:left;'>Buildings</strong>
			<span id='links' style='float:right;'><a href='buildinglayout.php' >Manage Building Layout</a></span>
		</div>

	
		<div class="module" id='canvasHolder' >
			<?php
			$buildings = new buildings;
                        $cabinets = new cabinets;
			if(!$buildings->returnCount() && !$cabinets->returnCount())
				echo "<p><div class='notice' style='position: relative; margin: 0px auto;width: 60%;'>No buildings exist in the system, these must be created before you start to place your items..<br/>
				<a href='buildinglayout.php' ><i><b>Click here to start creating</b></i></a></div></p>";
			else
			{
			?>
			<div id="viewPortal" >
				<div>
				<?php
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
				?>
					<div class="mapContent" style="height: <?php echo $canvasY;?>px;width: <?php echo $canvasX;?>px;border: 2px solid #000000;" >
					</div>
				</div>
                            

			</div>
                    
                <div id="menus" style='display:none;'></div>
                <div id="hoverName" style='display:none;'></div>
			<?php } ?>



		</div>
		
	</div>		


	<div id="right" style='display: none;'>
		<div class="module" id="helpBox"> 
			<strong>Actions</strong>
			<p>
			<ul>
				<li><a href="buildinglayout.php" >Manage Building Layout</a></li>
			</ul>
			</p>
			<br/><br/>
			
			<strong>Browse Buildings</strong>
			<p>
				Browse the available buildings within the map to the left, available buildings will provide a menu when clicked on.<br/>If no buildings are provided then you must create them with the link above.
			</p>
			<p><strong>Re-Ordering Floors</strong><br />You can reorder floors by draging the handle on the right hand size of a floor <img src='images/icons/drag_list.gif' /></p>
		</div> 
	</div>
</div>
<?php include "theme/" . $theme . "/base.php"; 
}?>