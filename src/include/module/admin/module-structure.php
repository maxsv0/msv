<?php
$tableInfo = MSV_getTableConfig(TABLE_STRUCTURE);
$tableInfo["fields"]["document_name"] = array(
	"name" => "document_name",
	"type" => "text",
);
$tableInfo["fields"]["document_text"] = array(
	"name" => "document_text",
	"type" => "doc",
);
MSV_assignData("admin_table_info", $tableInfo);

if (!empty($_POST["save_exit"]) || !empty($_POST["save"])) {
	MSV_proccessUpdateTable(TABLE_STRUCTURE, "form_");
	
	// save document
	API_updateDBItem(TABLE_DOCUMENTS, "text", "'".MSV_SQLEscape($_POST["form_document_text"])."'", " `id` = '".(int)$_POST["form_page_document_id"]."'");
	API_updateDBItem(TABLE_DOCUMENTS, "name", "'".MSV_SQLEscape($_POST["form_document_name"])."'", " `id` = '".(int)$_POST["form_page_document_id"]."'");
	
	// save seo
	$resultQuerySEO = API_getDBItem(TABLE_SEO, "`url` = '".MSV_SQLescape($_POST["form_url"])."'");
	if ($resultQuerySEO["ok"] && !empty($resultQuerySEO["data"])) {
		$rowSEO = $resultQuerySEO["data"];
		$rowSEO["title"] = $_POST["form_seo_title"];
		$rowSEO["description"] = $_POST["form_seo_description"];
		$rowSEO["keywords"] = $_POST["form_seo_keywords"];
		
		$resultSave = API_updateDBItemRow(TABLE_SEO, $rowSEO);
	} else {
		$resultSave = SEO_add($_POST["form_url"], $_POST["form_seo_title"], $_POST["form_seo_description"], $_POST["form_seo_keywords"]);
	}
	if (!$resultSave["ok"]) {
		MSV_redirect("/admin/?section=$section&edit=".$_POST["form_id"]."&save_error=".urlencode($resultSave["msg"]));
	}
}

if (!empty($_POST["save"])) {
	$_REQUEST["edit"] = $_POST["form_id"];
}

if (!empty($_REQUEST["duplicate"])) {
	$resultQueryItem = API_getDBItem(TABLE_STRUCTURE, "`id` = '".(int)$_REQUEST["duplicate"]."'");
	if ($resultQueryItem["ok"]) {
		$resultQueryItem["data"]["id"] = "";
		$resultQueryItem["data"]["page_document_id"] = "";
		MSV_assignData("admin_edit_structure", $resultQueryItem["data"]);
		
		// TODO:
		// do something with document_text.
		// it will not be saved
	}
}

if (!empty($_REQUEST["add_child"])) {
	$resultQueryItem = API_getDBItem(TABLE_STRUCTURE, "`id` = '".(int)$_REQUEST["add_child"]."'");
	if ($resultQueryItem["ok"]) {
		$resultQueryItem["data"]["parent_id"] = $resultQueryItem["data"]["id"];
		$resultQueryItem["data"]["url"] = $resultQueryItem["data"]["url"]."new-page/";
		$resultQueryItem["data"]["id"] = "";
		
		MSV_assignData("admin_edit_structure", $resultQueryItem["data"]);
	}
}

if (!empty($_REQUEST["delete"])) {
	$resultQueryDelete = API_deleteDBItem(TABLE_STRUCTURE, "`id` = '".(int)$_REQUEST["delete"]."'");
	MSV_MessageOK(_t("msg.deleted_ok"));
}

if (isset($_REQUEST["add_new"])) {
	$item = array("id" => "", "published" => 1, "deleted" => 0);
	if (!empty($_REQUEST["edit_key"])) {
		$item["id"] = $_REQUEST["edit_key"];
	}
	MSV_assignData("admin_edit_structure", $item);
}

if (!empty($_REQUEST["document_create"])) {
	$resultQueryItem = API_getDBItem(TABLE_STRUCTURE, "`id` = '".(int)$_REQUEST["document_create"]."'");
	if ($resultQueryItem["ok"]) {
		
		$structure_id = $resultQueryItem["data"]["id"];
		$name = $resultQueryItem["data"]["name"];
		$resultDocument = MSV_Document_add($name, "", "");
		
		// update structure=>set document
		if ($resultDocument["ok"]) {
			API_updateDBItem(TABLE_STRUCTURE, "page_document_id", $resultDocument["insert_id"], " id = '".$structure_id."'");
		}
		MSV_MessageOK(_t("msg.created_ok"));
	}
	$_REQUEST["edit"] = $_REQUEST["document_create"];
}

if (!empty($_REQUEST["edit"])) {
	$resultQueryItem = API_getDBItem(TABLE_STRUCTURE, "`id` = '".(int)$_REQUEST["edit"]."'");
	if ($resultQueryItem["ok"]) {
		$editStructure = $resultQueryItem["data"];
		
		if (!empty($editStructure["page_document_id"])) {
			$resultQueryDocument = API_getDBItem(TABLE_DOCUMENTS, "`id` = '".(int)$editStructure["page_document_id"]."'");
			if ($resultQueryDocument["ok"]) {
				$editStructure["document_text"] = $resultQueryDocument["data"]["text"];
				$editStructure["document_name"] = $resultQueryDocument["data"]["name"];
			}
		}
		
		$resultQuerySEO = API_getDBItem(TABLE_SEO, "`url` = '".MSV_SQLescape($editStructure["url"])."'");
		if ($resultQuerySEO["ok"]) {
			$editStructure["seo_title"] = $resultQuerySEO["data"]["title"];
			$editStructure["seo_description"] = $resultQuerySEO["data"]["description"];
			$editStructure["seo_keywords"] = $resultQuerySEO["data"]["keywords"];
		}
		
		MSV_assignData("admin_edit_structure", $editStructure);
	}
}


if (!empty($_REQUEST["sort"])) {
	// TODO: check if correct key
	$sort = $_REQUEST["sort"];
} else {
	$sort = "parent_id";
}

if (!empty($_REQUEST["sortd"])) {
	if ($_REQUEST["sortd"] === "desc") {
		$sortd = "desc";
		$sortd_rev = "asc";
	} else {
		$sortd = "asc";
		$sortd_rev = "desc";
	}
} else {
	$sortd = "asc";
	$sortd_rev = "desc";
}

MSV_assignData("table_sort", $sort);
MSV_assignData("table_sortd", $sortd);
MSV_assignData("table_sortd_rev", $sortd_rev);



// Load list of items
$resultQuery = API_getDBListPaged(TABLE_STRUCTURE, "", "`$sort` $sortd", 1000, "p");
if ($resultQuery["ok"]) {
	$adminList = $resultQuery["data"];
	$listPages = $resultQuery["pages"];
	MSV_assignData("admin_list_pages", $listPages);

	foreach ($tableInfo["fields"] as $field) {
		
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
					$listItem[$field["name"]."_data"] = $field["data"][$listItem[$field["name"]]];
				}
				
				$adminListFiltered[$listItemID] = $listItem;
			}
			$adminList = $adminListFiltered;
		}
	}
	
	MSV_assignData("admin_list", $adminList);
}
