$(document).ready(function ()
 {
    $('.defaultDOMWindow').openDOMWindow({
        eventType:'click',
        width:'750',
        height: '470',
        overlayOpacity: '30'
    });
	
    $('.newDOMWindow').openDOMWindow({
        fixedWindowY: 50,
        borderColor: '#3b4c50',
        borderSize: 2,
        eventType:'click',
        width:'750',
        height: '470',
        overlayOpacity: '30',
        windowPadding: 0
    });
    
    /* Close a launched module */
    $('.closeModule').click(function(){
        $(this).closest(".module").slideToggle("normal", function ()
        {
            $(this).remove();
        });
    });
    
    $('.launchCreate').click(function(){
        var item = $(this).attr("href");
        $(item).clone(true).insertAfter(item).slideToggle("normal");
    });


    /* use this style on a forms submit button
    posts contents as a GET request and handles basic error reporting on the page
    FORMNAME() is called when complete */
    $(".JSONsubmitForm").live('click',function()
    {
        // add a class value to allow toggle between post and get requests
        if($(this).hasClass("postForm"))
            var typeOfSubmission="POST";
        else
            var typeOfSubmission="GET";

        // collect the form details
        var submissionURL=$(this).closest('form').attr("action");
        var data=$(this).closest('form').serialize();
        var formName=$(this).closest('form').attr("id");
        
        $.ajax({
            type: typeOfSubmission,
            url: submissionURL,
            data: data,
            dataType: "json",
            success: function (returnVal)
            {
                $.each(returnVal, function(item,val)
                {
                    if(item=="error")
                    {
                        $('.inputError').removeClass('inputError');
                        $.each(val, function(name,error)
                        {
                            $("[name=" + error + "]").addClass('inputError');
                        });
                        
                        $("#createRackForm .error").html("<strong>Please check the highlighted values</strong>");
                    }

                    if(item==0 && val=="created")
                    {
                        $("#createRackForm .error").html("");
                        $(".success").show();
                        $(".inputError").removeClass('inputError');

                        // takes the form name and tries to run a function with the same name
                        // good for callback functions after posting a form
                        try{
                          eval(formName+'('+JSON.stringify(returnVal)+')');
                        }catch(e)
                        {
                            // possibly want to catch this later
                        }
                    }
                });
                return false;
            }
        });
        return false;
    });


    /* use this style on a forms submit button
    posts contents as a GET request and handles basic error reporting on the page
    FORMNAME() is called when complete */
    $(".JSONform").live('click',function()
    {
        // determine the type of submission for the form, if unknown default to POST
        var method = $(this).closest("form").attr("method");
        if(method==null)
            method="POST";

        // determine if submissionURL contains GET values so we can pass submissionType correctly
        var submissionURL=$(this).closest('form').attr("action");
        if(submissionURL.indexOf("?")!=-1)
            submissionURL += "&typeOfSubmission=ajax";
        else
            submissionURL += "?typeOfSubmission=ajax";
        var data=$(this).closest('form').serialize();
        var formName=$(this).closest('form').attr("id");

        $.ajax({
            type: method,
            url: submissionURL,
            data: data,
            dataType: "html",
            success: function (returnVal)
            {
                if(returnVal[0])
                {
                     try{
                      eval(formName+'('+JSON.stringify(returnVal[1])+')');
                    }catch(e)
                    {
                        // possibly want to catch this later
                    }
                }
                else
                    alert("Failed");
                return false;
            }
        });
        return false;
    });

    


    $('.manageAttachments').live('click',function() {
        var meta = $(this).attr("href").replace(/#/g,'');
        var assetID = meta.replace(/[^0-9]/g, '');
        var assetType = meta.replace(/[0-9]/g, '');
        $.openDOMWindow({width:'750',height: '365',overlayOpacity: '30',windowSource:'ajax',windowSourceURL: 'attachments.php?assetID='+assetID+'&assetType='+assetType,windowPadding: 10});
        return false;
    });


/* Menu binds for modal windows */
$('.editDevice').live('click',function() {
    var deviceID = $(this).attr("href").replace(/#/g,'');
    var height = $(window).height()-100;
    $.openDOMWindow({fixedWindowY: 50, borderColor: '#3b4c50', borderSize: 2, width:'950',height: height,overlayOpacity: '30',windowSource:'ajax',windowSourceURL: 'editDevice.php?deviceID=' + deviceID,modal:1,windowPadding: 0});
    return false;
});

$('.moreDeviceDetails').live('click',function() {
    var deviceID = $(this).attr("href").replace(/#/g,'');
    var height = $(window).height()-100;
    $.openDOMWindow({ fixedWindowY: 50, borderColor: '#3b4c50', borderSize: 2, width:'950',height: height,overlayOpacity: '30',windowSource:'ajax',windowSourceURL: 'viewDevice.php?deviceID=' + deviceID,windowPadding: 0});
    return false;
});

$('.deleteDevice').live('click',function() {
    var deviceID = $(this).attr("href").replace(/#/g,'');
    $.openDOMWindow({borderColor: '#3b4c50', borderSize: 2,  width:'600',height: '300',overlayOpacity: '30',windowSource:'ajax',windowSourceURL: 'deviceHandler.php?action=delete&deviceID=' + deviceID,windowPadding: 0});
    return false;
});


$('.upgradeDevice').live('click',function() {
    var deviceID = $(this).attr("href").replace(/#/g,'');
    $.openDOMWindow({fixedWindowY: 50, borderColor: '#3b4c50', borderSize: 2, width:'700',height: '250',overlayOpacity: '30',windowSource:'ajax',windowSourceURL: 'deviceHandler.php?action=upgrade&deviceID=' + deviceID,modal:1,windowPadding: 0});
    return false;
});

$('.upgradeDevice').live('click',function() {
    var deviceID = $(this).attr("href").replace(/#/g,'');
    $.openDOMWindow({fixedWindowY: 50, borderColor: '#3b4c50', borderSize: 2, width:'600',height: '225',overlayOpacity: '30',windowSource:'ajax',windowSourceURL: 'deviceHandler.php?action=upgrade&deviceID=' + deviceID,modal:1,windowPadding: 0});
    return false;
});

});




