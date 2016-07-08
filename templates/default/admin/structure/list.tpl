{assign var="listTable" value=$admin_list}

{if $listTable}
<div class="table-responsive">
<table class="table table-hover table-striped table-module">
<th></th>
<th>{$t["table.structure.id"]}</th>
<th>{$t["table.structure.url"]}</th>
<th class="text-nowrap">{$t["table.structure.template"]} / {$t["table.structure.page_template"]}</th>
<th>{$t["table.structure.access"]}</th>
<th>{$t["table.structure.sitemap"]}</th>
<th>{$t["table.structure.published"]}</th>
<th>{$t["table.structure.updated"]}</th>
<th>{$t["actions"]}</th>


{include file="$themePath/admin/structure/list-level.tpl" show_parent_id=0}

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


