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
    private $configm = array();

    /**
     * @var \Smarty
     */
    private $smarty;

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

        // Setup exception handler
        $this->initExceptionHandler();

        // Loading configuration
        $this->loadConfig();

        // Setup Smarty
        $this->initSmarty();
    }

    /**
     * Handle user input and display the template
     */
    public function view() {
        // Define design related variables
        $this->smarty->assign("resource_dir", "resources/" . $this->getDesign() . "/");

        // Define config
        $this->smarty->assign("config", $this->config);

        $this->smarty->display("main.tpl");
    }

    /**
     * Return if the system is in debug mode
     *
     * @return bool
     */
    public function isDebug() {
        return array_key_exists("general_debug", $this->config) ? $this->config["general_debug"] : false;
    }

    public function getDesign() {
        if(!empty($this->config["general_design"])) {
            return $this->config["general_design"];
        }
        return "default";
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
     * Create a exception handler
     */
    private function initExceptionHandler() {
        $this->exceptionHandler = new ExceptionHandler();
    }

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
     * Load the configuration
     *
     * @throws SystemException
     */
    private function loadConfig() {
        $MySQL = array();
        $GENERAL = array();
        $tmpMySQL = array();
        if(!file_exists(ROOT_DIR . "config" . DS . "config.php")) {
            exit("config.php is missing, please copy config.example.php and change this");
        }
        require(ROOT_DIR . "config" . DS . "config.php");

        foreach($GENERAL as $key => $value) {
            $this->config["general_" . $key] = $value;
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
            $this->databases[$usage] = new $sql_handler($data["host"], $data["user"], $data["password"], $data["database"]);
        }
    }

}