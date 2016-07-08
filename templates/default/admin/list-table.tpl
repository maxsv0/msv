{if $listTable}
<div class="table-responsive">
<table class="table table-hover table-striped table-module">

{foreach from=$listTable name=loop key=item_id item=item}

{if $smarty.foreach.loop.first}
<tr>
{foreach from=$item key=itemFieldID item=itemField} 
{if !in_array($itemFieldID, $admin_list_skip)}
<th>{$t["table.$admin_table.$itemFieldID"]}</th>
{/if}
{/foreach}
<th>{$t["actions"]}</th>
</tr>
{/if}

{if $item.published}
<tr>
{else}
<tr class="danger">
{/if}

{foreach from=$item key=itemFieldID item=itemField}
{if !in_array($itemFieldID, $admin_list_skip)}
{assign var="type" value=$admin_table_info.fields.$itemFieldID.type}
{if $type === "pic"}
<td><img src="{$itemField}" class="img-responsive" style="max-height:200px;"></td>
{elseif $type === "updated" || $type === "date"}
<td><small>{$itemField}</small></td>
{else}
<td>{$itemField|htmlspecialchars|truncate:60:".."}</td>
{/if}
{/if}
{/foreach}
<td class="text-nowrap">
	<a href="/admin/?section={$admin_section}&table={$admin_table}&delete={$item.id}" title="{$t['btn.delete']}" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>
	<a href="/admin/?section={$admin_section}&table={$admin_table}&duplicate={$item.id}" title="{$t['btn.duplicate']}" class="btn btn-warning"><span class="glyphicon glyphicon-duplicate"></span></a>
	<a href="/admin/?section={$admin_section}&table={$admin_table}&edit={$item.id}" title="{$t['btn.edit']}" class="btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>
</td>



</tr>
{/foreach}
</div>
</table>


{if $admin_list_pages}
{include file="$themePath/widget/pagination.tpl" pagination=$admin_list_pages urlsuffix="&section=$admin_section&table=$admin_table"}
{/if}



{else}

<div class="col-sm-6 col-md-offset-2">
<div class="alert alert-info">{$t["not_found"]}</div>
</div>

{/if} 

<div class="col-sm-6">
<a href="/admin/?section={$admin_section}&table={$admin_table}&add_new" class="btn btn-primary"><span class="glyphicon glyphicon-ok">&nbsp;</span>{$t["btn.add_new"]}</a>
</div>