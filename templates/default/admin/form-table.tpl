<div>
 
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
  
{foreach from=$admin_edit_tabs key=tabID item=tabInfo name=loop}
{if $tabInfo.fields}
<li role="presentation"{if $smarty.foreach.loop.first} class="active"{/if}><a href="#{$tabID}" aria-controls="{$tabID}" role="tab" data-toggle="tab">{$tabInfo.title}</a></li>
{/if}
{/foreach}
  </ul>
  
<form action="/admin/" class="form-horizontal" method="POST">
<br>
<br>

  <!-- Tab panes -->
  <div class="tab-content">
  
  
{foreach from=$admin_edit_tabs key=tabID item=tabInfo name=loop}
{if $tabInfo.fields}
<div role="tabpanel" class="tab-pane {if $smarty.foreach.loop.first}active{/if}" id="{$tabID}">

{foreach from=$tabInfo.fields item=itemField}
{include "$themePath/admin/field-form.tpl" form_id="form" item_type=$itemField.type item_id=$itemField.name item_name=$itemField.name value=$dataList[$itemField.name]}
{/foreach}

</div>
{/if}
{/foreach}
  
  </div>
 
<input type="hidden" value="{$admin_section}" name="section">
<input type="hidden" value="{$admin_table}" name="table">

<div class="form-group">
<div class="text-right">
	<button type="submit" class="btn btn-danger" type="button"><span class="glyphicon glyphicon-remove-circle">&nbsp;</span>{$t["btn.cancel"]}</button>
	<button class="btn btn-danger" type="reset"><span class="glyphicon glyphicon-ban-circle">&nbsp;</span>{$t["btn.reset"]}</button>
	<button type="submit" name="save" id="btnSave" value="1" class="btn btn-primary"><span class="glyphicon glyphicon-repeat">&nbsp;</span>{$t["btn.save"]}</button>
	<button type="submit" name="save_exit" value="1" class="btn btn-primary"><span class="glyphicon glyphicon-ok">&nbsp;</span>{$t["btn.save_exit"]}</button>
</div>
</div>
</form>



</div>





<!-- Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">File Upload</h4>
        
        <div class="row text-muted">
        <p class="col-sm-4">1. Select file</p>
        <p class="col-sm-4">2. Click "Upload File"</p>
        <p class="col-sm-4">3. Click "Save changes"</p>
        </div>
      </div>
      <div class="modal-body">

<div id="uploadPreview" class="hide">
<img src="about:blank" class="">
</div>    
      
    <div id="uploadDiv">
	<form action="/api/uploadpic/" name="uploadForm" method="post" enctype="multipart/form-data" target="uploadFrame">
	<input name="uploadFile" id="iUploadFile" type="file" class="form-control" style="height: 50px;"/>
	<input name="table" id="iUploadTable" type="hidden" value="{$admin_table}"/>
	<input name="field" id="iUploadField" type="hidden" value=""/>
	<input type="submit" name="submitBtn" value="Upload File" class="btn btn-warning btn-block"/>
	</form>
	<iframe id="uploadFrame" name="uploadFrame" src="about:blank" style="width:0;height:0;border:0px solid #fff;" onload="uploadFrameLoad(this)"></iframe>
	</div>
        
    <div id="uploadStatus" class="hide" style="margin-top:10px;">
    <div id="uploadAlert" class=""></div>
	</div>
      
      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="closeUploadModal();">Save changes</button>
      </div>
    </div>
  </div>
</div>

<input id="uploadFilePath" type="hidden" value=""/>