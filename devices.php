<?php
$maxPageResults='30';
session_start();
$selectedPage="search";
include "class/db.class.php";


// process the submission form to search for a cable
if(isset($_GET['action'],$_GET['barcode']) && $_GET['action']=="searchCables")
{
    $cables = new cables;
    $result = $cables->searchBarcode($_GET['barcode']);
    if(!$result)
        $result[0]='no_cables';
    echo json_encode(array($result));
}

// process the submission form to search for a cable
if(isset($_GET['action'],$_GET['name']) && $_GET['action']=="searchDevices")
{
    if(isset($_GET['firstEntry']) && is_numeric($_GET['firstEntry']))
        $start=(int)$_GET['firstEntry'];
    else
        $start=0;
    
    if(isset($_GET['limit']) && is_numeric($_GET['limit']))
        $limit=(int)$_GET['limit'];
    else
        $limit=$maxPageResults;

    
    $devices = new devices;
    $result=array();
    $result = $devices->searchGeneral($_GET['name'],$start,$limit);
    if(!$result)
        $result[0]='no_devices';


    echo json_encode(array($result));
}

// process the submission form to search for a cable
else if(isset($_GET['action'],$_GET['type']) && $_GET['action']=="genBarcode")
{	
    $result['barcode']=0;
    switch($_GET['type'])
    {
        case "cable":
            $cables = new cables;
            $result['barcode'] = $cables->genBarcode();
            break;
    };

    echo json_encode(array($result));
}

else if(!$_POST && !isset($_GET['action']))
{
    if(!isset($_GET['rackID'],$_GET['unitID']))
        $globalTopic="View Devices";
    include "theme/" . $theme . "/top.php";
?>
<link type="text/css" href="theme/rack.css" rel="stylesheet" />
<link rel='stylesheet' href='theme/room.css' type='text/css' />
<script src="theme/js/rack_drag.js" type="text/javascript" ></script>
<script type='text/javascript' src='theme/navigate.js'></script>
<script type='text/javascript' src='theme/js/mapbox.min.js'></script>
<script type="text/javascript"> 
    var firstEntry=0;
    var currentSearch='';
$(document).ready(function()
{
<?php
    // if the search has been passed from another page through a form
    // adjust the URL appropriately,
    if(isset($_GET['deviceSearch'])) { ?>
            window.location = "#searchDevice:<?php echo preg_replace('/[^0-9a-z+_-]/i', '', $_GET['deviceSearch']); ?>";
    <?php } ?>

        
    $('.ownerPopup').live('click',function() {
        var ownerID = $(this).attr("href").replace(/#/g,'');
        $.openDOMWindow({windowSourceURL: 'owners.php?mode=popup&id='+ownerID,fixedWindowY: 50, borderColor: '#3b4c50', borderSize: 2, width:'500',height: '275',overlayOpacity: '30',windowSource:'ajax',modal:1,windowPadding: 0});
        return false;
    });
    
    $('.moreResults').disableSelection();
    $('.moreResults').live('click',function(event) {
        event.preventDefault();
        firstEntry+=30;
        runDeviceSearch(currentSearch);
    });
    
    $('.lessResults').disableSelection();
    $('.lessResults').live('click',function(event) {
        event.preventDefault();
        firstEntry-=30;
        runDeviceSearch(currentSearch);
    });
    
    // Check if we need to load anything based on whats passed in the URL
    url=new String(window.location);
    if(url.indexOf('#')!=-1)
    {
        urlQuery=url.substr(url.lastIndexOf("#")+1,url.length);
        if(urlQuery.indexOf("searchDevice:")!=-1)
        {
            var searchTerm=urlQuery.replace("searchDevice:", '').replace(/[^0-9a-z+_-]/ig, '');
            $("#deviceSearch input[name=name]").val(searchTerm);
            var data=$("#deviceSearch").serialize();
            runDeviceSearch(data);
        }
        else if(urlQuery.indexOf("searchCable:")!=-1)
        {
            var cableSearch=urlQuery.replace("searchCable:", '').replace(/[^0-9a-z+_-]/ig, '');
            $("#cableSearch input#barcode").val(cableSearch);
            var data=$("#cableSearch").serialize();
            runCableSearch(data);
        }
        else if(urlQuery.indexOf("deviceID:")!=-1)
        {
            var deviceIDURL=urlQuery.replace("deviceID:", '').replace(/[^0-9a-z+_-]/ig, '');
            loadDevice(deviceIDURL);
        }
    }


    $('#viewPortal').mapbox({pan: true, zoom: false,defaultX:0,defaultY:0});
    
    $("#cableSearchSubmit").click(function()
    {
        var data=$("#cableSearch").serialize();
        runCableSearch(data);
    });

    function runCableSearch(data)
    {
        currentSearch=data;
        $("#deviceHolder").hide();
        $("#deviceSearchResults").hide();
        $("#deviceMiniMap").hide();
        window.location = "#searchCable:" + $("#cableSearch input#barcode").val().replace(" ","+").replace(/[^0-9a-z+_-]/ig, '');
        $.getJSON("devices.php?action=searchCables",data,function(data)
        {
            // Clear the table of search results
            $("#cableSearchResults tbody").html("");

            if(data == "no_cables")
                $("#cableSearchResults table tbody").html("<tr><td colspan='5'><i>no cables found</i></td></tr>");
            else
            {
                $("#cableSearchResults").fadeIn(500);

                // loop over each returned cable
                $.each(data, function(item,cableObject)
                {
                    $.each(cableObject, function(item,cable)
                    {
                        $("#cableSearchResults table tbody").append("<tr><td>" + cable['barcode'] + "</td>" +
                        "<td>" + cable['cableType'] + "</td>" +
                        "<td>" + cable['connected'] + "</td>" +
                        "<td>" + cable['device1'] + "</td>" +
                        "<td>" + cable['device2'] + "</td>" +
                        "</tr>");
                    });
                });
            }
            $("#cableSearchResults").slideDown('fast');
        });
        return false;
     }

	
    $("#deviceSearchSubmit").click(function()
    {
        var data=$("#deviceSearch").serialize();
        runDeviceSearch(data);
        window.location = "#searchDevice:" + $("#deviceSearch input#name").val().replace(" ","+").replace(/[^0-9a-z+_-]/ig, '');
    });

});

function runDeviceSearch(searchTerm)
{    
    currentSearch=searchTerm;
    $("#deviceHolder").hide();
    $("#deviceMiniMap").hide();
    $("#cableSearchResults").hide();
    var resultCount=0;
    $.getJSON("devices.php?action=searchDevices&firstEntry="+firstEntry+"&limit=<?php echo $maxPageResults; ?>",searchTerm,function(data)
    {
        $("#deviceSearchResults").show();
        // Clear the table of search results
        $("#deviceSearchResults tbody").html("");
        if(data == "no_devices")
            $("#deviceSearchResults tbody").html("<tr><td colspan='7'><i>no devices found</i></td></tr>");
        else
        {
            // loop over devices returned
            $.each(data[0], function(device,item)
            {
                // generate the position txt depending on set categories 
                var positioningText='';
                if(item['categories'][2])
                {
                    if(item['categories'][2][5].value)
                        RUsize=item['categories'][2][5].value;
                    else
                        RUsize='-';
                    
                    // if we have a parent name show it if not just say its rack
                    if(item['parentName']!='')
                    {
                        // check if the user added rack to the name so we don't duplicate'
                        if(item['parentName'].match(/rack/i))
                            positioningText+=item['parentName']+" ("+RUsize+"RU)";
                        else
                            positioningText+="Rack "+item['parentName']+" ("+RUsize+"RU)";
                        
                    }
                    else
                        positioningText+=" Rack Mounted ("+RUsize+"RU)";
                    
                }
                if(item['categories'][3]!=undefined)
                {
                    if(positioningText!="")
                        positioningText+=", Floor Device";
                    else
                        positioningText+="Floor Device";
                }

                var model="";
                var serial="";
                // as generic category is optional check it exists before we work with the values
                if(item['categories'][1])
                {
                    if(item['categories'][1][12])
                        model=item['categories'][1][12].value+" ";

                    if(item['categories'][1][42])
                        model+=item['categories'][1][42].value;

                    // set the serial number if it exists
                    if(item['categories'][1][10])
                        serial=item['categories'][1][10].value;
                }

                // put the entry onto the page
                $("#deviceSearchResults tbody").append("<tr><td><a href='#deviceID:"+item.deviceID+"' onclick='loadDevice(" + item.deviceID+");' >" + item.name + "</a></td>" +
                "<td>" + model + "</td>" +
                "<td>" + serial + "</td>" +
                "<td>" + item['deviceTypeName'] + "</td>" +
                "<td>" + positioningText + "</td>" +
                "<td><a class='ownerPopup' href='#"+item['ownerID']+"' >" + item['ownerName'] + "</a></td>" +
                "<td style='text-align:right;'><a href='#deviceID:"+item.deviceID+"' onclick='loadDevice(" + item.deviceID+");' >More</a></tr>");

                resultCount++;
                if(resultCount==<?php echo $maxPageResults; ?>)
                    return false;
            });

            // Determine what we do with the paging results
            if(firstEntry!=0)
                $('.lessResults').show();
            else
                $('.lessResults').hide();    
            
            if(resultCount==<?php echo $maxPageResults; ?>)
                $('.moreResults').show();
            else
                $('.moreResults').hide();                
        }
        $("#deviceSearchResults").slideDown('fast');
    });
};


function loadDevice(deviceID)
{
    $("#deviceSearchResults").hide();
    $("#deviceHolder").show();
    $("#deviceHolder div").html("<div style='height: 200px;padding-top:170px;text-align:center;' ><img src='images/loading_trans.gif' /><br/><br/><strong>Loading Device...</strong></div>");
    
    var deviceQuery = "action=deviceInfo&deviceID="+deviceID;
    $.post("handler.php",deviceQuery,function(deviceInfo)
    {
        $("#deviceMiniMap").show();
        $("#deviceManagement").show();

        $("#deviceHolder div").load('viewDevice.php?close=min&deviceID=' + deviceID);
        $("#deviceManagementLinks").html("");
        $("#deviceManagementLinks").append("<li class='edit' style='margin-bottom: 5px;'><a href='#"+deviceInfo.deviceID+"' class='editDevice' >Edit</a></li>");
        if(deviceInfo.uploads)
            $("#deviceManagementLinks").append("<li class='upload' style='margin-bottom: 5px;'><a href='#"+deviceInfo.deviceID+"device' class='manageAttachments' >File Attachments</a></li>");

        // load the minimap to show the containing rack
        
        if(deviceInfo.parentType=='rack')
        {
            $.post("handler.php","action=rackInfo&rackID="+deviceInfo.parentID,function(rackInfo)
            {
                if(rackInfo.parentType=='room')
                    loadRoom2(rackInfo.parentID);
            },'json');
        }
        else if(deviceInfo.parentType=="room")
        {
            loadRoom2(deviceInfo.parentID);
        }
        else if(deviceInfo.parentType=='cabinet')
        {
            //$.post("handler.php","action=getCabinet&cabinetID="+deviceInfo.parentID,function(cabinetInfo)
            //{
                // For now cabinets are only on the buildings page so we don't need to look into them
                loadBuildings2();
            //},'json');
        }
        else
        {
            $("#viewPortal").html("<i><center><strong>No Map Found</strong></center><i>");
            $("#navigation").hide();
            $("#mapSpacer").hide();
        }
        //highlightRack(deviceInfo.floorDeviceID);
    },'json');

}
</script>
<div id="main"> 
	
<div id="full" style="margin-bottom:0px;">
    <div class="module" id="searchBoxes">
        <table width='100%'>
            <tr>
                <td width='50%'>
                    <strong>Cable Search</strong>
                        <form method="post" action="" id="cableSearch">
                        <label for="name"><em>Barcode:</em></label> <input type="text" name="barcode" id="barcode" size="16" />
                        <input type="submit" id="cableSearchSubmit" name="btnSubmit" value="Locate" onclick="return false;"/>
                        </form>
                </td>
                <td width='50%'>
                    <strong>Device Lookup</strong>
                        <form method="post" action="" id="deviceSearch">
                        <label for="name"><em>Name/Model:</em></label> <input type="text" name="name" id="name" size="30" />
                        <input type="submit" id="deviceSearchSubmit" name="btnSubmit" value="Locate" onclick="return false;" />
                        </form>
                </td>
            </tr>
        </table>
    </div>

    <div class='module' id="cableSearchResults" style="display:none;">
        <strong>Cable Results</strong>
        <p>
            <table width="80%" class="dataTable">
                <thead>
                    <tr>
                        <th width="20%">Barcode</th>
                        <th width="20%">Cable Type</th>
                        <th width="10%">Connected</th>
                        <th width="20%">Device</th>
                        <th width="20%">Device</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </p>
    </div>

    <div class='module' id="deviceSearchResults" style="display:none;">
        <strong>Device Search Results:</strong>
        <p>
            <table width="80%" class="dataTable">
                <colgroup align="left" class="tblfirstRow"></colgroup>
                    <thead>
                        <tr>
                            <th width="20%">Name</th>
                            <th width="15%">Model</th>
                            <th width="5%">Serial</th>
                            <th width="10%">Type</th>
                            <th width="20%">Positioning</th>
                            <th width="15%">Maintainer</th>
                            <th width="5%">Action</th>
                        </tr>
                    </thead>
                <tbody>

                </tbody>
                <tfoot>
                    <tr><td colspan="7" class="tblfirstRow" style="padding: 3px 0px;">
                            <span style="float:left;" class="lessResults">&lt; Back</span>
                            <span style="float:right;" class="moreResults">Forward &gt;</span>
                    </td></tr> 
                </tfoot>
            </table>
        </p>
    </div>
</div>
    <div id="left" style="padding-left:0px;">
        <div class="module" id='deviceHolder' style="display:none;margin-left: 10px;padding:0px;">
            <div>
                
            </div>
        </div>
    </div>

    <div id="right" >
        <div class="module" id="deviceMiniMap" style="display:none;" >

            <div id="navigation" >
                <div id="currentArea" align="center" style="font-weight:bold;"></div>
                <div id="miniList"></div>
                <div id="viewPortal" >
                    <div id="miniMap"  align="center" ></div>
                </div>
                <div id="miniMapHover" style='display:none;'></div>
                <div id="navigate" style='cursor:pointer;display:none;'></div>
                <hr/>
            </div>

            <div id="mapSpacer"></div>

            
            <strong>Manage Device</strong>
            <ul id="deviceManagementLinks">
                
            </ul>
        </div>
    </div>
</div>
<?php include "theme/" . $theme . "/base.php";
}
?>