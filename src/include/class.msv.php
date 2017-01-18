<?php
// *** include/class.msv.php
// *** DO NOT EDIT THIS FILE
// *** WILL BE OVERWRITTEN DURING UPDATE


class MSV_Website {
	public $debug 			= false;									// debug current page
	public $instaled 		= false;									// installation status
	public $protocol 		= "http://";							// default protocol
	public $host 			= "";									// current hostname
	public $port 			= 80;									// website port
	public $masterhost 		= "";									// hostname of default language website
	
	public $lang 			= "";									// current language
	public $langDefault 	= "";									// default language
	public $langUrl 		= "";									// url laguage prefix
	public $langSubdomain 	= false;								// alllow multi domain for languages, example: en.domain.com, ru.domain.com
	
	public $requestUrl		= "";						
	public $requestUrlRaw	= "";						
	public $requestUrlMatch = array();						
	public $languages 		= array();						
	public $modules 		= array();								// avaliable modules
	public $modulesActive 	= array();								// currenly enabled modules					
		
	public $config 			= array();								// website config
	public $structure 		= array();								// website structure
	public $menu 			= array();								// website menu
	public $navigation 		= array();								// website navigation line (breadcrumps)
	
	public $constants 		= array();								// website constants
	public $tables 			= array();								// 
	public $filters 		= array();								// 
	public $locales 		= array();								// 
	public $api		 		= array();								// API fucntion from modules
	public $messages 		= array();								// messages that will be ouptuted  on current page	
	public $page 			= array();								// current page row (from table structure)
	public $template 		= "default";							// 
	public $pageTemplate 	= "main.tpl";	
	public $pageTemplatePath= "";	
	public $document		= array();

	public $customPHP 		= array();								// list of php files that will be included
	
	public $htmlHead 		= "";									// include this into <head></head>
	public $htmlBodyBegin 	= "";									// include this after <body>
	public $htmlBodyEnd		= "";									// include this before </body>
	public $htmlFooter		= "";									// include this after </footer>
								// 
	public $includeHead 	= array();								// include this lines beetween <head>
	public $includeCSS 		= array();								// insert this CSS files to head
	public $includeJS 		= array();								// insert this JS file to head
	public $includeJSCode 	= "";									// add this JS code in template
	public $includeHTMLCode = "";									// add this HTML code in template
	public $includeCSSCode 	= "";									// add this CSS code in template
	
	public $log				= "";									// 
	public $logDebug		= "";									// 
	public $smarty			= "";
																	//  Smarty obj
	public $user			= array();								//  user info array
	
	
	function __construct() {
		$this->log("MSV: __construct");
		$tm = time() + (float)substr((string)microtime(), 1, 8);
		$this->config["timestampStart"] = $tm;
		
		$this->messages["error"] = array();
		$this->messages["success"] = array();
	}

	
	function __destruct() {
		$this->log("MSV: __destruct");

		// write to debug log
		if ($this->debug) {
			if (!file_exists(DEBUG_LOG)) {
				@touch(DEBUG_LOG);
			}
			if (defined("DEBUG_LOG") && is_writable(DEBUG_LOG)) {
				@file_put_contents(DEBUG_LOG, $this->logDebug, FILE_APPEND);
			}
		}
	}
	
	
	function start() {
		if (defined("SITE_CLOSED") && SITE_CLOSED) {
			$this->outputForbidden();
		}
		
		// set languages
		if (defined("LANGUAGES")) {
			$this->languages = explode(",", LANGUAGES);
		} else {
			$this->outputError("Can't create website: Languages not set");
		}
		
		if (defined("DB_REQUIRED") && DB_REQUIRED) {
			if (!defined("DB_HOST")) {
				$this->outputError("DB_HOST not defined");
			}
			if (!defined("DB_LOGIN")) {
				$this->outputError("DB_LOGIN not defined");
			}
			if (!defined("DB_NAME")) {
				$this->outputError("DB_NAME not defined");
			}
		}
		
		// make db connection
		if (defined("DB_HOST") && defined("DB_LOGIN") && defined("DB_NAME")) {
			// set BD password
			$password = "";
			if (defined("DB_PASSWORD")) {
				$password = DB_PASSWORD;
			}

			// connect to database
			$conn = @mysqli_connect(DB_HOST, DB_LOGIN, $password, DB_NAME);
			$this->config["db"] = $conn;

			if (!$conn && DB_REQUIRED && $this->instaled) {
				$this->outputError("Can't connect to database. ".mysqli_error($this->config["db"]));
			}
			
			if ($this->config["db"]) {
				// set encoding, if DATABATE_ENCODING  
				if (defined("DATABATE_ENCODING") && DATABATE_ENCODING) {
					mysqli_query($this->config["db"], "set charset ".DATABATE_ENCODING);
					mysqli_query($this->config["db"], "set names ".DATABATE_ENCODING);
				}
			}
			
		} else {
			$this->config["db"] = false;
		}
		
		if (defined("SUBDOMAIN_LANGUAGES") && SUBDOMAIN_LANGUAGES) {
			$this->langSubdomain = true;
		} else {
			$this->langSubdomain = false;
		}

		// set host: current hostname of website
		// remove port from host
		if (strpos($_SERVER['HTTP_HOST'], ":") !== false) {
			list($this->host, $this->port) = explode(":", $_SERVER['HTTP_HOST']);
		} else {
			$this->host = $_SERVER['HTTP_HOST'];
		}
		
		// set lang: current language
		reset($this->languages);
		$this->lang = $this->langDefault = current($this->languages);

		if (!$this->langSubdomain) {
			// set masterhost
			$this->masterhost = $this->host;
		} else {
			// if $this->host start with en... ru .. 
			foreach ($this->languages as $k) {
				if (strpos($this->host, $k.".") === 0) {
					$this->lang = $k;
				}
			}
			
			// set masterhost
			if (strpos($this->host, $this->lang.".") === 0) {
				$this->masterhost = substr($this->host, strlen($this->lang)+1);
			} else {
				$this->masterhost = $this->host;
			}
		}

		if ($this->port !== 80) {
			$this->masterhost .= ":".$this->port;
		}

		// set lang  		
		if (!empty($_REQUEST["lang"])) {
			$lang = $_REQUEST["lang"];
	
			// ignore wrong $lang
			if (in_array($lang, $this->languages)) {
				$this->lang = $_REQUEST["lang"];
			}
		}
		
		// set langUrl
		if (!$this->langSubdomain && $this->lang !== $this->langDefault) {
			$this->langUrl = "/".$this->lang;
		} else {
			$this->langUrl = "";
		}
		
		// check MASTERHOST
		if (defined("MASTERHOST") && strlen(MASTERHOST) > 0) {
			if ($this->masterhost !== MASTERHOST) {
				$this->outputRedirect($this->protocol.MASTERHOST);
			}
		}
		
		// check DEBUG
		if (defined("DEBUG")) {
			$this->debug = DEBUG;
		} else {
			define("DEBUG", $this->debug);
		}
		
		// define constants
		
		define("HOST", $this->host);
		define("LANG", $this->lang); 
		
		// set defaut protocol
		if (defined("PROTOCOL")) {
			$this->protocol = PROTOCOL;
		}
		
		// set homepage for each language
		foreach ($this->languages as $langID) {
			if ($langID !== $this->langDefault) {
				if ($this->langSubdomain) {
					$langHome = $this->protocol.$langID.".".$this->masterhost."/";
				} else {
					$langHome = $this->protocol.$this->masterhost."/?lang=".$langID;
				}
				
			} else {
				$langHome = $this->protocol.$this->masterhost."/";
			}			
			
			$this->config["home"][$langID] = $langHome;
		}
		$this->config["home_url"] = $this->config["home"][$this->lang];
		define("HOME_URL", $this->config["home_url"]);
		define("HOME_LINK", substr($this->config["home_url"], 0, -1));
		
		
		$this->config["languages"] = $this->languages;
		
		if (!empty($_SERVER["HTTP_REFERER"])) {
			$this->config["referer"] = $_SERVER["HTTP_REFERER"];
		}
		
		$this->parseRequest();
		$this->activateCustom();
		$this->activateModules();
		
		// if module Install is in list => run install
		
		// TODO: run all msv-* module?
		// run core, api, seo
		$this->runModule("msv-core");
		$this->runModule("msv-api");
		$this->runModule("msv-seo");
		
		if (in_array("install", $this->modules)) {
			$this->log("MSV: setup required");
			$this->runModule("install");
		} else {
			$this->instaled = true;
		}
		
		$this->user = array(
			"access" => "anonymous",
			"pic" => CONTENT_URL."/images/icon-anonymous.png",
		);
		
		return true;
	}
	
	function load() {
		// redirect langDefault to main mirror
		if (strpos($this->host, $this->langDefault.".") === 0) {
			$urlGo = $this->protocol.$this->masterhost.$this->requestUrl;
			$this->outputRedirect($urlGo);
		}
		
		// run modules by level from 1 to 10
		for ($i = 1; $i <= 10; $i++) {
			$this->runLevel($i);
		}
		
		$this->loadPage($this->requestUrl);
		
		// redirect if FORSE_TRAILING_SLASH 
		// if page is NOT found
		if (empty($this->page) && defined("FORSE_TRAILING_SLASH") && FORSE_TRAILING_SLASH) {
			if (!$this->config["hasTrailingSlash"]) {
				$this->outputRedirect($this->requestUrl."/");
			}
		}
		
		// apply filters
		$this->runFilters();
	}
	
	function loadPage($requestUrl) {
		$this->log("MSV: loadPage -> $requestUrl");
		if (empty($requestUrl)) return false;
		
		$page = array();
		foreach ($this->structure as $item) {
			if (strcmp($item["url"], $requestUrl) == 0) {
				$page = $item;
			}
		}
		$this->page = $page;

		if (empty($this->page)) {
			return false;
		}

		define("DEBUG_PAGE", $page["debug"]);

		$this->template = $page["template"];
		$this->pageTemplate = $page["page_template"];
		
		return true;
	}
	
	function runFilters() {
		$this->log("MSV: runFilters");
		
		if (empty($this->filters)) return false;
		if (!is_array($this->filters)) return false;
		
		// run filter by module activation level
		for ($i = 1; $i <= 10; $i++) {
			
			$this->runFiltersLevel($i);
		}
		
		return true;
	}
	function runFiltersLevel($index) {
		// TODO: check $index
		foreach ($this->filters as $filter) {
			$obj = $this->{$filter["module"]};

			if ($obj->activationLevel == $index) {

				$r = $obj->runFilter($filter);
				if ($r) {
					$this->log("MSV: runFilter -> ".$filter["action"]." successfull (level $index)");
				} else {
					//$this->log("MSV: skip runFilter -> ".$filter["action"]." (level $index)");
				}
			}
		}
	}
	function runLevel($index) {
		// TODO: check $index
		
		foreach ($this->modules as $v) {
			if (in_array($v, $this->modulesActive)) continue;
			$obj = $this->{$v};
			if ($obj->activationLevel == $index) {
				
				$r = $this->runModule($v);
			}
		}
	}
	function activateModule($module) {
		$obj = new MSV_Module($module);
		if ($obj) {
        	// attach module object LINK to website 
        	// TODO: dont overwrite existing objects
        	$this->{$module} = &$obj;
        	$obj->website = &$this;
        }
		
	}
	function runModule($module) {
		// TODO: check $module
		// if in $this->modules[]
		// if in $this->modulesActive[]
		
		// TODO: check dep for $module
		
		$obj = $this->{$module};
        if ($obj) {
        	if (!empty($obj->config)) {
        		$this->config[$module] = $obj->config;
        	}
        	if (!empty($obj->constants) && is_array($obj->constants)) {
        		$this->constants = array_merge($this->constants, $obj->constants);
        	}
        	if (!empty($obj->tables) && is_array($obj->tables)) {
        		$this->tables = array_merge($this->tables, $obj->tables);
        	}
        	if (!empty($obj->filters) && is_array($obj->filters)) {
        		$this->filters = array_merge($this->filters, $obj->filters);
        	}
        	if (!empty($obj->locales) && is_array($obj->locales)) {
        		$this->locales = array_merge($this->locales, $obj->locales);
        	}
        	if (!empty($obj->api) && is_array($obj->api)) {
        		$this->api = array_merge($this->api, $obj->api);
        	}
        	
        	
        	if (in_array("install", $this->modules)) {
				//during installation run all php, dont check url
	        	$result = $obj->runUrl("*");
        	} else {
        		// include module php file
	        	$result = $obj->runUrl($this->requestUrl);
        	}
        	
        	if ($result) {
        		$this->modulesActive[] = $module;
        		$this->log("MSV -> $module active");
        	}
        }
	}
	function activateModules() {
		if ($handle = opendir(ABS_MODULE)) {
		    while (false !== ($entry = readdir($handle))) {
		    	if (strpos($entry, ".") === 0) {
		    		continue;
		    	}
		    	$modulePath = ABS_MODULE."/".$entry;
		    	if (!is_dir($modulePath)) {
		    		continue;
		    	}
		    	if (strpos($entry, "-") === 0) {
		    		$entry = substr($entry, 1);
		    		// TODO: check why cant accept moduel with -
		    		continue;
		    	}
		        // add module to list of avaliable modules
		        $this->modules[] = $entry;
		    }
			closedir($handle);
		}
		$this->log("MSV: activateModules -> ".implode(",",$this->modules));
		
		foreach ($this->modules as $v) {
			$this->activateModule($v);
		}
	}
	function activateCustom() {
		// TODO: check input data
		if (!defined("ABS_CUSTOM")) return false;
		if (!file_exists(ABS_CUSTOM)) return false;
		if (!is_dir(ABS_CUSTOM)) return false;
		
		if ($handle = opendir(ABS_CUSTOM)) {
		    while (false !== ($entry = readdir($handle))) {
		    	if (strpos($entry, ".") === 0) {
		    		continue;
		    	}
		    	$filePath = ABS_CUSTOM."/".$entry;
		    	if (is_dir($filePath)) {
		    		continue;
		    	}
		        $this->customPHP[] = $entry;
		    }
			closedir($handle);
		}
		$this->log("MSV: activateCustom -> ".implode(",",$this->customPHP));
		foreach ($this->customPHP as $v) {
			require_once(ABS_CUSTOM."/".$v);
		}
	}
	
	function setRequestUrl($url) {
		// TODO: check $url
		
		$this->requestUrl = $url;
	}
	function parseRequest() {
		$requestUrl = $_SERVER["REQUEST_URI"];
		$ar = explode("?", $requestUrl, 2);
		$requestUrl = $ar[0];
		if (!empty($ar[1])) {
			$params = $ar[1];
		}
		$this->requestUrl = $requestUrl;
		$this->requestUrlRaw = $requestUrl;
		
		$lastChar = substr($requestUrl, -1, 1);
		if ($lastChar === "/") {
			$this->config["hasTrailingSlash"] = true;
		} else {
			$this->config["hasTrailingSlash"] = false;
		}
	}
	
	function initSmarty() {
		if (!empty($this->smarty)) {
			return false;
		}
		
		$Smarty = new Smarty;
		
		if (!empty($this->page["debug"]) && $this->page["debug"] > 0) {
			$Smarty->debugging = true;
			
			// TODO: 
			//$this->debug = true;
		} else {
			$Smarty->debugging = false;
		}
		$Smarty->caching = false;
		$Smarty->cache_lifetime = 120;
		
		$Smarty->template_dir = ABS_TEMPLATE;
		$compile_dir = ABS_INCLUDE."/custom/smarty/cache";
		if (!is_writeable($compile_dir)) {
			$this->outputError("Cant write to $compile_dir");
		}
		
		$Smarty->compile_dir = $compile_dir;
		$Smarty->compile_check = true;
		
		$Smarty->assign("themeDefaultPath", ABS_TEMPLATE."/default");
		$Smarty->assign("themePath", ABS_TEMPLATE."/".$this->template);
		$Smarty->assign("themeUrl", ABS_TEMPLATEs."/".$this->template);
		
		
		$this->includeCSS = array_reverse($this->includeCSS);
		foreach ($this->includeCSS as $filePath) {
			$this->includeHead[] = "<link href=\"$filePath\" rel=\"stylesheet\">";
		}
		
		$this->includeHead = array_reverse($this->includeHead);
		foreach ($this->includeHead as $line) {
			$this->htmlHead .= $line."\n";
		}
		
		$includeHTML = "";
		foreach ($this->includeJS as $filePath) {
			$includeHTML = $includeHTML."<script src=\"$filePath\"></script>\n";
		}
		if ($this->includeCSSCode) {
			$includeHTML = "<style>\n".$this->includeCSSCode."</style>\n.$includeHTML";
		}
		
		if ($this->includeJSCode) {
			$includeHTML = $includeHTML."<script>\n".$this->includeJSCode."</script>\n";
		}
		if ($this->includeHTMLCode) {
			$includeHTML = "\n".$this->includeHTMLCode.$includeHTML;
		}
		// include HTML to footer
		//$this->htmlFooter = $includeHTML.$this->htmlFooter;
		
		// include HTML to head
		$this->htmlHead = $this->htmlHead.$includeHTML;

		$Smarty->assign("htmlHead", $this->htmlHead);
		
		$Smarty->assign("htmlFooter", $this->htmlFooter);
		
		$Smarty->assign("host", $this->host);
		$Smarty->assign("masterhost", $this->masterhost);
		$Smarty->assign("lang", $this->lang);
		$Smarty->assign("navigation", $this->navigation);
		
		$Smarty->assign("menu", $this->menu);
		$Smarty->assign("structure", $this->structure);
		$Smarty->assign("page", $this->page);
		$Smarty->assign("page_template", $this->page["page_template"]);
		
		foreach ($this->config as $param => $value) {
			$Smarty->assign($param, $value);
		}
		
		// assign page messages
		$messageError = implode("<br>\n", $this->messages["error"]);
		$Smarty->assign("message_error", $messageError);
		
		$messageSuccess = implode("<br>\n", $this->messages["success"]);
		$Smarty->assign("message_success", $messageSuccess);
		
		$Smarty->assign("document", $this->document);
		$Smarty->assign("tables", $this->tables);
		$Smarty->assign("user", $this->user);
		
		$Smarty->assign("t", $this->locales);
		$Smarty->assign("rand", rand());

		$Smarty->assign("request_url", $this->requestUrl);
		$Smarty->assign("lang_url", $this->langUrl);
		
		$this->smarty = $Smarty;
		
		return true;
	}
		
	function output($output, $code = 200) {
		if ($code === 200) {
			echo $output;
		} elseif ($code === 404) {
			header("HTTP/1.0 404 Not Found");
			echo $output;
		} elseif ($code === 403) {
			header('HTTP/1.0 403 Forbidden');
		} elseif ($code === 301) {
			header("HTTP/1.1 301 Moved Permanently"); 
			header("Location: $output");
			echo "<a href=='$output'>$output</a>";
		}
		exit;
	}
	function outputError($errorText = "") {
		$str = "<body style='background:#eee;height:100%;margin:0;'>";
		$str .= "<div style='position: absolute; bottom: 50%; left:47%;'>";
		$str .= "<span style='color:red;'>WEBSITE ERROR.</span>";
		$str .= "</div>";
		$str .= "<div style='position: absolute; bottom: 0; padding:5px 20px; background:#00f;'>";
		$str .= "<span style='color:red;'>ERROR: $errorText</span>";
		$str .= "</div>";
		$this->output($str, 200);
	}
	function outputNotFound($output = "") {
		if (empty($output)) {
			$output = "Page not found.";
		}
		$this->output($output, 404);
	}
	
	function outputForbidden() {
		$this->output("", 403);
	}
	
	function outputRedirect($url) {
		// TODO +++: emulate request on redirect (_POST, GET, _FILES..)
		$this->output($url, 301);
	}
	function checkAccess($pageAccess, $userAccess = '') {
		if (!$this->instaled) {
			return true;
		}
		
		if ($pageAccess === "everyone") {
			return true;
		}
		if ($pageAccess === "admin" && ($userAccess === "admin" || $userAccess === "superadmin")) {
			return true;
		}
		if ($pageAccess === "user" && ($userAccess === "user" || $userAccess === "admin" || $userAccess === "superadmin")) {
			return true;
		}
		return false;
	}
	function outputPage() {
		$this->log("MSV: outputPage");

		if (empty($this->page) && $this->instaled) {
			// set 404 template

			$this->log("Page not found, loading 404 template");
			$this->loadPage("/404/");

			// reload page document
			MSV_LoadPageDocument();

			header("HTTP/1.0 404 Not Found");

			// TODO: output this if 404 template not found
			//$this->outputNotFound("Page not found", 404);
		}
		
		$userAccess = $this->user["access"];
		$pageAccess = $this->page["access"];
		if (!$this->checkAccess($pageAccess, $userAccess)) {


			// set redirect url to return after login
			$_SESSION["redirect_url"] = $this->requestUrlRaw;
			
			if ($this->page["url"] === "/admin/") {
				// redirect to login page
				$this->outputRedirect("/admin/login/");
			} else {
				// redirect to login page
				$this->outputRedirect("/login/");
			}
		}

		// check template file
		$templatePath = ABS_TEMPLATE."/".$this->template."/".$this->pageTemplate;
		if (!file_exists($templatePath) || 
			!is_readable($templatePath) || 
			!is_file($templatePath)) {
			$this->outputError("Page template not found: $templatePath");
		}
		$this->pageTemplatePath = $templatePath;
		
		
		// proccess post/get admin functions  
		// TODO: check user rights?? 
		if ($this->instaled && $this->checkAccess("admin", $this->user["access"])) {
			$this->proccessAdmin();
		}
		
		// output debug console, if needed
		if (defined("DEBUG_PAGE") && DEBUG_PAGE) {
			$this->outputDebug();
			// TODO: move??
			if (!empty($_GET["debugCode"])) {
				eval($_GET["debugCode"]);
			}
		}
		
		// init smarty
		$r = $this->initSmarty();
		
		if (empty($this->smarty)) {
			$this->outputError("Template Engine not found");
		}
		// output current page, use Smarty object
		$this->smarty->display($this->pageTemplatePath);


		// calculate script running time and log
		$tm = time() + (float)substr((string)microtime(), 1, 8);
		$this->config["timestampEnd"] = $tm;
		$scriptTime = $this->config["timestampEnd"] - $this->config["timestampStart"];
		$scriptTime = round($scriptTime, 6);
		$this->log("Run time: $scriptTime sec");
			
		
		die;
	}
	
	function outputDebug() {
		$debugHTML = "";
		$debugHTML .= "<div class='debug_log'>";
		$debugHTML .= "<pre class='pre-scrollable'>";
		$debugHTML .= $this->logDebug;
		$debugHTML .= "</pre>";
		
		if (DEBUG && DEBUG_PAGE) {
			$debugHTML .= "<form style='padding:5px 20px;' id='debugConsole'>";
			$debugHTML .= "Run PHP code: <input type=text size=50 name='debugCode'> <input type='submit' value='Send'>";
			$debugHTML .= '</form>';
		}
		$debugHTML .= '</div>';
		
		$this->config["debug_code"] = $debugHTML;
		return true;
	}
	
	/// proccess admin functions
	function proccessAdmin() {
		if (!$this->checkAccess("admin", $this->user["access"])) {
			return false;
		}
		
		if (!empty($_GET["module_remove"])) {
			// TODO: DO
			// TODO: check $_GET["module_remove"]
			//MSV_removeModule($_GET["module_remove"]);
		}
		if (!empty($_GET["module_disable"])) {
			// TODO: check $_GET["module_disable"]
			MSV_disableModule($_GET["module_disable"]);
		}
		
		if (!empty($_GET["module_enable"])) {
			// TODO: check $_GET["module_enable"]
			MSV_enableModule($_GET["module_enable"]);
		}
		
		if (!empty($_GET["module_reinstall"])) {
			// TODO: check $_GET["module_reinstall"]
			MSV_reinstallModule($_GET["module_reinstall"], false);
		}

        if (!empty($_GET["table_action"])) {
            // TODO: check $_GET["module"]
            // TODO: check $_GET["table"]

            if ($_GET["table_action"] === "create") {

                API_createTable($_GET["module_table"]);

            } elseif ($_GET["table_action"] === "truncate") {

                API_emptyTable($_GET["module_table"]);

            } elseif ($_GET["table_action"] === "remove") {

                API_removeTable($_GET["module_table"]);

            }

            MSV_reinstallModule($_GET["table_action"], false);
        }


        if (isset($_GET["module_update_all"])) {
			foreach ($this->modules as $module) {
				MSV_reinstallModule($module, false);
			}
			$this->messages["success"][] = "Update ALL successfully";
			// TODO:
			// run install hooks??
		}
		
		if (!empty($_GET["module_install"])) {
			// TODO: check $_GET["module_install"]
			$module = $_GET["module_install"];
			
			MSV_installModule($module);
		}
		
		if (!empty($_GET["install_ok"])) {
			$this->messages["success"][] = "<b>{$_GET["install_ok"]}</b> installed successfully";
		}
		if (!empty($_GET["install_hook"])) {
			// TODO: check $_GET["install_hook"]

			$module = $_GET["install_hook"];
			$obj = $this->{$module};
			
			if (!$obj) {
				// 
				$this->messages["error"][] = "Error while installing {$module}";
				
			} else {
				if (!empty($obj->tables)) {
					$tableList = $obj->tables;
					if (!empty($tableList)) {
						foreach ($tableList as $tableName => $tableInfo) {
							$result = API_createTable($tableName);
						}
					}
				}
				$obj->runInstallHook();
				
				$this->outputRedirect("/admin/?section=module_settings&install_ok=".$module."&module=".$module."&module_install");
			}
		}
		
		return true;
	}
	
	function log($logText = "", $type = "warning") {
		$date = date("Y-m-d H:i:s").substr((string)microtime(), 1, 8);
		$logLine = $date." ".$logText."\n";
		
		if ($type === "debug") {
			$this->logDebug .= $logLine;
		} else {
			$this->log .= $logLine;
			$this->logDebug .= $logLine;
		}
	}
	
}
