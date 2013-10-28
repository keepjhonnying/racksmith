<?php
session_start();
$selectedPage="configure";
include "class/db.class.php";

$owners = new owners; // declare class object

if(isset($_GET['id']) && isset($_GET['mode']) && $_GET['mode'] == 'delete' && is_numeric($_GET['id'])) // link actions
{
    if($_GET['id']!=1)
        $owners->delete($_GET['id']);
    header("Location: owners.php");
}

if (isset($_GET['mode']) && $_GET['mode'] == 'popup' && is_numeric($_GET['id'])) {
        $currentOwner = new owner($_GET['id']);

// show edit form
?>
    <div>
        <div class="sectionHeader" >
            <strong>Contact: <?php echo $currentOwner->contactname; ?></strong>
            <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
        </div><br/>
        
        <div class="form" align="center">
        <table width="80%" class="dataTable">
            <colgroup align="left" class="tblfirstRow"></colgroup>
            <tr>
                <td width="30%"><label for="name" >Name:</label></td><td><?php echo $currentOwner->name; ?></td>
            </tr>
            <tr>
                <td><label for="contactname" >Contact Name:</label></td><td><?php echo $currentOwner->contactname; ?></td>
            </tr>
            <tr>
                <td><label for="phone" >Phone:</label></td><td><?php echo $currentOwner->phone; ?></td>
            </tr>
            <tr>
                <td><label for="afterHoursPhone" >After Hour Phone:</label></td><td><?php echo $currentOwner->afterHoursPhone; ?></td>
            </tr>
            <tr>
                <td><label for="email" >Email:</label></td><td><?php echo $currentOwner->email; ?></td>
            </tr>
            <tr>
                <td><label for="fax" >Fax:</label></td><td><?php echo $currentOwner->fax; ?></td>
            </tr>
            <tr>
                <td><label for="mobile" >Mobile:</label></td><td><?php echo $currentOwner->mobile; ?></td>
            </tr>
            <tr>
                <td><label for="serviceLevel" >Service Level:</label></td><td><?php echo $currentOwner->serviceLevel; ?></td>
            </tr>
        </table>
        </form>
        </div>
    </p>
    </div>
<?php
exit();
}

else if(isset($_POST['btnSubmit'])) // is page post back
{
    if (isset($_GET['mode']) && $_GET['mode'] == 'edit')
    {
        // edit
        $newOwner = new owner($_GET['id']); // get current

        // Change to new info
        $newOwner->name = $_POST['name'];
        $newOwner->contactname = $_POST['contactname'];
        $newOwner->phone = $_POST['phone'];
        $newOwner->afterHoursPhone = $_POST['afterHoursPhone'];
        $newOwner->email = $_POST['email'];
        $newOwner->fax = $_POST['fax'];
        $newOwner->mobile = $_POST['mobile'];
        $newOwner->serviceLevel = $_POST['serviceLevel'];
	
        // send to database
        $owners->update($newOwner);
        header("Location: owners.php");
    }
    else
    {
        // insert
        $newOwner = new owner; // create new class

        // Fill class with new info
        $newOwner->name = $_POST['name'];
        $newOwner->contactname = $_POST['contactname'];
        $newOwner->phone = $_POST['phone'];
        $newOwner->afterHoursPhone = $_POST['afterHoursPhone'];
        $newOwner->email = $_POST['email'];
        $newOwner->fax = $_POST['fax'];
        $newOwner->mobile = $_POST['mobile'];
        $newOwner->serviceLevel = $_POST['serviceLevel'];
		
        // send to database
        $owners->insert($newOwner);
        header("Location: owners.php");
    }

}

$globalTopic = "Manage Owners"; // page title
include "theme/" . $theme . "/top.php";
?>
<div id="main"> 
<div id="left">
<?php
if (isset($_GET['mode']) && $_GET['mode'] == 'edit' && is_numeric($_GET['id'])) {
        $currentOwner = new owner($_GET['id']);

// show edit form
?>
    <div class="module" id="editOwner">
    <strong>Update Owners Details</strong>
    <p>
        <div class="form" align="center">
        <form method="post" action="?mode=edit&id=<?php echo $_GET['id']; ?>" id="editOwner">
        <table width="80%" class="formTable">
            <colgroup align="left" class="tblfirstRow"></colgroup>
            <tr>
                <td><label for="name" >Name:</label></td><td><input name="name" type="text" id="name" value="<?php echo $currentOwner->name; ?>" /></td>
            </tr>
            <tr>
                <td><label for="contactname" >Contact Name:</label></td><td><input name="contactname" type="text" id="contactname" value="<?php echo $currentOwner->contactname; ?>" /></td>
            </tr>
            <tr>
                <td><label for="phone" >Phone:</label></td><td><input name="phone" type="text" id="phone" value="<?php echo $currentOwner->phone; ?>" /></td>
            </tr>
            <tr>
                <td><label for="afterHoursPhone" >After Hour Phone:</label></td><td><input name="afterHoursPhone" type="text" id="afterHoursPhone" value="<?php echo $currentOwner->afterHoursPhone; ?>" /></td>
            </tr>
            <tr>
                <td><label for="email" >Email:</label></td><td><input name="email" type="text" id="email" value="<?php echo $currentOwner->email; ?>" /></td>
            </tr>
            <tr>
                <td><label for="fax" >Fax:</label></td><td><input name="fax" type="text" id="fax" value="<?php echo $currentOwner->fax; ?>" /></td>
            </tr>
            <tr>
                <td><label for="mobile" >Mobile:</label></td><td><input name="mobile" type="text" id="mobile" value="<?php echo $currentOwner->mobile; ?>" /></td>
            </tr>
            <tr>
                <td><label for="serviceLevel" >Service Level:</label></td><td><textarea name="serviceLevel" id="serviceLevel" style="width: 500px;" rows="8" ><?php echo $currentOwner->serviceLevel; ?></textarea></td>
            </tr>
            <tr>
                <td></td><td><input type="submit" name="btnSubmit" value="Update" /></td>
            </tr>
        </table>
        </form>
        </div>
    </p>
    </div>
<?php
}
?>
    <div class="module" id="createOwner" style="display: none;">
        <div class="sectionHeader" >
            <strong>Create Owner</strong>
            <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
        </div>
	<p>
		<div class="form" align="center">
		<form method="post" action="?mode=insert" id="createOwner">
		<table width="80%" class="formTable">
                    <colgroup align="left" class="tblfirstRow"></colgroup>
			<tr>
				<td><label for="name" >Name:</label></td><td><input name="name" type="text" id="name" value="" /></td>
			</tr>
			<tr>
				<td><label for="contactname" >Contact Name:</label></td><td><input name="contactname" type="text" id="contactname" value="" /></td>
			</tr>
			<tr>
				<td><label for="phone" >Phone:</label></td><td><input name="phone" type="text" id="phone" value="" /></td>
			</tr>
			<tr>
				<td><label for="afterHoursPhone" >After Hour Phone:</label></td><td><input name="afterHoursPhone" type="text" id="afterHoursPhone" value="" /></td>
			</tr>
			<tr>
				<td><label for="email" >Email:</label></td><td><input name="email" type="text" id="email" value="" /></td>
			</tr>
			<tr>
				<td><label for="fax" >Fax:</label></td><td><input name="fax" type="text" id="fax" value="" /></td>
			</tr>
			<tr>
				<td><label for="mobile" >Mobile:</label></td><td><input name="mobile" type="text" id="mobile" value="" /></td>
			</tr>
			<tr>
				<td><label for="serviceLevel" >Service Level:</label></td><td><textarea name="serviceLevel" id="serviceLevel" style="width: 500px;" rows="7" ></textarea></td>
			</tr>
			<tr>
				<td></td><td><input type="submit" name="btnSubmit" value="Insert" /></td>
			</tr>
		</table>
		</form>
		</div>
	</p>
    </div>

	<div class="module" id="ownerExplanation">
	<strong>Owners</strong>
	<p>
		Owners are associated with all items you would consider assets & are mainly used for reporting.
		<br/>Please note at the moment owners are created here and not within the form where assets are inputted. It is best to create these entries as early as possible.
	<div id="ownerList">
		<table class="dataTable">
		<colgroup align="left" class="tblfirstRow"></colgroup>
		<thead> 
		<tr> 
			<th scope="col">Name</th>
			<th scope="col">Contact Name</th> 
			<th scope="col">Phone</th> 
			<th scope="col">After Hours</th> 
			<th scope="col">Email</th> 
			<th scope="col" width="100" align="center">Options</th>
		</tr> 
		</thead> 
		<tbody>
<?php
	// Loop Through Owners
	foreach($owners->getAll() as $owner) 
	{ 
?>
                    <tr>
                        <td><b><?php echo $owner->name; ?></b></td>
                        <td><?php echo $owner->contactname; ?></td>
                        <td><?php echo $owner->phone; ?></td>
                        <td><?php echo $owner->afterHoursPhone; ?></td>
                        <td><?php echo $owner->email; ?></td>
                        <td align="center">
                            <a href="owners.php?mode=edit&id=<?php echo $owner->ownerID; ?>">Edit</a>
                            <?php if($owner->ownerID!=1) { ?>
                             | <a href="#" onclick="if (confirm('Are you sure you want to delete this owner?')) { location.href = 'owners.php?action=owner&mode=delete&id=<?php echo $owner->ownerID; ?>'; } ">Delete</a>
                             <?php }?>
                        </td>
                    </tr>
<?php   } ?>
                </tbody>
                </table>
                </div>
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
		<div class="module" id="createOwner" align="center">
			<a href="#createOwner" class="newDOMWindow">Create a new Owner</a>
		</div>
	</div>
</div>
<?php
include "theme/" . $theme . "/base.php";
?>