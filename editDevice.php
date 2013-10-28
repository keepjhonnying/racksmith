<?php
session_start();
include "class/db.class.php";

if(isset($_GET['deviceID']) && !isset($_GET['action']) && is_numeric($_GET['deviceID']))
{
    // If the device ID is invalid  then show an error then exit
    $categoriesClass = new attrcategories;
    $listOfCategories=$categoriesClass->getAll();
    $device = new device($_GET['deviceID']);
    $device->fillCategories(1,1);
    if(!$device)
    {
        ?>
        <div id="editDevice" >
            <strong>Error</strong>
            <a href="#" class="closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
            <br/><font color="red" >Unable to find the device you requested.<br/>Please refresh the page and try again.</font>
        </div>
        <?php
        exit();
    }
    ?>

	<div class="sectionHeader" >
            <strong><em>edit:</em> <?php echo $device->name; ?></strong>
            <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
        </div>
        <div  class="categoryData" id="editDevice">
	<script type="text/javascript"> 
	$(document).ready(function()
	{	        
            $("#deviceInsertPort").click(function()
            {
                var portType = $("#editDeviceNetwork select[name=portType]").attr("value");
                var portTypeName = $("#editDeviceNetwork select[name=portType] :selected").text();
                var IPAddress = $("#editDeviceNetwork :input[name=IPAddress]").attr("value");
                if(IPAddress=="IP Address")
                    IPAddress="";
                var MACAddress = $("#editDeviceNetwork :input[name=MACAddress]").attr("value");
                if(MACAddress=="MAC Address")
                    MACAddress="";
                var bandwidth = $("#editDeviceNetwork select[name=bandwidth]").attr("value");

                var disporder = $("#editDeviceNetwork :input[name=disporder]").attr("value");
                var bandwidthText = $("#editDeviceNetwork select[name=bandwidth] :selected").text();
                var label = $("#editDeviceNetwork :input[name=label]").attr("value");

                $("#editDeviceNetwork tbody #ilomRow").before('<tr><td><input type="hidden" name="portID[]" value="0" /><input type="hidden" name="portType[]" value="' + portType + '" />' + portTypeName +
                '<input type="hidden" name="disporder[]" value="' + disporder + '" />' +
                '</td><td><input type="text" name="ipAddr[]" value="' + IPAddress + '" />' +
                '</td><td><input type="text" name="macAddr[]" value="' + MACAddress + '" />' +
                '</td><td><input type="text" name="bandwidth[]" value="' + bandwidth + '" />' +
                '</td><td><input type="text" name="label[]" value="' + label + '" />' +
                '<td align="right"><a onclick="$(this).closest(\'tr\').remove();" >delete</a></td></tr>');

                adjustPortCount();
            });
            $("#deviceAddLicence").click(function()
            {
                    var software = $("#editDeviceSoftware :input[name=software]").attr("value");
                    var licence = $("#editDeviceSoftware ::input[name=licence]").attr("value");
                    var softwareDetails = $("#editDeviceSoftware :input[name=softwareDetails]").attr("value");

                    $("#editDeviceSoftware tbody").prepend('<tr><td><input type="hidden" name="licenceID[]" value="" /><input type="text" style="width:200px;" name="softwareName[]" value="' + software + '" />' +
                    '</td><td><input type="text" name="licenceKey[]" style="width: 260px;" value="' + licence + '" />' +
                    '</td><td><input type="text" name="softwareNotes[]" style="width:300px;" value="' + softwareDetails + '" />' +
                    '<td><a onclick="$(this).closest(\'tr\').hide();" >delete</a></td></tr>');
            });


            $(":input[name=IPAddress],:input[name=MACAddress],:input[name=bandwidth],:input[name=label]").click(function ()
            {
                    if(this.value == this.defaultValue)
                            $(this).attr("value","");
            });

            $(":input[name=software],:input[name=licence],:input[name=softwareDetails]").click(function ()
            {
                    if(this.value == this.defaultValue)
                            $(this).attr("value","");
            });

           $(".hoverDescTooltip").live("mouseover mouseout",function(event) {
               if(event.type=="mouseover")
               {
                    var leftPos = $(this).offset().left+20; //$(event).pageX;
                    var topPos = $(this).offset().top; //$(event).pageY;

                    // position the popup
                    $("body").append('<div id="hoverBox" class="menuBox"><div class="content" id="rackDetails"></div></div>');
                    $("#hoverBox").css("position","absolute");
                    $("#hoverBox").css("left",leftPos + "px");
                    $("#hoverBox").css("top",topPos + "px");
                    $("#hoverBox").css("z-index",999);
                    $("#hoverBox").show();
                    $("#hoverBox .content").html($(this).find(".desc").text());

               }
               else
                    $("#hoverBox").remove();
           });
        });

	/* Called once the form has submitted, closes the window */
	function editDeviceForm(returnedItem)
	{
            // Update the name on any devices on this page
            $(".device"+returnedItem[0]).html(returnedItem[1]);
            $(".categoryData#editDevice").slideUp(500,function() {
                $(".categoryData#editDevice").html("<div class='module success' >Device Changes Saved</div>");
                $(".categoryData#editDevice").slideDown(500,function() {
                    setTimeout("$('.closeDOMWindow').click();$('.success').hide(); ",1200);
                    $("#menus").html("");
                });
            });
	}

        function toggleCategory(catID)
        {
           // count the items in this category to determine if we toggle
           // smooths out the animation for empty cats
           if($("#checkboxfor"+catID+":checked").length!=0)
               $("#category"+catID).slideDown(100);
           else
               $("#category"+catID).slideUp(100);
}
	</script>
            <form method="POST" action="editDevice.php?action=edit&return=racks" id="editDeviceForm">
                <input type="hidden" name="deviceid" value="<?php echo $device->deviceID; ?>"/>

                <table class="formTable" cellpadding="5">
                      <colgroup class="tblfirstRow"></colgroup>
                      <tr><td width='150px' align='right'>Name</td><td><input type="text" name="systemName" value="<?php echo $device->name; ?>" /></td></tr>
                      <tr><td align="right">Owner</td><td><?php
                      $owners = new owners;
                      echo "<select name='ownerid' >";
                        foreach($owners->getAll() as $owner)
                        {
                            echo "<option value='".$owner->ownerID."' ";
                            if($owner->ownerID==$device->ownerID)
                                echo "SELECTED";
                            echo ">".$owner->name."</option>";
                        }
                      echo "</select>";
                      ?></td></tr>
                </table>
<?php
            // default submission method, we change this if we run into a file upload
             $typeOfSubmission="ajax";
                foreach($listOfCategories as $category)
                {

                echo "<div id='category".$category->attrcategoryid."'";
                if(!isset($device->categories[$category->attrcategoryid]))
                    echo " style='display:none;' ";
                ?> >

                  <strong style="display:block;padding:5px;"><?php echo $category->name; ?></strong>
                  <table class="formTable" cellpadding="5">
                      <colgroup class="tblfirstRow"></colgroup>
                <?php
                $names = new attrnames;

                switch($category->attrcategoryid)
                {
                    case HAS_NETWORK_PORTS:
                        echo "<tr><td colspan='2'><strong>Define the detault ports:</strong>";
                        $ports = new ports;
                        $ports->templateEditForm($device->deviceID);
                        echo "</td></tr>";
                        $attributesfound = true;
                        break;

                    case IS_PATCH:
                        echo "<tr><td colspan='2'><strong>Default Patch Ports:</strong>";
                        $joins = new joins;
                        $joins->templateEditForm($device->deviceID);
                        echo "</td></tr>";
                        $attributesfound = true;
                        break;
                }


                // we now have 2 lists of categories
                // list of categories contains everything
                // template->category[<catID>][<nameID>]-> contains registered values
                foreach($names->getByParent($category->attrcategoryid, 'attrcategory') as $name)
                {
                    $itemValue="";
                    // display the title, hide it if the internal list of values doesnt exist (value isnt set)
                    if(isset($device->categories[$category->attrcategoryid]) && is_array($device->categories[$category->attrcategoryid]))
                    {
                        echo "<tr><td width='150px' align='right' >".$name->name."</td><td>";
                        if(isset($device->categories[$category->attrcategoryid][$name->attrnameid]->attrvalueid))
                            $itemName="existing".$device->categories[$category->attrcategoryid][$name->attrnameid]->attrvalueid;
                        else
                            $itemName="new".$name->attrnameid;
                        
                        // check that the value already exists first
                        if(isset($device->categories[$category->attrcategoryid][$name->attrnameid]->value))
                            $itemValue=$device->categories[$category->attrcategoryid][$name->attrnameid]->value;
                    }
                    else
                    {
                        echo "<tr><td width='150px' align='right'>".$name->name."</td><td>";
                        $itemName="new".$name->attrnameid;
                        $itemValue="";
                    }
                    // Values that already exist we name differently in the form
                    // existing<attrvalueid> so we can simply update the existing record (or delete as needed)
                    // new values all get named name<attrnameid>

                    if($name->control==1)
                        echo $itemValue;
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
                            case "Image":
                                $typeOfSubmission="redirect";
                                break;
                        }
                    }

                    if($name->desc)
                        echo " <span class='hoverDescTooltip'> ? <span class='desc' style='display:none;'>".$name->desc."</span></span>";
                    echo "</td></tr>";
                }
                echo "</table><hr/></div>";
            }

?>

        <table width="80%" class="dataTable">
            <colgroup align="left" class="tblfirstRow"></colgroup>
            <tr>
                <td bgcolor="#e0ecf1" colspan="2" onclick="$('#notesInput').slideToggle();"><strong><label for="notes" >Notes:</label></td>
            </tr>
            <tr id='notesInput'>
                <td colspan="2"><textarea name="notes" rows="7" style="width: 650px;max-width: 650px;"></textarea></td>
            </tr>
        </table>

        <table width="80%" class="dataTable">
            <colgroup align="left" class="tblfirstRow"></colgroup>
            <tr><td colspan="2" ><input type="submit" value="Submit" <?php if($typeOfSubmission=='ajax') { echo 'class="JSONform"'; } ?>/></td></tr>
            <tr><td class="error" style="display: none;" colspan="2"></td></tr>
        </table>

            <div class="twoColumns">
                <div style="padding:5px;">Adjust Traits:</div>

                <ul>
                <?php
                    $categoriesClass = new attrcategories;
                    $listOfCategories=$categoriesClass->getAll();
                    foreach($listOfCategories as $category)
                    {
                ?>
                    <li>
                        <input type="checkbox" name="category<?php echo $category->attrcategoryid ?>" id="checkboxfor<?php echo $category->attrcategoryid ?>" onclick="toggleCategory('<?php echo $category->attrcategoryid ?>');"
                               <?php
                               if(isset($device->categories[$category->attrcategoryid]))
                                   echo "CHECKED";
                               ?>/>
                        <label for='checkboxfor<?php echo $category->attrcategoryid ?>' ><?php echo $category->name; ?></label>
                    </li>
                <?php
                    }
                ?>
                </ul>
                <br style="clear:both;" />
            </div>
        </form>
            <div class="success" style="display: none;" >Device Updated</div>
        </p>

  <?php
echo 		"</form></table></div>";
}


// form submission
elseif(isset($_GET['action']) && $_GET['action']=="edit")
{
/*
	// determine if any required values are set/valid
	$errors=array();
	if(isset($_GET['systemName']) || !$_GET['systemName']) {
		$systemName =  preg_replace("/[^a-zA-Z0-9 -]/", '', $_GET['systemName']);  
		if(!$systemName)
			$errors[]="systemName";
	} else 
		$errors[]="systemName";

	if(!is_numeric($_GET['deviceID']))
		$errors[]="deviceID";
		
	if(!is_numeric($_GET['owner']))
		$errors[]="owner";
		
	if(count($errors)==0)
	{
		// make the items we need to pull values from and set
		$devices = new devices();
		$device = new device($_GET['deviceID']);
		$device->systemName = $_GET['systemName'];
                $device->serial = $_GET['serial'];
                $device->warranty=$_GET['warranty'];
		$device->OS=$_GET['OS'];
		$device->ownerID=$_GET['owner'];
		$device->revision = $_GET['revision'];
		$device->averageBTU = $_GET['averageBTU'];
		$device->ILO = $_GET['ILO'];
		$device->expansion = $_GET['expansion']; //$template->expansion;
		$device->notes = strip_tags($_GET['notes']);
		$device->maintainer=$_GET['maintainer'];

		$updated = $devices->update($device);
		
		if($updated)
		{
			if(isset($_GET['ipAddr']))
			{
				$ports = new ports;
				$i=0;
				foreach($_GET['ipAddr'] as $ip)
				{		
					if($_GET['portID'][$i]==0)
					{
                                            $port = new port();
                                            $port->deviceID = $_GET['deviceID'];
                                            $port->vlan = 1;
                                            $port->cableTypeID = $_GET['portType'][$i];
                                            if($_GET['ipAddr'][$i] == "IP Address")
                                                $_GET['ipAddr'][$i]="";
                                            $port->ipAddress = $_GET['ipAddr'][$i];
                                            if($_GET['macAddr'][$i]=="")
                                                $_GET['macAddr'][$i]="";
                                            $port->macAddress = $_GET['macAddr'][$i];
                                            $port->bandwidth = $_GET['bandwidth'][$i];
                                            $port->label = $_GET['label'][$i];
                                            $port->dispodrder = $_GET['disporder'][$i];
                                            $port->cableID = 0;
                                            $ports->insert($port);
					}
					elseif($_GET['ipAddr'][$i] =='deleted')
                                            $ports->delete($_GET['portID'][$i]);
					else
					{
                                            $port = new port($_GET['portID'][$i]);
                                            $port->ipAddress = $_GET['ipAddr'][$i];
                                            $port->cableTypeID = $_GET['portType'][$i];
                                            $port->macAddress = $_GET['macAddr'][$i];
                                            $port->bandwidth = $_GET['bandwidth'][$i];
                                            $port->disporder = $_GET['disporder'][$i];
                                            $port->label = $_GET['label'][$i];
                                            $ports->update($port);
					}
					$i++;
				}
			}

			// If there was at least one licence entered loop over them
			if(isset($_GET['softwareName']))
			{
				$licences = new licences;
				$i=0;
				foreach($_GET['softwareName'] as $ip)
				{		
					// If the licence values are delete we want to remove it
					// else assume its an update
					if($_GET['softwareName'][$i] =='deleted' && $_GET['licenceKey'][$i]=='deleted')
						$licences->delete($_GET['licenceID'][$i]);
					else if($_GET['licenceID'][$i] == 0)
					{
						$licence = new licence();
						$licence->deviceID = $_GET['deviceID'];
						$licence->software = $_GET['softwareName'][$i];
						$licence->licence = $_GET['licenceKey'][$i];
						$licence->softwareNotes = $_GET['softwareNotes'][$i];
						$licences->insert($licence);
					}
					else
					{
						$licence = new licence($_GET['licenceID'][$i]);
						$licence->deviceID = $_GET['deviceID'];
						$licence->software = $_GET['softwareName'][$i];
						$licence->licence = $_GET['licenceKey'][$i];
						$licence->softwareNotes = $_GET['softwareNotes'][$i];
						$licences->update($licence);
					}
					$i++;
				}
			}
		}
			
		echo json_encode(array("created"));
	}
	else
	{
		$returnError['error']=$errors;
		echo json_encode($returnError);	
	}
 * */
    $updateStatus=array();

    // update the device entry
    $device = new device($_POST['deviceid']);
    $device->name=$_POST['systemName'];
    $device->ownerID=$_POST['ownerid'];
    $devices=new devices;
    $updateStatus[]=$devices->update($device);

    // get an array of the selected categories in this post
    $selectedCategories=array();
    $valueIDsToUpdate=array();
    $valueIDsToCreate=array();
    foreach($_POST as $postKey=>$postval)
    {
        if(preg_match("/category/i", $postKey))
            $selectedCategories[]=preg_replace("/category/i","", $postKey);

        if(preg_match("/existing/i", $postKey))
            $valueIDsToUpdate[]=preg_replace("/existing/i","", $postKey);
        
        if(preg_match("/new/i", $postKey))
            $valueIDsToCreate[]=preg_replace("/new/i","", $postKey);
    }

    // grab all the categories for this device so we can check whats new, deleted or left
    $device->fillCategories(1,1);
    $categoriesToDelete=array();
    $categoriesToLeave=array();
    foreach($device->categories as $catKey=>$catVal)
    {
        // if the category doesnt have a match then we must delete it
        if(!in_array($catKey, $selectedCategories))
            $categoriesToDelete[]=$catKey;
        // everything else is new or edited
        else
            $categoriesToLeave[]=$catKey;
    }

    // conbine the del/leave array and figure out which items are new entries
    $items=array_merge($categoriesToDelete,$categoriesToLeave);
    $categoriesToCreate=array_diff($selectedCategories,$items);

    // from here create all these new entries and insert them
    $catValues = new attrcategoryvalues;
    $values = new attrvalues;
    $names = new attrnames;
    $categortyObjectToCreate=array();
    $valuesToCreate=array();
    foreach($categoriesToCreate as $category)
    {
        $cat = new attrcategoryvalue;
        $cat->parentID=$device->deviceID;
        $cat->parentType="device";
        $cat->categoryID=$category;
        $categortyObjectToCreate[]=$cat;

        // detect all the DB required fields for this category and add them to the list for creation
        foreach($names->getByParent($category, 'attrcategory') as $name)
        {
            $value=new attrvalue();
            $value->attrnameid=$name->attrnameid;
            $value->value=$_POST['name'.$name->attrnameid];
            $value->parentid=$device->deviceID;
            $value->parenttype='device';
            $valuesToCreate[]=$value;
        }
    }

    // these are new values that exist within the template and are usually part of an existing category
    foreach($valueIDsToCreate as $itemID)
    {
        $value=new attrvalue();
        $value->attrnameid=$itemID;
        $value->value=$_POST['new'.$itemID];
        $value->parentid=$device->deviceID;
        $value->parenttype='device';
        $valuesToCreate[]=$value;
    }
    $updateStatus[]=$catValues->insertMultiple($categortyObjectToCreate);
    $updateStatus[]=$values->insertMultiple($valuesToCreate);


    // update array [<valueID>]=newVal
    $arrayOfUpdates=array();
    foreach($valueIDsToUpdate as $item)
        $arrayOfUpdates[$item]=$_POST['existing'.$item];
    
    $updateStatus[]=$values->updateMultipleValues($arrayOfUpdates);

    // for each of the cats we need to delete
    $valuesToDelete=array();
    foreach($categoriesToDelete as $calDel)
    {
        // go within and determine the values we must delete
        foreach($names->getByParent($calDel, 'attrcategory') as $name)
            $valuesToDelete[]=$name->attrnameid;
    }
    // delete the values and the parent categories (there is no table for namevalues atm)
    $updateStatus[]=$values->deleteMultiple($device->deviceID,'device',$valuesToDelete);
    $updateStatus[]=$catValues->deleteMultiple($device->deviceID,'device',$categoriesToDelete);

    if(in_array("0",$updateStatus))
    {
        if($_GET['typeOfSubmission']=="ajax")
            echo json_encode(array(0,"there was an error"));
        else
        {
            if(isset($_GET['return']) && $_GET['return']=='racks')
                header("Location: racks.php");
        }
    }
    else
    {
        if($_GET['typeOfSubmission']=="ajax")
            echo json_encode(array(1,array($device->deviceID,$device->name)));
        else
        {
            if(isset($_GET['return']) && $_GET['return']=='racks')
                header("Location: racks.php?status=updatedDevice");
        }
    }
}
?>