<?php

$manualPath = ABS."/content/manual.html";
if (!empty($_POST["save_exit"]) || !empty($_POST["save"])) {
	file_put_contents($manualPath, $_POST["form_manual_content"]);
}
if (file_exists($manualPath)) {
	$manualCont = file_get_contents($manualPath);
	MSV_assignData("admin_manual", $manualCont);
}
if (isset($_REQUEST["edit_mode"])) {
	MSV_assignData("admin_manual_edit_mode", true);
}
if (!empty($_POST["save"])) {
	MSV_assignData("admin_manual_edit_mode", true);
	// add message : ok saved
}
