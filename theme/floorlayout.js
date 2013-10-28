$(document).ready(function()
{
        $(".roomedit").live('click',function() {
		var id = $(this).parents("li").attr("id").replace(/[^0-9]/g, '');;
		var url = "floorlayout.php?action=room&mode=edit&from=layoutPage&id=" + id;
		$.openDOMWindow({ width:'750',height: '270',overlayOpacity: '30',windowSource: 'ajax', windowSourceURL: url});
	});

	function makeDraggable()
	{
            $(".draggable").draggable({containment: 'parent',stop: function(event, ui)
            {
                var query = "";
                query += "action=updateLayoutItemPosition&";
                query += "layoutItemID=" + $(this).attr("id").replace("layoutItemID","") + "&";
                query += "posy=" + (ui.offset.top-$(this).parent().offset().top) + "&";
                query += "posx=" + (ui.offset.left-$(this).parent().offset().left);
                $.post("handler.php", query);
            }});

            $('.resizable').resizable({handles: 'e, s, se',stop: function(event, ui)
            {
                var query = "";
                query += "action=updateLayoutItemSize&";
                query += "layoutItemID=" + $(this).attr("id").replace("layoutItemID","") + "&";
                query += "posy=" + ui.position.top + "&";
                query += "posx=" + ui.position.left + "&";
                query += "width=" + ui.size.width + "&";
                query += "height=" + ui.size.height;

                $.post("handler.php", query,function(roomitemID){  });
            }});
		
            $(".deletable img.deleteImage").click(function()
            {
                var itemID=$(this).parents(".deletable").attr("id").replace("layoutItemID","");
                var query2 = "action=deleteLayoutItem&";
                query2 += "layoutItemID=" + itemID;
                $(this).parents(".deletable").remove();
                $.post("handler.php", query2);
            });
	}

	$.post("handler.php", "action=layoutItems&parentType=Floor&parentID=" + floorID,function(result)
	{
            $.each(result, function(i, val)
            {
                if (result[i].itemType == "Door")
                {
                    var content = "<div id='layoutItemID" + result[i].layoutItemID + "' style='background-image:url(images/icons/door.gif);display:block;width: " + result[i].width + "px; height: " + result[i].height + "px;position:absolute; top: " + result[i].posY + "px; left: " + result[i].posX + "px' class='deletable resizable draggable'>";
                    content += "";
                    content += "<div><img class='deleteImage' style='float:right;cursor:pointer;' src='images/icons/delete_small.gif' /></div></div>";

                    $(".mapContent").append(content);
                }
                else
                {
                    var content = "<div id='layoutItemID" + result[i].layoutItemID + "' style='display:block;width: " + result[i].width + "px; height: " + result[i].height + "px;position:absolute; top: " + result[i].posY + "px; left: " + result[i].posX + "px' class='floortile deletable room" + result[i].itemID + " color" + result[i].itemID + " draggable resizable'>";
                    content += "<div style='width: 100%; height: 100%; overflow:hidden;'>";
                    content += "<div style='float:left;padding:10px;font-weight:bold;'>" + result[i].itemName + "</div>";
                    content += "<img class='deleteImage' style='float:right;cursor:pointer;' src='images/icons/delete_small.gif' />";
                    content += "</div></div>";

                    $(".mapContent").append(content);
                }
            });
            makeDraggable();
	},"json");


	$("#createRoomForm form").submit(function () 
	{ 
            var data=$("#createRoomForm form").serialize();

            $.post("handler.php",data,function(returnValue)
            {
                var top=$("#posy").val();
                var left=$("#posx").val();
                var name=$("#roomName").val();
                var color=$("#color").val();
                var styleInfo = ".color" + returnValue + " {-khtml-opacity:.75; -moz-opacity:.75; -ms-filter:'alpha(opacity=75)'; filter:alpha(opacity=75); opacity:.75; background-color:" + $("#color").val() + "; width:32px; height:150px; display:block;}";

                $("#colours").append(styleInfo);

                var ulitem;
                ulitem='<li class="toolbaritem roomTile" style="background-color:'+color+';" id="room'+returnValue+'" onmouseover="$(\'.room'+returnValue+'\').addClass(\'roomHover\');" onmouseout="$(\'.room'+returnValue+'\').removeClass(\'roomHover\');">\
                        <span class="title" >'+name+'</span>\
                        <span class="edit roomedit" ></span>\
                        </li>';
                $("#roomList").append(ulitem);

                $("#room" + returnValue).draggable({ helper: 'clone', scope: 'floor' });

                var query = "";

                query += "action=insertLayoutItem&";
                query += "height=32&";
                query += "width=150&";
                query += "itemID=" + returnValue + "&";
                query += "itemName=" + name + "&";
                query += "itemType=Room&";
                query += "parentName=&";
                query += "color=" + color + "&";
                query += "parentType=Floor&";
                query += "parentID=" + floorID + "&";
                query += "posy=" + top + "&";
                query += "posx=" + left + "&";
                $.post("handler.php", query,function(roomitemID)
                {
                    var content = "<div id='layoutItemID" + roomitemID + "' style='width: 150px; height: 32px;position:absolute; top: " + top + "px; left: " + left + "px' class='floortile deletable room" + returnValue + " color" + returnValue + " draggable resizable'>";
                    content += "<div style='width: 100%; height: 100%; overflow:hidden;'>";
                    content += "<div style='float:left;padding:10px;font-weight:bold;'>" + name + "</div>";
                    content += "<img style='float:right;' class='deleteImage' src='images/icons/delete_small.gif' />";
                    content += "</div></div>";

                    $(".mapContent").append(content);

                    makeDraggable();
                });
                $.closeDOMWindow({windowSourceID:'#createRoom'});
            });
            return false;
	});


	$("#droppable").droppable({accept: ".toolbaritem", scope: "floor",tolerance: 'fit',drop: function(ev, ui) 
	{ 
            /* (ui.offset.top-$("#viewPortal").offset().top) */
            /* Dimensions of the item we are working with, used whenever placing or saving */
            var top=ui.offset.top-$(this).offset().top;
            var left=ui.offset.left-$(this).offset().left;
            if (ui.draggable.attr("id") == "door1")
            {
                var query = "";
                query += "action=insertLayoutItem&";
                query += "height=32&";
                query += "width=32&";
                query += "itemID=&";
                query += "itemName=&";
                query += "itemType=Door&";
                query += "parentName=&";
                query += "parentType=Floor&";
                query += "parentID=" + floorID +"&";
                query += "posy=" + top + "&";
                query += "posx=" + left;

                $.post("handler.php", query,function(roomitemID)
                {
                    var content = "<div id='layoutItemID" + roomitemID + "' style='background-image:url(images/icons/door.gif);width: 32px; height: 32px;position:absolute; top: " + top + "px; left: " + left + "px' class='deletable resizable draggable'>";
                    content += "";
                    content += "<img style='float:right;' class='deleteImage' src='images/icons/delete_small.gif' /></div>";
                    $(".mapContent").append(content);
                    makeDraggable();
                });

		}
		else if (ui.draggable.attr("id").replace("room","") == "0")
		{
                    $("#posx").val(left);
                    $("#posy").val(top);

                    $.openDOMWindow({ width:'750',height: '270',overlayOpacity: '30',windowSourceID:'#createRoom',modal:1});
                }
		else
		{
                    var roomName=ui.draggable.find("span.title").html();
                    var roomColor=ui.draggable.css("background-color");
                    var roomID= ui.draggable.attr("id").replace("room","");
                    var defaultWidth=150;
                    var defaultHeight=32;
                    var query = "";
                    query += "action=insertLayoutItem&";
                    query += "height="+defaultHeight+"&";
                    query += "width="+defaultWidth+"&";
                    query += "itemID=" + roomID + "&";
                    query += "itemName=" + roomName + "&";
                    query += "itemType=Room&";
                    query += "parentName=&";
                    query += "parentType=Floor&";
                    query += "parentID=" + floorID +"&";
                    query += "posy=" + top + "&";
                    query += "posx=" + left;
                    $.post("handler.php", query,function(roomitemID)
                    {
                        var content = "<div id='layoutItemID" + roomitemID + "' style='background-color:"+roomColor+";position:absolute;display:block;width: "+defaultWidth+"px; height: "+defaultHeight+"px;left: " + left + "px; top: " + top + "px' class='deletable draggable resizable'>";
                        content += "<div style='width: 100%; height: 100%; overflow:hidden;'>";
                        content += "<div style='float:left;padding:10px;font-weight:bold;'>" + roomName + "</div>";
                        content += "<img style='float:right;' class='deleteImage' src='images/icons/delete_small.gif' />";
                        content += "</div></div>";

                        $(".mapContent").append(content);
                        makeDraggable();
                    });
		}
            }});

	$(".toolbaritem").draggable({ helper: function(){ return $('.roomHelper').clone().show(); }, scope: 'floor' });

});
