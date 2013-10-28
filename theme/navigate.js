
// mouseover for racks within the minimap
$("#miniMap .rack1").live('mouseover mouseout',function(ev)
{
    if(ev.type=="mouseover")
    {
	// get the name of current item to display without JSON request
	var rackID = $(this).find('span').attr("id").replace('rackID','');
	var rackName = $(this).find("span").html();
	
	// position item to the top left of the rack
	var leftPos = $(this).position().left-225;
	var topPos = $(this).position().top+25;

	// position the popup
	$("#miniMapHover").show();		
	$("#miniMapHover").css("position","absolute");
	$("#miniMapHover").css("left",leftPos + "px");
	$("#miniMapHover").css("top",topPos+10 + "px");
	$("#miniMapHover").css("z-index",1003);

	// display the temp loading screen before we head into the jquery request
	var buildingName = '<div class="menuBox"><div class="title" >' + rackName + '</div>' +
		'<div class="content" id="rackDetails"><center><img src="images/loading_light.gif" alt="loading" /></center></div></div>';
	$("#miniMapHover").html(buildingName);	

	// pull in the rack summary page
	$(".menuBox .content#rackDetails").load("rackhandler.php?action=mouseover&rackID="+rackID);
    }
    else
    {
        $("#miniMapHover").html("");
        $("#miniMapHover").hide();
    }
});

// mouseover for devices within the minimap
$("#miniMap .device").live('mouseover mouseout',function(ev)
{
    if(ev.type=="mouseover")
    {
	var leftPos = $(this).position().left-225;
	var topPos = $(this).position().top+25;

	// position the popup
	$("#miniMapHover").show();
	$("#miniMapHover").css("position","absolute");
	$("#miniMapHover").css("left",leftPos + "px");
	$("#miniMapHover").css("top",topPos+10 + "px");
	$("#miniMapHover").css("z-index",1003);

        var menu = '<div class="popupMenu" > ' +
          '<div class="title"> ' +
            '<table><thead> ' +
                '<tr><th align="left" >' + $(this).find("span").text() + '</th></tr> ' +
            '</thead></table> ' +
            '</div> ' +
        '</div>';
	$("#miniMapHover").html(menu);
    }
    else
    {
        $("#miniMapHover").html("");
        $("#miniMapHover").hide();
    }
});

    $("#miniMap .device").live("click", function( ev )
    {
        // hide the mouseover
        $("#miniMapHover").hide();

        // Find the offset of the minimap
	var xPos = $(this).position().left+$("#sideNavigation").position().left-275;
	var yPos = $(this).position().top+50;
        // set the flag used to disable the mouseover
        var deviceID = $(this).find("span").attr("class").replace('device','');
        // if the menu has a chance of flowing off the page, reposition it higher
        if((ev.pageY+170) > $(window).height())
            $("#menus").css("top",(ev.pageY-170));

        $("#menus").show();
        $("#menus").css("position","absolute");
        $("#menus").css("display","block");
        $("#menus").css("left",xPos);
        $("#menus").css("top",yPos);
        $("#menus").css("z-index","1000");

        var menu = "";
        //var deviceQuery =
        $.ajax({
            url: "viewDevice.php",
            data: "action=deviceMenu&deviceID=" + deviceID,
            success: function(deviceInfo)
            {
                //menu=deviceInfo;
                $("#menus").html(deviceInfo);

                // Should no longer be needed here
                //$('#displayPortal').mapbox({pan: true, zoom: false,defaultX:0,defaultY:0});
            }
                // testing plugin to allow drag selection of ports for flooding
                //$(".devicePorts").selectable();
        });
    });

function loadBuildings2()
{
    $("#miniList").show();
    $("#viewPortal").hide();

    query = "action=buildingsLevel";

    $.post("handler.php",query,function(result)
    {   
        var content = "<ul class='miniMapList' >";
        $.each(result['buildings'], function(i, val)
        {
            content += "<li class='building' onclick='loadFloors2(" + val.buildingID + ");'>" + val.name + "</li>";
        });
        $.each(result['cabinets'], function(i, val)
        {
            content += "<li class='cabinet' onclick='loadCabinet(\"" + val.cabinetID + "\",\"0\",\"site\");'>" + val.name + "</li>";
        });
        content += "</ul>";

        $("#miniList").html(content);
        $("#navigate").html("");
        $("#currentArea").html("Select a building/cabinet");
        $("#currentArea").show();
    },"json");
}

function loadFloors2(buildingID)
{
    $("#miniList").show();
    $("#viewPortal").hide();
	
    var query = "action=floors&buildingID=" + buildingID;
    $.post("handler.php",query,function(result)
    {
        
        var content = "<ul class='miniMapList' >";
        $.each(result, function(i, val)
        {
            content += "<li class='floor' onclick='loadRooms2(" + result[i].floorID + ");'>level: " + result[i].name + "</li>";
        });

        content += "</ul>";
        $("#miniList").html(content);
		
    },"json");

    var query = 'action=buildingInfo&buildingID=' + buildingID;
    $.post("handler.php",query,function(result)
    {
        $("#currentArea").html("Building: <strong>"+result.name+"</strong>");
        $("#currentArea").show();
    },"json");

    //$("#backTable").show();
    $("#navigate").unbind('click');
    $('#navigate').bind("click", function(ev)
    {
        loadBuildings2();
    });
    $('#navigate').html("&laquo; Change Building");

}

function loadRooms2(floorID)
{
    $("#miniList").hide();
    $("#viewPortal").show();

    // the map needs to be absolute positioned
    // given in IE we cant align right work out the alignment based on browser width
    var pageWidth=$(document).width();
    $("#navigation").css("left",pageWidth-353+"px");
    // ensure on resize we recalculate position
    $(window).resize(function()
    {
        var pageWidth=$(document).width();
        $("#navigation").css("left",pageWidth-353+"px");
    });

    $("#miniMap").html("");
    $("#navigation").removeClass("padding");
    if ($("#miniMap").hasClass("room") == false)
        $("#miniMap").addClass("room")
	
    var query = "action=layoutItems&parentType=Floor&parentID=" + floorID;
    $.post("handler.php",query,function(result)
    {   
        $("#miniMap").css("height","1500px");
        $("#miniMap").css("width","2000px");
        $.each(result, function(i, val)
        {
            var positionX = result[i].posX / 2;
            var positionY = result[i].posY / 2;

            var content = "";
            if (result[i].itemType == "Door")
            {
                content = "<div id='layoutItemID" + result[i].itemID + "' style='background-image:url(images/icons/door.gif);display:block;width: " + (result[i].width / 2) + "px; height: " + (result[i].height / 2) + "px;position:absolute; top: " + positionY + "px; left: " + positionX + "px'>";
                content += "<div></div></div>";
            }
            else
            {
                content = "<div onclick='loadRoom2(" + result[i].itemID + ")' id='layoutItemID" + result[i].itemID + "' style='background-color: #CCC;cursor:pointer;display:block;width: " + (result[i].width / 2) + "px; height: " + (result[i].height / 2) + "px;position:absolute; top: " + positionY + "px; left: " + positionX + "px' class='room" + result[i].itemID + " color" + result[i].itemID + "'>";
                content += "<div style='width: 100%; height: 100%; overflow:hidden;'>";
                content += "<div style='float:left;padding:10px;font-weight:bold;font-size:8px;'>" + result[i].itemName + "</div>";
                content += "</div></div>";
            }
                $("#miniMap").append(content);
        });
    },"json");
	
	
    var query = 'action=floorInfo&floorID=' + floorID;
    $.post("handler.php",query,function(result)
    {
        $("#currentArea").html("Floor: <strong>" + result.name+"</strong>");
        $("#currentArea").show();

        $("#navigate").unbind('click');
        $('#navigate').bind("click", function(ev)
        {
            loadFloors2(result.buildingID);
        });

        $('#navigate').html("&laquo; Change Floor<span id='minimapEdit' ><a href='floorlayout.php?floor=" + floorID + "' >Edit layout</a></span>")
    },"json");	
}

function loadRoom2(roomID)
{
	$("#miniList").hide();
	$("#viewPortal").show();
	$("#miniMap").html("");
	$("#navigation").removeClass("padding");
	if ($("#miniMap").hasClass("room") == false)
            $("#miniMap").addClass("room")
	
	var query = "action=layoutItems&parentType=Room&parentID=" + roomID;
        $("#miniMap").css("height","1500px");
        $("#miniMap").css("width","2000px");
        $("#miniMap").html("Loading...");
    
	$.post("handler.php",query,function(result)
	{	
            $.each(result, function(i, val)
            {
                //alert(result[i].itemType);
                var content = "<div";
                if(result[i].itemType == "rack1")
                    content += " onclick='loadDevices2(" + result[i].itemID + ")' ";

                if(result[i].itemType == "device")
                    content += " ";

                content += " id='layoutItemID" + result[i].layoutItemID + "' style='";
			//content += " id='rackID" + result[i].itemID + "' style='";
                if (result[i].itemType == "rack1")
                    content += "cursor:pointer; "
                
                content += "width: " + (result[i].width / 2) + "px; height: " + (result[i].height / 2) + "px;position:absolute; top: " + (result[i].posY / 2) + "px; left: " + (result[i].posX / 2) + "px;z-index:"+result[i].zindex+"' \
                class=' " + result[i].itemType + " " + result[i].itemName + " small ";
                
                if (result[i].itemType == "rack1")
                    content += " miniMapRack'><span id='rackID" + result[i].itemID + "'>" + result[i].itemName + "</span>";
                else if (result[i].itemType == "device")
                    content += "'><span class='device" + result[i].itemID + "'>" + result[i].itemName + "</span>";
                else
                    content += "'>";
                
                content += "</div>";
			
                $("#miniMap").append(content);
			
                // if any rotation is set for the object associate it with the drawn item
                if(result[i].rotation > 0)
                    $('#layoutItemID' + result[i].layoutItemID).easyRotate({ degrees: result[i].rotation});
		});

                        /*
                         * REMOVED as we shouldn't show cable trays from this section
                         * if (result[i].itemType != "cabletray1" && result[i].itemType != "cabletray2")
			{
                            var content = "<div";
                            if (result[i].itemType == "rack1")
                                    content += " title='" + result[i].itemName + "' onclick='loadDevices2(" + result[i].itemID + ")'";
                            content += " id='layoutItemID" + result[i].layoutItemID + "' style='";
                            if (result[i].itemType == "rack1")
                                    content += "cursor:pointer;";
                            content += "background-color:#000; display:block; width: " + (result[i].width / 2) + "px; height: " + (result[i].height / 2) + "px;position:absolute; top: " + positionY + "px; left: " + positionX + "px' class='small deletable " + result[i].itemType + " " + result[i].itemName + "'>";
                            content += "</div>";
			}
			$("#miniMap").append(content);*/
	},"json");
	

	$.post("handler.php",'action=roomInfo&roomID='+roomID,function(result)
	{		
		$("#currentArea").html(result.name);
		$("#currentArea").show();
		$("#navigate").unbind('click');
		$('#navigate').bind("click", function(ev)
		{
                    loadRooms2(result.floorID);
		});
		$('#navigate').html("&laquo; Change Room<span id='minimapEdit' ><a href='roomlayout.php?room=" + roomID + "' >Edit layout</a></span>")
	},"json");	
}

function highlightRack(rackID)
{
    // TODO requires fix to remove timeout approach
    setTimeout(function(){
        $("#rackID"+rackID).parent().addClass('selected');
    },500);
}

function loadDevices2(rackID)
{
    loadRack(rackID);
}

/*function loadCabinetontpPage(cabinetID)
{
    alert("We are loading the cabinet now\nCabinetID:"+cabinetID);
    $.ajax({
            type: "GET",
            url: "racks.php",
            data: "action=loadCabinet&rackID="+cabinetID,
            async: false,
            context: this,
            success: function(cabinetCode)
            {
                $("#rackHolderData").append("<td valign='bottom' id='cabinetCell_"+cabinetID+"'>" + cabinetCode + "</td>");
                makedraggable("cabinettbl_" + cabinetID);
            }
    });
    return false;
}*/

// parent values used to determine back button
function loadCabinet(cabinetID,parentID,parentType)
{
    $("#miniList").show();
    $("#viewPortal").hide();

    // return array of [cabinet][] and [racks][0-4]...
    $.post("handler.php","action=getCabinet&cabinetID="+cabinetID,function(result)
    {
        var content = "<ul class='miniMapList' >";
        /*
        // if more than one rack was shown allow loading all at once
        if(result['racks'].length>1)
            content += "<li>Load all racks</li>";*/

        // loop over each and make a link
        $.each(result['racks'], function(i, val) {
            content += "<li class='cabinet' onclick='loadRack(" + val.rackID + ");'>" + val.name + "</li>";
        });
        content += "</ul>";

        // if none we're found show a clean error'
        if(result['racks'].length==0)
            $("#miniList").html("<ul class='miniMapList' ><li>No items found in cabinet</li></ul>");
        else
            $("#miniList").html(content);
            
        
        $("#currentArea").html("Select a building/cabinet");
        $("#currentArea").show();

        $("#navigate").unbind('click');
        // configure the back button as we may be visiting a cabinet from different parents
        if(parentType=="site")
        {
            $('#navigate').bind("click", function(ev) { loadBuildings2();});
            $('#navigate').html("&laquo; Back to Buildings");
        }        
    },"json");
}