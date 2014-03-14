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

class EventHandler {

    const REGEX_VALID_EVENT_NAME = '/^[a-zA-Z][a-zA-Z0-9]*$/';

    /**
     * @var array(callable)
     */
    private $events;

    /**
     * Add an event to the handler
     *
     * @param $event_name string
     * @param $event_callback callable
     * @throws SystemException
     */
    public function addEvent($event_name, $event_callback) {
        if(!is_callable($event_callback)) {
            throw new SystemException("Invalid callback");
        }

        if(!is_string($event_name) ||
            preg_match(self::REGEX_VALID_EVENT_NAME, $event_name) === 0) {
            throw new SystemException("Invalid event name");
        }

        $this->events[$event_name][] = $event_callback;
    }

    /**
     * Trigger all events related to a name
     *
     * @param $event_name string
     * @param $obj_subject object
     * @param $event_params array
     * @throws SystemException
     */
    public function triggerEvent($event_name, $obj_subject = null, array $event_params = null) {
        Logger::verbose('EventHandler::triggerEvent called. Checking if event_name is valid');
        if(is_string($event_name)) {
            Logger::verbose('Trigger event ' . $event_name);
            if(isset($this->events[$event_name])) {
                Logger::verbose('Event exists');
                array_unshift($event_params, $obj_subject);
                foreach($this->events[$event_name] as $callback) {
                    Logger::verbose('Call event (' . $callback . ')');
                    if(!is_callable($callback)) {
                        throw new SystemException("Invalid callback (should never thrown)");
                    }
                    call_user_func_array($callback, $event_params);
                }
            }
        } else {
            throw new SystemException("Invalid callback");
        }
    }

}