<?php
session_start();
include "class/db.class.php";
$attachments = new attachments;

if(!isset($_GET['action']) && isset($_GET['assetType'],$_GET['assetID']) && is_numeric($_GET['assetID']))
{?>
        <strong>Attached Files</strong>
        <a href="#" class="closeDOMWindow"><img src="images/icons/close_module.gif" border="0" alt="Close" /></a>
        <p>
            <script type="text/javascript">
            $(document).ready(function()
            {
                // binds associated with the attachments system
                $('.delAttachment').live('click',function() {
                    var objectID=$(this).attr('id').replace(/[^0-9]/g, '');
                    if (confirm('Are you sure you want to delete this attachment?'))
                    {
                        $.ajax({
                        type: "GET",
                        url: "attachments.php",
                        dataType: "json",
                        data: "action=delete&objectID="+objectID,
                        success: function(msg){
                            if(msg==1)
                                $('#attachmentRow'+objectID).slideUp();
                            else
                                alert("Error deleting attachment, return: "+msg);
                        }
                        });
                    }
                });
            });
          </script>
            <table width="100%" cellpadding="2" cellspacing="0" class="dataTable">
                <thead><tr><th>Filename:</th><th>Comment:</th><th>Uploaded:</th><th>User:</th><th>Options:</th></tr>
                    </thead>
                    <tbody>
        <?php
        $objects = new attachments();
        $list=$objects->getObjects($_GET['assetID'],$_GET['assetType']);
        
        if($list)
        {
            foreach($list as $object)
            {
                echo "<tr id='attachmentRow".$object['objectID']."'><td><a href='attachments.php?id=".$object['objectID']."' target='_dl' >".$object['filename']."</a></td>
                    <td>".$object['name']."</td><td>".$object['date']."</td><td>".$object['user']."</td><td><a class='delAttachment' id='attachment".$object['objectID']."'>Delete</a></td></tr>";
            }
        }
        else
            echo "<tr><td colspan='5' ><i>No attachments found</i></td>";
        ?>
                    </tbody>
            </table>
        </p>
        <hr/>
        <strong>Add</strong>
        <p>
            <form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>?action=upload" enctype="multipart/form-data">
            <input type="hidden" name="assetType" value="<?php echo $_GET['assetType']; ?>" />
            <input type="hidden" name="assetID" value="<?php echo $_GET['assetID']; ?>" />
            <table width="100%" cellpadding="2" cellspacing="0" class="dataTable">
                <colgroup align="left" class="tblfirstRow"></colgroup>
                <tr><td>File:</td><td><input type="file" name="file" /></td></tr>
                <tr><td>Comment:</td><td><input type="text" name="comment" size="100" /></td></tr>
                <tr><td colspan="2" ><input type="submit" name="Upload" value="Upload" /></td></tr>
            </table>
            </form>
        </p>
   <?php
}
else if(isset($_GET['action']) && $_GET['action']=="delete" && @is_numeric($_GET['objectID']))
{
    $action = $attachments->delObject($_GET['objectID']);
    if($action)
        echo json_encode($action);
    else
        echo json_encode(0);
}
else if(isset($_POST,$_FILES,$_GET['action']) && $_GET['action']=="upload")
{
    $meta['name']=$_POST['comment'];
    $work = $attachments->placeObject($_FILES, $_POST['assetID'], $_POST['assetType'], $meta);
    if($work[0][0]!=0)
        header("Location: racks.php");
    else
    {
        echo "<b>Error:</b> Unable to upload file.<br/>The error returned was <br/>
        <i>".$work[0][1]."</i><br/><br/>Please report this on the RackSmith forum";
        exit(0);
    }
}
else if(isset($_GET['id']) && is_numeric($_GET['id']))
{
    $file = $attachments->getObject($_GET['id']);
    if(file_exists($file['filepath']) && isset($file['filename']))
    {
        set_time_limit(0);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file['filename']));
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file['filepath']));
        @ob_clean();
        @flush();
        readfile($file['filepath']);
    }
    else
        echo "<b>Error</b> Unable to find file for download";
}
else
    echo "<B>Error:</b> Unable to load attachment module, not enough information was provided";
?>