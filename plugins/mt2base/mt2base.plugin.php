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

use system\Core;

class mt2base extends \system\Plugin {

    /**
     * Create the plugin information object
     */
    public function __construct() {
        parent::__construct("Metin2 Base Functions", "1.0.0", ROOT_DIR . "plugins" . DS . "mt2base" . DS);

        Core::$instance->getEventHandler()->addEvent("preparePage", array($this, "onPreparePage"));
    }

    /**
     * Automatically called by core when the plugin was loaded
     * @param $core Core
     * @return boolean
     */
    public function onLoadByCore($core)
    {
        $core->registerPages(array(
            "Home" => "plugins\\mt2base\\pages\\Home",
        ), true);

        $core->addNavigationPoint("Startseite", "?p=Home");
        $core->addNavigationPoint("Registrieren", "?p=Register");
        $core->addNavigationPoint("Download", "?p=Download");
        $core->addNavigationPoint("Community", "board/");
        $core->addNavigationPoint("Rangliste", "?p=Rankings");
        $core->addNavigationPoint("TeamSpeak3", "ts3server://");
        $core->addNavigationPoint("Itemshop", "?p=ItemShop");
    }

    /**
     * @param $core
     * @param $smarty
     */
    public function onPreparePage($core, $smarty) {
        if(!$core->isOffline()) {
            // Get server status
            $serverStatus = new ServerStatus();
            $status = $serverStatus->getAllStatus();
            $smarty->assign("useServerStatus", true);
            $smarty->assign("status", $status);
            $smarty->assign("status_refresh", date("H:i:s", $serverStatus->lastRefresh()));
        }
    }
}