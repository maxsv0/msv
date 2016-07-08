{assign var="listTable" value=$admin_list}

{if $listTable}
<div class="table-responsive">
<table class="table table-hover table-striped table-module">
<th>{$t["table.documents.id"]}</th>
<th>{$t["table.documents.name"]}</th>
<th>{$t["table.documents.external_link"]}</th>
<th>{$t["table.documents.updated"]}</th>
<th>{$t["actions"]}</th>


{foreach from=$listTable name=loop key=item_id item=item}
{if $item.published}
<tr>
{else}
<tr class="danger">
{/if}

<td>{$item.id}</td>

<td class="text-nowrap">
{$item.name|strip_tags|truncate:200:".."}
</td>

<td class="text-nowrap">
<a href="{$item.external_link}">{$item.external_link|strip_tags|truncate:200:".."}</a>
</td>

<td class="text-nowrap"><small>{$item.updated}</small></td>

<td class="text-nowrap">
	<a href="/admin/?section={$admin_section}&table={$admin_table}&delete={$item.id}" title="{$t['btn.delete']}" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>
	<a href="/admin/?section={$admin_section}&table={$admin_table}&duplicate={$item.id}" title="{$t['btn.duplicate']}" class="btn btn-warning"><span class="glyphicon glyphicon-duplicate"></span></a>
	<a href="/admin/?section={$admin_section}&table={$admin_table}&edit={$item.id}#document" title="{$t['btn.edit']}" class="btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>
</td>
</tr>
{/foreach}
</div>
</table>
{else}

<div class="col-sm-6 col-md-offset-2">
<div class="alert alert-info">{$t["not_found"]}</div>
</div>

{/if} 

<div class="col-sm-6">
<a href="/admin/?section={$admin_section}&add_new" class="btn btn-primary"><span class="glyphicon glyphicon-ok">&nbsp;</span>{$t["btn.add_new"]}</a>
</div>