<?php

if (!empty($_SESSION["msv_install_step"])) {
	$install_step = (int)$_SESSION["msv_install_step"];
} else {
	$install_step = 1;
}


$website = $this->website;
$website->page = array(1);
$website->template = "default";
$website->pageTemplate = "install.tpl";

$configListNames = array(
	"ABS", "LANGUAGES",
	"DB_HOST","DB_LOGIN","DB_PASSWORD","DB_NAME","DB_REQUIRED",
	"DATE_FORMAT","PROTOCOL","MASTERHOST",
	"UPLOAD_FILES_PATH","CONTENT_URL","PHP_HIDE_ERRORS",
	"DEBUG","DEBUG_LOG","SITE_CLOSED","SHOW_ADMIN_MENU",
	"PHP_LOCALE","PHP_TIMEZONE","DATABATE_ENCODING",
	"FORSE_TRAILING_SLASH","SUBDOMAIN_LANGUAGES","REP",
);


/// TODO:
// + dublicate template and set theme name // each lang
//

if (!empty($_POST["install_reset"])) {
	$_SESSION["msv_install_step"] = $install_step = 0;
	$website->outputRedirect("/");
}


if (!empty($_POST["install_step"]) && empty($website->messages["error"])) {
	$_POST["install_step"] = (int)$_POST["install_step"];
	
	if ($_POST["install_step"] === 3) {

		if (empty($_POST["msv_LANGUAGES"]) || !is_array($_POST["msv_LANGUAGES"])) {
			$website->messages["error"][] = "Please select languages";
		} else {

			$configList = array();
			$configPHP = "<?php \n";
			
			foreach ($configListNames as $name) {
				$value = $_POST["msv_".$name];
				
				$valueCurrent = constant($name);
				if ($name === "LANGUAGES") {
					$configPHP .= "define(\"".$name."\", \"".implode(",", $value)."\");\n";
				} elseif (is_bool($valueCurrent)) {
					$value = (int)$value;
					if ($value) {
						$configPHP .= "define(\"".$name."\", true);\n";
					} else {
						$configPHP .= "define(\"".$name."\", false);\n";
					}
					
				} elseif (is_int($valueCurrent)) {
					$value = (int)$value;
					$configPHP .= "define(\"".$name."\", $value);\n";
				} elseif (is_string($valueCurrent)) {
					$configPHP .= "define(\"".$name."\", \"".$value."\");\n";
				}
				
				$configList[$name] = $value;
			}
			
			if (is_writable(ABS."/config.php")) {
				file_put_contents(ABS."/config.php", $configPHP);
			} else {
				$website->messages["error"][] = "Can't write to ".ABS."/config.php";
			}
		}
	}
	
	if ($_POST["install_step"] === 4) {
		
		if (empty($website->messages["error"]) && 
			!empty($_POST["modules_local"]) && is_array($_POST["modules_local"])) {
			foreach ($_POST["modules_local"] as $module) {
				$obj = $website->{$module};
				
				if (!$obj->started) {
					$website->runModule($module);
				}
				if (!empty($obj->tables)) {
					$tableList = $obj->tables;
					
					if (!empty($tableList)) {
						
						foreach ($tableList as $tableName => $tableInfo) {
							$result = API_createTable($tableName);
						}
					}
				}
			}
			foreach ($_POST["modules_local"] as $module) {
				$obj = $website->{$module};
				$obj->runInstallHook();
			}
		} else {
			$website->messages["error"][] = "Can't start without modules";
		}
		
		if (!empty($_POST["admin_login"]) && !empty($_POST["admin_password"])) {
			
			UserAdd($_POST["admin_login"], 1, $_POST["admin_password"], "admin", "", "superadmin", "install");
			
		} else {
			$website->messages["error"][] = "Please enter login and password";
		}
		
	}
	
	
	if ($_POST["install_step"] === 5) {
		$_SESSION["msv_install_step"] = $install_step = 0;
		MSV_disableModule("install");
		$website->outputRedirect("/");
	}
	
	// if no errors, go to next step
	if (empty($website->messages["error"]) && !empty($install_step)) {
		$install_step = $_POST["install_step"];
		$_SESSION["msv_install_step"] = $install_step;
		$website->outputRedirect("/");
	}
}

if ($install_step >= 3) {
	if (empty($website->config["db"])) {
		$website->messages["error"][] = "WARNING: Database connection not established.";
	} else {
		$website->messages["success"][] = "SUCCESS: Database connection established.";
	}
}

if ($install_step === 2) {
	$configList = array();
	foreach ($configListNames as $name) {
		$value = constant($name);
		if (is_bool($value)) {
			$value = $value ? 1 : 0;
		}
		$configList[$name] = $value;
	}
	$website->config["configList"] = $configList;
}
if ($install_step === 3) {
	$modulesList = array();
	foreach ($website->modules as $module) {
		if ($module === "install") continue;
		$modulesList[$module] = array();
	}
	
	
	$website->config["modulesList"] = $modulesList;
	
	
	$website->config["admin_login"] = "admin@localhost";
	$website->config["admin_password"] = Install_generatePassword();
	
}

if (!empty($install_step)) {
	$website->config["install_step"] = $install_step;
}

$website->outputDebug();
$website->outputPage();


function Install_generatePassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }

    return $result;
}