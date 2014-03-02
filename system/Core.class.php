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
    private $databases;

    /**
     * @var ClassLoader
     */
    private $classLoader;

    private $config;

    /**
     * Creates the core which setup everything.
     *
     * @throws SystemException, SQLException
     */
    public function __construct() {
        // Initialize defines
        $this->initDefines();

        // Setup class loader
        $this->initClassLoader();

        // Loading configuration
        $this->config = $this->loadConfig();
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
     * Create a class loader instance to work without include / require
     */
    private function initClassLoader() {
        require(SYSTEM_DIR . "ClassLoader.class.php");

        $this->classLoader = new ClassLoader();
    }

    /**
     * Load the configuration
     */
    private function loadConfig() {

    }

}