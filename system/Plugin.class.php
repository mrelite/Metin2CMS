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

abstract class Plugin {

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $basedir;

    /**
     * Create a default plugin description
     *
     * @param $name string name of the plugin
     * @param $version string version of the plugin
     * @param $basedir string the basedir of the plugin
     */
    public function __construct($name, $version, $basedir) {
        $this->name = $name;
        $this->version = $version;
        $this->basedir = $basedir;

        Logger::verbose("Plugin " . $this->name . " " . $this->version . " loaded");
    }

    /**
     * Return the basedir
     *
     * @return string
     */
    public function getBasedir() {
        return $this->basedir;
    }

    /**
     * Automatically called by core when the plugin was loaded
     * @param $core Core
     * @return boolean
     */
    public abstract function onLoadByCore($core);

}