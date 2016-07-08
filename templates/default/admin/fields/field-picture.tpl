<div class="col-sm-10">
<p>
{if $value}
<div class="img-container">
<img class="img-responsive" src="{$value}" id="img-{$item_id}">
</div>
{else}
<div class="alert alert-danger">No stored image</div>
<div class="img-container">
<img class="img-responsive" src="" id="img-{$item_id}">
</div>
{/if}
</p>


<p>
File: <span id="value-{$item_id}">{$value}</span>

{if $value}
<input type="button" class="btn btn-danger btn-xs" value="Remove Link" onclick="removeLink('{$item_id}');">
<!--
TODO:
<input type="button" class="btn btn-danger btn-xs" value="Remove File Permanently" onclick="removeFile('{$item_id}');">
-->
{/if}
</p>

<p>
<input type="button" class="btn btn-warning" value="Select File" onclick="">
<input type="button" class="btn btn-warning" value="Upload New File" onclick="openUploadModal('{$item_id}');">
</p>


<input type="text" class="hide" name="{$form_id}_{$item_id}" id="path-{$item_id}" value="{$value}">

</div>

