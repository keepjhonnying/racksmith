// Get Information about Building Locations from Database using AJAX
$(document).ready(function()
{
    var drawnCount=0;
    var deviceMenu=false;
    var query = "action=layoutItems&parentType=Room&parentID=" + roomID;
    $.post("handler.php",query,function(result)
    {
        $.each(result, function(i, val)
        {
            var content = "<div";

            if (result[i].itemType == "rack1")
                content += " onclick='location.href=\"racks.php?rackID=" + result[i].itemID + "&roomID=" + result[i].parentID + "\"' ";

            content += " id='layoutItemID" + result[i].layoutItemID + "' style='";

            if (result[i].itemType == "rack1" || result[i].itemType == "device")
                content += "cursor:pointer; "

            content += "width: " + result[i].width + "px; height: " + result[i].height + "px;position:absolute; top: " + result[i].posY + "px; left: " + result[i].posX + "px;z-index:"+result[i].zindex+"' \
            class='layoutItem " + result[i].itemType + " '>";

            if (result[i].itemType == "rack1")
                content += "<span id='rackID" + result[i].itemID + "'>" + result[i].itemName + "</span>";
            if (result[i].itemType == "device")
                content += "<span id='deviceID" + result[i].itemID + "'>" + result[i].itemName + "</span>";
            content += "</div>";

            $(".mapContent").append(content);
            drawnCount++;

            // if any rotation is set for the object associate it with the drawn item
            if(result[i].rotation > 0)
                $('#layoutItemID' + result[i].layoutItemID).easyRotate({ degrees: result[i].rotation});
        });
        if(drawnCount==0)
        {
            $("#viewPortal").html("<div class='emptyMap' >This room is empty<br/><a href='roomlayout.php?room="+roomID+"' >Click here to add items</a></div>");
        }
    },"json");
	
	
    $(".layoutItem").live('mouseover mouseout',function(ev)
    {
        if(!deviceMenu)
        {
            if(ev.type=='mouseover')
            {
                // position item to the top left of the rack
                leftPos = $(this).offset().left + $(this).width()+25;
                topPos = $(this).offset().top;

                // position the popup
                $("#menus").show();
                $("#menus").css("position","absolute");
                $("#menus").css("left",leftPos + "px");
                $("#menus").css("top",topPos + "px");
                $("#menus").css("z-index",111);

                if($(this).hasClass("rack1"))
                {
                    // get the name of current item to display without JSON request
                    var rackName = $(this).html();
                    var rackID = $(this).find('span').attr("id").replace('rackID','');
                    // display the temp loading screen before we head into the jquery request
                    var buildingName = '<div class="menuBox"><div class="title" >' + rackName + '</div>' +
                    '<div class="content" id="rackDetails"><center><img src="images/loading_light.gif" alt="loading" /></center></div></div>';
                    $("#menus").html(buildingName);

                    // pull in the rack summary page
                    $(".menuBox .content#rackDetails").load("rackhandler.php?action=mouseover&rackID="+rackID);
                }
                else if($(this).hasClass("device"))
                {
                    var itemName = '<div class="popupMenu" > ' +
                          '<div class="title"> ' +
                                '<table><thead> ' +
                                    '<tr><th align="left" >' + $(this).html() + '</th></tr> ' +
                                '</thead></table> ' +
                        '</div> ' +
                    '</div>';
                    $("#menus").html(itemName);
                }
            }
            else
            {
                if(deviceMenu==false)
                {
                    $("#menus").html("");
                    $("#menus").hide();
                }
            }
        }
    });


    $(".device").live("click", function( ev )
    {
        var deviceID = $(this).find("span").attr("id").replace('deviceID','');

        // position item to the top left of the rack
        leftPos = $(this).offset().left + $(this).width()+25;
        topPos = $(this).offset().top;

        // position the popup
        $("#menus").show();
        $("#menus").css("position","absolute");
        $("#menus").css("left",leftPos + "px");
        $("#menus").css("top",topPos + "px");
        $("#menus").css("z-index",111);

        // display the temp loading screen before we head into the jquery request
        var buildingName = '<div class="menuBox"><div class="title" >' + $(this).html() + '</div>' +
        '<div class="content" id="rackDetails"><center><img src="images/loading_light.gif" alt="loading" /></center></div></div>';
        $("#menus").html(buildingName);

        $.ajax({
            url: "viewDevice.php",
            data: "action=deviceMenu&deviceID=" + deviceID,
            success: function(deviceInfo)
            {
                deviceMenu=true;
                $("#menus").html(deviceInfo);
            }
        });
    });
});