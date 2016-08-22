<fieldset class="legend">
    <legend class="legend">Upload files</legend>
    
   
    
<form class="form-inline" action="/api/uploadpic/" target="_blank" name="uploadForm" method="post" enctype="multipart/form-data">
  <div class="form-group">
    <label class="sr-only" for="iuploadFile">Select file</label>
    <input type="file" name="uploadFile" class="form-control" id="iuploadFile">
    <input type="hidden" name="uploadFilePath" value="{$upload_path}">
  </div>
  
  <button type="submit" class="btn btn-warning">Upload File</button>
  
  <span style="margin-left:10px;">Server path: <b>{$upload_path}</b></span>
</form>
    
</fieldset>