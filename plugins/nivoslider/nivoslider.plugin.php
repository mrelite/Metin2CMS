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

namespace plugins\nivoslider;

use system\Core;

class nivoslider extends \system\Plugin {

    /**
     * Create the plugin information object
     */
    public function __construct() {
        parent::__construct("Nivo-Slider", "3.2", ROOT_DIR . "plugins" . DS . "nivoslider" . DS);
    }

    /**
     * Automatically called by core when the plugin was loaded
     * @param $core Core
     * @return boolean
     */
    public function onLoadByCore($core)
    {

    }
}