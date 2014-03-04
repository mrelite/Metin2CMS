<?php
/**
 * Metin2CMS - Easy for Metin2
 * Copyright (C) 2014  ChuckNorris
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, see <http://www.gnu.org/licenses/>.
 */

namespace system;

class Core {

    /**
     * A array with every database connection (key = usage)
     *
     * @var array
     * @example Array ( "account" => database\MySQLDatabase("account_metin") )
     */
    private $databases = array();

    /**
     * @var ClassLoader
     */
    private $classLoader;

    /**
     * @var ExceptionHandler
     */
    private $exceptionHandler;

    /**
     * @var array
     */
    private $config = array();

    /**
     * @var \Smarty
     */
    private $smarty;

    /**
     * @var array
     */
    private $plugins = array();

    /**
     * @var array
     */
    private $pagesOverwrite = array();

    /**
     * @var EventHandler
     */
    private $eventHandler;

    /**
     * @var array
     */
    private $navigationPoints = array();

    /**
     * @var Core
     */
    public static $instance;

    /**
     * Creates the core which setup everything.
     *
     * @throws SystemException, SQLException
     */
    public function __construct() {
        // Set this instance
        Core::$instance = $this;

        // Initialize defines
        $this->initDefines();

        // Setup class loader
        $this->initClassLoader();

        // Setup event handler
        $this->initEventHandler();

        // Setup the logger
        $this->initLogger();

        // Setup exception handler
        $this->initExceptionHandler();

        // Loading configuration
        $this->loadConfig();

        // Setup Smarty
        $this->initSmarty();

        // Initialize plugins
        $this->initPlugins();
    }

    /**
     * Handle user input and display the template
     */
    public function view() {
        // Define design related variables
        $this->smarty->assign("resource_dir", "resources/" . $this->getDesign() . "/");

        // Define config
        $this->smarty->assign("config", $this->config);

        // Initialize page
        if(array_key_exists($this->getCurrentPage(), $this->pagesOverwrite)) {
            $pageClassName = $this->pagesOverwrite[$this->getCurrentPage()];
        } else {
            $pageClassName = "\\system\\pages\\" . $this->getCurrentPage();
        }
        Logger::verbose("Create a instance of " . $pageClassName);
        $page = new $pageClassName();
        if($page instanceof pages\Page) {
            Logger::verbose("Prepare the page");
            $page->prepare($this, $this->smarty);
            $this->eventHandler->triggerEvent("preparePage", $this, array($this->smarty));

            $this->smarty->assign("page_tpl", $page->getTemplateName());
            $this->smarty->assign("navigation_points", $this->navigationPoints);

            Logger::verbose("Displaying template");
            $this->smarty->display("main.tpl");
        } else {
            throw new SystemException("Error in page " . $this->getCurrentPage() . "! The class must be an instanceof Page");
        }

        Logger::close();
    }

    /**
     * Return if the system is in debug mode
     *
     * @return bool
     */
    public function isDebug() {
        return array_key_exists("general_debug", $this->config) ? $this->config["general_debug"] : false;
    }

    /**
     * Gets the current page name
     *
     * @return string
     */
    public function getCurrentPage() {
        if(array_key_exists($_GET, "p") && !empty($_GET["p"])) {
            if(file_exists(SYSTEM_DIR . "pages" . DS . $_GET["p"] . ".class.php")) {
                return $_GET["p"];
            } else {
                Logger::warning("Unknown page " . $_GET["p"]);
            }
        }

        return "Home";
    }

    /**
     * Get the choosen design
     *
     * @return string
     */
    public function getDesign() {
        if(!empty($this->config["general_design"])) {
            return $this->config["general_design"];
        }
        return "default";
    }

    /**
     * Get a database connection for the usage
     *
     * @param $usage string
     * @return MySQLDatabase
     * @throws SystemException
     */
    public function getSql($usage) {
        if(!empty($this->databases[$usage])) {
            return $this->databases[$usage];
        }

        throw new SystemException("Unknown sql connection for " . $usage);
    }

    public function addNavigationPoint($name, $link) {
        $this->navigationPoints[$name] = $link;
    }

    /**
     * Register a page
     *
     * @param $pages array
     * @param $overwrite boolean
     */
    public function registerPages(array $pages, $overwrite) {
        foreach($pages as $pagename => $file) {
            if(array_key_exists($pagename, $this->pagesOverwrite) && !$overwrite) {
                continue;
            }
            Logger::verbose("Overwrite " . $pagename . " with " . $file);
            $this->pagesOverwrite[$pagename] = $file;
        }
    }

    /**
     * Get a config variable
     *
     * @param $config string config key
     * @return mixed
     */
    public function getConfig($config) {
        return $this->config[$config];
    }

    /**
     * Get the event handler
     *
     * @return EventHandler
     */
    public function getEventHandler() {
        return $this->eventHandler;
    }

    /**
     * Init all defines:
     *
     * DS - Short form of DIRECTORY_SEPARATOR
     * ROOT_DIR - Root directory with system folder, template folder and libs folder
     * SYSTEM_DIR - System directory
     */
    private function initDefines() {
        if(!defined("DS")) {                    // Short form of DIRECTORY_SEPARATOR
            define("DS", DIRECTORY_SEPARATOR);
        }

        if(!defined("ROOT_DIR")) {              // The root directory (should be relative ../)
            define("ROOT_DIR", realpath(dirname(__FILE__) . DS . ".." . DS) . DS);
        }

        if(!defined("SYSTEM_DIR")) {
            define("SYSTEM_DIR", ROOT_DIR . "system" . DS);
        }
    }

    /**
     * Init the event handler
     */
    public function initEventHandler() {
        $this->eventHandler = new EventHandler();
    }

    /**
     * Create a exception handler
     */
    private function initExceptionHandler() {
        $this->exceptionHandler = new ExceptionHandler();
    }

    /**
     * Initialize Smarty
     */
    private function initSmarty() {
        require(ROOT_DIR . "libs" . DS . "smarty" . DS . "Smarty.class.php");

        $this->smarty = new \Smarty();
        $this->smarty->setTemplateDir(ROOT_DIR . "templates" . DS . $this->getDesign() . DS);
        $this->smarty->setCompileDir(ROOT_DIR . "templates" . DS . "compiled" . DS);
        $this->smarty->debugging = $this->isDebug();
        $this->smarty->caching = false;
    }

    /**
     * Create a class loader instance to work without include / require
     */
    private function initClassLoader() {
        require(SYSTEM_DIR . "ClassLoader.class.php");

        $this->classLoader = new ClassLoader();
    }

    /**
     * Initialize the logger
     */
    private function initLogger() {
        Logger::init(0);
    }

    /**
     * Load the configuration
     *
     * @throws SystemException
     */
    private function loadConfig() {
        Logger::verbose("Loading configuration");

        $MySQL = array();
        $GENERAL = array();
        $PLUGINS = array();
        $tmpMySQL = array();
        if(!file_exists(ROOT_DIR . "config" . DS . "config.php")) {
            Logger::error("config.php is missing, please copy config.example.php and change this");
            exit("config.php is missing, please copy config.example.php and change this");
        }
        require(ROOT_DIR . "config" . DS . "config.php");

        foreach($GENERAL as $key => $value) {
            $this->config["general_" . $key] = $value;
        }

        foreach($PLUGINS as $key => $value) {
            foreach($value as $key2 => $value2) {
                $this->config["plugin_" . $key . "_" . $key2] = $value2;
            }
        }

        foreach($MySQL as $usage => $data) {
            if($usage == "*") {
                foreach($data as $key => $value) {
                    $tmpMySQL["account"][$key] = $value;
                }
                foreach($data as $key => $value) {
                    $tmpMySQL["player"][$key] = $value;
                }
                foreach($data as $key => $value) {
                    $tmpMySQL["common"][$key] = $value;
                }
                foreach($data as $key => $value) {
                    $tmpMySQL["log"][$key] = $value;
                }
                foreach($data as $key => $value) {
                    $tmpMySQL["homepage"][$key] = $value;
                }

                // Update database name
                if(strpos($data["database"], "%usage%") !== false) {
                    // Replace %usage%
                    $tmpMySQL["account"]["database"] = str_replace("%usage%", "account", $tmpMySQL["account"]["database"]);
                    $tmpMySQL["player"]["database"] = str_replace("%usage%", "player", $tmpMySQL["player"]["database"]);
                    $tmpMySQL["common"]["database"] = str_replace("%usage%", "common", $tmpMySQL["common"]["database"]);
                    $tmpMySQL["log"]["database"] = str_replace("%usage%", "log", $tmpMySQL["log"]["database"]);
                    $tmpMySQL["homepage"]["database"] = str_replace("%usage%", "homepage", $tmpMySQL["homepage"]["database"]);
                }
            } else if(strpos($usage, "|") !== false) {
                $usages = explode("|" , $usage);
                foreach($usages as $toSetUsage) {
                    foreach($data as $key => $value) {
                        $tmpMySQL[$toSetUsage][$key] = $value;
                    }
                    if(strpos($data["database"], "%usage%") !== false) {
                        $tmpMySQL[$toSetUsage]["database"] = str_replace("%usage%", $toSetUsage, $tmpMySQL[$toSetUsage]["database"]);
                    }
                }
            } else {
                foreach($data as $key => $value) {
                    $tmpMySQL[$usage][$key] = $value;
                }
                if(strpos($data["database"], "%usage%") !== false) {
                    $tmpMySQL[$usage]["database"] = str_replace("%usage%", $usage, $tmpMySQL[$usage]["database"]);
                }
            }
        }

        // Create mysql connections
        foreach($tmpMySQL as $usage => $data) {
            if($usage != "account" && $usage != "player" && $usage != "common" && $usage != "log" && $usage != "homepage") {
                throw new SystemException("Failed to load config.php - invalid mysql usage " . $usage);
            }

            // Allow to define another sql connection class
            if(!empty($data["type"])) {
                $sql_handler = "system\\database\\" . $data["type"];
            } else {
                $sql_handler = "system\\database\\MySQLDatabase";
            }
            Logger::verbose("Connect to database for " . $usage . " (" . $data["user"] . "@" . $data["host"] . ")");
            $this->databases[$usage] = new $sql_handler($data["host"], $data["user"], $data["password"], $data["database"]);
        }
    }

    /**
     * Load all plugins
     */
    private function initPlugins() {
        $handle = opendir(ROOT_DIR . "plugins" . DS);
        while(false !== ($entry = readdir($handle))) {
            if($entry != "." && $entry != "..") {
                if(is_dir(ROOT_DIR . "plugins" . DS . $entry)) {
                    // Load plugins
                    if(file_exists(ROOT_DIR . "plugins" . DS . $entry . DS . $entry . ".plugin.php")) {
                        $classname = "\\plugins\\" . $entry . "\\" . $entry;
                        $obj = new $classname();
                        $obj->onLoadByCore($this);
                        $this->plugins[] = $obj;
                    } else {
                        throw new SystemException("Failed to load plugin " . $entry);
                    }
                }
            }
        }
    }

}