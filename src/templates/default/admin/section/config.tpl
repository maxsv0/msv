<form action="{$lang_url}/admin/" method="POST">

<table class="table">

{foreach from=$admin_config_list key=dataName item=dataValue}
<tr>
	<td>{$dataName}</td>
	<td><input type="text" value="{$dataValue}" name="config_{$dataName}" class="form-control"></td>
</tr>
{/foreach}

</table>


<input type="hidden" value="{$admin_section}" name="section">

<div class="form-group">
<div class="text-right">
	<button type="submit" name="save" id="btnSave" value="1" class="btn btn-primary"><span class="glyphicon glyphicon-repeat">&nbsp;</span>{$t["btn.save"]}</button>
</div>
</div>
</form>