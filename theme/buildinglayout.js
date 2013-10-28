$(document).ready(function()
{
        
	$(".itemList li .edit").live('click',function() {
            id = $(this).closest("li").attr("id").replace(/[^0-9]/g, '');
            var url;
            if($(this).closest("li").hasClass("buildingTile"))
                url = "buildings.php?action=building&mode=edit&from=layout&id=" + id;

            if($(this).closest("li").hasClass("cabinetTile"))
                var url = "buildings.php?action=cabinet&mode=edit&from=layout&id=" + id;
            $.openDOMWindow({borderColor: '#3b4c50', borderSize: 2,windowPadding:0, width:'750',height: '400',overlayOpacity: '30',windowSource: 'ajax', windowSourceURL: url});
	});


        // floor edit and save are duplicated within building.js
	$(".floorEdit").live('click', function() {
            id = $(this).attr("href").replace(/[^0-9]/g, '');
            currentValue = $("#floor" + id).html();

            if($("#floor" + id + " input").length <=0)
                $("#floor" + id).html("<input type='text' value='" + currentValue + "'/><input class='floorSave' type='submit' value='save' />");
	});

	$(".floorSave").live('click', function() {
		id = $(this).closest("tr").find(".floorEdit").attr("href").replace(/[^0-9]/g, '');
                newValue = $(this).parent().find("input[type=text]").val();

                $.post("handler.php", { action: "editFloor", floorID: id, name: newValue } );
                $("#floor" + id).html(newValue);
                $("#buildingMenuFloor" + id).html(newValue);
	});
        
			
	function makeDraggable()
	{
            $(".draggable").draggable({containment: 'parent',snap: '.draggable', snapMode: 'outer' , snapTolerance: 6,stop: function(event, ui)
            {
                var query = "";
                query += "action=updateLayoutItemPosition&";
                query += "layoutItemID=" + $(this).attr("id").replace("layoutItemID","") + "&";
                query += "posy=" + (ui.offset.top-$(this).parent().offset().top) + "&";
                query += "posx=" + (ui.offset.left-$(this).parent().offset().left);
                $.post("handler.php", query);
            }});

			// Only words for e,s,se as the position changes slightly when the west or north handles are dragged and isn't handled correclty
            $('.resizable').resizable({handles: 'e, s, se',stop: function(event, ui)
            {
                var query = "";
                query += "action=updateLayoutItemSize&";
                query += "layoutItemID=" + $(this).attr("id").replace("layoutItemID","") + "&";
                query += "posy=" + ui.position.top + "&";
                query += "posx=" + ui.position.left + "&";
                query += "width=" + ui.size.width + "&";
                query += "height=" + ui.size.height;

                $.post("handler.php", query,function(roomitemID){ });
            }});
		
            $(".deletable img").click(function(){
                var query2 = "";
                query2 += "action=deleteLayoutItem&";
                query2 += "layoutItemID=" + $(this).parent().attr("id").replace("layoutItemID","");
                $(this).parent().remove();
                $.post("handler.php", query2);
            });
	}

	$("#createBuildingForm form").submit(function () 
	{ 
            var top=$("#createBuildingForm input[name=itemposy]").val();
            var left=$("#createBuildingForm input[name=itemposx]").val();
            var name=$("#createBuildingForm #name").val();
            var data=$("#createBuildingForm form").serialize();
            $.post("handler.php",data,function(returnValue)
            {
                // generate new item for the building list panel
                var ulitem="\
                    <li class=\"toolbaritem buildingTile ui-draggable\" id=\"building"+returnValue+"\" onmouseover=\"$('.building"+returnValue+"').addClass('buildingHover');\" onmouseout=\"$('.building"+returnValue+"').removeClass('buildingHover');\"> \
                    <span class=\"title\" >"+name+" \
                    <span class=\"edit\" ></span> \
                </li> ";
                $("#buildingsList").append(ulitem);

                $("#building" + returnValue).draggable({ helper: function(){ return $('.buildingHelper').clone().show(); }, scope: 'floor' });

                var query = "";
                query += "action=insertLayoutItem&";
                query += "height=32&width=32&";
                query += "itemID=" + returnValue + "&";
                query += "itemName=&itemType=Building&";
                query += "parentName=&";
                query += "parentType=Building&parentID=0&";
                query += "posy=" + top + "&";
                query += "posx=" + left  + "&";

                $.post("handler.php", query,function(roomitemID)
                {
                    $("#droppable").append("<div id='layoutItemID" + roomitemID + "' style='width: 32px; height: 32px;position:relative; top: " + top + "px; left: " + left + "px;' class='nopan deletable building" + returnValue + " building draggable resizable transparent'><img src='images/icons/delete_small.gif' /></div>");
                    makeDraggable();
                });
                $.closeDOMWindow({windowSourceID:'#createBuilding'});
            });
            return false;
	});

	$("#createCabinetForm form").submit(function ()
	{
            var top=$("#createCabinetForm input[name=itemposy]").val();
            var left=$("#createCabinetForm input[name=itemposx]").val();
            var name=$("#createCabinetForm #name").val();
            var data=$("#createCabinetForm form").serialize();
            $.post("handler.php",data,function(returnValue)
            {
                // generate new item for the building list panel
                var ulitem="\
                    <li class=\"toolbaritem cabinetTile title\" id=\"cabinet"+returnValue+"\" onmouseover=\"$('.cabinet"+returnValue+"').addClass('cabinetHover');\" onmouseout=\"$('.cabinet"+returnValue+"').removeClass('cabinetHover');\"> \
                    <span class=\"title\" >"+name+" \
                    <span class=\"edit\" ></span> \
                </li> ";

                $("#cabinetList").append(ulitem);

                $("#cabinet" + returnValue).draggable({ helper: function(){ return $('.buildingHelper').clone().show(); }, scope: 'floor' });

                var query = "action=insertLayoutItem&";
                query += "height=32&";
                query += "width=32&";
                query += "itemID=" + returnValue + "&";
                query += "itemName="+name+"&";
                query += "itemType=Cabinet&";
                query += "parentName=&";
                query += "parentType=Building&";
                query += "parentID=0&";
                query += "posy=" + top + "&";
                query += "posx=" + left;
                $.ajax
                ({
                    type: "POST",
                    url: "handler.php",
                    data: query,
                    async: false,
                    success: function(itemID)
                    {
                        $("#droppable").append("<div id='layoutItemID" + itemID + "' style='width: 32px; height: 32px;position:absolute; top: " + top + "px; left: " + left + "px;' class='deletable cabinet" + returnValue + " cabinet draggable'><img src='images/icons/delete_small.gif' /></div>");
                        makeDraggable();
                    }
                });
                $.closeDOMWindow({windowSourceID:'#createCabinet'});
            });
                
            return false;
	});


	$("#droppable").droppable({accept: ".toolbaritem", scope: "floor",tolerance: 'fit',drop: function(ev, ui) 
	{ 
		/* Dimensions of the item we are working with, used whenever placing or saving */
		var top=ui.offset.top-$(this).parent().offset().top;
		var left=ui.offset.left-$(this).parent().offset().left;
                var itemID=$(ui.draggable).attr("id").replace(/[^0-9]/g, ''); // ID is either building## or cabinet##

                // find out if we are creating a building or cabinet by looking at the parent
                var createItemType;
                if($(ui.draggable).closest(".itemList").attr("id") == "buildingsList")
                    createItemType="Building";
                else if($(ui.draggable).closest(".itemList").attr("id") == "cabinetList")
                    createItemType="Cabinet";

                // we are creating a new item
		if(itemID == "0")
		{
                    // Use the ID value to determine which popup to display
                    if(createItemType=="Building")
                    {
                        $("#createBuildingForm input[name=itemposx]").val(left);
                        $("#createBuildingForm input[name=itemposy]").val(top);
                        $.openDOMWindow({ borderColor: '#3b4c50', borderSize: 2,width:'650',height: '370',overlayOpacity: '30',windowSourceID:"#createBuilding",modal:1,windowPadding:0});
                    }
                    else if(createItemType=="Cabinet")
                    {
                        $("#createCabinetForm input[name=itemposx]").val(left);
                        $("#createCabinetForm input[name=itemposy]").val(top);
                        $.openDOMWindow({borderColor: '#3b4c50', borderSize: 2, width:'650',height: '370',overlayOpacity: '30',windowSourceID:"#createCabinet",modal:1,windowPadding:0});
                    }
		}
                // we are moving an item or adding a new layoutItem to an existing one
		else
		{
                    var query = "";
                    // if this cabinet already exists lets just relocate it
                    if($(".cabinet"+itemID).length)
                    {
                        if(!confirm("This Cabinet already exists\nDoing this will relocate it, maintaining all internal connections"))
                            return false;
                        
                        // get and save the new location in the DB
                        var existingID = $(".cabinet"+itemID).attr("id").replace(/[^0-9]/g, '');
                        query += "action=updateLayoutItemPosition&";
                        query += "layoutItemID=" + existingID + "&";
                        query += "posy=" + (ui.offset.top-$(this).parent().offset().top) + "&";
                        query += "posx=" + (ui.offset.left-$(this).parent().offset().left);
                        $.post("handler.php", query);
                        // move the cabinet on the current map
                        $(".cabinet"+itemID).css("top",(ui.offset.top-$(this).parent().offset().top));
                        $(".cabinet"+itemID).css("left",(ui.offset.left-$(this).parent().offset().left));
                    }
                    // if its a building we can create multiple blocks
                    else
                    {
                        query += "action=insertLayoutItem&";
                        query += "height=32&";
                        query += "width=32&";
                        query += "itemID=" + itemID + "&";
                        query += "itemName=&";
                        query += "itemType="+createItemType+"&";
                        query += "parentName=&";
                        query += "parentType=Building&";
                        query += "parentID=0&";
                        query += "posy=" + top + "&";
                        query += "posx=" + left;

                        $.post("handler.php", query,function(layoutID)
                        {
                            $("#droppable").append("<div id='layoutItemID" + layoutID + "' style='width: 32px; height: 32px;position:relative; top: " + top + "px; left: " + left + "px' class='floortile deletable " + ui.draggable.attr("id") + " " + ui.draggable.attr("class").replace("toolbaritem ", "").replace(" ui-draggable", "") + " draggable resizable transparent'><img src='images/icons/delete_small.gif'' /></div>");
                            makeDraggable();
                        });
                    }
		}
	}});

	$("#buildingsList .toolbaritem").draggable({ helper: function(){ return $('.buildingHelper').clone().show(); }, scope: 'floor' });
        $("#cabinetList .toolbaritem").draggable({ helper: function(){ return $('.cabinetHelper').clone().show(); }, scope: 'floor' });

        /* Draw the items onto the canvas */
	$.post("handler.php", "action=layoutItems&parentType=Building&parentID=0",function(result)
	{
            $.each(result, function(i, item) {
                if(item.itemType=="Building")
                {
                    $("#droppable").append("<div title='"+item.itemName+"' id='layoutItemID"+item.layoutItemID+"' style='width:"+item.width+"px;height: " + item.height + "px;position:absolute;top: "+item.posY + "px;left: " + item.posX + "px' \
                    class='building deletable draggable resizable transparent building"+item.itemID+"'> \
                    <img src='images/icons/delete_small.gif' /></div>");
                }
                else if(item.itemType=="Cabinet")
                {
                    $("#droppable").append("<div title='" + item.itemName + "' id='layoutItemID"+item.layoutItemID+"' style='width:"+item.width+"px;height: " + item.height + "px;position:absolute;top: "+item.posY + "px;left: " + item.posX + "px' \
                    class='cabinet deletable draggable cabinet" + item.itemID + "'> \
                    <img src='images/icons/delete_small.gif' /></div>");
                }
            });

            makeDraggable();
	},"json");
});