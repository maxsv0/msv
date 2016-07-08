<?php

if (isset($_REQUEST["edit_mode"])) {
}
if (!empty($_POST["save_exit"]) || !empty($_POST["save"])) {
}
if (!empty($_POST["save"])) {
	// add message : ok saved
}


MSV_assignData("admin_locales", $this->website->locales);
