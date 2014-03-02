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

class ClassLoader {

    private $fileEnds;

    /**
     * Create the class loader instance and register the autoload
     */
    public function __construct() {
        $this->directories = array();
        $this->fileEnds = array("class.php", "php", "interface.php");

        spl_autoload_register(array($this, "load"));
    }

    /**
     * Try to load a class
     *
     * @param $name string class name
     * @throws \Exception
     */
    public function load($name) {
        if(class_exists($name)) {
            return;
        }
        $path = $this->getFilePath($name);
        if($path !== false) {
            require($path);
        } else {
            throw new \Exception("Failed to load class " . $name);  // Only throw a normal Exception here
                                                                    // because of preventing php fatal error
        }
    }

    /**
     * Search for the file
     *
     * @param $name string class name
     * @return bool|string
     */
    public function getFilePath($name) {
        // Fixing directory seperator for linux systems
        $name = str_replace("\\", DS, $name);

        foreach ($this->fileEnds as $fileEnd) {
            if(file_exists(ROOT_DIR . $name . "." . $fileEnd)) {
                return ROOT_DIR . $name . "." . $fileEnd;
            }
        }
        return false;
    }

}