<?php

// access groups:

// anonymous
// user
// admin
// website
// root

if (isset($_REQUEST['logout'])) {
	unset($_SESSION['user_id']);
	unset($_SESSION['user_email']);
}


if (!empty($_REQUEST["doSingUp"])) {
	if (empty($_REQUEST["email"])) {
		MSV_MessageError("Не указан Email");
	}
	if (empty($_REQUEST["password"])) {
		MSV_MessageError("Укажите ваш пароль");
	}
	if (!empty($_REQUEST["password"]) && empty($_REQUEST["password2"])) {
		MSV_MessageError("Укажите подтверждение пароля");
	}
	if (!MSV_HasMessageError() && $_REQUEST["password"] !== $_REQUEST["password2"]) {
		MSV_MessageError("Пароль и подтверждение пароля не совпадают");
	}
	if (!MSV_HasMessageError()) {
		$result = API_getDBItem(TABLE_USERS, " `email` = '".MSV_SQLEscape($_REQUEST["email"])."'");
		if ($result["ok"] && !empty($result["data"])) {
			MSV_MessageError("Пользователь с таким Email уже зарегистрирован");
		}
	}
	
	
	if (!MSV_HasMessageError()) {
		
		$result = UserAdd($_REQUEST["email"], 0, $_REQUEST["password"], $_REQUEST["name"], $_REQUEST["phone"], "user", "regform");
		if ($result["ok"] && !empty($result["insert_id"])) {
			
			$_SESSION['user_id'] = $result["insert_id"];
			$_SESSION['user_email'] = $_REQUEST["email"];
			header("location: /user/");
			exit;
			
		}
		
	}
	
	
	// pass data to template
	if (!empty($_REQUEST["email"])) {
		MSV_assignData("email", $_REQUEST["email"]);
	}
	if (!empty($_REQUEST["name"])) {
		MSV_assignData("name", $_REQUEST["name"]);
	}
	if (!empty($_REQUEST["phone"])) {
		MSV_assignData("phone", $_REQUEST["phone"]);
	}	
}



if (!empty($_REQUEST["doLogin"]) && !empty($_REQUEST["email"]) && !empty($_REQUEST["password"])) {
	
	$result = API_getDBItem(TABLE_USERS, " `email` = '".MSV_SQLEscape($_REQUEST["email"])."'");
	if ($result["ok"] && !empty($result["data"])) {
		
		// php 5.5+. store password hashed
		// versions under store plain password
		
		$login = false;
		if (function_exists("password_verify")) {
			if (password_verify($_REQUEST["password"], $result["data"]["password"])) {
				$login = true;
			}
		} else {
			if ($_REQUEST["password"] === $result["data"]["password"]) {
				$login = true;
			}
		}
		
		if ($login) {
			
			$_SESSION["user_id"] = $result["data"]["id"];
			$_SESSION["user_email"] = $result["data"]["email"];
			
			$redirect_uri = "/user/";
			if (!empty($_SESSION["redirect_url"])) {
				$redirect_uri = $_SESSION["redirect_url"];
				unset($_SESSION["redirect_url"]);
			}
			header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
			die;
		}
	} 
	
	MSV_MessageError("Неверно указан пароль");
}

if (!empty($_REQUEST["doSave"])) {

	if (!MSV_HasMessageError()) {
		// set user id
		$_REQUEST["user_id"] = $_SESSION['user_id'];
		
		// check if email was changed
		if ($_REQUEST["user_email"] !== $_SESSION['user_email']) {
			$_REQUEST["user_email_verified"] = 0;
		}
		
		// proccess update
		$result = MSV_proccessUpdateTable(TABLE_USERS, "user_");
		if ($result["ok"]) {
			MSV_MessageOK("Успешно сохранено");
		} else {
			MSV_MessageError($result["msg"]);
		}
		
	}
}
if (isset($_REQUEST["doVerify"])) {
	MSV_MessageOK("ok!");
	
	// TODO: send email
	//
}

	
if (!empty($_SESSION["user_id"])) {
	$rowUser = MSV_get("website.user");
	$rowUser["user_id"] = (int)$_SESSION["user_id"];
	
	if (!empty($rowUser["user_id"])) {
		$result = API_getDBItem(TABLE_USERS, " `id` = '".(int)$rowUser["user_id"]."' ");

		if (!$result["ok"]) {
			MSV_MessageError($result["msg"]);
		} else {
			// add info to user row
			$rowUser = array_merge($rowUser, $result["data"]);
			
			// write changes to website instance
			$this->website->user = $rowUser;
		}
	}
}



function loadUserSession($module) {
	$rowUser = MSV_get("website.user");

	if (empty($rowUser["user_id"])) {
		$user_auth_url = MSV_getConfig("user_auth_url");
		
		if (empty($user_auth_url)) {
			MSV_setConfig("user_auth_url", "/login/");
		}
	} else {
		MSV_setConfig("user_logout_url", "/?logout");
	}
}


function UserAdd($email, $email_verified = 0, $password = "", $name = "", $phone = "", $access = "user", $iss = "local") {
	
	if (!empty($password) && function_exists("password_hash")) {
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
	}
	
	$item = array(
		"published" => 1,
		"email" => $email,
		"email_verified" => $email_verified,
		"password" => $passwordHash,
		"name" => $name,
		"phone" => $phone,
		"lang_default" => LANG,
		"access" => $access,
		"iss" => $iss,
	);
	
	$result = API_itemAdd(TABLE_USERS, $item, "*");

	return $result;
}

function UsersInstall($module) {
	
	MSV_Structure_add("all", "/user/", "My Account", "default", "user.tpl", 1, "user", 1, "user");
	MSV_Structure_add("all", "/signup/", "Sing Up", "default", "user-signup.tpl", 1, "bottom", 1, "everyone", "/user/");
	MSV_Structure_add("all", "/login/", "Log In", "default", "user-login.tpl", 1, "bottom", 2, "everyone", "/user/");
	MSV_Structure_add("all", "/password-reset/", "Password Reset", "default", "user-password-reset.tpl", 1, "", 0, "everyone", "/user/");
	MSV_Structure_add("all", "/settings/", "My settings", "default", "user-settings.tpl", 1, "user", 2, "everyone", "/user/");
	
	$item = array(
		"published" => 1,
		"url" => "/?logout",
		"name" => "Logout",
		"menu_id" => "user",
		"order_id" => 100,
	);
	$resultMenu = API_itemAdd(TABLE_MENU, $item, "all");
}