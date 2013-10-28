<?php
session_start();
include "class/db.class.php";

if(isset($_GET['action']) && $_GET['action']=='delete' && is_numeric($_GET['deviceID']))
{
	$device = new device($_GET['deviceID']);
	if($device) 
	{ 
            // determine if the device has ports so we can decide how we show the text in the form
            $portClass = new ports;
            $ports=$portClass->getByDevice($device->deviceID);
            $portCount=count($ports);
            ?>
        <div class="sectionHeader" >
		<strong>Deleting: <?php echo $device->name;?></strong>
		<a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
        </div>
		<p style="padding: 5px;">
                    You are about to delete a device, this can remove it entirely from the system.<br/>
                    If you'd like to be able to use this system again, we strongly suggest moving it to the inventory.
                </p>
	<script type="text/javascript"> 
	function deleteDeviceForm()
	{
		var action = $('input[name=deleteAction]:checked').val();
		var delID = $('input[name=delID]').val();
		if(action == "inventoryRemoveCables" || action == "inventoryLeaveCables")
		{
                    // put it in the inventory and turn it into a stock item for future use
                    var invItem = "<li class='item' >" + $("span.device"+delID).parent().parent().html() + "</li>";
                    $(".inventory ul").append(invItem);
                    $("span.device"+delID).parent().addClass("stockItem");

                    // adjust the counter of inventory items
                    $('.inventoryTitle i').html($('#stock div').children().size()-2);

                    // remove the original and make new item draggable
                    $("#rackPanel span.device"+delID).parent().remove();
                    makedraggable("stock");
                }
		else
                    $("span.device"+delID).parent().remove();

                $("#deviceID"+delID).parent().remove();

		$("#menus").html("");
		setTimeout("$('.closeDOMWindow').click();$('.success').hide(); ",1000);
	}
	</script>
		<form method="get" action='deviceHandler.php?action=confirmDelete&deviceID=<?php echo $_GET['deviceID'];?>' id="deleteDeviceForm">
                <input type='hidden' name='delID' value='<?php echo $_GET['deviceID'];?>' />
                
		<ul style='padding-left:40px;background-color:#f3f3f3;list-style-type: none;border: 1px solid #cccccc;'>
                    <li style='padding: 6px;'><input type="radio" name="deleteAction" value="inventoryRemoveCables" id='invrem' style='margin-top: 2px;' checked="checked" /> <label for='invrem' >Move to <u>inventory</u><?php if($portCount) { ?> &amp; <u>remove all</u> cables<?php } ?></label></li>
                    <li style='padding: 6px;'><input type="radio" name="deleteAction" value="deleteCables" id='delrem' /> <label for='delrem' ><u>Delete</u> forever<?php if($portCount) { ?> &amp; <u>remove cables<?php } ?></label></u></li>
                    <?php if($portCount) { ?><li style='padding-left: 30px; padding-bottom: 5px;'><a href='#' onclick="$(this).parent().hide(); $('.hiddenoption').slideDown();" >More</a></li> <?php } ?>
                    <li class='hiddenoption' style='display:none;padding: 6px'><input type="radio" name="deleteAction" value="keepCables" id='delkep'/> <label for='delkep' ><u>Delete</u> &amp; <u>leave cables </u> connected at other end</label></li>
                    <li class='hiddenoption' style='display:none;padding: 6px;'><input type="radio" name="deleteAction" value="inventoryLeaveCables" style='margin-top: 2px;' id='invkep' /> <label for='invkep' >Move to <u>inventory</u> for re-use, <u>leave cables</u> connected.</label></li>
		</ul>
                    <input style='margin-left: 35px;' class='JSONsubmitForm' type="button" name="deleteDeviceForm" value="Delete" />
                    <input type="button" onclick="$('.closeDOMWindow').click();" name="cancel" value="Cancel" />
		</form>
<?php
	}
}



if(isset($_GET['action']) && $_GET['action']=='confirmDelete' && is_numeric($_GET['deviceID']))
{
    // either way the device is deleted, check to make sure layout object is taken off the maps page
    $layoutItems=new layoutItems;
    $layoutItems->deleteByDevice($_GET['deviceID'], 'device');

    if($_GET['deleteAction'] == 'inventoryLeaveCables')
    {
        $racks = new racks;
        $racks->move_device($_GET['deviceID'],0,0);
        echo json_encode(array("created"));
    }
    else if($_GET['deleteAction'] == 'inventoryRemoveCables')
    {
        $devices = new devices;
        $devices->moveToInventory($_GET['deviceID'],1,0);
        echo json_encode(array("created"));
    }
    else if($_GET['deleteAction'] == 'deleteCables')
    {
        $devices = new devices;
        $devices->delete($_GET['deviceID'],1);
        echo json_encode(array("created"));
    }
    else if($_GET['deleteAction'] == 'keepCables')
    {
        $devices = new devices;
        $devices->delete($_GET['deviceID'],0);
        echo json_encode(array("created"));
    }
}


if(isset($_GET['action']) && $_GET['action']=='upgrade' && is_numeric($_GET['deviceID']))
{
	$device = new device($_GET['deviceID']);
        // get a list of the categories of this device, we dont need the values
        // we use this to ensure new templates are the same floor/rack mountable
        $device->fillCategories(0,0);
	if($device) 
	{
?>
	<script type="text/javascript"> 
	function upgradeDeviceForm(results)
	{
            if(results[1]=='deviceSizeChanged')
            {
                $('#formHolder').html("The device you upgraded to is a different size to the previous.<br/>" +
                "Please check the racks page to ensure its new position doesn't conflict with any other devices." +
                "<center><br/><br/><input type='button' onclick='$(\"#menus\").html(\"\");$(\".closeDOMWindow\").click();' value='OK' /></center>");
            }
            else
            {
                $('#formHolder').html("<center><strong>Device Upgraded</strong></center><br/>" +
                "<center><br/><br/><input type='button' onclick='$(\"#menus\").html(\"\");$(\".closeDOMWindow\").click();' value='OK' /></center>");
                setTimeout("$('.closeDOMWindow').click();$('.success').hide(); ",1000);
            }

            $("#menus").html("");
	}
	</script>
        <div class="sectionHeader" >
            <strong>Upgrade: <?php echo $device->name;?></strong>
            <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
        </div>  
		<div id='formHolder' >
                <p class="note">
		Upgrading a device maintains all port &amp; cable associations but replaces the template and general device specifications.<br/><br/>
		<i>Please note: </i> This process overrides any original settings and details such as OS, maintainer &amp; ILO port are lost.
 Port mapping and order(0-4..) are maintained.</p>

		<p>
		<form method="post" action='deviceHandler.php?action=confirmUpgrade&deviceID=<?php echo $_GET['deviceID'];?>' id="upgradeDeviceForm">
		<strong>Move to Model:</strong> <select name='newTemplate' >
                    <?php $templates = new templates;
                    $newServers = $templates->getByDeviceType($device->deviceTypeID);
                    
                    $validtemplate=0;
                    foreach($newServers as $server)
                    {
                        // we don't want deleted templates
                        if($server->deleted!=1)
                        {
                            // get the categories so we can match up floor/rack mountable for templates
                            $server->fillCategories(1,1,array(GENERIC,RACK_MOUNTABLE,FLOOR_DEVICE));

                            // if the template matches the existing item lets not show it
                            if($device->templateID==$server->templateID)
                                continue;

                            // check it mounts in the same format, if so print the option
                            if((isset($device->categories[RACK_MOUNTABLE]) && isset($server->categories[RACK_MOUNTABLE])) || (isset($device->categories[FLOOR_DEVICE]) && isset($server->categories[FLOOR_DEVICE])))
                            {                            
                                echo "<option value='".$server->templateID."' >".ucwords($server->getMeta(12,1)) . ' ' . $server->name."</option>\n";
                                $validtemplate++;
                            }
                        }
                    }
                    // if we had no templates just put a filler, ID 0 so that if they hit submit we fail
                    if(!$validtemplate)
                        echo "<option value='0' >No Templates Available</option>";
			?>
		</select>
		</p>
                <p>
                    <input style='margin-left: 35px;' class='JSONsubmitForm' type="submit" value="Upgrade" /> <input type="button" onclick="$('.closeDOMWindow').click();" value="Cancel" />
                </p>
		</div></form>
<?php
	}
}

if(isset($_GET['action']) && $_GET['action']=='confirmUpgrade' && is_numeric($_GET['deviceID']))
{
    // TODO: allow device upgrades in new system.. interface complete, form submission required
	$return=array();
	$device = new device($_GET['deviceID']);
	
	if($_GET['newTemplate'] != 0)
	{
            $template = new template($_GET['newTemplate']);
            $device->model = $template->modelName;
            $device->vendor = $template->vendor;
            $device->depth = $template->depth;
            $device->PSUs = $template->PSUs;
            $device->templateID = $template->templateID;
            $device->PSUWattage = $template->PSUWattage;
            $device->peakPSUWattage = $template->peakPSUWattage;
            $device->averageBTU = $template->averageBTU;
            $device->weight = $template->weight;
            $device->copperPorts = $template->copperPorts;
            $device->ILO = $template->ILO;
            $device->frontPanelImage = $template->frontPanelImage;
            if($device->RU != $template->RU)
                    $return[1]="deviceSizeChanged";
            $device->RU = $template->RU;

            $devices = new devices;
            if($devices->replace($device))
                    $return[0]="created";
            else
                    $return[0]="error";
	}
	else
		$return[0]="error";
		
	echo json_encode($return);
}



else if(isset($_GET['templateID']) && !isset($_GET['action']) && is_numeric($_GET['templateID']))
{
    // as this form could be used for anything determine its parent details
    $parentID=$_GET['parentID'];
    $parentType=$_GET['parentType'];
    if(isset($_GET['position']))
        $position=$_GET['position'];
    else
        $position=0;


    // flag used to determine how we submit the form
    // either json submit or if there is a file we must do a post / refresh (for now)
    $hasFileUpload=0;

    $template = new template($_GET['templateID']);
    $template->fillCategories(1,1);
    if(!$template)
    { ?>
        <div class="module" id="createDevice" >
            <strong>Error</strong>
            <a href="#" class="closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
                <br/><font color="red" >Unable to find the template you requested.<br/>Please refresh the page and try again.</font>
        </div>
        <?php
        exit();
    }?>
        <script type="text/javascript">
        function createDeviceForm(returnDetails)
        {
            if(returnDetails['parentType']=='rack')
            {
                var back=0;
                if(back==1)
                    $("#racktbl_" + returnDetails['rackID'] + " .back.d" + returnDetails['position']).html("<div class='template"+returnDetails['templateID']+" r"+returnDetails['RU']+" drag' id='"+returnDetails['RU']+"' ><span class='device" + returnDetails['deviceID'] + "'>" + returnDetails['name'] + "</span></div>");
                else
                    $("#racktbl_" + returnDetails['rackID'] + " .d" + returnDetails['position']+":not('.back')").html("<div class='template"+returnDetails['templateID']+" r"+returnDetails['RU']+" drag' id='"+returnDetails['RU']+"' ><span class='device" + returnDetails["deviceID"] + "'>" + returnDetails['name'] + "</span></div>");
                $("#createDeviceForm .formError").removeClass("formError");
                $("#createDeviceForm .error").html("");

                // show the created message, make the rack draggable & close the window
                $(".success").show();
                makedraggable("racktbl_" + returnDetails['rackID']);
            }
            else if(returnDetails['parentType']=='room')
            {
                var query;
                query = "action=insertLayoutItem&";
                query += "height=32&";
                query += "width=32&";
                query += "itemID=" + returnDetails['deviceID'] + "&";
                query += "itemName=" + returnDetails['name'] + "&";
                query += "itemType=device&";
                query += "parentName=room&";
                query += "parentType="+returnDetails['parentType']+"&";
                query += "parentID=" + returnDetails['parentID'] +"&";
                query += "posx=" + returnDetails['posx'] + "&";
                query += "posy=" + returnDetails['posy'] + "&";
                query += "zindex=5";
                $.post("handler.php",query,function(layoutItemID)
                {
                    var content;
                    content = "<div title='" + returnDetails['name'] + "' id='layoutItemID" + layoutItemID + "' style='z-index:5;width:32px;height:32px;z-index:999; position:absolute; top: " + returnDetails['posy'] + "px; left: " + returnDetails['posx'] + "px' class='layoutItem deletable device draggable'>";
                    content += "<img style='float:left;' src='images/icons/delete_small.gif' />";
                    content += "<span>"+returnDetails['name']+"</span>";
                    content += "</div>";

                    $(".mapContent").append(content);
                    makeDraggable();
                });
            }
            setTimeout("$('.closeDOMWindow').click();$('.success').hide(); ",1000);
            return false;
        }
        </script>
        <form method="POST" action="deviceHandler.php?action=create" id="createDeviceForm">
            <input type="hidden" name="templateID" value="<?php echo $template->templateID; ?>" />
            <input type="hidden" name="parentID" value="<?php echo $parentID; ?>" />
            <input type="hidden" name="parentType" value="<?php echo $parentType; ?>" />
            <input type="hidden" name="position" value="<?php echo $position; ?>"/>
            <?php
            if(isset($_GET['posx']) && is_numeric($_GET['posx']))
                echo '<input type="hidden" name="posx" value="'.$_GET['posx'].'"/>';
            if(isset($_GET['posx']) && is_numeric($_GET['posy']))
                echo '<input type="hidden" name="posy" value="'.$_GET['posy'].'"/>';
            ?>
	<div class="sectionHeader" id="createDevice" >
            <strong>Create a new Device</strong>
            <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
        </div>
            <p>
                <table width="80%" class="formTable">
                    <colgroup align="left" class="tblfirstRow"></colgroup>
                <tr>
                    <td width="150px" align="right" valign="top"><label for="deviceType" >Device Type</label></td>
                    <td width="300px"><?php
                        $name = new deviceType($template->deviceTypeID);
                        echo $name->name;
                    ?></td>
                    <td rowspan="4" valign="top"><strong>Un-configurable Traits</strong>
                        <ul>
                <?php
            $categoriesClass = new attrcategories;
            $listOfCategories=$categoriesClass->getAll();
                foreach($template->categories as $key=>$catCount)
                {
                    if($key==HAS_NETWORK_PORTS||$key==IS_PATCH)
                        continue;
                    if(empty($catCount))
                    {
                        echo "<li>".$listOfCategories[$key]->name."</li>";
                    }
                }
                ?>
                        </ul></td>
                </tr>
                <tr><td align="right" valign="top"><label for="name" >Template Name</label></td><td valign="top"><?php echo $template->name; ?></td></tr>
                <tr><td align="right" ><label for="systemName" >System Name</label></td><td><input type="text" name="systemName" size="40"/> (required) </td></tr>
                <tr><td align="right" ><label for="ownerid" >Owner</label></td><td><select name="ownerID" >
                <?php
                    $owners = new owners;
                    foreach($owners->getAll() as $owner)
                        echo "<option value='".$owner->ownerID."' >".$owner->name."</option>";
                    ?>
                </select></td></tr>
                </table>
            </p>
<?php

        $names = new attrnames;
        foreach($listOfCategories as $category)
        {
            if(isset($template->categories[$category->attrcategoryid]))
                echo "<div id='category".$category->attrcategoryid."' >
                    <input type='hidden' name='category".$category->attrcategoryid."' value='1' />";
            else
                continue;
            ?>
              <strong style="display:block;padding:5px;"><?php echo $category->name; ?></strong>
              <table class="formTable" cellpadding="5">
                  <colgroup class="tblfirstRow"></colgroup>
            <?php
            // pickup the custom forms for ports and patches
            switch($category->attrcategoryid)
            {
                case HAS_NETWORK_PORTS:
                echo "<tr><td colspan='2'><strong>Define the detault ports:</strong>";
                $ports = new ports;
                $ports->createForm($template->templateID);
                echo "</td></tr>";
                $attributesfound = true;
                break;

                case IS_PATCH:
                echo "<tr><td colspan='2'><strong>Default Patch Ports:</strong>";
                $joins = new joins;
                $joins->createForm($template->templateID);
                echo "</td></tr>";
                $attributesfound = true;
                break;
            }


            // we now have 2 lists of categories
            // list of categories contains everything
            // template->category[<catID>][<nameID>]-> contains registered values
            foreach($names->getByParent($category->attrcategoryid, 'attrcategory') as $name)
            {
                // reset to prevent previous entry follow-on
                $itemName="";
                $itemValue="";
                
                // display the title, hide it if the internal list of values doesnt exist (value isnt set)
                if(isset($template->categories[$category->attrcategoryid]) && is_array($template->categories[$category->attrcategoryid]))
                {
                    $itemName="name".$name->attrnameid;
                    if(isset($template->categories[$category->attrcategoryid][$name->attrnameid]->value))
                       $itemValue=$template->categories[$category->attrcategoryid][$name->attrnameid]->value;
                }

                echo "<tr><td width='150px' align='right'>".$name->name."</td><td>";
                // Values that already exist we name differently in the form
                // existing<attrvalueid> so we can simply update the existing record (or delete as needed)
                // new values all get named name<attrnameid>

                if($name->control==1)
                    echo "<input type='hidden' name='".$itemName."' value='".$itemValue."' />".$itemValue;
                else
                {
                    switch ($name->type)
                    {
                        case "Textbox":
                            echo "<input type='text' name='".$itemName."' value='".$itemValue."' />";
                            break;
                         case "Date":
                            echo "<input class='date' type='text' name='".$itemName."' value='".$itemValue."' />";
                            break;
                        case "Number":
                            echo "<input style='width:50px;' class='number' type='text' name='".$itemName."' value='".$itemValue."' /> " . $name->units;
                            break;
                        case "Text Area":
                            echo "<textarea class='number' rows='5' name='".$itemName."' >".$itemValue."</textarea>";
                            break;
                        case "Checkbox":
                            echo "<input type='checkbox' name='".$itemName."' "; if($itemValue) { echo "CHECKED"; } echo "/>";
                            break;
                        case "Radio Buttons":
                            $values = explode(",",$name->options);
                            foreach ($values as $key=>$value)
                            {
                                echo "<input id='radio_".$itemName."_".$key."' type='radio' name='".$itemName."' value='" . $value . "' "; if($itemValue==$value) { echo "CHECKED"; } echo "/> <label for='radio_".$itemName."_".$key."' >" . $value . "</label><br />";
                            }
                            break;
                        case "Checkbox List":
                            $values = explode(",",$name->options);
                            foreach ($values as $value)
                            {
                                echo "<input type='radio' name='".$itemName."' "; if($itemValue==$value) { echo "CHECKED"; } echo "/><br />";
                            }
                            break;
                        case "Dropdown List":
                            $values = explode(",",$name->options);
                            echo "<select name='".$itemName."'>";
                            foreach ($values as $value)
                            {
                                echo "<option>" . $name->name . "</option>";
                            }
                            echo "</select>";
                            break;
                        case "File":
                            $hasFileUpload=1;
                            break;
                        case "Image":
                            $hasFileUpload=1;
                            break;
                    }

                    if($name->desc)
                        echo " <span class='hoverDescTooltip'> ? <span class='desc' style='display:none;'>".$name->desc."</span></span>";
                }
                echo "</td></tr>";
            }
            echo "</table><hr/></div>";
        }
        echo "<p><input style='margin:10px;float:left;' type='button' value='Cancel &amp; Close' class='closeDOMWindow' />
        <input type='submit' style='margin:10px;float:right;' name='submit' value='Create Device' class='JSONform' /></p></form>";
?>
        </div></form>
<?php
}

elseif(isset($_GET['action']) && $_GET['action']=="create")
{

    $devices = new devices;
    // determine if any required values are set/valid
    $badFields=array();
    if(isset($_POST['systemName']) || !$_POST['systemName']) {
        $systemName =  preg_replace("/[^a-zA-Z0-9 -]/", '', $_POST['systemName']);
        if(!$systemName)
            $badFields[]="systemName";
    } else
        $badFields[]="systemName";
		
    if(!isset($_POST['templateID']) || !is_numeric($_POST['templateID']))
        $badFields[]="templateID";

    if(!isset($_POST['ownerID']))
        $badFields[]='ownerID';
		
    if(count($badFields)==0)
    {
        // make the items we need to pull values from and set
        $template = new template($_POST['templateID']);

        $newDevice = new device;
        $newDevice->name=$_POST['systemName'];
        $newDevice->ownerID=$_POST['ownerID'];
        $newDevice->deviceTypeID=$template->deviceTypeID;
        $newDevice->parentID=$_POST['parentID'];
        $newDevice->parentType=$_POST['parentType'];
        $newDevice->position=$_POST['position'];
        $newDevice->templateID=$_POST['templateID'];
        $deviceID = $devices->insert($newDevice);

        // if not null the device was created and we continue
        if($deviceID)
        {
            $catValues = new attrcategoryvalues;
            $values = new attrvalues;
            $names = new attrnames;

            $selectedCategories=array();
            foreach($_POST as $postKey=>$postVal)
                if(preg_match("/category/i", $postKey))
                    $selectedCategories[]=preg_replace("/category/i","", $postKey);

            $categoriesToCreate=array();
            $valuesToCreate=array();
            foreach($selectedCategories as $category)
            {
                $cat = new attrcategoryvalue;
                $cat->parentID=$deviceID;
                $cat->parentType="device";
                $cat->categoryID=$category;
                $categoriesToCreate[]=$cat;

                // detect all the DB required fields for this category and add them to the list for creation
                foreach($names->getByParent($category, 'attrcategory') as $name)
                {
                    $value=new attrvalue();
                    $value->attrnameid=$name->attrnameid;
                    if(isset($_POST['name'.$name->attrnameid]))
                        $value->value=$_POST['name'.$name->attrnameid];
                    $value->parentid=$deviceID;
                    $value->parenttype='device';
                    $valuesToCreate[]=$value;
                }
            }

            // Insert the categories and name values for this item
            if(!$catValues->insertMultiple($categoriesToCreate) || !$values->insertMultiple($valuesToCreate))
                $status[0]=0;

            if(in_array(HAS_NETWORK_PORTS,$selectedCategories))
            {
                $ports = new ports;
                if(!$ports->createPorts($deviceID,$_POST))
                    $status[0]=0;
            }

            if(in_array(IS_PATCH,$selectedCategories))
            {
                $joins = new joins;
                if(!$joins->createJoins($deviceID,$_POST))
                    $status[0]=0;
            }
        }
        else
            $error=array(1,"Unable to create device");
    }

    // Determine the type of submission so we know how to redirect the user
    if($_GET['typeOfSubmission']=='ajax')
    {
        $response=array();
        $response['parentType']=$newDevice->parentType;
        $response['parentID']=$newDevice->parentID;
        $response['rackID']=$newDevice->parentID;
        $response['position']=$newDevice->position;
        $response['templateID']=$newDevice->templateID;
        $response['deviceID']=$deviceID;
        $response['name']=$newDevice->name;

        if(isset($_POST['name5']) && is_numeric($_POST['name5']))
            $response['RU']=$_POST['name5'];

        if(isset($_POST['posx']) && is_numeric($_POST['posx']))
            $response['posx']=$_POST['posx'];
        if(isset($_POST['posy']) && is_numeric($_POST['posy']))
            $response['posy']=$_POST['posy'];
        echo json_encode(array(1,$response));

    }
    else
        header("Location: racks.php");
}
?>