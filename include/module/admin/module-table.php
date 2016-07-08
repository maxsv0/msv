<?php
if (empty($table)) {
	return false;
}
if (empty($section)) {
	return false;
}

$tableInfo = MSV_getTableConfig($table);
MSV_assignData("admin_table_info", $tableInfo);

if (!empty($_POST["save_exit"]) || !empty($_POST["save"])) {
	$result = MSV_proccessUpdateTable($table, "form_");
	if (!$result["ok"]) {
		MSV_redirect("/admin/?section=$section&table=$admin_table&save_error=".$result["msg"]);
	}
}
if (!empty($_POST["save"])) {
	$_REQUEST["edit"] = $_POST["form_id"];
}
if (!empty($_REQUEST["edit"])) {
	$resultQueryItem = API_getDBItem($table, "`id` = '".(int)$_REQUEST["edit"]."'");
	if ($resultQueryItem["ok"]) {
		MSV_assignData("admin_edit", $resultQueryItem["data"]);
	}
}
if (!empty($_REQUEST["duplicate"])) {
	$resultQueryItem = API_getDBItem($table, "`id` = '".(int)$_REQUEST["duplicate"]."'");
	if ($resultQueryItem["ok"]) {
		$resultQueryItem["data"]["id"] = "";
		MSV_assignData("admin_edit", $resultQueryItem["data"]);
	}
}
if (!empty($_REQUEST["delete"])) {
	$resultQueryDelete = API_deleteDBItem($table, "`id` = '".(int)$_REQUEST["delete"]."'");
	MSV_MessageOK(_t("msg.deleted_ok"));
}
if (isset($_REQUEST["add_new"])) {
	$item = array(
		"id" => "", 
		"published" => 1, 
		"deleted" => 0,
		"lang" => LANG,
	);
	if (!empty($_REQUEST["edit_key"])) {
		// toDO: ket from table config
		//$item["id"] = $_REQUEST["edit_key"];
	}
	MSV_assignData("admin_edit", $item);
}

$resultQuery = API_getDBListPaged($table, "", "`id` asc", 100, "p");
if ($resultQuery["ok"]) {
	MSV_assignData("admin_list", $resultQuery["data"]);
	
	$adminList = $resultQuery["data"];
	$listPages = $resultQuery["pages"];
	MSV_assignData("admin_list_pages", $listPages);
	
	$adminListSkipFields = array();
	$adminListSkipFields[] = "deleted";
	$adminListSkipFields[] = "published";
	$adminListSkipFields[] = "author";
	$adminListSkipFields[] = "updated";

	foreach ($tableInfo["fields"] as $field) {
		if($field["listskip"] > 0) {
			$adminListSkipFields[] = $field["name"];
		}
				
		if (!empty($field["select-from"])) {
			$field["type"] = "select";
			
			if ($field["select-from"]["source"] === "table") {
				$cfg = MSV_getTableConfig($field["select-from"]["name"]);
				// TODO: multi index support
				// index from config?
				$index = "id";
				$title = $cfg["title"];
				
				$queryData = API_getDBList($field["select-from"]["name"], "", "`$title` asc");
				if ($queryData["ok"]) {
					$arData = array();
					foreach ($queryData["data"] as $item) {
						$arData[$item[$index]] = $item[$title];
					}
					$field["data"] = $arData;
				}
			} elseif ($field["select-from"]["source"] === "list") {
				
				$field["data"] = array();
				$list = explode(",", $field["select-from"]["name"]);
				foreach ($list as $listItem) {
					$field["data"][$listItem] = _t($field["name"].".".$listItem);
				}
				
			}

			$adminListFiltered = array();
			foreach ($adminList as $listItemID => $listItem) {
				
				if (!empty($listItem[$field["name"]])) {
					$listItem[$field["name"]] = $field["data"][$listItem[$field["name"]]];
				}
				
				$adminListFiltered[$listItemID] = $listItem;
			}
			$adminList = $adminListFiltered;
		}
	}
	
	MSV_assignData("admin_list_skip", $adminListSkipFields);
	MSV_assignData("admin_list", $adminList);
}

