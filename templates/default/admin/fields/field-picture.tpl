<div class="col-sm-10">
<p>
{if $value}
<div class="img-container">
<img class="img-responsive" src="{$value}?{$rand}" id="img-{$item_id}">
</div>
{else}
<div class="alert alert-danger">No stored image</div>
<div class="img-container">
<img class="img-responsive" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" id="img-{$item_id}">
</div>
{/if}
</p>


<p>
File: <span id="value-{$item_id}">{$value}</span>

{if $value}
<input type="button" class="btn btn-danger btn-xs" value="Remove Link" onclick="removeLink('{$item_id}');">
{/if}
</p>

<p>
{if $item_id !== "pic"}
<input type="button" class="btn btn-warning" value="Создать" onclick="openPicLibraryModal('{$item_id}');">
{/if}
<input type="button" class="btn btn-warning" value="Загрузить файл" onclick="openUploadModal('{$item_id}');">
</p>


<input type="text" class="hide" name="{$form_id}_{$item_id}" id="path-{$item_id}" value="{$value}">
<input type="text" class="hide" id="aspectRatio-{$item_id}" value="{$itemField["max-width"]/$itemField["max-height"]}">
<input type="text" class="hide" id="width-{$item_id}" value="{$itemField["max-width"]}">
<input type="text" class="hide" id="height-{$item_id}" value="{$itemField["max-height"]}">

</div>

