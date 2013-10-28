<?php
session_start();
include "class/db.class.php";
if(isset($_GET['rackID']) && is_numeric($_GET['rackID']) && isset($_GET['action']) && $_GET['action']=='mouseover')
{
    $rack = new rack($_GET['rackID']);
    $devices=new devices;
    if(!$rack)
    {
        echo "Unable to find a valid rack";
        exit();
    }
    $devicesInRack=$devices->getByParent($_GET['rackID'],'rack');
    $deviceCount=count($devicesInRack);

    $weight=0;
    $PSUWattage=0;
    $peakPSUWattage=0;
    $averageBTU=0;
    $RUcount=0;

    // calculate totals for all the devices
    foreach($devicesInRack as &$device)
    {
        $device->fillCategories(1,1,array(GENERIC,DRAWS_POWER,RACK_MOUNTABLE));
        //$weight+=$device['weight'];
        //$PSUWattage+=$device['PSUWattage'];
        //$peakPSUWattage+=$device['peakPSUWattage'];
        //$averageBTU+=$device['averageBTU'];
        if(isset($device->categories[RACK_MOUNTABLE][5]->value))
            $RUcount+=$device->categories[RACK_MOUNTABLE][5]->value;

        if(isset($device->categories[DRAWS_POWER][19]->value))
            $PSUWattage+=$device->categories[DRAWS_POWER][19]->value;

        if(isset($device->categories[GENERIC][14]->value))
            $weight+=$device->categories[GENERIC][14]->value;
    }
    echo "<b>Usage:</b> ".$RUcount." of ".$rack->RU." ru, ";
    echo ($rack->RU)-$RUcount." remaining";


        
    if($rack->model)
        echo "<br/><b>Model:</b> ".$rack->model;

    if($weight >0 || $PSUWattage >0)
        echo "<p><b>Totals:</b>";
    if($weight>0)
        echo "<br/><b>Device Weight:</b> ".number_format($weight)." kg";
    if($PSUWattage > 0)
        echo "<br/><B>Power Draw:</b> ".number_format($PSUWattage)." watts";

}
else if(isset($_GET['rackID']) && is_numeric($_GET['rackID']) && isset($_GET['action']) && $_GET['action']=='edit' && !isset($_GET['name']))
{
	$owners = new owners;
	$rack = new rack($_GET['rackID']);
?>
    <div class="sectionHeader" >
        <strong><em>Edit Rack:</em> <?php echo $rack->name; ?></strong>
        <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
    </div>
            <div class="form" id="editRackForm">
            <p>
                <form method="post" id='editRackForm' action='rackhandler.php?action=edit' >
                <input type='hidden' name='rackID' value='<?php echo $rack->rackID;?>' />
                <table width="80%" class="formTable">
                    <colgroup align="left" class="tblfirstRow"></colgroup>
                        <tr>
                            <td><label for="name" >Name:</label></td>
                            <td colspan="3"><input name="name" type="text" id="name" value="<?php echo $rack->name; ?>" size="16"/></td>
                        </tr>
                        <tr>
                            <td><label for="ownerID" >Owner:</label></td>
                            <td colspan="3">
                                <select name="ownerID">
                                <?php foreach($owners->getAll() as $owner)
                                {
                                    if($owner->ownerID == $rack->ownerID)
                                            $selected="SELECTED";
                                    else
                                            $selected="";
                                    echo '<option value="'.$owner->ownerID.'" '.$selected.'>'.$owner->name.'</option>';
                                } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="model" >Model:</label></td>
                            <td colspan="3"><input name="model" type="text" value="<?php echo $rack->model; ?>" id="model" size="30" /></td>
                        </tr>
                        <tr>
                            <td><label for="RU" >RU:</label></td>
                            <td><input name="RU" type="text" id="RU" size="2" value="<?php echo $rack->RU; ?>" /> <i>rack units</i></td>
                            <td class="tblfirstRow"><label for="width" >Width:</label></td>
                            <td><input name="width" type="text" id="width" value="<?php echo $rack->width; ?>" size="3" /> <i>inch</i></td>
                        </tr>

                        <tr>
                            <td><label for="depth" >Depth:</label></td><td>
                                    <select name="depth" id="depth" >
                                        <option value="450" <?php if($rack->depth == 450) { echo "SELECTED"; }?>>450mm</option>
                                        <option value="600" <?php if($rack->depth == 600) { echo "SELECTED"; }?>>600mm</option>
                                        <option value="800" <?php if($rack->depth == 800) { echo "SELECTED"; }?>>800mm</option>
                                        <option value="900" <?php if($rack->depth == 900) { echo "SELECTED"; }?>>900mm</option>
                                        <option value="1000" <?php if($rack->depth == 1000) { echo "SELECTED"; }?>>1000mm</option>
                                    </select>
                            </td>
                            <td class="tblfirstRow"><label for="height" >Height:</label></td>
                            <td><input name="height" type="text" id="height" value="<?php echo $rack->height; ?>" size="3" /> <i>inch</i></td>
                        </tr>
                        <tr>
                            <td><label for="notes" >Notes:</label></td>
                            <td colspan="3"><textarea name='notes' rows="8" cols="75"><?php echo $rack->notes; ?></textarea></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="3">
                            <input type="submit" value="Submit" class="JSONsubmitForm" />
                            <div class="error" style="display:none;"></div>
                            </td>
                        </tr>
                    </table>
                    </form>
                    <script>
                    function editRackForm() {
                        $(".success").show();
                        setTimeout("$('.closeDOMWindow').click();$('.success').hide(); ",3000);
                        return false;
                    }
                    </script>
                    <div class="success" style="display: none;" >Rack Adjusted, changes will take place when page is refreshed next</div>
				
                </p>
            </div>
		
<?php 
}
else if(isset($_GET['name']) && isset($_GET['action']) && $_GET['action']=='edit')
{
    $errors = array();
    $racks = new racks;
    $rack = new rack($_GET['rackID']);

    if(!$_GET['name'])
        $errors[]="name";
    else
        $rack->name = $_GET['name'];

    // update the layout item
    // needed for when the name or image depth changes on the minimap
    $layoutItems=new layoutItems;
    $layout=$layoutItems->getByType($_GET['rackID'], 'rack1');
    $layout->height=$_GET['depth']/18.75;
    $layout->name=$_GET['name'];
    $layoutItems->update($layout);
    
    $rack->ownerID = $_GET['ownerID'];
    $rack->model = $_GET['model'];
    if(!is_numeric($_GET['RU']) || $_GET['RU']<=0 || $_GET['RU']>100)
        $errors[]="RU";
    else
        $rack->RU = $_GET['RU'];
    $rack->width = $_GET['width'];
    $rack->height = $_GET['height'];
    $rack->notes = $_GET['notes'];
    $rack->depth = $_GET['depth'];
    if(count($errors)!=0)
        echo json_encode(array('error'=>$errors));
    else
    {
        if($racks->update($rack))
            echo json_encode(array('created'));
        else
            echo json_encode(array('error'=>'unable_to_save'));
    }
}
else
    echo "<b>Error:</b> Unable to find a valid rack ID";
?>