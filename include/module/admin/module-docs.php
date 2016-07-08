<?php
if (!is_writable(UPLOAD_FILES_PATH)) {
	MSV_MessageError("Can't write to ".UPLOAD_FILES_PATH);
}


$galleryList = array();
$filesList = array();


$media_dir_default = UPLOAD_FILES_PATH."/images";
$media_dir = $media_dir_default;
if (!empty($_REQUEST["path"])) {
	// TODO+++++
	// sanitrize $_REQUERS["path"]
	$media_dir = $_REQUEST["path"];
}

$media_url = substr($media_dir, strlen(UPLOAD_FILES_PATH));
$media_url = HOME_URL.substr(CONTENT_URL, 1).$media_url;

if ($media_dir !== $media_dir_default) {
	$galleryList[$media_dir] = "..";
}

if ($handle = opendir($media_dir)) {
    while (false !== ($entry = readdir($handle))) {
    	if (strpos($entry, ".") === 0) {
    		continue;
    	}
    	$filePath = $media_dir."/".$entry;
    	if (!is_dir($filePath)) {
    		
    	}
    	$pathinfo = pathinfo($filePath);
    	$basename = $pathinfo["basename"];
    	$galleryList[$filePath] = $basename;
    }
	closedir($handle);
}




///

$media_dir_default = UPLOAD_FILES_PATH."/users";
$media_dir = $media_dir_default;
if (!empty($_REQUEST["allpath"])) {
	// TODO+++++
	// sanitrize $_REQUERS["path"]
	$media_dir = $_REQUEST["allpath"];
}

$media_url = substr($media_dir, strlen(UPLOAD_FILES_PATH));
$media_url = HOME_URL.substr(CONTENT_URL, 1).$media_url;

if ($media_dir !== $media_dir_default) {
	$filesList[$media_dir] = "..";
}

if (file_exists($media_dir) && $handle = opendir($media_dir)) {
    while (false !== ($entry = readdir($handle))) {
    	if (strpos($entry, ".") === 0) {
    		continue;
    	}
    	$filePath = $media_dir."/".$entry;
    	if (!is_dir($filePath)) {
    		
    	}
    	$pathinfo = pathinfo($filePath);
    	$basename = $pathinfo["basename"];
    	$filesList[$filePath] = $basename;
    }
	closedir($handle);
}
///





$media_table = admin_build_image_library();
$files_table = admin_docs_build_list($filesList, "allpath");


$media_files = "
<div id='media_table'>
$files_table
</div>";

$media_library = "
<div id='media_table'>
$media_table
</div>";


$media_upload = '<fieldset class="legend">
    <legend class="legend">Upload files</legend>
    
    
    
<form class="form-inline" action="/api/uploadpic/" name="uploadForm" method="post" enctype="multipart/form-data">
  <div class="form-group">
    <label class="sr-only" for="iuploadFile">Select file</label>
    <input type="file" name="uploadFile" class="form-control" id="iuploadFile">
  </div>
  
  <button type="submit" class="btn btn-warning">Upload File</button>
</form>
    
</fieldset>';


MSV_assignData("media_library", $media_library);
MSV_assignData("media_files", $media_files);
MSV_assignData("media_upload", $media_upload);

$service_folder_id = MSV_getConfig("service_folder_id");
if (!empty($service_folder_id)) {
	MSV_assignData("service_folder_manager", '<iframe src="https://drive.google.com/embeddedfolderview?id='.$service_folder_id.'#grid" style="width:100%; height:300px; border:0;"></iframe>');
	MSV_assignData("service_folder_link", 'https://drive.google.com/drive/folders/'.$service_folder_id.'');
}






function admin_build_image_library() {
	$filesList = array();
	
	$media_dir_default = UPLOAD_FILES_PATH."/images";
	$media_dir = $media_dir_default;
	if (!empty($_REQUEST["allpath"])) {
		// TODO+++++
		// sanitrize $_REQUERS["path"]
		$media_dir = $_REQUEST["allpath"];
	}

	if ($media_dir !== $media_dir_default) {
		$filesList[$media_dir] = "..";
	}
	
	$media_url = substr($media_dir, strlen(UPLOAD_FILES_PATH));
	$media_url = HOME_URL.substr(CONTENT_URL, 1).$media_url;


	if ($handle = opendir($media_dir)) {
	    while (false !== ($entry = readdir($handle))) {
	    	if (strpos($entry, ".") === 0) {
	    		continue;
	    	}
	    	$filePath = $media_dir."/".$entry;
	    	if (!is_dir($filePath)) {
	    		
	    	}
	    	$pathinfo = pathinfo($filePath);
	    	$basename = $pathinfo["basename"];
	    	$filesList[$filePath] = $basename;
	    }
		closedir($handle);
	}

	$media_table = "<table class='table table-hover'>";
	$media_table .= "<tr>";
	$media_table .= "<th class='col-sm-2'>Type</th>";
	$media_table .= "<th class='col-sm-4'>Name</th>";
	$media_table .= "<th class='col-sm-4'>Preview</th>";
	$media_table .= "<th class='col-sm-1'>Access</th>";
	$media_table .= "<th class='col-sm-1'>Actions</th>";
	$media_table .= "</tr>\n";
	foreach ($filesList as $filePath => $fileName) {
		$info = mime_content_type($filePath);
		$media_table .= "<tr>";
		
		$media_table .= "<td>";
		$media_table .= $info;
		$media_table .= "</td>";
		
		$pathinfo = pathinfo($filePath);
		$pathImage = $media_url."/".$fileName;
		
		$media_table .= "<td>";
		$media_table .= "<p>".$pathinfo["basename"]." ";
		$media_table .= "<a href='".$pathImage."' target='_blank'><span class='glyphicon glyphicon-new-window'></span></a>";
		$media_table .= "</p>";
		if ($info === "image/png" || $info === "image/gif") {
			$media_table .= "<input value='$pathImage' class='form-control text-muted input-path' readonly>";
		}
		$media_table .= "</td>";
		
		$media_table .= "<td>";
		if ($info === "directory") {
			$media_table .= "<a href='/admin/?section=media_library&path=".$filePath."'>$fileName</a>";
		} else {
			if ($info === "image/png" || $info === "image/gif") {
				$media_table .= "<p><img src='".$pathImage."' class='img-responsive'></p>";
			} 
		}
		$media_table .= "</td>";
		
		
		$media_table .= "<td></td>";
		$media_table .= "<td></td>";
		$media_table .= "</tr>\n";
	}
	$media_table .= "</table>";
	
	return $media_table;
}

function admin_docs_build_list($fileList, $path = "path") {
	global $media_url;
	$media_table = "<table class='table table-hover'>";
	$media_table .= "<tr>";
	$media_table .= "<th class='col-sm-2'>Type</th>";
	$media_table .= "<th class='col-sm-4'>Name</th>";
	$media_table .= "<th class='col-sm-4'>Preview</th>";
	$media_table .= "<th class='col-sm-1'>Access</th>";
	$media_table .= "<th class='col-sm-1'>Actions</th>";
	$media_table .= "</tr>\n";
	foreach ($fileList as $filePath => $fileName) {
		$info = mime_content_type($filePath);
		$media_table .= "<tr>";
		
		$media_table .= "<td>";
		$media_table .= $info;
		$media_table .= "</td>";
		
		$pathinfo = pathinfo($filePath);
		$media_table .= "<td>";
		$media_table .= "".$pathinfo["basename"]."  <a href='".$media_url."/".$fileName."' target='_blank'><span class='glyphicon glyphicon-new-window'></span></a>";
		$media_table .= "</td>";
		
		$media_table .= "<td>";
		if ($info === "directory") {
			$media_table .= "<a href='/admin/?section=media_library&".$path."=".$filePath."#files'>$fileName</a>";
		} else {
			if ($info === "image/png" || $info === "image/gif") {
				$media_table .= "<p><img src='".$media_url."/".$fileName."' class='img-responsive'></p>";
			} 
		}
		$media_table .= "</td>";
		
		
		$media_table .= "<td></td>";
		$media_table .= "<td></td>";
		$media_table .= "</tr>\n";
	}
	$media_table .= "</table>";
	
	return $media_table;
}