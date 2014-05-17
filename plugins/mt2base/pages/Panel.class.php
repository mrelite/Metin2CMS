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

namespace plugins\mt2base\pages;

use plugins\mt2base\RegisterField;
use system\Core;
use system\Language;
use system\Logger;
use system\pages\Page;

class Panel implements Page {

	/**
	 * Create an instance of the page
	 */
	public function __construct() {

	}

	/**
     * Get the template file for this page
     *
     * @return string
     */
    public function getTemplateName()
    {
        return "mt2base/panel.tpl";
    }

    /**
     * Prepare smarty for use this template.
     * Assign variables and do queries here.
     *
     * @param $core \system\Core
     * @param $smarty \Smarty
     */
    public function prepare($core, $smarty)
    {

    }

}