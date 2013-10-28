$('.room .editRack').live('click',function() {
    var rackID = $(this).attr("href").replace(/#/g,'');
$.openDOMWindow({borderColor: '#3b4c50', borderSize: 2, width:'750',height: '420',overlayOpacity: '30',windowSource:'ajax',windowSourceURL: 'rackhandler.php?action=edit&rackID='+rackID,windowPadding: 0});
    return false;
});

$('.cabinet .editRack').live('click',function() {
    var cabinetID=$(this).parents(".globalRack.cabinet").attr("id").replace(/[^0-9]/g, '');
    $.openDOMWindow({borderColor: '#3b4c50', borderSize: 2,  width:'750',height: '420',overlayOpacity: '30',windowSource:'ajax',windowSourceURL: 'buildings.php?action=cabinet&mode=edit&id='+cabinetID,windowPadding: 0});
    return false;
});

/* Toggle the display of a front/back view */
function rotateRack(rackID)
{
    var newRackView, oldRackView, back;
    // If front exists but back doesnt
    if($("#rackHolder_"+rackID+"_front").length!=-1 && $("#rackHolder_" + rackID+"_back").length<=0)
    {
        currentRackView='front';
        newRackView="back";
        back=1;
    }
    else if($("#rackHolder_" + rackID+"_back").length!=-1 && $("#rackHolder_" + rackID+"_front").length<=0)
    {
        currentRackView='back';
        newRackView="front";
        back=0;
    }
    // to get here both must already exists, so lets just end
    else
    {
        return false;
    }
    
    // the object must not exist so query and create it
    var returned=0;
    $.ajax({
        type: "GET",
        url: "racks.php",
        data: "action=loadRack&rackID="+rackID+"&back="+back,
        async: false,
        dataType: "text",
        success: function(rackInfo){
            //alert("\n"+"#rackHolder_"+rackID+"_"+rackView);
            var frontClasses = $("#rackHolder_"+rackID+"_"+currentRackView).parent().attr("class");
            //alert("we're in there");
            //alert(frontClasses);
            //$("#rackHolder_"+rackID+"_"+rackView).parent().after("<td valign='bottom' class='globalRack "+rackInfo.parentType+"' id='"+rackInfo.parentType+"_"+rackInfo.parentID+"' >" + rackInfo + "</td>");
            $("#rackHolder_"+rackID+"_"+currentRackView).parent().after("<td valign='bottom' id='rackHolder_"+rackID+"' class='"+frontClasses+" backView'>" + rackInfo + "</td>");
            makedraggable("#rackHolder_"+rackID+"_"+newRackView);
        }
    });
    return false;
}   

// manual binds for drag events
function makedraggable(rackName){
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// called when a rack device item begins to drag
	$('#' + rackName + " .drag")
		.bind( "dragstart", function( event )
		{
                    $("#menus").hide();
			// filters and rebinds events on any new items
			$.dropManage();
			
			// Foreach draggable item(rack) figure out its size
			// make all .drop's under it undroppable
			$.each($(".drag"), function()
			{
				var $size = $(this).attr("id");
				$(this).closest(".drop").prev().nextAll(".drop:lt("+$size+")").addClass("undroppable");
			});

			// find the current device and make it droppable
			var $id = parseInt(event.dragTarget.id);			
			$( this ).closest(".drop").prev().nextAll().filter(":lt("+$id+")").removeClass("undroppable");	// remove next
			$( this ).addClass("outline");			
			return $( this ).clone().appendTo( document.body ).addClass("ghost");
		})
			
		// Place a movement delay of 3px onto the drag so the context menu client can be performed
		.bind( "drag", { distance:3 }, function( event )
		{
			// update the "proxy" element position
			$( event.dragProxy ).css({
				left: event.offsetX, 
				top: event.offsetY
				});
		})
		.bind( "dragend", function( event ) {
			// remove the "proxy" element
			$( event.dragProxy ).fadeOut( "normal", function()
                        {
                            $( event.dragProxy ).remove();
			});
			
			// restore to a normal state
			$( this ).removeClass("outline");	
		});
			
			
	$('#' + rackName + " .drop")
		.bind( "dropstart", function( event ){
			// don't drop in itself
			var $id = parseInt(event.dragTarget.id)-1;

			// Check to see if we can drop an element here
			if ($( this ).is(".undroppable")) return false;
			if ($( this ).nextAll().filter(":lt("+$id+")").is(".undroppable")) return false;
			if ($( this ).nextAll().filter(":lt("+$id+")").is(".base")) return false;

			// Display our mouseover
			$( this ).closest(".drop").addClass("active");
			$( this ).closest(".drop").nextAll().filter(":lt("+$id+")").addClass("active");
		})
		.bind( "drop", function( event ) {
			// If we are dropping onto original
			// remove the styles and exit so we don't query DB
			if ( this == event.dragTarget.parentNode )
			{
				var $id = parseInt(event.dragTarget.id)-1;
				$( this ).closest(".drop").removeClass("active");
				$( this ).closest(".drop").nextAll().filter(":lt("+$id+")").removeClass("active");
				$(event.dragTarget).removeClass("original");
				return false;
			}
                        // lets get some details to use in future queries
                        var rackID = $(this).parent().attr("id").replace('RU_','');
                        var deviceID = $(event.dragTarget).find("span").attr("class").replace(/[^0-9]/g, '');

                        // we've moved an item, hide the menu as its not in a new location'
                        $("#menus").html("");
                        $("#menus").css("display","none");
                        
                        // we must be creating a new item
			if($(event.dragTarget).hasClass("inventoryItem"))
			{
                                var back=0;
                                if($(this).hasClass("back"))
                                    back=1;
                                else
                                    back=0;
                                
				var templateID = $(event.dragTarget).attr("href").replace(/#/g,'');
				var dropID = $(this).attr("id");
                                var height = $(window).height()-100;

				$.openDOMWindow({ fixedWindowY: 50, borderColor: '#3b4c50', borderSize: 2,width:'950',height: height,overlayOpacity: '30',windowSource:'ajax',
				windowSourceURL: 'deviceHandler.php?templateID='+templateID+"&position="+dropID+"&parentType=rack&parentID="+rackID+"&back="+back ,modal:1,windowPadding: 0});
				return false;
			}
			// An item has been moved so we must save it to the DB
			else
			{
				// only just the inventory count when we have dropped an item from it
				if($(event.dragTarget).hasClass("stockItem"))
				{
                                    $('#inventoryTitle i').html($('#stock div').children().size()-3);
                                    $(event.dragTarget).removeClass('stockItem');
                                    $(event.dragTarget).removeClass('outline');
                                    $(event.dragTarget).parent().addClass("ghost");
                                    //var clone = $(event.dragTarget).clone();
                                    //$(event.dragTarget).parent("li").remove();
                                    //event.dragTarget=clone;
				}
				
				$( this ).html(event.dragTarget);	// place item in new location
				
				// remove styles from original
				$(event.dragTarget).removeClass("original");
				$( this ).removeClass("active");
				$(".drop").removeClass("undroppable");

                                var back=0;
                                if($(this).hasClass("back"))
                                    back=1;
                                else
                                    back=0;

				// Query and save item to DB, leaving responce atm should actually check it
				$.ajax({
                                    type: "GET",
                                    url: "racks.php?action=moveDevice&deviceID="+deviceID+"&rackID="+rackID+"&newPos="+this.id+"&back="+back,
                                    async: true,
                                    success: function(returned)
                                    {
                                    }
				});
                                    
			}
		})
		.bind( "dropend", function( event ) {
			// deactivate mouseover
                        $(".ghost").remove();
			var $id = parseInt(event.dragTarget.id)-1;
			$( this ).removeClass("active");
			$( this ).nextAll().filter(":lt("+$id+")").removeClass("active");
                        
		});
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        
	}	

function make_undraggable(rackName) {
    $('#' + rackName + ".drag").unbind("dragstart");
    $('#' + rackName + ".drag").unbind("drag");
    $('#' + rackName + ".drag").unbind("dragend");
    $('#' + rackName + ".drop").unbind("dropstart");
    $('#' + rackName + ".drop").unbind("drop");
    $('#' + rackName + ".drop").unbind("dropend");
}

$(document).ready(function () {
	makeRacksLoadable();
        //makeStockDraggable()
	makedraggable("stock");


            $('#stock').bind("drop", function( event )
            {
                    // Don't drop an inventory item if it is already in the stock/inventory
                    if( $(event.dragTarget).is(".inventoryItem") || $(event.dragTarget).is(".stockItem") ) return false;

                    // display the mouseover/clicked menu
                    $("#menus").html("");
                    $("#menus").css("display","none");

                    // make the movement transaction
                    var deviceID = $(event.dragTarget).find("span").attr("class").replace(/[^0-9]/g, '');
                    var returned=0;
                    $.ajax(
                    {
                        type: "GET",
                        url: "racks.php?action=moveToStock&deviceID="+deviceID,
                        async: false,
                        success: function(returned)
                        {
                            if(returned !=0)
                            {
                                // move the object into the stock inventory
                                var newItem="<li class='item'>"+$(event.dragTarget).parent().html()+"</li>";
                                $("#stock ul").append(newItem);

                                    //adjust the inventory count to show the new number of boxes
                                $('#inventoryTitle i').html($('#stock div').children().size()-2);
                                $("#stock ul li div").removeClass();
                                $("#stock ul li div").addClass("r"+event.dragTarget.id);
                                $("#stock ul li div").addClass("stockItem");
                                $("#stock ul li div").addClass("drag");
                                // remove the item from the rack as its now in stock
                                $(event.dragTarget).remove();
                                makedraggable("stock");

                                // style the item so it shows as a stock item
                                //$("#stock ul li#item div").addClass('stockItem');
                            }
                            else
                                alert("error while moving device, no response from handler");
                        }
                    });
                return false;
            });

	// Inventory items are draggable just like rackitems above
	// However we remove the logic of saving on drop and bind that to the .drop div within the rack
	$(".inventoryItem")
		.bind( "dragstart", function( event ){
                    $("#menus").hide();
                    $(".r1").closest(".drop").addClass("undroppable");
                    $(".r2").closest(".drop").prev().nextAll(".drop:lt(2)").addClass("undroppable");
                    $(".r3").closest(".drop").prev().nextAll(".drop:lt(3)").addClass("undroppable");
                    $(".r4").closest(".drop").prev().nextAll(".drop:lt(4)").addClass("undroppable");
                    $(".r5").closest(".drop").prev().nextAll(".drop:lt(5)").addClass("undroppable");
                    $(".r6").closest(".drop").prev().nextAll(".drop:lt(6)").addClass("undroppable");
                    $(".r7").closest(".drop").prev().nextAll(".drop:lt(7)").addClass("undroppable");
                    $(".r8").closest(".drop").prev().nextAll(".drop:lt(8)").addClass("undroppable");
                    $(".r9").closest(".drop").prev().nextAll(".drop:lt(9)").addClass("undroppable");
                    $(".r10").closest(".drop").prev().nextAll(".drop:lt(10)").addClass("undroppable");

                    // find the current device and make it droppable
                    var $id = parseInt(event.dragTarget.id);
                    var $drag = $( this ), $proxy = $drag.clone();
                    $( this ).parent().prev().nextAll().filter(":lt("+$id+")").removeClass("undroppable");	// remove next

                    $drag.addClass("outline"); // Overlay existing item with theme to
                    $( this ).addClass("original"); // Theme the original a little more

                    // insert and return the "proxy" element
                    return $proxy.appendTo( document.body ).addClass("ghost");
                    })
                    
                 // update the "proxy" element position
		.bind( "drag", function( event ){  $( event.dragProxy ).css({left: event.offsetX,top: event.offsetY}); })
                
		.bind( "dragend", function( event ){
                    // remove the "proxy" element
                    $(".ghost").fadeOut( "normal", function(){
                            $(".ghost").remove();
                            });

                    // restore to a normal state
                    $( this ).removeClass("outline");
                    $(this).removeClass("drop");
                  });

    // enable the acordian for the inventory lists
    $(".inventoryHolder").accordion({ autoHeight: false,collapsible: true });


    $(".closeDOMWindow").click(function() {
            $('#createRackForm .error').html('');
            $('.formError').removeClass('formError');
    });


    // Places a rack item onto the page and refreshes the draggable/droppable items if needed
    // as items move on the page we need to refresh the handle or the mouseover events are offset the size of the newobject
    function makeRacksLoadable()
    {
        $(".rackLoad").click(function()
        {
            var rackID = $(this).attr("href").replace(/#/g,'');
            loadRack(rackID);
            return false;
        });
        return false;
    }

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
    $('.inventoryClearSearch').click(function ()
    {
            $(this).parent().find(".inventorySearchField").val(" ");
            $(this).closest('ul').children("li").show();
            $(this).hide();
    });

    $(".rackTitle:not(a)").live('mouseover mouseout',function(ev)
    {
        if (ev.type == 'mouseover')
        {
            // get the name of current item to display without JSON request
            var rackName = $(this).text();
            var rackID = $(this).find("a").attr("href").replace('#','');

            // position item to the top left of the rack
            leftPos = $(this).offset().left + $(this).width()+25;
            topPos = $(this).offset().top;

            // position the popup
            $("#hoverName").show();
            $("#hoverName").css("position","absolute");
            $("#hoverName").css("left",leftPos + "px");
            $("#hoverName").css("top",topPos + "px");
            $("#hoverName").css("z-index",111);

            // display the temp loading screen before we head into the jquery request
            var buildingName = '<div class="menuBox">' +
                    '<div class="content" id="rackDetails"><center><img src="images/loading_light.gif" alt="loading" /></center></div></div>';
            $("#hoverName").html(buildingName);

            // pull in the rack summary page
            $(".menuBox .content#rackDetails").load("rackhandler.php?action=mouseover&rackID="+rackID);
        }

        if (ev.type == 'mouseout')
        {
            $("#hoverName").html("");
            $("#hoverName").hide();
        }
    });
});


function loadRack(rackID,back)
{
    // default the view to the front
    var shownView;
    if(back!=1)
    {
        back=0;
        shownView="front";
    }
    else 
    {
        back=1;
        shownView="back ";
    }

    $("#rackLink_"+rackID).parent().toggleClass("highlighted_rack");

    // If the item already exists then lets remove it
    //alert("#rackHolder_"+rackID+"_"+shownView);
    if ($("#rackHolder_"+rackID+"_"+shownView).length > 0 )
    {
            $("#hoverName").html("");
            $("#hoverName").hide();
            make_undraggable("#rackHolder__"+rackID+"_"+shownView);
            $("#rackHolder_"+rackID+"_"+shownView).parent().remove();

            // remove the rack from the users session
            $.ajax({
                    type: "GET",
                    url: "racks.php",
                    data: "action=closeRack&rackID="+rackID+"&back="+back,
                    async: false
                });
            return false;
     }
    else
    {
            // get metadata on the rack we're loading
            // used to help determine style (cabinet/plain rack)
            $.ajax({
                type: "POST",
                url: "handler.php",
                data: "action=rackInfo&rackID=" + rackID,
                async: false,
                dataType: 'json',
                success: function(rackInfo)
                {                 
                    // now we have the meta data make what we need
                    $.ajax({
                        type: "GET",
                        url: "racks.php",
                        data: "action=loadRack&rackID="+rackID+"&back="+back,
                        async: false,
                        dataType: 'text',
                        success: function(returned){
                            if(returned!=0)
                            {
                                if(back)
                                    $("#rackHolderData").append("<td valign='bottom' class='globalRack "+rackInfo.parentType+" backView' id='"+rackInfo.parentType+"_"+rackInfo.parentID+"'>" + returned + "</td>");
                                else
                                    $("#rackHolderData").append("<td valign='bottom' class='globalRack "+rackInfo.parentType+"' id='"+rackInfo.parentType+"_"+rackInfo.parentID+"'>" + returned + "</td>");

                                makedraggable("racktbl_" + rackID);
                            }
                        }
                        });
                }
            });
            return false;
    }
}