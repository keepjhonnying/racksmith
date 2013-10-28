<?php
session_start();
$selectedPage="configure";
include "class/db.class.php";

$globalTopic = "Templates Listing"; // page title
$templates = new templates; // declare class object

if (isset($_GET['id']) && isset($_GET['mode']) && $_GET['mode'] == 'delete') // link actions
{
    if(is_numeric($_GET['id']))
    {
        $templates->delete($_GET['id']);
        header("Location: templates.php");
    }
    else
        header("Location: templates.php?error=invalidID");
}

include "theme/" . $theme . "/top.php";
?>
<div id="main"> 
	<div id="left">
	<div class="module" id="templates">
            <strong>Device Templates</strong>
            <p>
                All devices within RackSmith link back to a template, this essentially relates to the model of the device.
                <br/>
                Here you can create and manage all the templates which are later used when you create devices.
            </p><p><a href="manageTemplates.php">Create a new Template</a></p><i>Select a device type to view templates</i>
            
            <?php
            $deviceTypes = new deviceTypes;
            $typeListing=$deviceTypes->getAll();
            
            $cachedTemplates=array();
            ?>
            <div id="deviceTypeListing" class="listObjects"  >
                <ul>
                    <?php
                    foreach($typeListing as $type)
                    {
                        $cachedTemplates[$type->deviceTypeID]=$templates->getByDeviceType($type->deviceTypeID);
                        $count=0;
                        if(is_array($cachedTemplates[$type->deviceTypeID]))
                            $count=count($cachedTemplates[$type->deviceTypeID]); 
                        
                        echo "<li style='display:inline-block;margin-right:10px;' alt='deviceType".$type->deviceTypeID."' onclick=\"$(this).toggleClass('highlighted');$('#templateType".$type->deviceTypeID."').slideToggle('fast');\"><div class='name' >".$type->name;
                        if($count)
                            echo " (".$count.")";
                        echo "</div></li>";
                    }
                    ?>
                </ul>
                
            </div>
           
<?php
	
	foreach($typeListing as $type)
	{ 
		?>
		<p>
	        <div class="templateTable" id="templateType<?php echo $type->deviceTypeID; ?>" style="display:none;">
                <table width="100%" class="dataTable">
                <colgroup align="left" class="tblfirstRow"></colgroup>
                <thead>
                    <tr>
                        <th scope="col" width='25%'>Model</th>
                        <th scope="col" width='10%'>Vendor</th>
                        <th scope="col" width='33%'>Positioning</th>
                        <th scope="col" width='7%' >Ports</th>
                        <th scope="col" width="15%">Options</th>
                    </tr>
                </thead>
                <tbody>
	<?php
	// Loop Through Owners
	//$listType = $templates->getByDeviceType($type->deviceTypeID);
        $listType=$cachedTemplates[$type->deviceTypeID];
	if($listType)
        {
            foreach($listType as $template)
            {
                $template->fillCategories(1,1,array(GENERIC,RACK_MOUNTABLE,FLOOR_DEVICE,OUTDOOR_ITEM));
                if($template->deleted==0)
                {
                    $portCount=$template->numPorts();
                    if(!is_numeric($portCount))
                        $portCount=0;
                ?>
                    <tr>
                        <td><b><?php echo $template->name; ?></b></td>
                        <td><?php echo $template->getMeta(12,1); ?></td>
                        <td>
                            <?php
                            $position=array();

                            if(isset($template->categories[RACK_MOUNTABLE]))
                                $position[]="Rack Mount (".$template->categories[RACK_MOUNTABLE][5]->value."RU)";
                            if(isset($template->categories[FLOOR_DEVICE]))
                                $position[]="Floor Item";
                            if(isset($template->categories[OUTDOOR_ITEM]))
                                $position[]="Outdoor Item";
                            echo implode(", ", $position);
                            ?>
                        </td>
                        <td align="right"><?php echo $portCount; ?></td>
                        <td align="center">
                            <a href="editTemplates.php?mode=edit&id=<?php echo $template->templateID; ?>">Edit</a> |
                            <a href="#" onclick="if (confirm('Are you sure you want to delete this template? \nIt will still be available for existing items')) { location.href = 'templates.php?action=template&mode=delete&id=<?php echo $template->templateID; ?>'; } ">Delete</a>
                        </td>
                    </tr>
<?php
                }
            }
        }
?>
                        </tbody>
                        <tfoot>
                        <tr>
                        <td colspan="5" style="padding-left:10px;">
                                <a href="manageTemplates.php?deviceType=<?php echo $type->deviceTypeID;?>" >Create a new <?php echo $type->name;?> template</a>
                        </td>
                        </table>
                    </div>
	<?php
           
        }?>
			</p>	
		</div>
	</div>

	<div id="right">
        <div class="module sideMenu">
            <strong>Devices &amp; Templates</strong>
            <p>
            <ul>
                    <li><a href="manageTemplates.php">Create a new Template</a></li>
                    <li><a href="templates.php">Templates List</a></li>
                    <li><a href="templateMover.php">Import/Export Templates</a></li>
                    <li><a href="metadata.php" >Device Information</a></li>
                </ul>
            </p>
        </div>

        <div class="module sideMenu">
            <strong>System</strong>
            <p>
            <ul>
                <li><a href="cables.php" >Cable Types</a></li>
                <li><a href="owners.php" >Equipment Owners</a></li>
                <li><a href="management.php?action=accounts" >User Accounts</a></li>
                <li><a href="system.php" >System</a></li>
                <li><a href="logs.php" >Logs</a></li>
                <li><a href="templateMover.php" >Import / Export Templates</a></li>
            </ul>
            </p>
        </div>
    <?php
        if(isset($_GET['error']) && $_GET['error'] == "templateNotFound")
            echo "<div class='module error' >The template you requested could not be found</div>";
        if(isset($_GET['insert']) && $_GET['insert'] == 'Success')
            echo '<div class="module" ><div class="success" >Created Template!</div></div>';
        if(isset($_GET['update']) && $_GET['update'] == 'Success')
            echo '<div class="module" ><div class="success" >Updated Template!</div></div>';
    ?>
	</div>
</div>
<?php
include "theme/" . $theme . "/base.php";
?>