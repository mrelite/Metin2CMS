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

abstract class Login {

    /**
     * Check if login request send and try data
     *
     * @param $core \system\Core
     * @return int error type
     */
    public abstract function tryLogin($core);

    /**
     * Is currently a user logged in?
     *
     * @param $core \system\Core
     * @return bool
     */
    public abstract function isLogin($core);

    /**
     * Has the current user right to do or see this?
     *
     * @param $right string right identifier
     * @return bool
     */
    public abstract function hasRight($right);

    /**
     * Returns the current user instance
     *
     * @return mixed
     */
    public abstract function getCurrentUser();

}