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

namespace plugins\mt2base;

use system\SystemException;

class NewsHandler {

    /**
     * From which source should the news loaded.
     * Possible options from config:
     * intern - Own script
     * wbb - Woltlab Burning Board (need extra configuration: wbb_board_id)
     *
     * @var string
     */
    private $loadType = "intern";

    public function __construct() {
        $loadType = \system\Core::$instance->getConfig("plugin_mt2base_load_type");
    }

    public function readNews($start, $count) {
        if($this->loadType == "intern") {
            $news = \system\Core::$instance->getSql("homepage")->select("news", array("title", "content", "date", "author"));
            return $news;
        } else {
            throw new SystemException("Unsupported news load type " . $this->loadType);
        }
    }

}