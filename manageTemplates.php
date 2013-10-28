<?php
session_start();
$selectedPage="configure";
include "class/db.class.php";

if (isset($_POST['submitTemplate']) && isset($_GET['mode']) && $_GET['mode'] == 'insert')
{
        // Create Device Template
        $template = new template;
        $templates = new templates;
        if(!isset($_POST['deviceType']) || empty($_POST['deviceType']) || $_POST['deviceType']==0)
        {
            echo json_encode(array("error","deviceType"));
            exit();
        }
        
        if(!isset($_POST['templateName']) || empty($_POST['templateName']))
        {
            echo json_encode(array("error","templateName"));
            exit();
        }

        $template->deviceTypeID = $_POST['deviceType'];
        $template->name = $_POST['templateName'];
        

        // loop over each value and detect which categories were posted
        $selectedCategories=array();
        foreach($_POST as $postKey=>$postVal)
            if(preg_match("/category/i", $postKey))
                $selectedCategories[]=preg_replace("/category/i","", $postKey);

        // insert the template and generate an ID for use later
        $templates->insert($template);
        if($template->templateID!=0)
        {
            $catValues = new attrcategoryvalues;
            $values = new attrvalues;
            $names = new attrnames;
            $categoriesToCreate=array();
            $valuesToCreate=array();
            foreach($selectedCategories as $category)
            {
                $cat = new attrcategoryvalue;
                $cat->parentID=$template->templateID;
                $cat->parentType="template";
                $cat->categoryID=$category;
                $categoriesToCreate[]=$cat;

                // detect all the DB required fields for this category and add them to the list for creation
                foreach($names->getByParent($category, 'attrcategory') as $name)
                {
                    $value=new attrvalue();
                    $value->attrnameid=$name->attrnameid;
                    if(isset($_POST['name'.$name->attrnameid]))
                        $value->value=$_POST['name'.$name->attrnameid];
                    $value->parentid=$template->templateID;
                    $value->parenttype='template';
                    $valuesToCreate[]=$value;
                }
            }
            
            $catValues->insertMultiple($categoriesToCreate);
            $values->insertMultiple($valuesToCreate);
            
            // If there are ports to create
            if(isset($_POST['portType']) && is_array($_POST['portType']))
            {
                $portsToCreate=array();
                foreach($_POST['portType'] as $key=>$item)
                {
                    $entry=array();
                    $entry['templateID']=$template->templateID;
                    $entry['portTypeID']=$_POST['portType'][$key];
                    $entry['isJoin']="0";
                    $entry['bandwidth']=$_POST['bandwidth'][$key];
                    $entry['count']=$_POST['count'][$key];
                    $entry['disporder']=($key+1);
                    $portsToCreate[]=$entry;
                }
                if(!$templates->createTemplatePorts($portsToCreate))
                    echo "Error creating template ports";
            }

            // if there are joins to make
            if(isset($_POST['joinPortType']) && is_array($_POST['joinPortType']))
            {
                $joinsToCreate=array();
                // loop over them and fill the array we pass in later
                foreach($_POST['joinPortType'] as $key=>$item)
                {
                    $entry=array();
                    $entry['templateID']=$template->templateID;
                    $entry['portTypeID']=$_POST['portType'][$key];
                    $entry['isJoin']="1";
                    $entry['bandwidth']='';
                    $entry['count']=$_POST['joinCount'][$key];
                    $entry['disporder']=($key+1);
                    $joinsToCreate[]=$entry;
                }
                if(!$templates->createTemplatePorts($joinsToCreate))
                    echo "Error creating template joins";
            }

            //echo json_encode("Created");
            header("Location: templates.php");
        }
        else
            echo "Error creating the initial template, attr not initialised";
}
else
{
    $globalTopic="Create New Template";
    include "theme/" . $theme . "/top.php";
?>
<script type="text/javascript" >
function toggleCategory(catID)
{
   if($("#checkboxfor"+catID+":checked").length!=0)
       $("#category"+catID).slideDown(100);
   else
       $("#category"+catID).slideUp(100);
}

$(document).ready(function() {

   $(".hoverDescTooltip").live("mouseover mouseout",function(event) {
       if(event.type=="mouseover")
       {
            var leftPos = $(this).offset().left+20; //$(event).pageX;
            var topPos = $(this).offset().top; //$(event).pageY;

            // position the popup
            $("body").append('<div id="hoverBox" class="menuBox"><div class="content" id="rackDetails"></div></div>');
            $("#hoverBox").css("position","absolute");
            $("#hoverBox").css("left",leftPos + "px");
            $("#hoverBox").css("top",topPos + "px");
            $("#hoverBox").css("z-index",999);
            $("#hoverBox").show();
            $("#hoverBox .content").html($(this).find(".desc").text());

       }
       else
            $("#hoverBox").remove();
   });
});
</script>


<div id="main"> 
    <div id="left">
	<form method="POST" action="?action=template&mode=insert" enctype="multipart/form-data" >
	<div class="module" id="createTemplate"> 
            <strong>Create Template</strong><br/><br/>
                    <table class="formTable">
                    <colgroup class="tblfirstRow"></colgroup>
                    <tr>
                        <td width="20%" align="right"><label for="manufacture" >Device Type:</label></td>
                        <td align="left">
                        <select id="deviceType" name="deviceType">
                            <option value="0" style="color: #7b7b7b;" >Select Device Type</option>
                            <?php
                                $deviceTypes = new deviceTypes;
                                foreach($deviceTypes->getAll() as $deviceType)
                                {
                                    echo '<option value="'.$deviceType->deviceTypeID.'" ';

                                    if(isset($_GET['deviceType']) && is_numeric($_GET['deviceType']))
                                        if($deviceType->deviceTypeID==$_GET['deviceType'])
                                            echo "SELECTED";
                                    echo '>'.$deviceType->name.'</option>';
                                }
                            ?>
                        </select>
                        </td>
                    </tr>
                    <tr><td align="right"><label for="templateName" >Template Name:</label></td><td><input size="40" type="text" name="templateName" id="templateName" /></td></tr>
                    </table>
            <div class="twoColumns">

                <div style="padding:5px;">Select all traits associated with your template:</div>
                         
                <ul>
                <?php
                    $categories = new attrcategories;
                    $listOfCategories=$categories->getAll();
                    foreach($listOfCategories as $category)
                    {
                ?>

                    <li>
                        <input type="checkbox" name="category<?php echo $category->attrcategoryid ?>" id="checkboxfor<?php echo $category->attrcategoryid ?>" onclick="toggleCategory('<?php echo $category->attrcategoryid ?>');" />
                        <label for='checkboxfor<?php echo $category->attrcategoryid ?>' ><?php echo $category->name; ?></label>
                    </li>
                <?php
                    }
                ?>
                </ul>
                <br style="clear:both;" />
            </div>
           
                <?php
                        foreach($listOfCategories as $category)
                        {
                    ?>
              <div id="category<?php echo $category->attrcategoryid; ?>" style="display:none;">
                  <strong style="display:block;padding:5px;"><?php echo $category->name; ?> Properties</strong>
              <table class="formTable" cellpadding="5">
                      <colgroup class="tblfirstRow"></colgroup>

                    <?php
                        $attributesfound = false;
                        switch($category->attrcategoryid)
                        {
                            case HAS_NETWORK_PORTS:
                                echo "<tr><td colspan='2'><strong>Define the detault ports:</strong>";
                                $ports = new ports;
                                $ports->templateCreateForm();
                                echo "</td></tr>";
                                $attributesfound = true;
                                break;
                            
                            case IS_PATCH:
                                echo "<tr><td colspan='2'><strong>Default Patch Ports:</strong>";
                                $joins = new joins;
                                $joins->templateCreateForm();
                                echo "</td></tr>";
                                $attributesfound = true;
                                break;
                        }
                            $names = new attrnames;
                            foreach($names->getByParent($category->attrcategoryid, 'attrcategory') as $name)
                            {
                    ?>
                            <tr>
                                <td width="20%" align="right"><?php echo $name->name; ?></td>
                                <td width="80%">
                            <?php
                            switch ($name->type)
                            {
                                case "Textbox":
                                    echo "<input type='text' name='name" . $name->attrnameid . "' />";
                                    break;
                                 case "Date":
                                    echo "<input class='date' type='text' name='name" . $name->attrnameid . "' />";
                                    break;
                                case "Date":
                                    echo "<input class='date' type='text' name='name" . $name->attrnameid . "' />";
                                    break;
                                case "Number":
                                    echo "<input style='width:50px;' class='number' type='text' name='name" . $name->attrnameid . "' /> " . $name->units;
                                    break;
                                case "Text Area":
                                    echo "<textarea class='number' rows='5' name='name" . $name->attrnameid . "'></textarea>";
                                    break;
                                case "Checkbox":
                                    echo "<input id='checkbox_".$name->attrnameid."' type='checkbox' name='name" . $name->attrnameid . "' /> <label for='checkbox_".$name->attrnameid."' >Yes/No</label>";
                                    break;
                                case "Radio Buttons":
                                    $values = explode(",",$name->options);
                                    foreach ($values as $key=>$value)
                                    {
                                        echo "<input id='radio_".$name->attrnameid."_".$key."' type='radio' name='name" . $name->attrnameid . "' value='" . $value . "' /> <label for='radio_".$name->attrnameid."_".$key."' >" . $value . "</label> <br />";
                                    }
                                    break;
                                case "Checkbox List":
                                    $values = explode(",",$name->options);
                                    foreach ($values as $value)
                                    {
                                        echo "<input type='radio' name='name" . $name->attrnameid . "' /><br />";
                                    }
                                    break;
                                case "Dropdown List":
                                    $values = explode(",",$name->options);
                                    echo "<select name='name" . $name->attrnameid . "'>";
                                    foreach ($values as $value)
                                    {
                                        echo "<option>" . $name->name . "</option>";
                                    }
                                    echo "</select>";
                                    break;
                                case "File":
                                    break;
                                case "Image":
                                    break;
                            }

                            if($name->desc)
                                echo " <span class='hoverDescTooltip'> ? <span class='desc' style='display:none;'>".$name->desc."</span></span>";
                            ?>
                                <span class="dropDown templateConfigure" style="display:none;">Configure
                                    <ul style="display:none;">
                                        <li>Read/Write Everywhere</li>
                                        <li>Read Only</li>
                                        <li>Hidden</li>
                                    </ul></span>
                                </td>
                            </tr>
                    <?php
                                $attributesfound = true;
                            }
                    if ($attributesfound == false)
                    {
                    ?>
                            <tr><td><em>There are no configurable properties for this trait.</em></td></tr>
                    <?php
                    }

                    ?>

                     </table>
            </div>
                <?php
                        }
                ?>
               
                <br/>
                <div style="text-align: right;">
                    <input type="submit" id="submitTemplate" name="submitTemplate" value="Create Template" />
                </div>
            </div>
    </form>
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

<?php include "theme/" . $theme . "/base.php"; } ?>