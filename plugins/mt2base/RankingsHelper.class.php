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
use system\Logger;
use system\SystemException;

class RankingsHelper
{

    /**
     * @var int
     */
    private $lifetime;

    /**
     * @var int
     */
    private $caching_count;

    /**
     * Create a rankings helper
     * Do caching for you and so
     */
    public function __construct() {
        $this->lifetime = Core::$instance->getConfig('plugin_mt2base_rankings_refresh_interval');
        $this->caching_count = Core::$instance->getConfig('plugin_mt2base_rankings_cache_count');
    }

    /**
     * Get the cached rankings (if the cache is out refresh)
     *
     * @param $count int
     * @throws \system\SystemException
     * @return array
     */
    public function getRankings($count) {
        if(!is_int($count)) {
            throw new SystemException('Invalid call of RankingsHelper::getRankings');
        }
        $homepage_sql = Core::$instance->getSql('homepage');
        $player_sql = Core::$instance->getSql('player');

        $variables = $homepage_sql->select('variables', array('content'), '`name`="rankings_cache"');
        $rankings_cache = 0;
        if(count($variables) > 0) {
            $rankings_cache = $variables[0]['content'];
            $insert = false;
        } else {
            $insert = true;
        }

        // Check cache
        if($rankings_cache + $this->lifetime < time()) {
            Logger::verbose('Recaching rankings cache');

            // Refresh cache
            $result = $player_sql->query('SELECT `player`.`name`, `player`.`level`, `player`.`exp`, `player_index`.`empire`
                      FROM `player` INNER JOIN `player_index` ON `player_index`.`id`=`player`.`account_id`
                      ORDER BY `player`.`level` DESC, `player`.`exp` DESC LIMIT 0, ' . $this->caching_count);
            $array = $player_sql->createArray($result);

            // Go throw array and save to cache
            $homepage_sql->clear('mt2base_cache_rankings');
            $place = 1;
            foreach($array as $player) {
                $homepage_sql->insert('mt2base_cache_rankings',
                    array('place', 'name', 'level', 'exp', 'empire'),
                    array($place, $player['name'], $player['level'], $player['exp'], $player['empire']));

                $place++;
            }

            // Update time
            if($insert) {
                $homepage_sql->insert('variables', array('name', 'content'), array('rankings_cache', time()));
            } else {
                $homepage_sql->update('variables', array('content'), array(time()), '`name`="rankings_cache"');
            }
        }

        return $homepage_sql->select('mt2base_cache_rankings', array('place', 'name', 'level', 'exp', 'empire'), '', '0, ' . $count);
    }

}