<?php
session_start();
$selectedPage="configure";
include "class/db.class.php";

$categories = new attrcategories;

if (isset($_GET['mode']))
{
    if($_GET['mode'] == 'createCategory' && isset($_POST['name']))
    {
            $category = new attrcategory;
            $category->name = $_POST['name'];
            $categories->insert($category);
            header("Location: metadata.php");
    }
    if($_GET['mode'] == 'deleteMetadata' && isset($_GET['id']))
    {
            $names = new attrnames;
            $names->delete($_GET['id']);
            header("Location: metadata.php?category=" . $_GET['category']);
    }
    if($_GET['mode'] == 'deleteCategory' && isset($_GET['id']))
    {
            $categories->delete($_GET['id']);
            header("Location: metadata.php");
    }
    if ($_GET['mode'] == 'createMetadata')
    {
        $names = new attrnames;
        $name = new attrname;
        $name->name = $_POST['name'];
        $name->parentid = $_POST['category'];
        $name->parenttype = "attrcategory";
        $name->type = $_POST['type'];
        $name->default = $_POST['default'];
        $name->units = $_POST['units'];
        $name->desc = $_POST['desc'];
        $id = $names->insert($name);

        $options = new attroptions;
        $option = new attroption;
        if ($_POST['options'] == "")
        {
            $optionList = explode(",",$_POST['options']);
            $option->attrnameid = $id;
            foreach ($optionList as $opt)
            {
                $option->name = $opt;
                $options->insert($option);
            }
        }
        header("Location: metadata.php?category=" . $_POST['category']);
    }
    if($_GET['mode'] == 'saveCategoryOrder' && isset($_POST['data']))
    {
        $attrcategories = new attrcategories;
        echo $attrcategories->update_sort($_POST['data']);
        exit();
    }

    if($_GET['mode'] == 'saveNameOrder' && isset($_POST['data']))
    {
        $attrnames = new attrnames;;
        echo $attrnames->update_sort($_POST['data']);
        exit();
    }
}
$globalTopic = "Device data &amp; Attributes"; // page title
include "theme/" . $theme . "/top.php";
?>
<script>

function getData()
{
    var data = "action=attrnames&attrcategory=" + $("[name=attrcategory]").val();
    $.post("handler.php",data,function(data)
    {
        // Clear the table of search results
        $("#attrnames table tbody").html("");
        var count=0;
        // loop over each returned cable
        $.each(data, function(item,name)
        {
                var item = "<tr id=rowItem"+name['attrnameid']+"'><td>" + name['name'] +
                    "</td><td>" + name['type'] + "</td><td>" + name['default'] +
                    "</td><td>" + name['units'] + "</td><td>"+name['desc']+"</td><td >";

                    if(name['static']!=1)
                        item+= "<a href='#' >Edit</a> :: <a href='?category=" + $("[name=attrcategory]").val() + "&mode=deleteMetadata&id=" + name['attrnameid'] + "'>Delete</a>";
                    item+="</td><td align='center'><span class='handle' ><img src='images/icons/drag_list.gif' alt='Sort' /></span></td>" +
                "</tr>";

            $("#attrnames table tbody").append(item);
            count++;
        });
        if(count==0)
        {
            $("#attrnames table tbody").append("<tr><td colspan='7'><em>This trait currently has no attributes</em></td></tr>");
        }
    },"json");
}

$(document).ready(function() {

    <?php
    if(isset($_GET['category']) && is_numeric($_GET['category']))
        echo "getData();";
    ?>
    $('[name=attrcategory]').change(function() {
        $("#addNewOptionBtn").show();
        getData();
    });

    //getData();

    $("#traitsTable tbody").sortable({
        items: "tr",
        handle: "span.handle",
        update : function ()
        {
            var sequence = $(this).sortable('toArray');
         $.ajax
            ({
                type: "POST",
                url: "metadata.php?mode=saveCategoryOrder",
                data: "data="+sequence,
                async: false,
                success: function(responce)
                {
                    if(responce==0)
                        alert("Error: Unable to save list order");
                }
            })
        }
    });

    $("#attrnames table tbody").sortable({
        items: "tr",
        handle: "span.handle",
        update : function ()
        {
            var sequence = $(this).sortable('toArray');
         $.ajax
            ({
                type: "POST",
                url: "metadata.php?mode=saveNameOrder",
                data: "data="+sequence,
                async: false,
                success: function(responce)
                {
                    if(responce==0)
                        alert("Error: Unable to save list order");
                }
            })
        }
    });

});
</script>

<div id="main"> 
<div id="left">

    <div class="module" id="createCategory" style="display:none;">
	<strong>Traits</strong>
            <a href="#" class="closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
            <div class="form" align="center">
            <table class="formTable" id="traitsTable">
		<colgroup align="left" class="tblfirstRow"></colgroup>
		<thead>
		<tr>
                    <th scope="col" width="450px">Name</th>
                    <th align="center" width="110px"></th>
		</tr>
		</thead>
                <tbody>
                <tr class="creationRow">
                    <td><form method="post" action="?mode=createCategory" ><input name="name" type="text" id="name" value="" size="60"/></td>
                    <td><input type="submit" name="btnSubmit" value="Create" /></form></td>
                </tr>
             <?php

                foreach ($categories->getAll() as $row)
                {
                ?>
                <tr id="rowItem<?php echo $row->attrcategoryid;?>">
                        <td width="450px"><?php echo $row->name; ?></td>
                        <td width="110px" align="right">
                            <?php if ($row->static != 1) { ?><a href="?mode=deleteCategory&id=<?php echo $row->attrcategoryid; ?>">Delete</a> - <?php } ?>
                            <span class="handle" ><img src="images/icons/drag_list.gif" alt="Sort" /></span></td>
                </tr>
                <?php
                }
                ?>
                </tbody>
                </table>
            </div>
    </div>


    <div class="module" id="createMetadata" style="display:none">
        <script>
        $('[name=type]').change(function() {
                $("#unitList").hide();
            switch($(this).val())
            {
                case 'Number':
                    $("#unitList").show();
                case 'Textbox':
                case 'Date':
                case 'Text Area':
                case 'Image':
                case 'File':
                case 'Checkbox':
                    $("#optionsList").hide();
                    break;
                default:
                    $("#optionsList").show();                    
            }
        });
        </script>
	<strong>New Metadata</strong>
	<a href="#" class="closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
            <div class="form" align="center">
            <form method="post" action="?mode=createMetadata" >
            <table width="80%" class="formTable">
                <colgroup align="left" class="tblfirstRow"></colgroup>
                <tr>
                    <td>Category:</td><td id="createCategoryName"></td>
                </tr>
                <tr>
                    <td align="left" width="100"><label for="name" >Name:</label></td>
                    <td align="left"><input name="name" type="text" id="name" value="" /><input type="hidden" name="category" /></td>
                </tr>
                <tr>
                    <td align="left"><label for="type" >Type:</label></td>
                    <td align="left"><select name="type">
                            <option>Textbox</option>
                            <option>Number</option>
                            <option>Date</option>
                            <option>Text Area</option>
                            <option>Checkbox</option>
                            <option>Radio Buttons</option>
                            <option>Checkbox List</option>
                            <option>Dropdown List</option>
                            <option>File</option>
                            <option>Image</option>
                        </select></td>
                </tr>
                <tr id="optionsList" style="display:none;">
                    <td align="left"><label for="options">Options</label></td>
                    <td align="left"><input name="options" /> enter comma separated values</td>
                </tr>
                <tr>
                    <td width="100" align="left"><label for="default">Default Value:</label></td>
                    <td align="left"><input name="default" /> (optional)</td>
                </tr>
                <tr id="unitList" style="display:none;">
                    <td align="left"><label for="units">Units:</label></td>
                    <td align="left"><input name="units" /> (optional)</td>
                </tr>
                <tr>
                    <td align="left"><label for="desc">Description:</label></td>
                    <td align="left"><input name="desc" size="70"/> (optional)</td>
                </tr>
                <tr>
                    <td></td><td align="left"><input type="submit" name="btnSubmit" value="Create" /></td>
                </tr>
                <tr>


                    
                </tr>
            </table>
            </form>
            </div>
    </div>

	<div class="module" id="manageMetaData">
	<strong>Meta Data</strong>
	<p>
            Devices within RackSmith can store a wide range of information, here we define the defaults which are applied to the default categories within RackSmith.</p>
        <p>
            Each device can have a collection of the following traits, the values within each of them are then associated by default.
        </p>
        Trait: <select name="attrcategory">
            <option value='0' disabled>-- Select Trait --</option>
            <?php
                foreach ($categories->getAll() as $row)
                {
            ?>
                <option <?php if (isset($_GET['category']) && $_GET['category'] == $row->attrcategoryid) { echo "selected=selected"; } ?> value="<?php echo $row->attrcategoryid; ?>"><?php echo $row->name; ?></option>
            <?php
                }
            ?>
            

        </select> <a href="#" onclick="$.openDOMWindow({ width:'580',height: '450',overlayOpacity: '30', windowSourceID: '#createCategory'});">[Add &amp Edit]</a> <br /><br />

        <input type="button" value="Add New Option" style="display:none;" id="addNewOptionBtn" onclick="$.openDOMWindow({ width:'580',height: '220',overlayOpacity: '30', windowSourceID: '#createMetadata'});$('[name=category]').val($('[name=attrcategory]').val());$('#createCategoryName').html($('[name=attrcategory] option:selected').text());">

	<div id="attrnames">
		<table class="dataTable">
		<colgroup align="left" class="tblfirstRow"></colgroup>
		<thead> 
		<tr> 
                    <th scope="col" width="15%">Name</th>
                    <th scope="col" width="10%">Type</th>
                    <th scope="col" width="5%">Default</th>
                    <th scope="col" width="4%">Units</th>
                    <th scope="col" width="55%">Description</th>
                    <th scope="col" width="10%" align="center">Options</th>
                    <th scope="col" width="1%" align="center">Sort</th>
		</tr> 
		</thead> 
		<tbody>
                    <tr><td colspan="7" ><em>Please select a trait above to start configuring</em></td></tr>
                </tbody>
                </table>
                </div>
		</div>	
	</div>

	<div id="right">
        <div class="module sideMenu">
            <strong>Devices &amp; Templates</strong>
            <p>
                <ul>
                    <li><a href="manageTemplates.php">Create a new Template</a></li>
                    <li><a href="templates.php">Templates List</a></li>
                    <li><a href="templateMover.php">Import/Export Templates</a></li>
                    <li><a href="metadata.php" >Device Information</a></li>
                </ul>
            </p>
        </div>

        <div class="module sideMenu">
            <strong>System</strong>
            <p>
            <ul>
                <li><a href="cables.php" >Cable Types</a></li>
                <li><a href="owners.php" >Equipment Owners</a></li>
                <li><a href="management.php?action=accounts" >User Accounts</a></li>
                <li><a href="system.php" >System</a></li>
                <li><a href="logs.php" >Logs</a></li>
                <li><a href="templateMover.php" >Import / Export Templates</a></li>
            </ul>
            </p>
        </div>
	</div>
</div>
<?php
include "theme/" . $theme . "/base.php";
?>