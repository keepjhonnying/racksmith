
var deviceMenu;

$(document).ready(function(){
    // *********************** Device Menu
    $(".drag:not(.inventoryItem):not(.stockItem)").live("click", function( ev )
    {
        var xPos = $(this).position().left+$(this).width()+5;
        var yPos = $(this).position().top;
        // set the flag used to disable the mouseover
        var deviceID = $(this).find("span").attr("class").replace('device','');

        // if the menu has a chance of flowing off the page, reposition it higher
        if((ev.pageY+170) > $(window).height())
            $("#menus").css("top",(ev.pageY-170));

        $("#menus").css("left",xPos);
        $("#menus").css("top",yPos);             

        var menu = "";
        //var deviceQuery =
        $.ajax({
            url: "viewDevice.php",
            data: "action=deviceMenu&deviceID=" + deviceID,
            success: function(deviceInfo)
            {
                menu += deviceInfo;


                //$('#menus').hide();    
                $("#menus").html(menu);
                //$("#menus").fadeIn('fast');  

                // Should no longer be needed here
                //$('#displayPortal').mapbox({pan: true, zoom: false,defaultX:0,defaultY:0});
            }
        });
    });



    // use to determine if the menu is on to disable the mouseover
    var deviceMenu=false;
    // *********************** DEVICE MOUSEOVER
    $(".drag:not(.inventoryItem):not(.stockItem)").live("mouseover mouseout", function(event)
    {               
        if($("#menus").css("display")=="none")
        {
            if(event.type=="mouseover")
            {
                var xPos = $(this).position().left+$(this).width()+5;
                var yPos = $(this).position().top;

                $("#menus").css("display","block");
                $("#menus").css("position","absolute");
                $("#menus").css("left",xPos);
                $("#menus").css("top",yPos);
                $("#menus").css("z-index","1000");
                var menu = '<div class="popupMenu" > ' +
                      '<div class="title"> ' +
                            '<table><thead> ' +
                                '<tr><th align="left" >' + $(this).text() + '</th></tr> ' +
                            '</thead></table> ' +
                    '</div> ' +
            '</div>';
                $("#menus").html(menu);
            }
        }
        else
        {
            // if the user hasn't clicked a device this link doesn't exist
            // we can then remove the mouseover'
            if($(".editDevice").html()==null)
            {
                $("#menus").html("");
                $("#menus").css("display","none");
            }
        }
    });

    /* The mouseover for ports to display location
    Location is stored in the strong item within the port itself */
    $('.port a.connected, .port a.singleconnected').live('mouseover mouseout', function(event)
    {
        var hoverMenu = $(this).find("strong").html(); // the existing value used to determine if we need to query
        var savedName=''; // stores the return value we save back to the webpage
        var portID=$(this).parent('li').attr('id').replace(/[^0-9]/g, ''); // portID from parent item in list

        if (event.type == 'mouseover' && typeof portID == "number") {
            if(!hoverMenu)
            {
                $(".devicePortSummary#hoverMenu").show();
                // query for the endpoint
                $.ajax({
                   type: "POST",
                   url: "handler.php",
                   context: this,
                   async:false,
                   // only show endpoint restricts patches from appearing
                   data: "action=recursivePortSearch&onlyShowEndpoint=1&startPort=" + portID,
                   success: function(endPortDetails){
                        $('.devicePortSummary#hoverMenu').prepend(endPortDetails);
                        $(".devicePortSummary#hoverMenu").show();
                        $(this).find("strong").html(endPortDetails); // so we can call it in future
                   }
                 });
            }
            else
            {
                $('.devicePortSummary#hoverMenu').prepend(hoverMenu);
                $(".devicePortSummary#hoverMenu").show();
            }
        } else {
                $(".devicePortSummary#hoverMenu").hide();
                $(".devicePortSummary#hoverMenu").html("");
        }
    });


    /* The mouseover for ports to display location
    Location is stored in the strong item within the port itself */
    $('.patches a').live('mouseover mouseout', function(event)
    {
        if (event.type == 'mouseover')
        {
            var hoverMenu = $(this).find("strong").html(); // the existing value used to determine if we need to query

            $('.devicePortSummary#hoverMenu').html(hoverMenu);
            $(".devicePortSummary#hoverMenu").show();
        }
        else
        {
            $(".devicePortSummary#hoverMenu").fadeOut(200);
        }
    });

    
});