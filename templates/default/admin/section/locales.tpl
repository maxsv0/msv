<div class="col-sm-12">
<table class="table table-hover table-module">
<tr>
<th>{$t["admin.locale_param"]}</th>
<th>{$t["admin.locale_value"]}</th>
<th>{$t["actions"]}</th>
</tr>

{foreach from=$admin_locales key=localeID item=localeText}
<tr>
<td>{$localeID}</td>
<td>{$localeText}</td>
<td class="text-nowrap">
	<a href="/admin/?section={$admin_section}&delete={$localeID}" title="{$t['btn.delete']}" class="disabled btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>
	<a href="/admin/?section={$admin_section}&duplicate={$localeID}" title="{$t['btn.duplicate']}" class="disabled btn btn-warning"><span class="glyphicon glyphicon-duplicate"></span></a>
	<a href="/admin/?section={$admin_section}&edit={$localeID}" title="{$t['btn.edit']}" class="disabled btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>
</td>
</tr>
{/foreach}

</table>





<div class="col-sm-6">
<a href="/admin/?section={$admin_section}&table={$admin_table}&add_new" class="disabled btn btn-primary"><span class="glyphicon glyphicon-ok">&nbsp;</span>{$t["btn.add_new"]}</a>
</div>



