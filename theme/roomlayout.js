$(document).ready(function()
{
    $(".roomInventory").accordion({ collapsible: true,autoHeight: false, active: 'none'});
   

    // Accepts toolbar
    // 20101104 - adjustment to make droppable the portal and not entire map, was causing issues dropping items on the hidden map
    $("#viewPortal").droppable({tolerance: 'fit', drop: function(ev, ui)
    {
      var posY=(ui.offset.top-$(".mapContent").offset().top);
      var posX=(ui.offset.left-$(".mapContent").offset().left);
    // If its a rack a form needs to be filled in first.
    if(ui.draggable.hasClass("newRack"))
    {
        $("#posx").val(posX);
        $("#posy").val(posY);
        $.openDOMWindow({borderColor: '#3b4c50', borderSize: 2,windowPadding: 0,width:'750',height: '470',overlayOpacity: '30',windowSourceID:'#createRack',modal:1});
    }
    else if(ui.draggable.hasClass("newDevice"))
    {
        var templateID = $(ui.draggable).attr("href").replace(/#/g,'');
        
        $.openDOMWindow({ fixedWindowY: 50, borderColor: '#3b4c50', borderSize: 2,width:'950',height: ($(window).height()-100),overlayOpacity: '30',windowSource:'ajax',
        windowSourceURL: 'deviceHandler.php?templateID='+templateID+"&parentType=room&parentID="+roomID+"&posx="+posX+"&posy="+posY,modal:1,windowPadding: 0});
        return false;
    }
    else if (ui.draggable.hasClass("inventoryItem"))
    {
        // Move the device from inventory to the page
        var itemID=ui.draggable.attr("id").replace(/[^0-9]/g, '');
        var rackName = ui.draggable.text();
        var query;
        query = "action=insertLayoutItem&";
        query += "height=32&";
        query += "width=32&";
        query += "itemID=" + itemID + "&";
        query += "itemName=" + rackName + "&";
        query += "itemType=rack1&";
        query += "parentName=&";
        query += "parentType=Room&";
        query += "parentID=" + roomID +"&";
        query += "posy=" + posY + "&";
        query += "posx=" + posX;

        $.post("handler.php",query,function(layoutItemID)
        {
            $.post("handler.php","action=updateFloorItemParent&itemID="+itemID+"&parentID="+roomID+"&parentType=room",function(updated)
            {
                if(updated==1)
                {
                    var content;
                    content = "<div title='" + rackName + "' id='layoutItemID" + layoutItemID + "' style='width:32px;height:32px;z-index:999; position:absolute; top: " + posY + "px; left: " + posX + "px' class='deletable rack1 draggable'>";
                    content += "<img style='float:left;cursor:pointer;' src='images/icons/delete_small.gif' />";
                    content += "<span>"+rackName+"</span>";
                    content += "</div>";

                    $(".rackID"+itemID).closest("li").fadeOut('fast',function() { $(this).remove(); });

                    $(".mapContent").append(content);
                    makeDraggable();
                }
            });
        });
    }
    else if((ui.draggable.hasClass("toolbaritem")) && (ui.draggable.hasClass("inventoryDevice")))
    {
        var itemID=ui.draggable.attr("id").replace(/[^0-9]/g, '');
        var itemName=ui.draggable.text();
        
        var query;
        query = "action=insertLayoutItem&";
        query += "height=32&";
        query += "width=32&";
        query += "itemID=" + itemID + "&";
        query += "itemName=" + itemName + "&";
        query += "itemType=device&";
        query += "parentName=&";
        query += "parentType=Room&";
        query += "parentID=" + roomID +"&";
        query += "posy=" + posY + "&";
        query += "posx=" + posX;
        // insert the floor item
        $.post("handler.php",query,function(layoutItemID)
        {
            // update the device parent so it's no longer in the inventory
            $.post("handler.php","action=updateDeviceParent&itemID="+itemID+"&parentID="+roomID+"&parentType=room",function(updated)
            {
                if(updated==1)
                {
                    // remove the menu item before we write the new object to the page
                    $(".toolbaritem#device"+itemID).remove();

                    // write the new layout item to the map
                    var content;
                    content = "<div title='" + itemName + "' id='layoutItemID"+layoutItemID+"' style='width:32px;height:32px;z-index:1; position:absolute; top: " + posY + "px; left: " + posX + "px' class='layoutItem deletable device draggable ui-draggable'>";
                    content += "<img style='float:left;cursor:pointer;' src='images/icons/delete_small.gif' />";
                    content += "<span>"+itemName+"</span>";
                    content += "</div>";

                    $(".mapContent").append(content);
                    makeDraggable();
                }
            });
        });
    }
    else if(!ui.draggable.hasClass("deletable"))
    {
        // Create a new non rack item
        var itemID=ui.draggable.attr("id").replace(/[^0-9]/g, '');
        var tileName = ui.draggable.attr('title');

        var extraClasses="";
        if(ui.draggable.attr("id").indexOf("floortile") != -1 || ui.draggable.attr("id").indexOf("cabletray") != -1 || ui.draggable.attr("id").indexOf("Aisle") != -1)
            extraClasses += " resizable";
       if(ui.draggable.attr("id").indexOf("cabletray") != -1)
            extraClasses += " rotation layoutItem";
       if(ui.draggable.attr("id").indexOf("Aisle") != -1)
            extraClasses += " rotation";
        var query;
        query = "action=insertLayoutItem&";
        query += "height=32&";
        query += "width=32&";
        query += "itemID=0&";
        query += "itemName=" + tileName + "&";
        query += "itemType=" + ui.draggable.attr("id") + "&";
        query += "parentName=&";
        query += "parentType=Room&";
        query += "parentID=" + roomID +"&";
        query += "posy=" + posY + "&";
        query += "posx=" + posX;

        $.post("handler.php",query,function(layoutItemID)
        {
            var content;
            content = "<div title='" + tileName + "' id='layoutItemID" + layoutItemID + "' style='z-index:5;width: 32px; height: 32px;position:absolute; top: " + posY + "px; left: " + posX+ "px' class='deletable " + ui.draggable.attr("class").replace("toolbaritem ", "").replace(" ui-draggable", "") + " "+ extraClasses + " " + ui.draggable.attr("id") + " draggable'>";
            content += "<img style='float:left;cursor:pointer;' src='images/icons/delete_small.gif' />";
            content += "</div>";

            $(".mapContent").append(content);
            makeDraggable();
        });
        }
    }});


    // Draw items from the database
    $.post("handler.php", "action=layoutItems&parentType=Room&parentID=" + roomID,function(result)
    {
        $.each(result, function(i, val) {
            var content;
            content = "<div \
                title='" + result[i].itemName + "' \
                id='layoutItemID" + result[i].layoutItemID + "' \
                style='width: " + result[i].width + "px; height: " + result[i].height + "px;z-index:"+result[i].zindex+";position:absolute; top: " + result[i].posY + "px; left: " + result[i].posX + "px' \n\
                class='layoutItem deletable " + result[i].itemType;

                if(result[i].itemType.indexOf("cabletray") != -1)
                    content += " rotation ";
				if(result[i].itemType.indexOf("floortile") != -1)
					content += " floortile ";
				if(result[i].itemType.indexOf("Aisle") != -1 || result[i].itemType.indexOf("rack") != -1)
            		content += " rotation ";
                if($('#lockfloortiles').is(":checked") && result[i].itemType.indexOf("floortile") != -1)
                    content += " ";
                else
                    content += " draggable ";
                if(result[i].itemType.indexOf("floortile") != -1 || result[i].itemType.indexOf("cabletray") != -1 || result[i].itemType.indexOf("Aisle") != -1)
                    content += " resizable";

            content += " '>";

            if (result[i].itemType == "device" || result[i].itemType == "rack1")
                content += "<span id='deviceID" + result[i].itemID + "'>" + result[i].itemName + "</span>";

            content +="<img style='float:left;cursor:pointer;' src='images/icons/delete_small.gif' />";
            content += "</div>";

            $(".mapContent").append(content);

            // if any rotation is set for the object associate it with the drawn item
            if(result[i].rotation > 0)
                $('#layoutItemID' + result[i].layoutItemID).easyRotate({degrees: result[i].rotation});
        });
        makeDraggable();
    },"json");
	

	// Submittion of the Create Rack Form
	$(".rackCreateSubmit").click(function() 
	{
            var name=$("#createRackForm form #name").attr("value");
            var room=$("#createRackForm form #room").val();
            var data=$("#createRackForm form").serialize();
		
            $.getJSON("racks.php?action=create&" + data,function(data)
            {
                $.each(data, function(item,val)
                {
                    switch(val)
                    {
                        case "error_name":
                            $("label[for=name]").closest("td").addClass("formError");
                            $("#createRackForm .error").html("<strong>Please check the name</strong>");
                            break;
                        case "error_RU":
                            $("label[for=RU]").closest("td").addClass("formError");
                            $("#createRackForm .error").html("<strong>Please check the highlighted values</strong>");
                            break;
                        case "error_model":
                            $("label[for=model]").closest("td").addClass("formError");
                            $("#createRackForm .error").html("<strong>Please check the highlighted values</strong>");
                            break;
                        case "created":
                            var id=data[1];

                            var query;
                            query = "action=insertLayoutItem&";
                            query += "height=" + $("#depth").val() / 18.75 + "&";
                            query += "width=32&";
                            query += "itemID=" + id + "&";
                            query += "itemName=" + name + "&";
                            query += "zindex=10&";
                            query += "itemType=rack1&";
                            query += "parentName=&";
                            query += "parentType=Room&";
                            query += "parentID=" + roomID +"&";
                            query += "posy=" + $("#posy").val() + "&";
                            query += "posx=" + $("#posx").val()

                            $.post("handler.php",query,function(layoutItemID)
                            {
                                var content;
                                content = "<div title='" + name + "' id='layoutItemID" + layoutItemID + "' style='z-index: 1000;width: 32px; height: " + $("#depth").val() / 18.75 + "px; position:absolute; top: " + $("#posy").val() + "px; left: " + $("#posx").val() + "px' class='layoutItem ";
                                content += " rack1 ";
                                content += "deletable draggable rotation ";
                                //content += "resizable";
                                content += "'>";
                                content += "<img style='float:left;cursor:pointer;' src='images/icons/delete_small.gif' />";
                                content += "</div>";

                                $(".mapContent").append(content);
                                makeDraggable();
                            });

                            // remove the load links from all the menus and reappply to cover the new link
                            $("#createRackForm .error").html("");
                            $(".success").show();
                            setTimeout("$('.closeDOMWindow').click();$('.success').hide(); ",1000);
                            break;
                            };
			});
		});
		
		
		return false;
	});

	// Clears Errors if closed
	$(".closeDOMWindow").click(function() 
	{
            $('#createRackForm .error').html('');
            $('.formError').removeClass('formError');
	});	
	
	
	
	$("#lockfloortiles").click(function() 
	{
            if($(".mapContent .floortile").hasClass("draggable") || $(".mapContent .floortile1").hasClass("draggable"))
            {
                lockFloorTiles();
                $.post("handler.php","action=lockFloorTiles");
            }
            else
            {
                unlockFloorTiles();
                $.post("handler.php","action=unlockFloorTiles");
            }
	});
	

	$("#floortiles").click(function() 
	{
            $(".mapContent .floortile").toggle();
	});	
	$("#racks").click(function() 
	{	
            $(".mapContent .rack1").toggle();
	});	
	$("#cabletrays").click(function() 
	{
            $(".mapContent .cabletray1").toggle();
            $(".mapContent .cabletray2").toggle();
	});


        $("body").live('click',function(ev)
        {
            $("#menus").html("");
            $("#menus").hide();
        });


        $(".deletable img").live('click',function()
        {
            var layoutItemID=$(this).parent().attr("id").replace(/[^0-9]/g, '');
            if($("#layoutItemID"+layoutItemID).hasClass("rack1"))
            {
                $.ajax({
                    type: 'POST',
                    url: "handler.php",
                    data: "action=moveFloorRackToInventory&layoutItemID="+layoutItemID,
                    dataType: "json",
                    success: function(move)
                    {
                        var newInventoryItem='<li><div class="inventoryItem rack rackID'+move['rackID']+'" id="rack'+move['rackID']+'" >'+move['name']+'</div></li>';
                        $("#floorPageInventory ul").append(newInventoryItem);

                        makeDraggable();

                        var removeQuery="action=deleteLayoutItem&layoutItemID=" + layoutItemID;
                        $.post("handler.php", removeQuery,function()
                        {
                            $("#layoutItemID"+layoutItemID).remove();
                        });
                    }
                });
            }

            else if($("#layoutItemID"+layoutItemID).hasClass("device"))
            {
                $.ajax({
                    type: 'POST',
                    url: "handler.php",
                    data: "action=moveFloorDeviceToInventory&layoutItemID="+layoutItemID,
                    dataType: "json",
                    success: function(move)
                    {
                        var removeQuery="action=deleteLayoutItem&layoutItemID=" + layoutItemID;
                        $.post("handler.php", removeQuery,function()
                        {
                            var newItem='<li class="toolbaritem item inventoryDevice ui-draggable" id="device'+move.itemID+'">'+move.name+'</li>';
                            $("#floorPageInventory.inventory ul").append(newItem);
                            $("#layoutItemID"+layoutItemID).remove();

                            // as we just added an item to the toolbar we should refresh the drag binds
                            makeDraggable();
                        });
                    }
                });
            }
            
            else
            {
                var removeQuery="action=deleteLayoutItem&layoutItemID=" + layoutItemID;
                $.post("handler.php", removeQuery,function()
                {
                    $("#layoutItemID"+layoutItemID).remove();
                });
            }


            return false;
        });
	
	// the popup window for rotating items
	$(".rotation:not(img)").live('click',function(ev)
	{
		/* Position of current building, used to position hover off the side */
		leftPos = ev.pageX;
		topPos = ev.pageY;

		// if we're about to span off the page (within 300 of the edge, display to the left of the mouse
		if(($(document).width() - ev.pageX) < 100)
			width=ev.pageX-120;
		else
			width=ev.pageX;

		$("#menus").css("position","absolute");
		$("#menus").css("left",leftPos + "px");
		$("#menus").css("top",topPos + "px");
		$("#menus").css("z-index",999);
		var itemID=$(this).attr("id").replace("layoutItemID","");

		var buildingName = '	<div class="menuBox" style="width: 52px"><div class="content" style="padding: 1px">' +
		'<a href="#' + itemID + '" class="rotateControl" id="upLeft" ><strong><img src="images/icons/arrows/up_left.png" border="0"/></strong></a>' +
		'<a href="#' + itemID + '" class="rotateControl" id="up" ><strong><img src="images/icons/arrows/up.png" border="0"/></strong></a>' +
		'<a href="#' + itemID + '" class="rotateControl" id="upRight" ><strong><img src="images/icons/arrows/up_right.png" border="0"/></strong></a><br/>' +
		'<a href="#' + itemID + '" class="rotateControl" id="left" ><strong><img src="images/icons/arrows/left.png" border="0" /></strong></a>' +
		'<a><strong><img src="images/icons/arrows/blank.png" border="0"/></strong></a>' +
		'<a href="#' + itemID + '" class="rotateControl" id="right" ><strong><img src="images/icons/arrows/right.png" border="0"/></strong></a><br/>' +
		'<a href="#' + itemID + '" class="rotateControl" id="downLeft" ><strong><img src="images/icons/arrows/down_left.png" border="0"/></strong></a>' +
		'<a href="#' + itemID + '" class="rotateControl" id="down" ><strong><img src="images/icons/arrows/down.png" border="0"/></strong></a>' +
		'<a href="#' + itemID + '" class="rotateControl" id="downRight" ><strong><img src="images/icons/arrows/down_right.png" border="0"/></strong></a>' +
		'</div></div>';
		
		$("#menus").html(buildingName);
		$("#menus").slideDown('fast');
		  
		$(".closeContextMenu").click(function () 
		{
                    $("#menus").html("");
                    $("#menus").hide();
		}); 
		return false;
	});
	
	// called when a rotate action is clicked, basically pick the movement and position the item
	// we don't consider the existing orientation
	$(".rotateControl").live('click',function (ev) {
		var newAngle=0;
		
		switch($(this).attr("id"))
		{
			case "upLeft":
				newAngle=315;
				break;
			case "upRight":
				newAngle=45;
				break;
			case "right":
				newAngle=90;
				break;
			case "left":
				newAngle=270;
				break;
			case "downRight":
				newAngle=135;
				break;
			case "down":
				newAngle=180;
				break;
			case "downLeft":
				newAngle=225;
				break;
		}
		
		var query = "";
		query += "action=rotateFloorItem";
		query += "&layoutItemID=" + $(this).attr("href").replace("#","");
		query += "&newAngle=" + newAngle;
		var itemID = $(this).attr("href").replace("#","");

		$.post("handler.php",query,function(newRotation)
		{
                    $('#layoutItemID' + itemID).easyRotate({degrees: newRotation});
		});
	});




    $('.inventorySearchField').keyup(function () {
            var query = $(this).val().toLowerCase();// find the search query
            $(this).parent().find(".inventoryClearSearch").show();
            // loop over each of the entries in the inventory UL
            $(this).closest('ul').children("li").find('div').each(function()
            {
                // check if its a searchable item
                if($(this).hasClass("inventoryItem") || $(this).hasClass("stockItem"))
                {
                    var sysName = $(this).find('span').html();
                    if(sysName.toLowerCase().indexOf(query) == -1)
                        $(this).parent().hide();
                    else
                        $(this).parent().show();
                }
            });
    });
    //clear the closest search field and show all invent items
    $('.inventoryClearSearch').live('click',function ()
    {
        $(this).parent().find(".inventorySearchField").val(" ");
        $(this).closest('ul').children("li").show();
        $(this).hide();
    });

    $(".layoutItem").live('mouseover mouseout',function(ev)
    {
            if(ev.type=='mouseover')
            {
                if($(this).find("span").length!=-1)
                    var itemName=$(this).find("span").html();
                else
                    var itemName="";
                
                if($(this).hasClass("rack1"))
                {
                    // get the name of current item to display without JSON request
                    
                    $("#highlightedName").html(itemName);
                    $("#highlightedDevType").html("Rack");
                    
                    // pull in the rack summary page
                    //var rackID = $(this).find('span').attr("id").replace('rackID','');
                    //$(".menuBox .content#rackDetails").load("rackhandler.php?action=mouseover&rackID="+rackID);
                }
                else if($(this).hasClass("device"))
                {
                    $("#highlightedName").html(itemName);
                    $("#highlightedDevType").html("Device");
                }
                else if($(this).hasClass("cabletray1") || $(this).hasClass("cabletray2") || $(this).hasClass("cabletray3"))
                {
                    $("#highlightedName").html("");
                    $("#highlightedDevType").html("Cable Tray");
                }
            }
            else
            {
            }
    });

    
});

// Makes items Resizable Draggable and Deleteable
// we should move to live where possible
function makeDraggable()
{
    $(".draggable").draggable({containment: '.mapContent',stop: function(event, ui)
    {
        var itemID=$(this).attr("id").replace("layoutItemID","");
        var query = "";
        query += "action=updateLayoutItemPosition&";
        query += "layoutItemID=" + itemID + "&";
        query += "posy=" + (ui.offset.top-$(this).parent().offset().top) + "&";
        query += "posx=" + (ui.offset.left-$(this).parent().offset().left);
        $.post("handler.php", query);
    }});

    $('.mapContent .resizable').resizable({handles: 'e, s, se',stop: function(event, ui)
    {
        var query = "";
        query += "action=updateLayoutItemSize&";
        query += "layoutItemID=" + $(this).attr("id").replace("layoutItemID","") + "&";
        query += "width=" + ui.size.width + "&";
        query += "height=" + ui.size.height;

        $.post("handler.php", query,function(roomitemID){ });
    },
    resize: function(event, ui) {
        tileWidth=Math.ceil((ui.size.width/32)*100)/100;
        tileHeight=Math.ceil((ui.size.height/32)*100)/100;
        $("#resizeWidth").html(tileWidth);
        $("#resizeHeight").html(tileHeight);
    }});

    // Makes toolbar draggable
    $(".toolbaritem,.inventoryItem").draggable({scroll: false,appendTo: 'body',helper: 'clone',opacity: 0.35, zIndex:150});

        // taken from rack_drag.js allows for searching of inventory
    $('.inventorySearchField').keyup(function () {
            var query = $(this).val().toLowerCase();// find the search query
            $(this).parent().find(".inventoryClearSearch").show();
            // loop over each of the entries in the inventory UL
            $(this).closest('ul').children("li").find('div').each(function()
            {
                // check if its a searchable item
                if($(this).hasClass("newDevice") || $(this).hasClass("inventoryDevice") || $(this).hasClass("rack"))
                {
                    var sysName = $(this).find('span').html();
                    if(sysName.toLowerCase().indexOf(query) == -1)
                        $(this).parent().hide();
                    else
                        $(this).parent().show();
                }
            });
    });

    //clear the closest search field and show all invent items
    $('.inventoryClearSearch').click(function ()
    {
            $(this).parent().find(".inventorySearchField").val(" ");
            $(this).closest('ul').children("li").find('div').show();
            $(this).hide();
    });
}
        
// toggle the drag action with floor tiles to help with panning
function lockFloorTiles() {
	$(".mapContent .floortile, .mapContent .floortile1").draggable("destroy");
	// remove the class so we dont rebind upon makedraggable()
	$(".mapContent .floortile, .mapContent .floortile1").removeClass("draggable");
	$(".mapContent .floortile, .mapContent .floortile1").removeClass("ui-draggable");
	makeDraggable(); 
}
function unlockFloorTiles() {
	// add the classes back
	$(".mapContent .floortile, .mapContent .floortile1").addClass("draggable");
	$(".mapContent .floortile, .mapContent .floortile1").addClass("ui-draggable");
	makeDraggable(); 
}