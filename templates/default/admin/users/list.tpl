{assign var="listTable" value=$admin_list}

{if $listTable}
<div class="table-responsive">
<table class="table table-hover table-striped table-module">
<th>{$t["table.users.id"]}</th>
<th>{$t["table.users.email"]}</th>
<th>{$t["table.users.name"]}</th>
<th>{$t["table.users.phone"]}</th>
<th>{$t["table.users.access"]}</th>
<th>{$t["table.users.iss"]}</th>
<th>{$t["table.users.updated"]}</th>
<th>{$t["actions"]}</th>


{foreach from=$listTable name=loop key=item_id item=item}
{if $item.published}
<tr>
{else}
<tr class="danger">
{/if}

<td>{$item.id}</td>

<td class="text-nowrap">
{$item.email}
</td>

<td class="text-nowrap">
{$item.name|strip_tags|truncate:200:".."}
</td>

<td class="text-nowrap">
{$item.phone|strip_tags|truncate:200:".."}
</td>

<td>{$item.access}</td>
<td>{$item.iss}</td>
<td class="text-nowrap"><small>{$item.updated}</small></td>
<td class="text-nowrap">
	<a href="/admin/?section={$admin_section}&table={$admin_table}&delete={$item.id}" title="Delete" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>
	<a href="/admin/?section={$admin_section}&table={$admin_table}&duplicate={$item.id}" title="Duplicate" class="btn btn-warning"><span class="glyphicon glyphicon-duplicate"></span></a>
	<a href="/admin/?section={$admin_section}&table={$admin_table}&edit={$item.id}" title="Edit" class="btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>
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