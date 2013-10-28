<?php
session_start();
$selectedPage="configure";
include "class/db.class.php";

// Verify a ldap connection
if(isset($_POST['action']) && $_POST['action'] =='verifyLDAP')
{
	$return[0]=0;
		
	if(!isset($_POST['ldaps']))
		$_POST['ldaps']=0;
	else
		$_POST['ldaps']=1;
		
	// get connection values from DB
	$basedn		=$_POST['LDAPBaseDN'];
	$prefix		=$_POST['LDAPPrefix'];
        $postfix        =$_POST['LDAPPostfix'];
	$requiredGroup	=$_POST['LDAPRequiredGroup'];
	$ldap_server	=$_POST['LDAPServer'];

   // connect but detect ldap/s
	if($_POST['ldaps'])
		$adcon = ldap_connect("ldaps://".$ldap_server);
	else
		$adcon = ldap_connect("ldap://".$ldap_server);

        // check if these need to be enabled / disabled for AD
        // [FOLLOWUP]
	ldap_set_option($adcon, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($adcon, LDAP_OPT_REFERRALS, 0);

	// Bind to the directory server.
	$username=$_POST['ldapTestUser'];
	$password=$_POST['ldapTestPass'];

        // with a prefix and baseDN set we bind as CN=<user>,ou=bla,dc=blah......
        // without we just bind as <user><postfix>, eg user@test.com
        if($prefix && $basedn)
            $bind_rdn=$prefix."=".$username.",".$basedn;
        else
            $bind_rdn=$username.$postfix;

        //$bind_rdn="dan@ansto.gov.au";
        $boundcon = @ldap_bind($adcon,$bind_rdn,$password);

        $field = $_POST['LDAPRequiredField'];
	// Perform the search and grab the users groups
	$searchres = @ldap_search($adcon,$basedn,"($field=$username)",array("memberof"));
	$entries = @ldap_get_entries($adcon, $searchres);

	if($entries)
		$return[0]=1;

	// loop over the returned groups and find our required
	if($entries[0]['memberof'])
	foreach($entries[0]['memberof'] as $group)
	{
		if(preg_match('/^CN='.$requiredGroup.'/i',$group))
			$return[0]=$group;
	}
		echo json_encode(array($return));
}


// Default action
else
{
    $globalTopic="System Management";
    include "theme/" . $theme . "/top.php";
    $config = new config;
?>

<div id="main">
<?php
if (isset($_POST['btnSubmit']))
{
	if(!isset($_POST['chkEnableLDAP']))
		$_POST['chkEnableLDAP']=0;
	else
		$_POST['chkEnableLDAP']=1;
		
	if(!isset($_POST['ldaps']))
		$_POST['ldaps']=0;
	else
		$_POST['ldaps']=1;
		
	$config = new config;
	$config->setItem('ldap_auth',$_POST['chkEnableLDAP']);
	$config->setItem('ldaps_enabled',$_POST['ldaps']);
	$config->setItem('ldap_server',$_POST['LDAPServer']);
	$config->setItem('ldap_prefix',$_POST['LDAPPrefix']);
        $config->setItem('ldap_postfix',$_POST['LDAPPostfix']);
	$config->setItem('ldap_basedn',$_POST['LDAPBaseDN']);
        $config->setItem('ldap_field',$_POST['LDAPRequiredField']);
	$config->setItem('ldap_group',$_POST['LDAPRequiredGroup']);
	$config->setItem('webaddress',$_POST['txtWebAddress']);

	if(!isset($_POST['chkEnableUploads']))
		$_POST['chkEnableUploads']=0;
	else
		$_POST['chkEnableUploads']=1;
        $config->setItem('attachments_enabled',$_POST['chkEnableUploads']);

        if($_POST['upload_path'][strlen($_POST['upload_path'])-1]!="/")
            $_POST['upload_path']=$_POST['upload_path']."/";

        $config->setItem('attachment_path',$_POST['upload_path']);
        $config->setItem('attachment_maxUpload',$_POST['upload_size']);
}
?>
	<div id="left">
	
		<div class="module" id="configuration"> 
			<strong>System Settings</strong>
		
	<script type="text/javascript"> 
	$(document).ready(function()
	{
		url='http://racksmith.net/api/version.php';
		jQuery.getJSON(url+"?callback=?", function(data) {
		current=$('#version').html();
		if(current == data.version)
		{
			$('#upgrade').html("<img src='images/list_ok.png' alt='Up to date' /> Latest Release");
			$('#upgrade').addClass('statusok');
		} else {
			$('#upgrade').html('<a href="http://racksmith.net/download/?upgrade=1" target="_new" ><img src="images/list_notice.png" alt="update available" />' + data.version + ' is availabe</a>'); 
			$('#upgrade').addClass('statusnotice');
		}
		});
	 });
	
	
	//$('#runTest').live('click',function()
        function runLDAPTest()
	{
		var data = $('#systemForm').serialize();
		$.post("system.php","action=verifyLDAP&"+data,
                function(returnData) {
			if(returnData[0]!="0")
			{
				$('#ldapTestStatus').html('<font color="green" >Authenticated Correctly</font>');
				if(returnData[0]!="1")
					$('#ldapTestStatus font').append(' & found valid group');
                                else
                                        $('#ldapTestStatus font').after(" but couldn't find group");
			}
			else
				$('#ldapTestStatus').html('<font color="red" >Failed to authenticate</font>');
		},"json");
	};
	</script> 
	<p>
			
		<script type="text/javascript">
			function LDAPCheck()
			{
                            if (document.getElementById("chkEnableLDAP").checked == 1)
				$('#ldap_form').slideDown();
                            else
				$('#ldap_form').slideUp();
			}
			function uploadsCheck()
			{
                            if (document.getElementById("chkEnableUploads").checked == 1)
				$('#uploads_form').slideDown();
                            else
				$('#uploads_form').slideUp();
			}
		</script>
			<form action="system.php" method="POST" id='systemForm'>
				<strong>LDAP Authentication</strong>

                <div class="form_container" id="system" style="clear:both;width:500px;">
                 <table class="formTable">
                     <colgroup align="left" class="tblfirstRow"></colgroup>
                    	<tbody>
                            <tr><td width='160px' >
                                <strong>Enable LDAP:</strong>
                                </td><td>
                                <input type="checkbox" name="chkEnableLDAP" id="chkEnableLDAP" onclick="LDAPCheck();" <?php if($config->returnItem('ldap_auth')) echo "checked=true"; ?>(/>
                            </td></tr>
			</tbody>
                 </table>
                 <table class="formTable" id='ldap_form' <?php if(!$config->returnItem('ldap_auth')) { ?>style='display:none;<?php } ?>'>
                     <colgroup align="left" class="tblfirstRow"></colgroup>
                    	<tbody>
                        <tr><td colspan="2" ><i>With LDAP authentication enabled your default root account continue to authenticate against the local database.</i></td></tr>
			<tr>
                            <td width='160px'>LDAPs:</td>
                            <td>
				<input type="checkbox" name="ldaps" id="ldaps" <?php if($config->returnItem('ldaps_enabled')) echo "checked=true";?>(/>
				</td></tr>
                            <tr><td>LDAP Server:</td><td>
				<input type="text" name="LDAPServer" id="LDAPServer" value="<?php echo $config->returnItem('ldap_server'); ?>" "/>
				</td></tr>
                            <tr><td>LDAP Bind Prefix:</td><td>
				<input type="text" name="LDAPPrefix" id="LDAPPrefix" value="<?php echo $config->returnItem('ldap_prefix'); ?>" "/>
                                </td></tr>
                            <tr><td>LDAP Bind PostFix:</td><td>
				<input type="text" name="LDAPPostfix" id="LDAPPostfix" value="<?php echo $config->returnItem('ldap_postfix'); ?>" "/>
                                </td></tr>
                            <tr><td>LDAP Base DN:</td><td>
				<input type="text" size='45' name="LDAPBaseDN" id="LDAPBaseDN" value="<?php echo $config->returnItem('ldap_basedn'); ?>" "/>
                                </td></tr>
                            <tr><td>LDAP Required Field:</td>
                                <td><input type="text" size="30" name="LDAPRequiredField" if="LDAPRequiredField" value="<?php echo $config->returnItem('ldap_field'); ?>" />
                            <tr><td>LDAP Required Group:</td>
                                <td>
				<input type="text" size='30' name="LDAPRequiredGroup" id="LDAPRequiredGroup" value="<?php echo $config->returnItem('ldap_group'); ?>" />
				</td></tr>
				<tr><td colspan='2' ><strong>Test Connection</strong> - <span id='ldapTestStatus'><a href='#' onclick="$('#ldapHelp').toggle();" >?</a></span></td></tr>
				<tr><td colspan='2' style='display:none' id='ldapHelp' ><font size='1'>--ldap help item 1--</font></td></tr>
				<tr><td>Username</td><td><input type="text" name="ldapTestUser" id="ldapTestUser" /></input></td></tr>
				<tr><td>Password</td><td><input type="password" name="ldapTestPass" id="ldapTestPass"/></td></tr>
				<tr><td colspan='2' ><a onclick="runLDAPTest();">Run Test</a></td></tr>
			</tbody>
		</table>	
		</div>	
                    </p>
				<strong>Installation Address</strong>
				<p>The web address where your copy of RackSmith is installed.</p>
				<div class="form_container"  style="clear:both;width:500px;">             
					<table class="formTable">
                                            <colgroup align="left" class="tblfirstRow"></colgroup>
						<tbody>
							<tr>
							<td>Address:</td>
							<td>
                                                        	<input type="text" name="txtWebAddress" id="txtWebAddress" value="<?php echo $config->returnItem('webaddress');?>" size='55'/>
							</td>
							</tr>
						</tbody>
					</table>	
				</div>
                                <br/>
                <strong>Uploads</strong>
                <div class="form_container"  style="clear:both;width:500px;">
                 <table class="formTable">
                     <colgroup align="left" class="tblfirstRow"></colgroup>
                    	<tbody>
                            <tr><td width='160px' >
                                <strong>Enabled:</strong>
                                </td><td>
                                <input type="checkbox" name="chkEnableUploads" id="chkEnableUploads" onclick="uploadsCheck();" <?php if($config->returnItem('attachments_enabled')) echo "checked=true"; ?>(/>
                            </td></tr>
			</tbody>
                 </table>
                 <table class="formTable" id='uploads_form' <?php if(!$config->returnItem('attachments_enabled')) { ?>style='display:none;<?php } ?>'>
                     <colgroup align="left" class="tblfirstRow"></colgroup>
                    	<tbody>
                        <tr><td colspan="2" ><i>Enabling/Disabling of uploads will take effect on next logon</i></td></tr>
			<tr>
                            <td width='160px' style='border-top: 0px;'>Upload Path:</td>
                            <td style='border-top: 0px;'>
				<input type="text" size="50" name="upload_path" id="upload_path" value="<?php echo $config->returnItem('attachment_path');?>"/>
                            </td>
                        </tr>
                        <tr><td colspan="2" ><i>The upload path is relative to the RackSmith install location or /.<br/>Please maintain a trailing in / on this folder</i></td></tr>
			<tr>
                            <td width='160px' style='border-top: 0px;'>Max File Size:</td>
                            <td style='border-top: 0px;'>
				<input type="text" size="2" name="upload_size" id="upload_size" value="<?php echo $config->returnItem('attachment_maxUpload');?>"/> MB
                            </td>
                        </tr>

                 </table>
                                </div>
<hr/>
				<input type="submit" name="btnSubmit" value="Save" />
			</form>
		</div>
            
		</p>
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
            if(file_exists('class/build.inc.php'))
            {
                include "class/build.inc.php";
            ?>
            <div class='module' >
                <strong>Version</strong>
                <ul id="systemVersion">
                    <li>Build: <span id='version'><i><?php echo $buildNumber; if(!($buildStream=='prod'||!$buildStream)) { echo "_$buildStream"; }  ?></i></span></li>
                    <li class='serverResponce'><font color='#cccccc' ><i>checking for upgrade...</i></font></li>
                </ul>

                <script type="text/javascript">
                $(document).ready(function()
                {
                    url='http://racksmith.net/api/version.php';
                    $.getJSON(url+"?stream=<?php echo $buildStream; ?>&build=<?php echo $buildNumber;?>&callback=?", function(data)
                    {
                        if(<?php echo $buildNumber;?> == data.version)
                        {
                            $('.serverResponce').html("<i>Up to date</i>");
                            $('.serverResponce').addClass('statusok');
                        }
                        else
                        {
                            if(data.updateURL)
                            {
                                var url=data.updateURL;
                            }
                            else
                            {
                                var url="http://racksmith.net/download/?upgrade=1";
                            }
                            
                            $('.serverResponce').html('<a href="'+url+'" target="_new" >Upgrade Available</a>');
                            
                            if(data.extra || data.releaseDate)
                            {
                                var versionBox="<div class='upgradeNote' >";
                                if(data.releaseDate)
                                {
                                   // var newDate=new Date(data.releaseDate);
                                   // alert(newDate.toString("Ymd"));
                                   versionBox=versionBox+"Released: "+data.releaseDate+"<br/>"; 
                                }
                                if(data.extra)
                                {
                                    versionBox=versionBox+data.extra+"</div>";
                                }
                            }
                                $('.serverResponce').append(versionBox);
                            $('.serverResponce').addClass('statusnotice');
                        }

                        $('#newTemplates').html("no new templates available");
                    });
                 });
                </script>
            </div>
            <?php
            }
            ?>
	</div>
</div>
<?php
	include "theme/" . $theme . "/base.php";	
}
?>