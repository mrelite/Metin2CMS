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

namespace system\database;

class SQLException extends \Exception {

    /**
     * Create a SQLException
     *
     * @param $message string exception message
     * @param $core int defined exception code
     * @param $previous \Exception previous exception if nested exception
     */
    public function __construct($message, $core = 0, \Exception $previous = null) {
        parent::__construct($message, $core, $previous);
    }

}