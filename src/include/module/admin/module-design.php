<?php


$designs = array();
foreach ($this->website->modules as $module) {
	if (strpos($module, "theme-") !== 0) continue;
	
	$moduleObj = MSV_get("website.".$module);
	$designs[$module] = $moduleObj->files;
}
MSV_assignData("admin_designs", $designs);

var_dump($designs);

