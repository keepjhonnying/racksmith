<?php
session_start();
include "class/db.class.php";
$selectedPage="configure";
$logs = new logs;

// When saving a group of logs to an event
if(isset($_GET['action']) && $_GET['action'] == "save_selection" && $_POST)
{
	// these are the filter items associated
	if(isset($_GET['userID']) && is_numeric($_GET['userID']))
        	$userID=$_GET['userID'];
	else
	        $userID='0';
	if(isset($_GET['hour']) && is_numeric($_GET['hour']))
	        $hour=$_GET['hour'];
	else
	        $hour='0';

	// retrieve a name if one was set
	$selection_title = preg_replace("/([^a-zA-z0-9- _])/",'',$_POST['selection_name']);
	unset($_POST['selection_name']);
	if(!$selection_title) 
		$selection_title="Default Selection";

	// loop over all the selected values and move them into an array
	$selectedLogs=array();
	foreach($_POST as $selection=>$val)
	{
		if(is_numeric($selection))
			$selectedLogs[]=$selection;
	}

	// pass of Woop!
	if($logs->saveEvent($selection_title,$selectedLogs))
		header("Location: logs.php?selection=saved&hour=$hour&userID=$userID");
	else
		header("Location: logs.php?selection=error&hour=$hour&userID=$userID");
	exit();
}


// delete a saved event
else if(isset($_GET['action']) && $_GET['action'] == "deleteSavedEvent" && is_numeric($_GET['eventID']))
{	
	if($logs->deleteSavedEvent($_GET['eventID']))
		header("Location: logs.php?event=deleteSaved");
	else
		header("Location: logs.php?error=unableToDelete");
}

else
{
	$globalTopic="Logs &amp; Saved Events";
	include "theme/" . $theme . "/top.php";

	// retrieve and check the values used for filtering
	if(isset($_GET['userID']) && is_numeric($_GET['userID']))
		$userID=$_GET['userID'];
	else
		$userID='0';
		
	if(isset($_GET['hour']) && is_numeric($_GET['hour']))
		$hour=$_GET['hour'];	
	else
		$hour='0';
		
	if(isset($_GET['search']))
		$search=htmlspecialchars($_GET['search']);	
	else
		$search='0';

	
?>
<script language="javascript" type="text/javascript">
$(document).ready(function()
{
	$("#statusLog tbody tr td:not('#nonselect')").click(function(event) { 	
		$(this).closest("tr").toggleClass("selected"); 

		if( $(this).closest("tr").find(':checkbox').attr('checked') == true)
			$(this).closest("tr").find(':checkbox').attr('checked',false);
		else
			$(this).closest("tr").find(':checkbox').attr('checked',true);
	});
	
	$("#statusLog tbody tr td input:checkbox").click(function(event) { 	
		$(this).closest("tr").toggleClass("selected"); 
	});

	
	$(":input[name=selection_name]").click(function () 
	{
		if(this.value == this.defaultValue)
			$(this).attr("value","");
	});
});
</script>
<div id="main"> 
	<div id="left">
            <div class="module" id="logs">

            <strong>Logs</strong>

            <div id="statusLog">
			
                <table class="dataTable">
                    <colgroup align="left" class="tblfirstRow"></colgroup>
                    <thead>
                    <tr>
                        <th scope="col" align="center" style='padding:2px 0px 0px;'>
                            <input type="checkbox" onclick="var checked=$('input:checkbox').attr('checked'); $('input:checkbox').attr('checked',checked);" />
                        </th>
                        <th scope="col" width="15%" >Date</th>
                        <th scope="col" width="77%" >Event</th>
                        <th scope="col" width="8%" >User</th>
                    </tr>
                    </thead>
                    <form action="logs.php?action=save_selection&hour=<?php echo $hour;?>&userID=<?php echo $userID;?>" method="POST">
                    <tbody>
<?php
$results=0;
$no_results_flag=0;	// set if any filters return null results

// retrieve a saved event
if(isset($_GET['savedEvent']) && is_numeric($_GET['savedEvent']))
{
    $results = $logs->getEvent($_GET['savedEvent']);
    if(!$results) $no_results_flag=1;
}

if(isset($search) && $search)
{
    $results = $logs->filterByTerm($search);
    if(!$results) $no_results_flag=1;
}


// Determine which filters to apply
if(isset($hour) && $hour)
{
    $results = $logs->filterByHour($hour);
    if(!$results) $no_results_flag=1;
}
if(isset($userID) && $userID)
{
    $results = $logs->filterByUser($userID);
    if(!$results) $no_results_flag=1;
}

// get the variables associated with paging
if(isset($_GET['offset']) && is_numeric($_GET['offset']))
    $offset=(int)$_GET['offset'];
else
    $offset=0;
	
if(isset($_GET['limit']) && is_numeric($_GET['limit']))
    $limit=(int)$_GET['limit'];
else
    $limit=30;

if(!$results)
    $results = $logs->getAll($limit,$offset);

// If any of the filters returned no results we don't want to loop
if($no_results_flag)
	echo "<tr><td colspan=\"4\" ><i><font color=\"#b33838\" >No events were found for your search</font></i></td></tr>";
else
{
        $yesterday=strtotime("-1 day");
	foreach($results as $log) 
	{
           // look for events within the last day
           if($log->timestamp>$yesterday)
               $highlight=' class="selected"';
           else
               $highlight='';
           
            echo "	<tr ".$highlight.">
		<td align=\"center\" id='nonselect' ><input type=\"checkbox\" name=\"".$log->logID."\"  /></td>
		<td align=\"center\">".$log->eventTime."</td>
		<td>".$log->event."</td>
		<td align=\"center\"><a href=\"logs.php?userID=".$log->userID."\">".$log->user()->UserName."</a></td>
	</tr>\n";
	}
        
	if(!$results)
            echo "<tr><td colspan='4' ><i>No logged events</i></td></tr>";

	echo '	</tbody>
                    <tfoot>
                    <tr>';
        if(!isset($_GET['savedEvent']))
        {
            echo '<td colspan="4" class="tblfirstRow"><input size="35" name="selection_name" value="Event Title" /><input type="submit" value="Save Selected Events" /></a>';
            if(isset($_GET['selection']) && $_GET['selection']=="saved")
                    echo "<br/><strong>Selection Saved</strong>
            </td>";
        }
        else
        {
            echo '<td colspan="4" class="tblfirstRow" >Viewing Saved Event: <strong>'.$logs->getEventName($_GET['savedEvent']).'</strong><br/>
                    '."<a href=\"#\" rel=\"nofollow\" onclick=\"if (confirm('Are you sure you want to delete this saved event?')) { location.href = 'logs.php?action=deleteSavedEvent&eventID=".$_GET['savedEvent']
                    ."'; } \">[delete]</a>
            </td>";
        }
?>
            </tr>
            <tr><td colspan='4'>
                <div style='float:left;'><?php
                    if(($offset-$limit) >= 0)
                    { ?>
                        <a href='?limit=<?php echo $limit.'&offset='.($offset-$limit); ?>' >&lt;&lt; Back </a></div>
                    <?php } ?>
                    </a></div>
                <div style='float:right;'><?php
                    if(count($results) == $limit)
                    { ?>
                        <a href='?limit=<?php echo $limit.'&offset='.($limit+$offset); ?>' >Forward >> </a></div>
                    <?php } ?>
            </td></tr>
            </tfoot>
<?php
}
?>
		</table>
		</form>
		</div>
		</div>
	</div>

    <div id="right">
        <div class="module" id="menuBox">
            <strong>Devices &amp; Templates</strong>
            <p>
                <ul>
                    <li><a href="manageTemplates.php">Create a new Template</a></li>
                    <li><a href="templates.php">Templates List</a></li>
                    <li><a href="templateMover.php">Import/Export Templates</a></li>
                </ul>
            </p>
        </div>

        <div class="module" id="menuBox">
            <strong>System</strong>
            <p>
            <ul>
                <a href="cables.php" ><li>Cable Types</li></a>
                <a href="metadata.php" ><li>Device Information</li></a>
                <a href="owners.php" ><li>Equipment Owners</li></a>
                <a href="management.php?action=accounts" ><li>User Accounts</li></a>
                <a href="system.php" ><li>System</li></a>
                <a href="logs.php" ><li>Logs</li></a>
                <a href="templateMover.php" ><li>Import / Export Templates</li></a>
            </ul>
                </p>
        </div>
		<div class="module" id="logFilter"> 
		<strong>Filter Results</strong>		
		</p>
			<form action="logs.php" method="get">
			<table class="formTable">
                            <colgroup align="left" class="tblfirstRow"></colgroup>
<?php
			
			// Don't allow timeframe selection for saved events
			// the class doesn't support this anyway (time queries are detected within the DB)
			if(!isset($_GET['savedEvent']))
			{
			echo '<tr><td>Timeframe:</td>
			<td><select name="hour" >
				<option value="" ></option>
				<option value="1" '; if($hour==1) { echo "SELECTED"; } echo '>1 hour</option>
				<option value="12" '; if($hour==12) { echo "SELECTED"; } echo '>12 hours</option>
				<option value="24" '; if($hour==24) { echo "SELECTED"; } echo '>1 day</option>
				<option value="48" '; if($hour==48) { echo "SELECTED"; } echo '>2 days</option>
				<option value="168" '; if($hour==168) { echo "SELECTED"; } echo '>1 week</option>
				<option value="730" '; if($hour==730) { echo "SELECTED"; } echo '>1 month</option>
			</select></td></tr>';
			}
			echo '
			
			<tr><td>User:</td>
			<td><select name="userID" >
				<option value="" ></option>';
			
			// loop over users and allow them to be selected for the filter
			$users = new users;
			foreach($users->getAll() as $user)
			{
				echo '<option value="'.$user->userID.'"';
				if($userID==$user->userID)
					echo "SELECTED";
				echo '>'.$user->UserName;
				if($user->external!='0')
					echo " via ".$user->external;
				echo '</option>';
			}
			echo '<select>';
			
			// If we are already viewing a saved event then pass the value as a hidden value so we can filter these results
			if(isset($_GET['savedEvent']) && is_numeric($_GET['savedEvent']))
				echo "<input type=\"hidden\" name=\"savedEvent\" value=\"".$_GET['savedEvent']."\" />";
				
			?></td></tr>
			<tr><td>Search</td><td><input type="text" name='search' style='width: 200px' value='<?php if(isset($_GET['search'])) { echo strip_tags($_GET['search']); }?>'/></td></tr>
			<tr><td></td><td><input type="submit" value="filter" /></td></tr>
			<?php
			if($userID || $search || $hour)
			{
				echo "<tr><td colspan='2' ><a href='logs.php' ><img src='images/icons/delete_small.gif' alt='Clear Search' border='0' />
				 <font size='1px' >Clear Search</font></a></td></tr>";
			}
			?>

			</table>
			</form>
		</p>
		<p>
			<strong>Saved Events</strong>
		</p>
		<p><ul>
		
			<a href="logs.php" ><li>All Logs</li></a>
		<?php
			// List all saved events for the right menu
			$events = $logs->listSavedEvents();
			if($events)
			foreach($events as $item)
				echo "<a href=\"?savedEvent=".$item['eventID']."\" ><li>".stripslashes($item['name']).'</li></a>';
		?>		
	</ul></p>	</div>
		
		
		<div class="module" id="logHelp" >
        		<strong>Help</strong>
            	<p>
                    <i>Saved Events</i>
                </p>
                    <p>Save a selection of logs for future viewing.
                    In future outlines of physical work to be completed can be generated from here.
                    </p>
		</div>
	</div>
		
</div>

<?php
include "theme/".$theme."/base.php";
}
?>