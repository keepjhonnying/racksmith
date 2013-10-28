<?php
session_start();
include "class/db.class.php";

if (isset($_POST['submitTemplate']) && is_numeric($_POST['id']) && isset($_GET['mode']) && $_GET['mode'] == 'edit')
{
    $template = new template($_POST['id']);
    $template->name=$_POST['name'];
    $template->deviceTypeID=$_POST['deviceType'];
    $templates = new templates;
    $templates->update($template);


    
    $template->fillCategories(1,1);
    // get an array of the selected categories in this post
    $selectedCategories=array();
    $valueIDsToUpdate=array();
    foreach($_POST as $postKey=>$postval)
    {
        if(preg_match("/category/i", $postKey))
            $selectedCategories[]=preg_replace("/category/i","", $postKey);

        if(preg_match("/existing/i", $postKey))
            $valueIDsToUpdate[]=preg_replace("/existing/i","", $postKey);
    }

    // which were set before edit
    $categoriesToDelete=array();
    $categoriesToLeave=array();
    foreach($template->categories as $catKey=>$catVal)
    {
        // if the category doesnt have a match then we must delete it
        if(!in_array($catKey, $selectedCategories))
            $categoriesToDelete[]=$catKey;
        // everything else is new or edited
        else
            $categoriesToLeave[]=$catKey;
    }
    // conbine the del/leave array and figure out which items are new entries
    $items=array_merge($categoriesToDelete,$categoriesToLeave);
    $categoriesToCreate=array_diff($selectedCategories,$items);

    // from here create all these new entries and insert them
    $catValues = new attrcategoryvalues;
    $values = new attrvalues;
    $names = new attrnames;
    $categortyObjectToCreate=array();
    $valuesToCreate=array();
    foreach($categoriesToCreate as $key=>$category)
    {
        $cat = new attrcategoryvalue(0);
        $cat->parentID=$template->templateID;
        $cat->parentType="template";
        $cat->categoryID=$category;
        $categortyObjectToCreate[]=$cat;

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

    $catValues->insertMultiple($categortyObjectToCreate);
    $values->insertMultiple($valuesToCreate);

    // update array [<valueID>]=newVal
    $arrayOfUpdates=array();
    foreach($valueIDsToUpdate as $item)
        $arrayOfUpdates[$item]=$_POST['existing'.$item];
    $values->updateMultipleValues($arrayOfUpdates);

    // for each of the cats we need to delete
    $valuesToDelete=array();
    foreach($categoriesToDelete as $calDel)
    {
        // go within and determine the values we must delete
        foreach($names->getByParent($calDel, 'attrcategory') as $name)
            $valuesToDelete[]=$name->attrnameid;
    }
    // delete the values and the parent categories (there is no table for namevalues atm)
    $values->deleteMultiple($template->templateID,'template',$valuesToDelete);
    if(isset($categoriesToDelete[0]))
        $catValues->deleteMultiple($template->templateID,'template',$categoriesToDelete);

    foreach($selectedCategories as $catVal)
    {
        switch($catVal)
        {
            case HAS_NETWORK_PORTS:
                $ports = new ports;
                $ports->templateEditPost($template->templateID,$_POST);
                break;

            case IS_PATCH:
                $joins = new joins;
                $joins->templateEditPost($template->templateID,$_POST);
                break;
        }
    }

    header("Location: editTemplates.php?mode=edit&id=".$template->templateID."&status=updated");
}

elseif(is_numeric($_GET['id']))
{
    $template = new template($_GET['id']);
    if(!$template->name)
    {
        header("Location: templates.php?error=templateNotFound");
        exit(0);
    }
    $template->fillCategories(1,1);
	
$globalTopic="Edit a Template";
include "theme/" . $theme . "/top.php";
?>
<script type="text/javascript" >
function toggleCategory(catID)
{
   // count the items in this category to determine if we toggle
   // smooths out the animation for empty cats
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
    <form method="post" action="?action=template&mode=edit" enctype="multipart/form-data" >
        <input type='hidden' name='id' value='<?php echo $template->templateID;?>'/>
        <div class="module" id="editTemplate">
	<strong>Edit A Template</strong> 
	<p>
            <table width="80%" class="formTable">
                <colgroup align="left" class="tblfirstRow"></colgroup>
            <tr>
                <td width="150px" align="right"><label for="deviceType" >Device Type:</label></td>
                <td><select name='deviceType' ><?php
                    $names = new deviceTypes();
                    foreach($names->getAll() as $name)
                    {
                        echo "<option value='".$name->deviceTypeID."' ";
                        if($template->deviceTypeID == $name->deviceTypeID)
                            echo "SELECTED";
                        echo ">".$name->name."</option>";
                    }
                ?></select></td>
            </tr>
            <tr><td align="right"><label for="name" >Template Name:</label></td><td><input type='text' name='name' id='name' value="<?php echo $template->name; ?>" /></td></tr>
            <tr><td colspan="2" ><em>NOTE: Changes are not applied to existing devices and will only appear within new entries</em></td></tr>
            </table>
                
            <div class="twoColumns">
                <div style="padding:5px;">Select all traits associated with your template:</div>
                <ul>
                <?php
                    $categoriesClass = new attrcategories;
                    $listOfCategories=$categoriesClass->getAll();
                    foreach($listOfCategories as $category)
                    {
                ?>
                    <li>
                        <input type="checkbox" name="category<?php echo $category->attrcategoryid ?>" id="checkboxfor<?php echo $category->attrcategoryid ?>" onclick="toggleCategory('<?php echo $category->attrcategoryid ?>');"
                               <?php
                               if(isset($template->categories[$category->attrcategoryid]))
                                   echo "CHECKED";
                               ?>/>
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
                    echo "<div id='category".$category->attrcategoryid."'";
                    if(!isset($template->categories[$category->attrcategoryid]))
                        echo " style='display:none;' ";
                ?> >
                  <strong style="display:block;padding:5px;"><?php echo $category->name; ?></strong>
                  <table class="formTable" cellpadding="5">
                      <colgroup class="tblfirstRow"></colgroup>
                <?php
                $names = new attrnames;
                
                switch($category->attrcategoryid)
                {
                    case HAS_NETWORK_PORTS:
                        echo "<tr><td colspan='2'><strong>Define the detault ports:</strong>";
                        $ports = new ports;
                        $ports->templateEditForm($template->templateID);
                        echo "</td></tr>";
                        $attributesfound = true;
                        break;

                    case IS_PATCH:
                        echo "<tr><td colspan='2'><strong>Default Patch Ports:</strong>";
                        $joins = new joins;
                        $joins->templateEditForm($template->templateID);
                        echo "</td></tr>";
                        $attributesfound = true;
                        break;
                }


                // we now have 2 lists of categories
                // list of categories contains everything
                // template->category[<catID>][<nameID>]-> contains registered values 
                foreach($names->getByParent($category->attrcategoryid, 'attrcategory') as $name)
                {
                    // display the title, hide it if the internal list of values doesnt exist (value isnt set)
                    if(isset($template->categories[$category->attrcategoryid][$name->attrnameid]) && is_object($template->categories[$category->attrcategoryid][$name->attrnameid]))
                    {
                        echo "<tr><td width='150px' align='right' >".$name->name."</td><td>";
                        $itemName="existing".$template->categories[$category->attrcategoryid][$name->attrnameid]->attrvalueid;

                        $itemValue=$template->categories[$category->attrcategoryid][$name->attrnameid]->value;
                    }
                    else
                    {
                        echo "<tr><td width='150px' align='right'>".$name->name."</td><td>";
                        $itemName="name".$name->attrnameid;
                        $itemValue="";
                    }
                    // Values that already exist we name differently in the form
                    // existing<attrvalueid> so we can simply update the existing record (or delete as needed)
                    // new values all get named name<attrnameid>

                    switch ($name->type)
                    {
                        case "Textbox":
                            echo "<input type='text' name='".$itemName."' value='".$itemValue."' />";
                            break;
                         case "Date":
                            echo "<input class='date' type='text' name='".$itemName."' value='".$itemValue."' />";
                            break;
                        case "Number":
                            echo "<input style='width:50px;' class='number' type='text' name='".$itemName."' value='".$itemValue."' /> " . $name->units;
                            break;
                        case "Text Area":
                            echo "<textarea class='number' rows='5' name='".$itemName."' >".$itemValue."</textarea>";
                            break;
                        case "Checkbox":
                            echo "<input type='checkbox' name='".$itemName."' "; if($itemValue) { echo "CHECKED"; } echo "/>";
                            break;
                        case "Radio Buttons":
                            $values = explode(",",$name->options);
                            foreach ($values as $key=>$value)
                            {
                                echo "<input id='radio_".$itemName."_".$key."' type='radio' name='".$itemName."' value='" . $value . "' "; if($itemValue==$value) { echo "CHECKED"; } echo "/> <label for='radio_".$itemName."_".$key."' >" . $value . " </label><br />";
                            }
                            break;
                        case "Checkbox List":
                            $values = explode(",",$name->options);
                            foreach ($values as $value)
                            {
                                echo "<input type='radio' name='".$itemName."' "; if($itemValue==$value) { echo "CHECKED"; } echo "/><br />";
                            }
                            break;
                        case "Dropdown List":
                            $values = explode(",",$name->options);
                            echo "<select name='".$itemName."'>";
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
                    echo "</td></tr>";
                }
                echo "</table><hr/></div>";
            }
            ?>
            </table>
	</p>
        
	<div style="float: right; margin: 0px;top: 0px;" >
            <input type="submit" id="submitTemplate"  name="submitTemplate"  value="Edit Template"  />
	</div><br/>
	
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

<?php
if(isset($_GET['status']) && $_GET['status']=="updated")
{
    echo '<div class="module completed" >
        <strong>Saved Changes!</strong>
    </div>';
} ?>
</div>
</form> 
<?php
include "theme/" . $theme . "/base.php";
}
?>