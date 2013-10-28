<?php
session_start();
$selectedPage="configure";
include "class/db.class.php";


if(!isset($_GET['action']))
{
	$globalTopic="System Configuration";
	include "theme/" . $theme . "/top.php";
?>
<div id="main"> 
    <div id="left">
        <div class="module" id="configuration">
            <strong>Management</strong>
            <p>
                The System Management console lets you manage user access and system settings.
            </p>
            <br/>
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
    </div>
</div><?php
	include "theme/" . $theme . "/base.php";
}


else if($_GET['action']=='accounts')
{
	// If the user is trying to edit root and they arn't root get out of here
	if(isset($_GET['id']))
		if($_GET['id']==1 && $_SESSION['userid']!=1)
			header("Location: management.php?action=accounts&error=protected_root");
			
	if (isset($_GET['mode']) && $_GET['mode'] == 'delete')
	{
		$users = new users;
		$users->invalidateAccount($_GET['id']);
		header("Location: management.php?action=accounts&notice=deleted");
	}  
	else if (isset($_GET['mode']) && $_GET['mode'] == 'edit')
	{
	  	$newUser = new user($_POST['id']);
		//$newUser->UserName = $_POST['username'];
		$newUser->Email = $_POST['email'];
		$newUser->Phone = $_POST['phone'];

		// if ther passwords have changed
		if(isset($_POST['password1']) && $_POST['password1'] == $_POST['password2'] && $_POST['password1'])
			$newUser->Password = md5($_POST['password1']);

                if($_POST['measurement']=="imperial")
                    $newUser->metric=0;
                else
                    $newUser->metric=1;

                if($_POST['dateformat']=="dmy")
                    $newUser->dateformat="d-m-Y";
                else
                    $newUser->dateformat="m-d-Y";

                // if the user is currently logged in we can adjust their dateformat in this session
                if($newUser->userID==$_SESSION['userid'])
                    $_SESSION['dateFormat']=$newUser->dateformat;

		$users = new users;
		if($users->update($newUser))
                    echo json_encode(array("created"));
	  }


	  else if (isset($_GET['mode']) && $_GET['mode'] == 'new')
	  {
                $errors=array();
		$newUser = new user;
                $users = new users;

                // Check to see if the username is already in use
                $duplicateName = $users->searchUser($_POST['username'],'username',1);
                if($duplicateName)
                    $errors[]="username";
                else
                    $newUser->UserName = $_POST['username'];

                if($_POST['password']!=$_POST['password2']||$_POST['password']=='')
                {
                    $errors[]="password";
                    $errors[]="password2";
                }
                else
                    $newUser->Password = md5($_POST['password']);
		$newUser->external=0;
		$newUser->Email = $_POST['email'];
		$newUser->Phone = $_POST['phone'];
                if($_POST['measurement']=="imperial")
                    $newUser->metric=0;
                else
                    $newUser->metric=1;

                if($_POST['dateformat']=="dmy")
                    $newUser->dateformat="d-m-Y";
                else
                    $newUser->dateformat="m-d-Y";
		
                if(empty($errors))
                {
                    $userID=$users->insert($newUser);
                    if($userID)
                        echo json_encode(array("created",$userID,$newUser->UserName,$newUser->Email));
                    else
                        echo json_encode(array("error" => "Unable to create"));
                }
                 else
                    echo json_encode(array("error" => $errors));
	  }
          else
          {

	$globalTopic="Manage Accounts";
	include "theme/" . $theme . "/top.php";
	$users = new users;
	// FORM TO CREATE A NEW USER
	?>
        <script type="text/javascript">
            $(document).ready(function()
            {
                $(".editUser").live('click',function() {
                    url=$(this).attr("href");
                    $.openDOMWindow({ fixedWindowY: 50, borderColor: '#3b4c50', borderSize: 2,windowPadding:0, width:'750',height: '350', overlayOpacity: '30', windowSource:'ajax', windowSourceURL: url});
                    return false;
                });
            });
        </script>

	<div id="main"> 
		<div id="left">

	<?php
	
	if(isset($_GET['error']))
	if($_GET['error']=="protected_root")
		echo "<div class=\"module\" id=\"notice\" ><strong>ERROR! You don't have access to edit root</strong></div>";
		
	if(isset($_GET['notice']))
	{
		if($_GET['notice']=="created")
			echo "<div class=\"module\" id=\"complete\" ><strong>Account Created</strong></div>";
		if($_GET['notice']=="edited")
			echo "<div class=\"module\" id=\"complete\" ><strong>Account Updated</strong></div>";
		if($_GET['notice']=="deleted")
			echo "<div class=\"module\" id=\"complete\" ><strong>Account Deleted</strong></div>";
	}
	
//	if (isset($_GET['mode']) && $_GET['mode'] == 'edit') { $currentUser = new user($_GET['id']); 
?>

	<div class="module" id="addAccount" style="display:none;">
            <div class="sectionHeader" >
                <strong>Add Account</strong>
                <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
            </div>
            <p>
		<div class="form" align="center">
		<form action="management.php?action=accounts&mode=new" id="addUser">
                    <script type="text/javascript">
                    function addUser(returned)
                    {
                        $(".success").show();
                        setTimeout("$('.closeDOMWindow').click();$('.success').hide(); ",1000);
                        $("#menus").html("");

                        $("#existingAccounts table").append("<tr><td><b>"+returned[2]+"</b></td><td>"+returned[3]+"</td><td align='center'> \
                        <a href=\"management.php?action=editaccount&id="+returned[1]+"\" class=\"editUser\"  ><span>Edit</span></a></td>    </tr>");
                    }
                    </script>
		<table class="formTable">
                    <colgroup align="left" class="tblfirstRow"></colgroup>
			<tr>
				<td width="140px"><label for="username" >Username:</label></td><td><input name="username" type="text" id="username" value="" /></td>
			</tr>
			<tr>
				<td><label for="password" >Password:</label></td><td><input name="password" type="password" style="width:120px;" id="password" value="" /></td>
			</tr>
			<tr>
				<td><label for="password2" >Confirm Password:</label></td><td><input name="password2" type="password" style="width:120px;" id="password2" value="" /></td>
			</tr>
			<tr>
				<td><label for="email" >Email:</label></td><td><input name="email" type="text" id="email" value="" /></td>
			</tr>
			<tr>
				<td><label for="phone" >Phone:</label></td><td><input name="phone" type="text" id="phone" value="" /></td>
			</tr>
			<tr>
                            <td valign="top">Date Format:</td><td>
                                <input name="dateformat" type="radio" id="ddmmyyyy" value="ddmmyyyy" checked/> <label for="ddmmyyyy" >DD-MM-YYYY</label><br/>
                                <input name="dateformat" type="radio" id="mmddyyyy" value="mmddyyyy" /> <label for="mmddyyyy" >MM-DD-YYYY</label>
                            </td>
			</tr>
			<tr>
                            <td valign="top">Measurements:</td><td>
                                <font size="x-small" >
                                <input name="measurement" type="radio" id="metric" value="metric" checked/> <label for="metric" >Metric (mm,cm,m)</label><br/>
                                <input name="measurement" type="radio" id="imperial" value="imperial"/> <label for="imperial" >Imperial (in,ft)</label>
                                </font>
                            </td>
			</tr>
			<tr>
                            <td></td><td><input type="submit" name="btnSubmit" value="Update" class="postForm JSONsubmitForm"  /></td>
			</tr>
                        <tr class="success" style="display:none;"><td colspan="2">
                            Account Created
                            </td>
                        </tr>
			<?php 
                        $config = new config;
                        $ldap = $config->returnItem('ldap_auth');
                        if($ldap) { ?>
			<tr>
			<td colspan='2' ><font size='2' >Please note: <i>new accounts are created locally and not within LDAP/Active Directory.
			Please contact your system administrator if you'd like an account for this..</i></font></td>
			</tr>
			<?php } ?>
		</table>
		</form>
		</div>
            </p>
	</div>
	<?php if($ldap) { ?>
		<div class='module notice' >
		You're currently in LDAP mode, the only local account which is active in this mode is the root account.<br/>
		</div>
	<?php } ?>
	<div class="module" id="currentUsers"> 
	<strong>Existing Accounts</strong>
	<p>
		<div id="existingAccounts">
		<table class="dataTable">
                    <thead>
                    <tr>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col" width="100" align="center">Options</th>
                    </tr>
                    </thead>
			
			<tbody>
<?php
	$userData = $users->getAll();
	if($userData)
	foreach($userData as $user)
	{
            // ldap users and deleted accounts (no password) should not be displayed
            if($user->external!='0' || !$user->Password)
                continue;
			
	if(strtolower($_SESSION['username']) == strtolower($user->UserName) && isset($_SESSION['external']) && $_SESSION['external']!='ldap')
            $temp="class=\"selected\"";
	else
            unset($temp);
		//here is where you would add it to an array
		?>

	<?php

	echo "		<tr "; if(isset($temp)) echo $temp; echo ">
			<td><b>".$user->UserName." </b></td>
			<td>".$user->Email." </td>
			<td align=\"center\">";
				// If we are printing the root user, only show the edit link for the root user
                        if($user->UserName=="root" && $_SESSION['username']!="root") { } else
                            echo "<a href=\"management.php?action=editaccount&id=".$user->userID."\" class=\"editUser\"  ><span>Edit</span></a>";
                                    
                        // Don't display the delete link for the root user
                        if($user->UserName!="root")
                            echo " | <a href=\"#\" onclick=\"if (confirm('Are you sure you want to delete this user?')) { location.href ='management.php?action=accounts&mode=delete&id=".$user->userID."'; } \"><span>Delete</span></a>";
			echo "</td>
		</tr>";
	
	}

?>
		</tbody>
		</table>		
		</div>
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
		
		<div class="module" align="center" style='margin-bottom: 6px;'>
                    <strong><a href="#addAccount" class="newDOMWindow">Add a new user</a></strong>
		</div> 
		<div class="module" align="center"style='margin-top: 6px;'>
                    <strong><a href="system.php#LDAP" >LDAP Authentication</a></strong>
		</div> 
		<?php if($ldap) { ?>
                    <div class="module" id="helpBox"> 
                        <strong>Notice</strong>
                        <p>
                        The root user account remains active even while in LDAP mode.
                        </p>
                    </div>
                <?php } ?>

    </div>

<?php
	include "theme/" . $theme . "/base.php";
}
}

// show the edit form for the set user
else if($_GET['action']=='editaccount' && is_numeric($_GET['id']))
{
    $user = new user($_GET['id']);
    if(@!$user->UserName)
    {
        echo "<strong>Error</strong><br/>Unable to find user to edit";
        exit(0);
    }

?>    
        <div class="sectionHeader" >
            <strong>Edit User Account</strong>
            <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
        </div>
	<p>
            <div class="form" align="center">
		<form action="management.php?action=accounts&mode=edit" id="editUser">
                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
                    <script type="text/javascript">
                    	function editUser()
                        {
                            $(".success").show();
                            setTimeout("$('.closeDOMWindow').click();$('.success').hide(); ",1000);
                            $("#menus").html("");
                        }
                        </script>
		<input type="hidden" name="uid" value="" id="uid" />
		<table width="80%" class="formTable">
                    <colgroup align="left" class="tblfirstRow"></colgroup>
			<tr>
                            <td><label for="username" >Username:</label></td><td><input name="username" readonly="true" type="text" id="username" value="<?php echo $user->UserName; ?>" /></td>
			</tr>
			<tr>
                            <td><label for="password" >Password:</label></td><td><input name="password1" type="password" id="epassword1"
/><span>* Leave blank to keep existing password</span></td>
			</tr>
			<tr>
                            <td><label for="password" >Confirm Password:</label></td><td><input name="password2" type="password" id="epassword2" /></td>
			</tr>
			<tr>
                            <td><label for="email" >Email:</label></td><td><input name="email" type="text" value="<?php echo $user->Email; ?>" /></td>
			</tr>
			<tr>
                            <td><label for="phone" >Phone:</label></td><td><input name="phone" type="text" value="<?php echo $user->Phone; ?>" /></td>
			</tr>
			<tr>
                            <td valign="top">Date Format:</td><td>
                                <input name="dateformat" type="radio" id="dmy" value="dmy" <?php if($user->dateformat=="d-m-Y") { echo "checked"; } ?>/> <label for="dmy" >DD-MM-YYYY</label><br/>
                                <input name="dateformat" type="radio" id="mdy" value="mdy" <?php if($user->dateformat=="m-d-Y") { echo "checked"; } ?>/> <label for="mdy" >MM-DD-YYYY</label>
                            </td>
			</tr>
			<tr>
                            <td valign="top">Measurements:</td><td>
                                <font size="x-small" >
                                <input name="measurement" type="radio" id="metrica" value="metric" <?php if($user->metric) { echo "checked"; } ?>/> <label for="metrica" >Metric (mm,cm,m)</label><br/>
                                <input name="measurement" type="radio" id="imperiala" value="imperial" <?php if(!$user->metric) { echo "checked"; } ?>/> <label for="imperiala" >Imperial (in,ft)</label>
                                </font>
                            </td>
			</tr>
			<tr>
                            <td></td><td><input type="submit" name="btnSubmit" value="Update" class="postForm JSONsubmitForm"  /></td>
			</tr>
                        <tr class="success" style="display:none;"><td colspan="2">
                            User Updated
                            </td>
                        </tr>
		</table>
                
		</form>
		</div>
	</p>
<?php
} ?>