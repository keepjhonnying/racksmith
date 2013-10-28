// Get Information about Building Locations from Database using AJAX
$(document).ready(function()
{
    // draw items onto the map
    var query = "action=layoutItems&parentType=Building&parentID=0";
    $.post("handler.php",query,function(result)
    {
        $.each(result, function(i, val)
        {
            if(val.itemType=="Building")
            {
                $(".mapContent").append("<div alt='building"+val.itemID+"' id='layoutItemID"+val.layoutItemID+"' class='building building"+val.itemID+"' style='\
                width: " + val.width + "px; height: " + val.height + "px;position:absolute; top: " + val.posY + "px; left: " + val.posX + "px' ><span>" + val.itemName + "</span></div>");
            }
            else if(val.itemType=="Cabinet")
            {
                $(".mapContent").append("<div alt='cabinet"+val.itemID+"' id='layoutItemID"+val.layoutItemID+"' class='cabinet cabinet"+val.itemID+"' style='\
                cursor:pointer;width: " + val.width + "px; height: " + val.height + "px;position:absolute; top: " + val.posY + "px; left: " + val.posX + "px' ><span>Cabinet: " + val.itemName + "</span></div>");
            }
        });

    },"json");


    $(".building").live('click',function(ev)
    {
   
     

                leftPos=(ev.pageX+10);
                topPos=(ev.pageY-50);
                // if we're about to flow off the page move the object back left
                if((leftPos+300) > $(document).width())
                    leftPos=$(document).width()-320;

                $("#hoverName").css("position","absolute");
                $("#hoverName").css("left",leftPos+"px");
                $("#hoverName").css("top",topPos+"px");
       
   
   
   
   
        var buildingID = $(this).attr("alt").replace('building','');
        $("#hoverName").hide();
        $.post("handler.php", "action=getBuildingMenu&buildingID=" + buildingID, function(buildingMenu)
        {
            $("#menus").css("position","absolute");
            $("#menus").css("left",$("#hoverName").css("left"));
            $("#menus").css("top",$("#hoverName").css("top"));

            $("#menus").html(buildingMenu);
            $("#menus").slideDown('fast');

            $('.floors').sortable({
                handle : 'img',
                update : function ()
                {
                    var sequence = $(this).sortable('toArray');
                    $.ajax
                    ({
                        type: "POST",
                        url: "buildings.php?action=savefloors&data="+sequence,
                        async: false,
                        success: function(responce)
                        {
                            if(responce!=1)
                                alert("ERROR: Unable to save new floor order\nPlease report this on the RackSmith forum");
                        }
                    })
                }
            });
        });
    });


    $(".cabinet").live('click',function(ev)
    {
    
    
                 leftPos=(ev.pageX+10);
                topPos=(ev.pageY-50);
                // if we're about to flow off the page move the object back left
                if((leftPos+300) > $(document).width())
                    leftPos=$(document).width()-320;

                $("#hoverName").css("position","absolute");
                $("#hoverName").css("left",leftPos+"px");
                $("#hoverName").css("top",topPos+"px");
       
   
        /* Position of current building, used to position hover off the side */
        leftPos=(ev.pageX+10);
        topPos=(ev.pageY-50);

        var cabinetID = $(this).attr("alt").replace('cabinet','');
        $("#hoverName").hide();
        $.post("handler.php", "action=getCabinetMenu&cabinetID=" + cabinetID, function(cabinetMenu)
        {
            $("#menus").css("position","absolute");
            $("#menus").css("left",$("#hoverName").css("left"));
            $("#menus").css("top",$("#hoverName").css("top"));

            $("#menus").html(cabinetMenu);
            $("#menus").slideDown('fast');

        });
    });

    // Close the menu
    $(".closeContextMenu, .mapContent").live('click',function ()
    {
        $("#menus").html("");
        $("#menus").hide();
    });
	
    $(".mapContent .building, .mapContent .cabinet").live('mouseover mouseout',function(ev)
    {
        /* If theres no main menu for a building shown we can display our hover name */
        if($("#menus").is(':hidden') != "")
        {
            if (ev.type == 'mouseover')
            {
                $("#hoverName").show();

                leftPos=(ev.pageX+10);
                topPos=(ev.pageY-50);
                // if we're about to flow off the page move the object back left
                if((leftPos+300) > $(document).width())
                    leftPos=$(document).width()-320;

                $("#hoverName").css("position","absolute");
                $("#hoverName").css("left",leftPos+"px");
                $("#hoverName").css("top",topPos+"px");

                var itemName = '<div class="popupMenu" > ' +
                      '<div class="title"> ' +
                            '<table><thead> ' +
                                '<tr><th align="left" >' + $(this).html() + '</th></tr> ' +
                            '</thead></table> ' +
                    '</div> ' +
            '</div>';
                $("#hoverName").html(itemName);
            }
           else
            {
                $("#hoverName").html("");
                $("#hoverName").hide();
            }
         }
    });
    
    
    
    

    // floor edit and save are duplicated within buildinglayout.js
    $(".floorEdit").live('click', function() {
        id = $(this).attr("href").replace(/[^0-9]/g, '');
        currentValue = $("#floor" + id).html();

        if($("#floor" + id + " input").length <=0)
            $("#floor" + id).html("<input type='text' value='" + currentValue + "'/><input class='floorSave' type='submit' value='save' />");
    });

    $(".floorSave").live('click', function() {
        id = $(this).closest("tr").find(".floorEdit").attr("href").replace(/[^0-9]/g, '');
        newValue = $(this).parent().find("input[type=text]").val();

        $.post("handler.php", {action: "editFloor", floorID: id, name: newValue} );
        $("#floor" + id).html(newValue);
        $("#buildingMenuFloor" + id).html(newValue);
    });


});


function AddFloor(buildingID)
{
    var query = 'action=insertFloor&building=' + buildingID;
    query += '&name=' + $('.addFloor input#name').val();
    query += '&notes=' + $('.addFloor input#notes').val();

    // Send new Floor
    $.post('handler.php',query,function()
    {
        var query4 = "action=getBuildingMenu&buildingID=" + buildingID;
        $.post("handler.php", query4,function(result4)
        {
            $("#menus").html(result4);

            $('.floors').sortable({
                handle : 'img',
                update : function ()
                {
                    var sequence = $(this).sortable('toArray');
                    $.ajax
                    ({
                        type: "POST",
                        url: "buildings.php?action=savefloors&data="+sequence,
                        async: false,
                        success: function(responce)
                        {
                            if(responce!=1)
                                alert("ERROR: Unable to save new floor order\nPlease report this on the RackSmith forum");
                        }
                    })
                }
            });
        });
    });
}