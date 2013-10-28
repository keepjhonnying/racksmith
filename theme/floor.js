// Get Information about Building Locations from Database using AJAX
$(document).ready(function()
{
	var itemsDrawn=0;
	var content;
	
	var query = "action=layoutItems&parentType=Floor&parentID=" + floorID;
	$.post("handler.php",query,function(result)
	{	
		$.each(result, function(i, val) 
		{	
                    if (result[i].itemType == "Door")
                    {
                            content = "<div id='layoutItemID" + result[i].layoutItemID + "' style='background-image:url(images/icons/door.gif);display:block;width: " + result[i].width + "px; height: " + result[i].height + "px;position:absolute; top: " + result[i].posY + "px; left: " + result[i].posX + "px' class='deletable resizable draggable'>";
                            content += "";
                            content += "<div></div></div>";
                    }
                    else
                    {
                            //content = "<div id='layoutItemID" + result[i].layoutItemID + "' style='display:block;width: " + result[i].width + "px; height: " + result[i].height + "px;position:absolute; top: " + result[i].posY + "px; left: " + result[i].posX + "px' class='floortile deletable room" + result[i].itemID + " color" + result[i].itemID + " draggable resizable'>";
                            content = "<div onclick='location.href=\"room.php?room=" + result[i].itemID + "\"' id='layoutItemID" + result[i].layoutItemID + "' style='cursor:pointer;display:block;width: " + result[i].width + "px; height: " + result[i].height + "px;position:absolute; top: " + result[i].posY + "px; left: " + result[i].posX + "px;margin:0px;' class='room" + result[i].itemID + " color" + result[i].itemID + " floortile'>";
                            content += "<div style='width: 100%; height: 100%; overflow:hidden;margin:0px;'>";
                            content += "<div style='float:left;padding:10px;font-weight:bold;'>" + result[i].itemName + "</div>";
                            content += "</div></div>";
                    }

                    $(".mapContent").append(content);
                    var itemsDrawn=1;
		});
		
		// If the room is empty print a helper screen
		if($(".mapContent").is(":empty"))
		{
			// hide the pannable map, the page is gauranteed to refresh when returning from layout page
			$(".mapContent").hide();
			topPadding = ($('#viewPortal').height()/2)-80;
			$("#viewPortal").css("background-color","#efefef");

			$('#viewPortal').append("<div class='module' style='position: relative;width: 310px;top:" + topPadding + "px;height: 80px;margin:0 auto; \
			'> <strong>Empty Floor</strong>\
			<p><img src='images/icons/dev-small.png' style='float: left;padding:3px;'/>This floor has no rooms, to start working<br/>\
			<a href='floorlayout.php?floor=" + floorID +"' >Click here</a></p> \
			</div>");
		}
	},"json");
});