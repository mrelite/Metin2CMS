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

class ServerStatus {

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var int
     */
    private $refresh_interval;

    /**
     * Only available after getAllStatus
     * @var int
     */
    private $last_refresh;

    /**
     * Create a ServerStatus handler
     *
     * @param $timeout int
     * @param $refresh_interval int
     */
    public function __construct($timeout = -1, $refresh_interval = null) {
        $this->timeout = Core::$instance->getConfig("plugin_mt2base_server_timeout");
        $this->refresh_interval = Core::$instance->getConfig("plugin_mt2base_refresh_interval");
    }

    /**
     * Get the stats from every core / db / whatever
     *
     * @return array
     */
    public function getAllStatus() {
        $sql = Core::$instance->getSql("homepage");
        $result = $sql->select("mt2base_serverstatus", array("name", "ip", "port", "last", "lastcheck"));
        foreach($result as $stat) {
            if($stat["name"] == "const_player_count") {
                continue;
            }
            $timestamp = strtotime($stat["lastcheck"]);
            $isOnline = $stat["last"] == 1;
            if($timestamp + $this->refresh_interval < time()) {
                $isOnline = @fsockopen($stat["ip"], $stat["port"], $er, $es, $this->timeout) !== false;
                // refresh data
                $sql->update("mt2base_serverstatus", array("last", "lastcheck"), array($isOnline ? 1 : 0, "NOW()"), "`name`='" . $stat["name"] . "'", true);
            }

            $status[$stat["name"]] = $isOnline;
            $this->last_refresh = $timestamp;
        }

        return $status;
    }

    /**
     * Get the number of online players
     *
     * @return int
     */
    public function getPlayerOnline() {
        if(Core::$instance->getConfig("plugin_mt2base_user_count_method") == "mysql") {
            $sql = Core::$instance->getSql("homepage");
            $result = $sql->select("mt2base_serverstatus", array('last', 'lastcheck'), '`name`="const_player_count"');
            if(count($result) < 1) {
                $count = Core::$instance->getSql('player')->selectCount('player', 'DATE_SUB(NOW(), INTERVAL 5 MINUTE) < last_play');
                $sql->insert('mt2base_serverstatus', array('name', 'last', 'lastcheck'), array('const_player_count', $count, 'NOW()'));
                return $count;
            } else {
                $timestamp = strtotime($result[0]['lastcheck']);
                if($timestamp + $this->refresh_interval < time()) {
                    $count = Core::$instance->getSql('player')->selectCount('player', 'DATE_SUB(NOW(), INTERVAL 5 MINUTE) < last_play');
                    $sql->update('mt2base_serverstatus', array('last', 'lastcheck'), array($count, 'NOW()'), '`name`="const_player_count"');
                    return $count;
                }

                return $result[0]['last'];
            }
        }
    }

    /**
     * Only available after ServerStatus::getAllStatus()
     * @return int
     */
    public function lastRefresh() {
        return $this->last_refresh;
    }

}