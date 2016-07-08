<div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Media library</a></li>
    <li role="presentation"><a href="#files" aria-controls="profile" role="tab" data-toggle="tab">Users Media</a></li>
    <li role="presentation"><a href="#blog" aria-controls="profile" role="tab" data-toggle="tab">Blog Media</a></li>
    <li role="presentation"><a href="#gallery" aria-controls="profile" role="tab" data-toggle="tab">Gallery Media</a></li>
    <li role="presentation"><a href="#upload" aria-controls="profile" role="tab" data-toggle="tab" class="text-danger">Upload File</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="home">
    
    {$media_library}
    
    </div>
    <div role="tabpanel" class="tab-pane" id="files">
    
    
	{$media_files}
    
    
    </div>
    <div role="tabpanel" class="tab-pane" id="upload">
    
    
	{$media_upload}
    
    
    </div>
  </div>

</div>




{if $service_folder_link}
<a class="pull-right btn btn-primary" target="_blank" href="{$service_folder_link}">Open in Google Drive</a>
{/if}

{if $service_folder_manager}
<h3>Service Folder</h3>
{$service_folder_manager}
{/if}
