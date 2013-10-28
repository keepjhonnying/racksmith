<?php


function uploadFile($file,$path='uploads/',$saveFilename='0',$overwrite=0) 
{
        $maxSize=40;    // max upload size in MB
	$acceptedExtensions=array("jpg","jpeg","gif","png");
	$acceptedMimes=array("image/jpg","image/jpeg","image/gif","image/png");
	
	if((isset($file)) && ($file['error'] == 0)) 
	{
		// get name and extenson
		$filename = basename($file['name']);
		$ext = substr($filename, strrpos($filename, '.') + 1);
		$ext = strtolower($ext);
	  
		// check if we're allowed extension and size is < 2MB (value is in bytes)
		if((in_array($ext, $acceptedExtensions)) && (in_array($file["type"],$acceptedMimes)) && ($file["size"] < ($maxSize*1024*1024)))
		{
			// if a filename was passed to the function use it
			// else use uploaded name
			if(!$saveFilename)			
				$newname = $path.$filename;
			else
				$newname = $path.$saveFilename;

			// we don't want to overwrite anything
			if (!file_exists($newname) || $overwrite) 
			{
				// actually upload the file
				if((@move_uploaded_file($file['tmp_name'],$newname))) 
					return 1;
				else
					return "error_uploading_moving";
			} 
			else 
			return "file_exists";
		}
		else
		return "file_size_or_ext";
	} 
	else 
	return "no_file_provided";
}
?>