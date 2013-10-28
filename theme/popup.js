// Get Information about Building Locations from Database using AJAX
$(document).ready(function()
{
	var offsetX = 0;
	var offsetY = 0;
	var parentID = 0;
});


var parentID;
function launchCableMGR(startPortID)
{
    parentID=startPortID;
    // query and lets determine the status of this port
    $.ajax({
       type: "POST",
       url: "handler.php",
       async:false,
       dataType: "json",
       data: "action=portInfo&portID=" + startPortID,
       success: function(startPort)
       {
           if(!startPort.join)
           {
               // highlight the starting port we are working with
               $("#portID"+startPortID).addClass("selected");
           }
           
           if($("#cableConnectionMenu").css("display")!="visible")
                $("#cableConnectionMenu").fadeIn("fast");
            
            $("#currentTitle").html(startPort.deviceName);
            
            loadDevice(startPort.deviceID);
       }
    });    
}

function deleteCable()
{
    $.ajax({
       type: "POST",
       url: "handler.php",
       async:false,
       dataType: 'json',
       data: "action=deleteCable&portID=" + parentID,
       success: function(deleteStatus)
       {
            $("#manageConnection").slideUp();
            $("#displayArea").slideDown();
            if(deleteStatus==1)
            {
                $("#displayArea").html("<strong>Cable Deleted</strong>");
                $("#displayArea").append("<p><a onclick='$(\"#menus\").html(\"\");'>Click here to continue</a></p>");
            }
            else
            {
                $("#displayArea").html("<strong>Error deleting cable</strong><p>Please visit the racksmith forum for if you wish to ask for support</p>");
                $("#displayArea").append("<p><a onclick='$(\"#menus\").html(\"\");'>Click here to continue</a></p>");
            }
        }
     });

    return 1;
}

function deleteFlood()
{
    $.ajax({
       type: "POST",
       url: "handler.php",
       async:false,
       dataType: 'json',
       data: "action=deleteFlood&portID=" + parentID,
       success: function(deleteStatus)
       {
            $("#manageConnection").slideUp();
            $("#displayArea").slideDown();
            if(deleteStatus==1)
            {

                $("#displayArea").html("<strong>Flood Deleted</strong>");
                $("#displayArea").append("<p><a onclick='$(\"#menus\").html(\"\");'>Click here to continue</a></p>");
            }
            else
            {
                $("#displayArea").html("<strong>Error deleting flood</strong><p>Please visit the racksmith forum for if you wish to ask for support</p>");
                $("#displayArea").append("<p><a onclick='$(\"#menus\").html(\"\");'>Click here to continue</a></p>");
            }
        }
     });

    return 1;
}

function deleteEntireCableRun()
{
    $.ajax({
       type: "POST",
       url: "handler.php",
       async:false,
       data: "action=recursivePortDelete&portID=" + parentID,
       success: function(portInfo){
            $("#manageConnection").slideUp();
            $("#displayArea").slideDown();
            if(portInfo)
            {
                $("#displayArea").html("<strong>Cable Segment Deleted</strong>");
                $("#displayArea").append("<p><a onclick='$(\"#menus\").html(\"\");'>Click here to continue</a></p>");
            }
            else
            {
                $("#displayArea").html("<strong>Error deleting cable</strong><p>Please visit the racksmith forum for if you wish to ask for support</p>");
                $("#displayArea").append("<p><a onclick='$(\"#menus\").html(\"\");'>Click here to continue</a></p>");
            }
       }
     });
    return 1;
}

function manageConnection(type)
{
    // Default the type of action here if undefined
    type = typeof(type) != 'undefined' ? type : 0;

    /* values for type
     * 0 - no connection exists and we should hide the manage interface
     * 1 - A connection to the back of the device exists
     * 2 - A connection the front of the device exists
     * 3 - This "port" is fully connected
     * 4 - A standard port with a cable only connected on one end (basically a 0)
     */

    // if we know there is no connection then lets exist
    if(!type)
    {
        $("#manageConnection").hide();
        $("#displayArea").show();
    }
    else
    {
        parentID=String(parentID);
        var hideManagement=0; // flag to determine if we hide the mgmt interface later

        // determine the focus if we had a patch panel
        if(parentID.indexOf("join")!=-1)
        {
            parentFocus = $(".selectedView").html();
            if(parentFocus == null || parentFocus == "Front")
            {
                parentFocus = "prim";
                if(type==2) // if we selected the front but the function was called with a 2 (back port), change flag
                    hideManagement=1;
            }
            else
            {
                parentFocus = 'sec';
                if(type==1) // as above, check view and passed arg, determine if we hide menu
                    hideManagement=1;
            }
                parentID=parentID+parentFocus;
         }

        // if the flag things we should exist, hide mgmt and show existing list
        if(hideManagement)
        {
            $("#manageConnection").hide();
            $("#displayArea").show();
        }
        // else we must display the cable trace & mgmt options
        else
        {
            $("#displayArea").hide();
            $("#manageConnection").show();
            $("#manageConnection").html("");
      
            /* Start testing recursive cable lookup & print */
            $.ajax({
               type: "POST",
               url: "handler.php",
               async:false,
               data: "action=recursivePortSearch&onlyShowEndpoint=0&startPort=" + parentID,
               success: function(portInfo){
                    $("#manageConnection").append("<strong>Full Cable Trace:</strong><br/>"+portInfo + "");
               }
             });
            if(parentID.indexOf("join")==-1)
            {
                 // complete a loopup for cable flooding and so you could delete it
                $.ajax({
                   type: "POST",
                   url: "handler.php",
                   async:false,
                   dataType: "json",
                   data: "action=portInfo&flood=check&existingCable=1&portID=" + parentID,
                   success: function(ExistingportInfo){
                       ableToDelete=ExistingportInfo.flood;
                   }
                    });
                    
                    // display the options for this cable
                    var delMenu = "<p><strong>Options</strong>\
                    <ul>\
                    <li><a onclick='$(\"#manageConnection\").slideUp();$(\"#displayArea\").slideDown();' >Reposition cable</a></li>\
                    <li><a onclick='deleteCable();' >Delete cable</a></li>";
               
                    if(ableToDelete>1)
                        delMenu+="<li><a onclick='deleteFlood();' >Delete flood <br/>\n\
                         <em>(inc. the following "+(ableToDelete-1)+" ports)</em></a></li>";

                    delMenu+="<li><a onclick='deleteEntireCableRun();' >Delete entire cable run (with patches)</a></li>\
                    </ul>";

                    $("#manageConnection").append(delMenu);
                  
            }
            else
            {
                var joinID = parentID.replace(/[a-z_]/g,'');
                var ableToDelete=0;
                 // complete a loopup for cable flooding and so you could delete it
                $.ajax({
                   type: "POST",
                   url: "handler.php",
                   async:false,
                   dataType: "json",
                   data: "action=joinInfo&flood=check&existingCable=1&focus="+parentFocus+"&joinID=" + joinID,
                   success: function(ExistingportInfo){
                       ableToDelete=ExistingportInfo.flood;
                   }
                   });

                    var delMenu="<p><strong>Options</strong>\
                    <ul>\
                    <li><a onclick='deleteCable();' >Delete cable</a></li>";
                    if(ableToDelete>1)
                        delMenu+="<li><a onclick='deleteFlood();' >Delete flood <br/>\n\
                         <em>(inc. the following "+(ableToDelete-1)+" ports)</em></a></li>";
                        
                    delMenu+="</ul>";

                    $("#manageConnection").append(delMenu);
            }

     }
    }
}

function loadBuildings()
{
    $("#displayPortal").hide();
    $("#displayArea").show();

    var query = "action=buildings";

    $.post("handler.php",query,function(result)
    {
            var content = "<ul class='popupList' >";
            var i = 0;
            $.each(result, function(i, val)
            {
                content += "<li class='popupBuildingListing' onclick='loadFloors(" + result[i].buildingID + ")'>" + result[i].name + "</li>";
            });
            content += "</ul>";
            $("#displayArea").html(content);
            $('#backText').parent().hide();
            $("#currentTitle").html("Select a building");
            $("#currentTitle").show();
    },"json");
}
	
	
function loadFloors(buildingID)
{
        $('#backText').parent().show();
        $("#displayPortal").hide();
        $("#displayArea").show();

        $("#displayMap").html("");

        var query = "action=floors&buildingID=" + buildingID;
        $.post("handler.php",query,function(result)
        {
                var content = "<ul class='popupList' >";
                $.each(result, function(i, val)
                {
                    content += "<li class='popupFloorListing' onclick='loadRooms(" + result[i].floorID + ")'>level: " + result[i].name + "</li>";
                });
                content += "</ul>";
                
                $("#displayArea").html(content);
        },"json");

        query = 'action=buildingInfo&buildingID=' + buildingID;
        $.post("handler.php",query,function(result)
        {
            $("#currentTitle").html(result.name);
            $("#currentTitle").show();
        },"json");

        $("#backTable").show();
        $("#backText").unbind('click');
        $('#backText').bind("click", function(ev)
        {
            loadBuildings();
        });
        $('#backText').html("&laquo; Change Building")
}
	
function loadRooms(floorID)
{
        $("#displayPortal").show();
        $("#displayArea").hide();
        $("#displayMap").html("");

        $("#displayMap").css("height","1500px");
        $("#displayMap").css("width","2000px");

        $("#displayMap").html("");

        var query = "action=layoutItems&parentType=Floor&parentID=" + floorID;
        $.post("handler.php",query,function(result)
        {
                var content = "";
                $.each(result, function(i, val)
                {
                        var positionX = (result[i].posX / 2);
                        var positionY = (result[i].posY / 2);

                        if (result[i].itemType == "Door")
                        {
                                content = "<div id='layoutItemID" + result[i].layoutItemID + "' style='background-image:url(images/icons/door.gif);display:block;width: " + (result[i].width / 2) + "px; height: " + (result[i].height / 2) + "px;position:absolute; top: " + positionY + "px; left: " + positionX + "px' class='deletable resizable draggable'>";
                                content += "<div></div></div>";
                        }
                        else
                        {
                                content = "<div onclick='loadRoom(" + result[i].itemID + ")' id='layoutItemID" + result[i].itemID + "' style='background-color: #CCC;cursor:pointer;display:block;width: " + (result[i].width / 2) + "px; height: " + (result[i].height / 2) + "px;position:absolute; top: " + positionY + "px; left: " + positionX + "px' class='floortile deletable room" + result[i].itemID + " color" + result[i].itemID + " draggable resizable'>";
                                content += "<div style='width: 100%; height: 100%; overflow:hidden;'>";
                                content += "<div style='float:left;padding:10px;font-weight:bold;font-size:8px;'>";

                                if (result[i].itemType == "rack1")
                                        content += "<span id='rackID" + result[i].itemID + "'>" + result[i].itemName + "</span>";
                                else
                                        content += result[i].itemName;

                                content +="</div>";
                                content += "</div></div>";
                        }
                        $("#displayMap").append(content);
                });
        },"json");

        query = 'action=floorInfo&floorID=' + floorID;
        $.post("handler.php",query,function(result)
        {
                $("#currentTitle").html("Level: " + result.name);
                $("#currentTitle").show();
                $("#backTable").show();
                $("#backText").unbind('click');
                $('#backText').bind("click", function(ev)
                {
                        loadFloors(result.buildingID);
                });
                $('#backText').html("&laquo; Change Floor")
        },"json");
}
	
	
function loadRoom(roomID)
{
        $("#displayPortal").show();
        $("#displayArea").hide();
        $("#displayMap").html("");

        $("#displayMap").css("height","1500px");
        $("#displayMap").css("width","2000px");

        var query = "action=layoutItems&parentType=Room&parentID=" + roomID;
        $.post("handler.php",query,function(result)
        {
                var content = "";
                $.each(result, function(i, val)
                {
                        var positionX = (result[i].posX / 2);
                        var positionY = (result[i].posY / 2);

                        if (result[i].itemType != "cabletray1" && result[i].itemType != "cabletray2")
                        {
                                content += "<div";
                                if (result[i].itemType == "rack1")
                                        content += " title='" + result[i].itemName + "' onclick='loadDevices(" + result[i].itemID + ")'";

                                content += " id='layoutItemID" + result[i].layoutItemID + "' style='";
                                if (result[i].itemType == "rack1")
                                        content += "cursor:pointer;";

                                content += "background-color:#000; display:block; width: " + (result[i].width / 2) + "px; height: " + (result[i].height / 2) + "px;position:absolute; top: " + positionY + "px; left: " + positionX + "px' class='small deletable " + result[i].itemType + " " + result[i].itemName + "'>";
                                content += "</div>";
                        }
                        $("#displayMap").append(content);
                });
        },"json");

        query = 'action=roomInfo&roomID=' + roomID;
        $.post("handler.php",query,function(result)
        {
                $("#currentTitle").html(result.name);
                $("#currentTitle").show();
                $("#backTable").show();
                $("#backText").unbind('click');
                $('#backText').bind("click", function(ev)
                {
                        loadRooms(result.floorID);
                });
                $('#backText').html("&laquo; Change Room")
        },"json");
}
	
	
function loadDevices(rackID)
{
    $("#displayArea").html("");
    parentID=String(parentID);
        // create the appropriate queryURL for a port or a join
        if(parentID.indexOf("join")!=-1)
        {
                // determine if a front/back port is in use
                parentFocus = $(".selectedView").html();
                if(parentFocus == null || parentFocus == "Front")
                        parentFocus = "prim";
                else
                        parentFocus = 'sec';
                // query about this join
                var joinID = parentID.replace(/[a-z_]/g,'');
                var query2 = 'action=joinInfo&joinID=' + joinID + '&focus=' + parentFocus;
        }
        // else we have a standard device port
        else
                var query2 = 'action=portInfo&portID=' + parseInt(parentID);

        $.post("handler.php",query2,function(result2)
        {
                // create the txt to display in interface, different for port / join
                if(parentID.indexOf("join")!=-1)
                {
                        if(parentFocus=='sec')
                                parentFocus = 'Back Port';
                        else
                                parentFocus = 'Front Port';
                        connection2Text= result2.patchName + " " + parentFocus + " port " + result2.disporder;
                }
                else
                {
                    //if(result2.cableID!=0)
                    //    $("#displayArea").prepend("A cable is connected: ID=" + result2.cableID);
                }
        },"json");

        $("#displayPortal").hide();
        $("#displayArea").show();
        $("#portsAttach").removeClass("padding");

        if ($("#displayArea").hasClass("room") == false)
                $("#displayArea").addClass("room")

        var query = 'action=devices&rackID=' + rackID;
        $.post("handler.php",query,function(result)
        {
                var content = "<ul class='popupList' ><li class='patchMenu' >";
                content += "<span class='minimapSelectedView' onclick=\"$('.minimapSelectedView').removeClass('minimapSelectedView');$(this).addClass('minimapSelectedView');$('ul.popupList li.frontDevice').show();$('ul.popupList li.backDevice').hide();\" >Front</span>";
                content += "<span onclick=\"$('.minimapSelectedView').removeClass('minimapSelectedView');$(this).addClass('minimapSelectedView');$('ul.popupList li.frontDevice').hide();$('ul.popupList li.backDevice').show();\" style=\"float:right\" >Back</span> </li>";
                $.each(result, function(i, val)
                {
                        content += "<li class='popupDeviceListing ";
                        if(result[i].back==1)
                            content+="backDevice";
                        else
                            content+="frontDevice";
                        content += "' onclick='loadDevice(" + result[i].deviceID + ")'>" + result[i].systemName + "</li>";
                });
                if (content == "<ul class='popupList' style='margin:5px;'>")
                        content += "<li class='popupDeviceListing'><em>There are no devices in this rack</em></li>";

                content += "</ul>";
                $("#displayArea").append(content);

                // hide any devices which are at the back of the rack
                $('ul.popupList li.backDevice').hide();
                
        },"json");

        query = 'action=rackInfo&rackID=' + rackID;
        $.post("handler.php",query,function(result)
        {
                $("#currentTitle").html(result.name);
                $("#currentTitle").show();
                $("#backTable").show();
                $("#backText").unbind('click');
                $('#backText').bind("click", function(ev)
                {
                    loadRoom(result.parentID);
                });
                $('#backText').html("&laquo; Change Rack")
        },"json");
}
	
	
function loadDevice(deviceID)
{   
    // show the section
    $("#displayPortal").hide();

    // prepare to display
    $("#displayArea").html("");
    $("#portsAttach").removeClass("padding");
    if ($("#displayArea").hasClass("room") == false)
        $("#displayArea").addClass("room");

        // load device information
        var query = 'action=deviceInfo&deviceID=' + deviceID;
        $.post("handler.php",query,function(device)
        {
            // setup the navigation links for the box
            $("#currentTitle").html(device.name);
            $("#backTable").show();
            $("#backText").unbind('click');
            $('#backText').bind("click", function(ev)
            {             
                loadDevices(device.parentID);
            });
            

            $('#backText').html("&laquo; Change Device");

            // set the default view
            currentView = $(".selectedView").html();
            if(currentView == null)
                currentView = "Front";
            var query = 'action=ports&deviceID=' + deviceID;
            $.post("handler.php",query,function(result)
            {
                var portsAvailable=0;
                var content = "<ul class='popupList' >";
                $.each(result, function(i, val)
                {
                    portsAvailable++;
                    content += "<li class='popupPortListing";
                    if(result[i].cableID!=0)
                        content+=' connected';
                    content+= "' onclick='joinPort(" + result[i].portID + ")'>" + result[i].label + "</li>";
                });

                if(portsAvailable==0)
                        content+="<li class='popupPortListing' ><i>No ports available of this cable type</li>";

                content+="<li class='portKey' >\
                        <div class='connected' ><div class='box'></div>Connected</div> \
                        <div class='singlePort' ><div class='box'></div>Connected this end only</div>\
                        <div id='keyAvailablePort' ><div class='box'></div>Available</div> \
                </li>";

                content += "</ul>";
                $("#displayArea").append(content);
                $("#displayArea").show();
            },"json");


/*
                // query for the joins and if they have connected ports(a port = cable connected)
                var query = 'action=joins&deviceID=' + deviceID;// + '&cableTypeID=' + portType;
                $.post("handler.php",query,function(result)
                {
                    // display the patch
                    var portsAvailable=0;
                    var content = "<ul class='popupList' ><li class='patchMenu' >";
                    content += "<span class='minimapSelectedView' onclick=\"$('.minimapSelectedView').removeClass('minimapSelectedView');$(this).addClass('minimapSelectedView');$('ul.popupList .singleconnected').removeClass('singleconnected');$('ul.popupList .primPort').addClass('singleconnected');$('ul.popupList .secPort').removeClass('singleconnected');\" >Front</span>";
                    content += "<span onclick=\"$('.minimapSelectedView').removeClass('minimapSelectedView');$(this).addClass('minimapSelectedView');$('ul.popupList .singleconnected').removeClass('singleconnected');$('ul.popupList .secPort').addClass('singleconnected');$('ul.popupList .primport').removeClass('singleconnected');\" style=\"float:right\" >Back</span> </li>";
                    $.each(result, function(i, val)
                    {
                        // show only cables that match the cableType
                        if(result[i].cableTypeID == portType)
                        {
                            portsAvailable++;
                            // conected port
                            if(result[i].primPort != 0 && result[i].secPort != 0)
                                content += "<li class='popupPortListing connected ";
                            // connected at only front OR back
                            else if(result[i].primPort != 0)
                                content += "<li onclick='joinPort(\"join" + result[i].joinID + "\")' class='popupPortListing primPort singleconnected";
                            else if(result[i].secPort != 0)
                                content += "<li onclick='joinPort(\"join" + result[i].joinID + "\")' class='popupPortListing secPort singleconnected";
                            // disconnected port
                            else
                                content += "<li onclick='joinPort(\"join" + result[i].joinID + "\")' class='popupPortListing ";

                            content += "'>" + (i+1) + "</li>";
                        }
                    });

                    if(portsAvailable==0)
                        content+="<li class='popupPortListing' ><i>No ports available of this cable type</li>";

                },"json");
                */
        },"json");
}
	
	
function joinPort(portID)
{	
        // pull the selected view from within the minimap (incase were usign a patch panel)
        portFocus = $(".minimapSelectedView").html();

        $("#displayPortal").hide();
        $("#displayArea").show();

        $("#displayArea").html("");
        $("#portsAttach").removeClass("padding");
        if ($("#displayArea").hasClass("room") == false)
                $("#displayArea").addClass("room")

        portID=String(portID);
        // check to see if we are working with a join or port and create the correct query URL
        if(portID.indexOf("join")!=-1)
        {
                // determine if the user is working with a front or back facing port
                if(portFocus == null || portFocus == "Front")
                        portFocus = "prim";
                else
                        portFocus = 'sec';

                // query for join info
                var joinID = portID.replace(/[a-z_]/g,'');
                var query = 'action=joinInfo&joinID=' + joinID + '&focus=' + portFocus + "&flood=check";
        }
        else
                var query = 'action=portInfo&portID=' +  parseInt(portID) + "&flood=check";

         var previousDeviceID;
        // query for the 2nd port
        $.post("handler.php",query,function(result)
        {
            previousDeviceID=result.deviceID;
            
            // if the ports a join create the appropriate txt to display
            if(portID.indexOf("join")!=-1)
            {
                if(portFocus=='sec')
                        portFocus = 'Back Port';
                else
                        portFocus = 'Front Port';
                var connection1Text= result.patchName + " " + portFocus + " port " + result.disporder;
            }
            // if the ports typical show the device it belongs to
            else
                var connection1Text= result.deviceName + " &raquo; " + result.label;;

            //parentID=String(parentID);
            parentID=String(portID);
            
                // create the appropriate queryURL for a port or a join
                if(parentID.indexOf("join")!=-1)
                {
                        // determine if a front/back port is in use
                        parentFocus = $(".selectedView").html();
                        if(parentFocus == null || parentFocus == "Front")
                                parentFocus = "prim";
                        else
                                parentFocus = 'sec';
                        // query about this join
                        var joinID = parentID.replace(/[a-z_]/g,'');
                        var query2 = 'action=joinInfo&joinID=' + joinID + '&focus=' + parentFocus + "&flood=check";
                }
                // else we have a standard device port
                else
                        var query2 = 'action=portInfo&portID=' + parseInt(parentID) + "&flood=check";

                $.post("handler.php",query2,function(result2)
                {
                        // create the txt to display in interface, different for port / join
                        if(parentID.indexOf("join")!=-1)
                        {
                                if(parentFocus=='sec')
                                        parentFocus = 'Back Port';
                                else
                                        parentFocus = 'Front Port';
                                var connection2Text= result2.patchName + " " + parentFocus + " port " + result2.disporder;
                        }
                        else
                                var connection2Text= result2.deviceName + " &raquo; " + result2.label;

                        var displayTxt="";
                        displayTxt += "<div style='padding:0px 5px;'><p>You're about to connect:</p>";
                        displayTxt += "<div class='popupComp' id='conFirstHost' >" + connection2Text + "</div>";
                        displayTxt += "<center><img src='images/icons/connect_down.png' alt='Connect Cables' /><span id='connectDetails' ></span></center>";
                        displayTxt += "<div class='popupComp' id='conSecondHost' >" +connection1Text + "</div>";
                        displayTxt += "<div class='barcodeEntry' ><p>Cable Barcode: <input type='text' id='barcode' style='font-style:italic;color:#2b2b2b;'/></P>";

                        // if both ends are capable of flooding
                        if(result.flood > 0 && result2.flood > 0)
                        {
                            maxFlood=0;
                            // display prompt txt
                            // show the lowest number of floods available (both ends need to support this #)
                            if(result.flood > result2.flood)
                                maxFlood=result2.flood;
                            else
                                maxFlood=result.flood;

                            displayTxt+="<strong>You can flood up to "+maxFlood+" extra ports</strong><select id='floodValue' name='flood' >";
                            for(i=0;i<=maxFlood;i++)
                            {
                                displayTxt+="<option value='" + i + "' >" + i + "</option>";
                            }
                            displayTxt+="</select>";
                            //$('#floodSlider').slider({min:0,value: 0,max: maxFlood})



                        }
                        displayTxt += "<p><center><button type='button' id='joinNow'>Join Now</button></center></p>";

                        displayTxt +="</div>"
                        // show the user what we think their trying to achieve
                        $("#displayArea").html(displayTxt);


                        if(result.flood > 0 && result2.flood > 0)
                        {
                            $('#floodValue').change(function(){
                                var num = $(this + "option:selected").val();
                                if(num==0)
                                    $('#connectDetails').html("");
                                else
                                    $('#connectDetails').html("x" + (parseInt(num)+1));
                            });
                        }
                            
                        // generate a new barcode for this cable
                        // set its value in the interface
                        $.getJSON('devices.php?action=genBarcode&type=cable', function(data)
                        {
                                $('#barcode').val(data[0].barcode);
                                $('#barcode').focus(function() {
                                if(this.value==data[0].barcode || this.value=='')
                                        this.value='';
                                });

                                $('#barcode').blur(function() {
                                        if(this.value=='')
                                            this.value=data[0].barcode;
                                });
                        });

                        // if they've submitted sent everything off to conncet
                        $('#joinNow').bind("click", function(ev)
                        {
                                // if we're dealing with joins prepare them to send to the connector
                                // we should clean up this and send it as a JSON array !!!!!!!!!!!!!!!!!!!!!!
                                if(parentID.indexOf("join")!=-1)
                                        parentID=parentID + "_" + parentFocus;

                                if(portID.indexOf("join")!=-1)
                                        portID=portID + "_" + portFocus;

                                if($('#floodValue').length >0)
                                    var floodCount=$('#floodValue option:selected').val();
                                else
                                    var floodCount=0;

                                var query3 = 'action=insertCable&cableTypeID=' + portType + "&barcode=" + $('#barcode').val() + "&end1=" + portID + "&end2=" + parentID + "&floodCount=" + floodCount;
                                $.post("handler.php",query3,function(returnStat)
                                {
                                    if(returnStat)
                                    {
                                        if(floodCount!=0)
                                            $("#displayArea").html("<strong>Flood Wiring Created</strong>");
                                        else
                                            $("#displayArea").html("<strong>Cable Created</strong>");
                                        
                                        $("#displayArea").append("<p><a onclick='$(\"#menus\").html(\"\");'>Click here to continue</a></p>");
                                    }
                                    else
                                    {
                                        $("#displayArea").html("<strong>Error Creating Cable</strong><p>Please visit the racksmith forum for if you wish to ask for support</p>");
                                        $("#displayArea").append("<p><a onclick='$(\"#menus\").html(\"\");'>Click here to continue</a></p>");
                                    }
                                });
                        });
                },"json");

                // display the above onto
                $("#currentTitle").html(result.label);
                $("#currentTitle").show();
                $("#backTable").show();
                $("#backText").unbind('click');
                $('#backText').bind("click", function(ev)
                {
                        loadDevice(previousDeviceID);
                });
                $('#backText').html("&laquo; Change Port")
        },"json");
}



$(document).ready(function(){

    $("ul.devicePorts li.port, ul.devicePorts li.join").live('click',function()
    {
        if($(this).hasClass("join"))
            alert("It is a join");

        if($(this).hasClass("port"))
            alert("It is a port");

        // detect parent device
    });

    
});