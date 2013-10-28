$(document).ready(function()
{
    
    $("select#deviceType").change(function () 
    {
        $('#submitTemplate').attr("disabled", false); 	// Activate the submit button
			
        selectedDropdown = $("select#deviceType option:selected").attr("value");	// Find out what div to display
        $(".deviceForm").slideUp('fast');	// Hide whatever is currently shown

        // for valid DIVS show the selected
        switch (selectedDropdown)
        {
        case '0': // If no field is selected disable the submit
                $('#submitTemplate').attr("disabled", true);
                break;
        case '2':
                $("#datastorage").slideDown('fast');
                $("#templatePorts").slideDown('fast');
                break;
        case '3':
                $("#server").slideDown('fast');
                $("#templatePorts").slideDown('fast');
                break;
        case '4':
                $("#switch").slideDown('fast');
                $("#templatePorts").slideDown('fast');
                break;
        case '7':
                $("#templatePorts").slideDown('fast');
                break;
        case '5':
                $("#powerdevice").slideDown('fast');
                $("#templatePorts").slideDown('fast');
                break;
        };
    });



});


// Adds a port to the list on the templates page, when the form gets
// when the form gets submitted these appended lines are handled by PHP for the actual creation
function addTemplatePort()
{
    var portName = $("#newTemplatePort select[name=createPortType] :selected").text();
    var portType = $("#newTemplatePort select[name=createPortType] :selected").val();
    var portCount = parseInt($("#newTemplatePort select[name=createPortCount] :selected").val());

    // if we actually have a port to make
    if(portCount > 0)
    {
        $("#newTemplatePort select[name=createPortCount]").removeClass("formError");
        // check to see if theres an existing form entry for this portType
        if($("#templatePorts tbody tr").hasClass("portType"+portType))
        {
            // find out how many of these ports already existed
            var existingPortCount = parseInt($("#templatePorts tbody tr.portType"+portType+" input[name=portCount[]]").val());

            var newContents = "<td><input type='hidden' name='portType[]' value='"+portType+"' />"+portName+" </td> \
                <td><input type='hidden' name='portCount[]' value='"+(portCount+existingPortCount)+"' />"+(portCount+existingPortCount)+"</td> \
                <td><a onclick='$(this).closest(\"tr\").remove();' >Delete</td>";
            // rewrite the entry with the new port count
            $("#templatePorts tbody tr.portType"+portType).html(newContents);
        }
        // else there are no ports of this type existing, make a new entry
        else
        {
            // write a new entry for this portType
            var newLine = "<tr class='portType"+portType+"'> \
                <td><input type='hidden' name='portType[]' value='"+portType+"' />"+portName+" </td> \
                <td><input type='hidden' name='portCount[]' value='"+portCount+"' />"+portCount+"</td> \
                <td><a onclick='$(this).closest(\"tr\").remove();' >Delete</td>\
                </tr>";
            $("#templatePorts tbody").append(newLine).fadeIn('fast');
        }
    }
    else
    {
        $("#newTemplatePort select[name=createPortCount]").addClass("formError");
        return false;
    }
}



// Performed when adding or adjusting the network devices on the edit page
// we basically want to submit the form as an array of values, as  there are multiple arrays 
// certain values need to be padded with null to ensure a consistent sequence
function editTemplatePort()
{
    $("input[name=adjustedPorts]").val("1");
    // get the details of the existing ports on the page
    var portName = $("#newTemplatePort select[name=createPortType] :selected").text();
    var portType = $("#newTemplatePort select[name=createPortType] :selected").val();
    var portCount = parseInt($("#newTemplatePort select[name=createPortCount] :selected").val());

    if(portCount > 0)
    {
        // clear any errors
        $("#newTemplatePort select[name=createPortCount]").removeClass("formError");
        
        // if we have an existing port we need to update its count or undelete it
        if($("#templatePorts tbody tr").hasClass("portType"+portType))
        {
            // get current port count
            var existingPortCount = parseInt($("#templatePorts tbody tr.portType"+portType+" input[name=portCount[]]").val());

            // create new row with addition of new ports
            var newContents = "<td><input type='hidden' name='portType[]' value='"+portType+"' />"+portName+" </td> \
                <td><input type='hidden' name='portCount[]' value='"+(portCount+existingPortCount)+"' />"+(portCount+existingPortCount)+"</td> \
                <td><a onclick='$(this).closest(\"tr\").remove();' >Delete</td>";
            
            $("#templatePorts tbody tr.portType"+portType).html(newContents);
        }
        
        // else no existing row, create new
        else
        {
            var newLine = "<tr class='portType"+portType+"'>\
                <td><input type='hidden' name='portType[]' value='"+portType+"' />"+portName+" </td> \
                <td><input type='hidden' name='portCount[]' value='"+portCount+"' />"+portCount+"</td> \
                <td><a onclick='$(this).closest(\"tr\").remove();' >Delete</td>\
                </tr>";
            $("#templatePorts tbody").append(newLine).fadeIn('fast');
        }
    }
    else
    {
        // error when adding 0 ports
        $("#newTemplatePort select[name=createPortCount]").addClass("formError");
        return false;
    }
}
