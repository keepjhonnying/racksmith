<?php
session_start();
include "class/db.class.php";
$selectedPage="home";
include "theme/" . $theme . "/top.php";
?>
<div id="main"> 	
    <div id="left">
	
        <div class="module" id="gettingStarted">
            <strong>Getting Started</strong>
			
            <p style="padding-left:10px;">Welcome to RackSmith, A data center Device &amp; Cable Management solution.<br/>
             If you are new to RackSmith you can learn more about the software in the <a href="http://help.racksmith.net/guide.php?guide=install#gettingstarted" target="_new" alt="User Guide">User Guide</a>
            </p>
            <table class="dataTable" style="margin: 0px 5px; width: 100%">
                <thead>
                    <tr><th colspan="2" align="left">Getting Started</th></tr>
                </thead>
                <tbody>
                    <tr><td width="150px;"><strong><a href="buildings.php">Layout Manager</a></strong></td><td>Create buildings, floors and rooms where your assets are placed.</td></tr>
                    <tr><td><strong><a href="racks.php">Device Management</a></strong></td><td>Install & maintain devices</td></tr>
                    <tr><td><strong><a href="devices.php">Search</a></strong></td><td>Search assets and cable connections</td></tr>
                    <tr><td><strong><a href="owners.php">Owners</a></strong></td><td>Maintain a list of owners which are get associated with assets</td></tr>
                    <tr><td><strong><a href="templates.php">Templates</a></strong></td><td>All devices start as one of these templates, create one for each model of device.</td></tr>
                </tbody>
            </table>
        </div>

        <div class="module" id="eventLog">
            <strong>Recent Events</strong>
            <p>
            <div id="statusLog">
                <table class="dataTable">
                    <colgroup align="left" class="tblfirstRow"></colgroup>
                    <thead>
                    <tr>
                        <th scope="col" width="20%" >Date</th>
                        <th scope="col" width="65%" >Action</th>
                        <th scope="col" width="15%" >User</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php $logs = new logs;
                           $findlogs=0;
                           // logs.class.php includes the limits on logs shown (fix this)
                           $yesterday=strtotime("-1 day");
                           foreach($logs->getAll(12) as $log) { 
                               $findlogs=1;
                           
                               if($log->timestamp>$yesterday)
                                   $highlight=' class="selected"';
                               else
                                   $highlight='';
                           ?>
                           
                        <tr>
                                <td align="center"><?php echo $log->eventTime ?></td>
                                <td<?php echo $highlight; ?>><?php echo $log->event ?></td>
                                <td align="center" <?php echo $highlight; ?>><a href="logs.php?userID=<?php echo $log->userID ?>"><?php echo $log->user()->UserName ?></a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr> <?php if($findlogs) { ?>
                            <td colspan="3"><a href="logs.php"><em>View more items</em></a></td>
                             <?php } else { ?>
                            <td colspan="3"><a href="logs.php"><em>No logged events</em></a></td>
                             <?php } ?>
                        </tr>
                    </tfoot>
                </table>
            </div>
            </p>
        </div>
		
	</div>
        <div id='right' >

            <div class='module' style="text-align: center;">
                <FORM ACTION="devices.php" METHOD="GET">
                    Device Search: <input style="font-style: italic;" type="text" name="deviceSearch" value="Device Name/Model" onclick="if(this.defaultValue==this.value) this.value = ''"/>
                    <input type="submit" name="submit" value="Go" />
                </FORM>
            </div>
            <?php
            if(file_exists('class/build.inc.php'))
            {
                include "class/build.inc.php";
            ?>
            <div class='module' >
                <strong>System Details</strong>
                <ul id="systemVersion"><!--Version
                    <hr/>-->
                    <li>Build: <span id='version'><i><?php echo $buildNumber; if(!($buildStream=='prod'||!$buildStream)) { echo "_$buildStream"; }  ?></i></span></li>
                    <!--<li>Released: <?php echo date('Y-m-d', strtotime($buildDate)); ?></li>-->
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
include "theme/base.php";
?>