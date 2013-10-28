<?php
session_start();
$selectedPage="configure";
include "class/db.class.php";
$cableTypes = new cableTypes;


if(isset($_GET['mode'])) // is page post back
{
    if($_GET['mode'] == 'deleteCategory' && is_numeric($_GET['id']))
    {
        if($cableTypes->deactivateCategory($_GET['id']))
            header("Location: cables.php");
        else
            header("Location: cables.php?error=cannotDisableCategory");
    }

    if($_GET['mode'] == 'deleteCableFromGroup' && is_numeric($_GET['id']))
    {
        if($cableTypes->removeCable($_GET['id']))
            echo 1;
        else
            echo 0;
    }

    if($_GET['mode'] == 'createCategory' && isset($_POST['name']) && isset($_POST['cableType']))
    {
        if($cableTypes->newCategory($_POST['name'], $_POST['cableType']))
            header("Location: cables.php");
        else
            header("Location: cables.php?error=cannotCreateCategory");
    }


    if($_GET['mode'] == 'createCableType' && isset($_POST['name']) && isset($_POST['cableType']))
    {
        if($cableTypes->newCableType($_POST['name'], $_POST['cableType']))
            header("Location: cables.php");
        else
            header("Location: cables.php?error=cannotCreateCategory");
    }

    if($_GET['mode'] == 'addToCategory' && is_numeric($_GET['catID']) && is_numeric($_GET['cableType']))
    {
        $return=0;
        $return=$cableTypes->addCableTypeToCategory($_GET['catID'], $_GET['cableType']);
        echo $return;
    }
}
else
{

$allCableTypes = $cableTypes->getAll();

$globalTopic = "Cable Standards"; // page title
include "theme/" . $theme . "/top.php";
?>
<link rel='stylesheet' href='theme/cables.css' type='text/css' />
<script type="text/javascript" >
$(document).ready(function() {

    $(".cableCategories div .close").bind("click",function(){
        if(confirm("Are you sure you wish the delete this Cable Group from future use?"))
        {
            var catID=$(this).parents(".acceptCable").attr("id").replace(/[^0-9]/g, '');
            $.ajax({
                url: 'cables.php?mode=deleteCategory&id='+catID,
                context: $(this),
                success: function(data) {
                    $(this).parents(".acceptCable").fadeOut("fast",function() { $(this).remove() }); 
                }
            });            
        }
    });

    $("ul li span.delete").live("click",function() {
        var itemID = $(this).parent().attr("id").replace(/[^0-9]/g, '');
        $.ajax({
            url: 'cables.php?mode=deleteCableFromGroup&id='+itemID,
            context: $(this),
            success: function(data) {
                if(data=="1")
                {
                    // fade the item out then remove it
                    $(this).parent().fadeOut("fast",function() {
                        // If we are removing the last item, place the help text in the box
                        if($(this).closest(".acceptCable").find("li").size()==1)
                            $(this).closest(".acceptCable ul").append("<em class='empty'>Drop cable to add</em>");

                        $(this).remove();
                    });
                }
                else
                    alert("failed to delete entry, please refresh");
            }
        });
    });

    $(".cableCategories div .close").live("mouseover mouseout",function(event) {
        //alert($(this).parents(".acceptCable").html());
          if (event.type == 'mouseover') {
                $(this).parents(".acceptCable").addClass("deletableBox");
          } else {
            $(this).parents(".acceptCable").removeClass("deletableBox");
          }
    });
    
    function bindActions()
    {
        $(".draggableCable").draggable({ revert: true});
        $(".acceptCable").droppable({accept: ".draggableCable",
            drop: function( event, ui ) {
                var catID=$(this).attr("id").replace(/[^0-9]/g, '');
                var newItemType = $(ui.draggable).attr('alt').replace(/[^0-9]/g, '');

                // If the cable already exists we can stop here
                if($(this).find('li[alt=cableTypeID'+newItemType+']').length)
                {
                    $(this).removeClass("acceptingCable");
                    return false;
                }

                // add to the category 
                $.ajax({
                    url: 'cables.php?mode=addToCategory&catID='+catID+"&cableType="+newItemType,
                    context: $(this),
                    success: function(entryID) {
                        $(this).find(".empty").remove();
                        // duplicate the cable item and drop it into the box
                        var cableBox = $(ui.draggable).clone().css("position","").css("top","").css("left","");
                        $(cableBox).attr("id","entryID"+entryID); // this is used later when deleting
                        $(cableBox).appendTo($(this).find("ul"));
                        $(this).removeClass("acceptingCable");
                        bindActions();
                    }
                });


            },
                over: function(event, ui) { $(this).addClass("acceptingCable"); },
                out: function(event, ui) { $(this).removeClass("acceptingCable"); }
        });
    };
    bindActions();

});
</script>
<div id="main">
<div id="left">

    <div class="module" id="createCategory" style="display: none;">
        <div class="sectionHeader" >
            <strong>New Cable Category</strong>
            <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
        </div>
	<p>
            <div class="form" align="center">
            <form method="post" action="?mode=createCategory" >
            <table width="80%" class="formTable">
                <colgroup align="left" class="tblfirstRow"></colgroup>
                <tr>
                    <td width="100px"><label for="name" >Name:</label></td>
                    <td><input name="name" type="text" id="name" value="" /></td>
                </tr>
                <tr>
                    <td><label for="cabletype" >Cable Type:</label></td>
                    <td><input type="radio" name="cableType" value="1" id="cableTypeData" CHECKED/><label for="cableTypeData" >Data </label><br/>
                        <input type="radio" name="cableType" value="2" id="cableTypePower"/><label for="cableTypePower" >Power</label></td>
                </tr>
                <tr>
                    <td></td><td><input type="submit" name="btnSubmit" value="Create" /></td>
                </tr>
            </table>
            </form>
            </div>
	</p>
    </div>

    <div class="module" id="createCableType" style="display: none;">
        <div class="sectionHeader" >
            <strong>New Cable Standard</strong>
            <a href="#" class="closeLink closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
        </div>
	<p>
            <div class="form" align="center">
            <form method="post" action="?mode=createCableType" >
            <table width="80%" class="formTable">
                <colgroup align="left" class="tblfirstRow"></colgroup>
                <tr>
                    <td width="100px"><label for="name" >Name:</label></td>
                    <td><input name="name" type="text" id="name" value="" /></td>
                </tr>
                <tr>
                    <td>Cable Type:</td>
                    <td><input type="radio" name="cableType" value="1" id="cableTypeData" CHECKED/><label for="cableTypeData" >Data </label><br/>
                        <input type="radio" name="cableType" value="2" id="cableTypePower"/><label for="cableTypePower" >Power</label></td>
                </tr>
                <tr>
                    <td></td><td><input type="submit" name="btnSubmit" value="Create" /></td>
                </tr>
            </table>
            </form>
            </div>
	</p>
    </div>

	<div class="module" id="cableExplaination">
            <strong>Cables Standards</strong>
            <p>
                Ports can accept cables of different standards (eg. cat5, cat5e, cat6) you may want to manage the grouping of these connection types.<br/>
                Each port or patch panel gets assignment one of the following categories which defines the cables it accept.
            </p>

            <strong>Port Types</strong>
            <p>
            <div class="cableCategories listObjects" >

                <?php
                // loop over all categories and display enabled ones
                $categories = $cableTypes->getCategories();
                if($categories)
                    foreach($categories as $cat)
                    {
                        if($cat['enabled'])
                        {?>
                
                        <div class="genericDropBox acceptCable" id="categoryID<?php echo $cat['categoryID']; ?>">
                            <table cellspacing="0" cellpadding="0"><tr>
                                    <td width="100%"><strong><?php echo $cat['name']; ?></strong></td>
                                    <td><span class="close">x</span></td></tr></table>
                            <ul>
                            <?php
                            // for each device alreddy in this category display its details
                            $entries=$cableTypes->getCategoryEntries($cat['categoryID']);
                            if(count($entries)==0)
                            {
                                echo "<em class='empty'>Drop cable to add</em>";
                            }
                            else
                            {
                                foreach($entries as $entry)
                                {
                                    // we need to pull its name and specs out of the array we created on load
                                    echo "<li class='draggableCable ";
                                    if($allCableTypes[$entry['cableTypeID']]->isPower)
                                        echo "powerCable";
                                    else
                                        echo "dataCable";
                                    // set correct ID so we can reference
                                    echo "' id='entryID".$entry['entryID']."' alt='cableTypeID".$entry['cableTypeID']."'><div class='name'>".$allCableTypes[$entry['cableTypeID']]->name."</div><span class='delete'></span></li>";
                                }
                            }
                            ?>
                            </ul>
                        </div>
                    <?php
                        }
                    }
                ?>
                <div id="createNewCategory" class="genericDropBox" onclick="$.openDOMWindow({ fixedWindowY: 50, borderColor: '#3b4c50', borderSize: 2,windowPadding: 0,width:'580',height: '220',overlayOpacity: '30', windowSourceID: '#createCategory'});"><strong>Click for New Category</strong></div>
            </div>
            </p>

            <strong>Available Cable Connections</strong>
            <p>
                Drag into the categories above.
            <div id="cableInventory" class="genericDropBox listObjects" >
                <ul>
                    <?php
                    foreach($allCableTypes as $type)
                    {
                        echo "<li class='draggableCable ";
                        if($type->isPower)
                            echo "powerCable";
                        else
                            echo "dataCable";
                        echo "' alt='cableTypeID".$type->cableTypeID."'><div class='name' >".$type->name."</div><span class='delete' ></span></li>";
                    }
                    ?>
                    <li id="addNewCableType" onclick="$.openDOMWindow({fixedWindowY: 50, borderColor: '#3b4c50', borderSize: 2,windowPadding: 0, width:'580',height: '220',overlayOpacity: '30', windowSourceID: '#createCableType'});">Add New Type</li>
                </ul>
                
            </div>
            </p>

        </div>
    <div style="clear: both;"></div>
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
}
?>