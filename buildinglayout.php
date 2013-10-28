<?php
session_start();
$selectedPage="layout";
include "class/db.class.php";

if(isset($_POST) && isset($_FILES['userfile']) && $_FILES['userfile'])
{
    include "class/upload.fn.php";
    // uploadFile(file,folderPath,fileName,overwriteFlag)
    $val = uploadFile($_FILES['userfile'],"images/uploads/","buildings.jpg",1);
    if($val==1)
    {
        // find the size of the uploaded image if possible
        // if its > 700x700 then save its dimentions to the config for the canvas size
        // else default to a 1500x1500 canvas
        $imageSize = getimagesize('images/uploads/buildings.jpg');
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

        header("Location: ".$_SERVER['PHP_SELF']."?upload=complete");
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
elseif(isset($_GET['action']) && $_GET['action'] == 'removeBG')
{
    $deleted=0;
    if(file_exists('images/uploads/buildings.jpg'))
        if(unlink('images/uploads/buildings.jpg'))
            $deleted=1;

    if($deleted)
        header("location: ".$_SERVER['PHP_SELF']."?action=deletedBackground");
    else
        header("Location: ".$_SERVER['PHP_SELF']."?error=cannotDelBG");
}
else
{
    $buildings = new buildings;
    $allBuildings = $buildings->getAll();

    $cabinets = new cabinets;
    $allCabinets = $cabinets->getAll();

    $header = "
    <script type='text/javascript' src='theme/js/mapbox.min.js'></script>
    <script type='text/javascript' src='theme/buildinglayout.js'></script>";

    $globalTopic="Building Layout";
    include "theme/" . $theme . "/top.php";
?> 
<script type="text/javascript"> 
    $(document).ready(function() { 		
        resizeCanvas("#viewPortal");
        $('#viewPortal').mapbox({pan: true, zoom: false,defaultX:0,defaultY:0});
    }); 
</script> 
<style><?php
// Only include the background image if the file actually exists
if(file_exists('images/uploads/buildings.jpg'))
	echo "#droppable { background-image:url('images/uploads/buildings.jpg'); background-repeat:no-repeat; background-color:#EEE;}";
else
	echo "#droppable { background-color:#EEE;}";
?></style>
	
<div id="main"> 
    <div id="left">
        <div class="module" id="breadcrumb">
            <strong style="float:left;" >Building Layout</strong>
            <span id='links' style='float: right;' ><a href='buildings.php' >Back to Buildings Page</a> - <a href="http://help.racksmith.net/guide.php?guide=managelayout" target="_help">Help</a></span>
        </div>
		
        <div class="module" id='canvasHolder' >
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
                    <div class="mapContent" id="droppable" style="height: <?php echo $canvasY;?>px;width: <?php echo $canvasX;?>px;border: 2px solid #000000;" ></div>
                </div>
            </div>
        </div>

			
			
<!-- section for popup forms -->			
    <div class="module" id="createBuilding" style="display: none">
        <div class="sectionHeader" >
            <strong>Create Building</strong>
            <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
        </div>
            <p>
            <div class="form" align="center" id="createBuildingForm">
            <form method="post">
            <table width="80%" class="formTable">
                <colgroup align="left" class="tblfirstRow"></colgroup>
                <tr>
                    <td><label for="name" >Name:</label></td><td><input name="name" type="text" id="name" value="" />
                        <input name="itemposx" type="hidden" value="" />
                        <input name="itemposy" type="hidden" value="" />
                        <input name="action" type="hidden" id="action" value="insertBuilding" /></td>
                </tr>
                <tr>
                    <td><label for="ownerID" >Owner:</label></td><td><select name="ownerID">
                    <?php
                      $owners = new owners;
                      foreach($owners->getAll() as $owner) 
                        echo '<option value="'.$owner->ownerID.'">'.$owner->name.'</option>';
                    ?>
                        </select> <a href="owners.php" target="_blank">[manage owners]</a></td>
                </tr>
                <tr>
                    <td><label for="description" >Description:</label></td><td><textarea name="description" id="description" rows="5" style="width: 350px;" ></textarea></td>
                </tr>
                <tr>
                    <td><label for="notes" >Notes:</label></td><td><textarea name="notes" id="notes" rows="5" style="width: 350px;" ></textarea></td>
                </tr>
                <tr>
                    <td></td><td><input type="submit" id="btnSubmit" name="btnSubmit" value="Create" /></td>
                </tr>
                </table>
                </form>
            </div>
            </p>
        </div>

        <div class="module" id="createCabinet" style="display: none">
            <div class="sectionHeader" >
                <strong>Create Cabinet</strong>
                <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
            </div>
                    <p>
                        <div class="form" align="center" id="createCabinetForm">
                        <form method="post">
                            <table width="80%" class="formTable">
                                <colgroup align="left" class="tblfirstRow"></colgroup>
                                <tr>
                                    <td><label for="name" >Name:</label></td><td><input name="name" type="text" id="name" value="" />
                                    <input name="itemposx" type="hidden" value="" />
                                    <input name="itemposy" type="hidden" value="" />
                                    <input name="action" type="hidden" id="action" value="insertCabinet" /></td>
                                </tr>
                                <tr>
                                    <td><label for="ownerID" >Owner:</label></td><td><select name="ownerID">
                                    <?php
                                      $owners = new owners;
                                      foreach($owners->getAll() as $owner)
                                        echo '<option value="'.$owner->ownerID.'">'.$owner->name.'</option>';
                                    ?>
                                    </select> <a href="owners.php" target="_blank">[manage owners]</a></td>
                                </tr>
                                <tr><td colspan="2">
                                    <em class="createFormNote">
                                        A cabinet is considered a weatherproof container for racks. The rack can accept any standard mountable item, this includes shelves for patching, patch panels & UPS.
                                    </em>
                                </td></tr>
                                <tr>
                                    <td><label for="racks" >Racks within cabinet:</label></td><td>
                                        <select name="racks" >
                                            <option value="0" >0</option>
                                            <option value="1" >1</option>
                                            <option value="2" >2</option>
                                            <option value="3" >3</option>
                                            <option value="4" >4</option>
                                        </select></td>
                                </tr>
                                <tr>
                                    <td><label for="RU" >Rack Size (RU):</label></td><td>
                                        <input name="RU" style="width:20px;"> RU</td>
                                </tr>
                                <tr>
                                    <td><label for="notes" >Notes:</label></td><td><textarea name="notes" id="notes" rows="5" style="width: 350px;" ></textarea></td>
                                </tr>
                                <tr>
                                    <td></td><td><input type="submit" id="btnSubmit" name="btnSubmit" value="Create" /></td>
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
			<div id="uploadBackgroundForm" class="popupModule">
		<?php
			if(is_writable('images/uploads/'))
			{ 
		?>
			If you have a campus map or detail drawing you can upload it as a background for this page.<br/>
			This will help with item placement and context.
			Images much be in PNG, GIF, JPG format and we suggest having it 1000px X 1000px or larger.<br/><br/><hr/>
			<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data"> 
			<input type="file" name="userfile" size="40" maxlength="100"><hr/>
			<input type="submit" name="submit" value="Start Uploading"> 
			
			<?php
			if(file_exists('images/uploads/buildings.jpg'))
				echo '<hr/>
				<a href="#" onclick="if (confirm(\'Are you sure you wish to delete the existing background?\')) { location.href = \''.$_SERVER["PHP_SELF"].'?action=removeBG\'; } "><img src="images/icons/delete_small.gif" /> Remove current background</a>
				<p><center>
					<img src="images/uploads/buildings.jpg" alt="Racksmith Building Background" width="650px" style="border: 4px solid #cccccc" />
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
<!-- End popup forms -->
		
	</div>

	<div id="right" style="position:relative; z-index:3;">
		<div class="module">
			<strong>Buildings</strong>
			<div class='block' <?php if(isset($_GET['view']) && $_GET['view']=='cabinets') { echo "style='display: none;'"; } ?>>
				<p>Drag these blocks onto the panel to represent the area a building takes up. Combine multiple tiles for more complex shapes.</p>
				
				<ul class='itemList' id="buildingsList">
					<li class="addNew toolbaritem" id="building0">
						<span>Drag to add building</span>
					</li>
                                    <?php
					foreach($allBuildings as $building)
					{ ?>
					<li class="toolbaritem buildingTile" id="building<?php echo $building->buildingID ?>" onmouseover="$('.building<?php echo $building->buildingID;?>').addClass('buildingHover');" onmouseout="$('.building<?php echo $building->buildingID;?>').removeClass('buildingHover');">
                                            <span class="title" style="float:left;width:180px;overflow:hidden;" ><?php echo $building->name;?></span>
                                            <span class="edit buildingedit" ></span>
					</li>	
                                    <?php } ?>
				</ul>
			</div>
		</div>
		<div class="module">
                    <strong>Fiber Cabinets</strong>
                    <p>For building interconnects and kit out in the field you can drop cabinets.</p>
                    <ul class='itemList' id="cabinetList">
                        <li class="addNew toolbaritem" id="cabinet0"><span>Drag to add cabinet</span></li>
                        <?php
                            foreach($allCabinets as $cabinet)
                            { ?>
                        <li class="toolbaritem cabinetTile" id="cabinet<?php echo $cabinet->cabinetID;?>"  onmouseover="$('.cabinet<?php echo $cabinet->cabinetID;?>').addClass('cabinetHover');" onmouseout="$('.cabinet<?php echo $cabinet->cabinetID;?>').removeClass('cabinetHover');"><span class="title"  style="float:left;width:180px;overflow:hidden;" ><?php echo $cabinet->name;?></span><span class="edit"></span></li>
                        <?php } ?>
                    </ul>
                </div>

            <div class="module">
                <center><strong><a href="#uploadBackground" class="newDOMWindow">Adjust the Background</a></strong>
                    <?php
                    if(isset($_GET['upload']) && $_GET['upload']=="complete")
                        echo "<center><b><font color='#3f8f50' >Upload Complete!</font></b></center>";
                    if(isset($_GET['action']) && $_GET['action']=="deletedBackground")
                        echo "<center><b>Background Deleted!</b></center>";
                    if(isset($_GET['error']) && $_GET['error']=="cannotDelBG")
                        echo "<center><b><font color='#a94244' >Error deleting background</font>/b></center>";
                    ?>
                    </center>
            </div>
	</div>
</div>
<div class="buildingHelper" style="display:none;"></div>
<div class="cabinetHelper" style="display:none;"></div>

<?php  include "theme/" . $theme . "/base.php"; 

}
?>