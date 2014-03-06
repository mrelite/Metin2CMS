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

class Language
{

    /**
     * @var string
     */
    private static $language;

    /**
     * Get the current language
     *
     * @return string
     */
    public static function getLanguage() {
        return Language::$language;
    }

    /**
     * @var array
     */
    private static $keys;

    /**
     * Init the translator
     *
     * @param $language string
     * @throws SystemException
     */
    public static function init($language) {
        Language::$language = $language;

        // Load language file
        if(!file_exists(ROOT_DIR . 'languages' . DS . $language . '.lang')) {
            throw new SystemException('Invalid language ' . $language);
        }
        Language::$keys = parse_ini_file(ROOT_DIR . 'languages' . DS . $language . '.lang');
    }

    /**
     * Translate this and replace params
     *
     * @param $key string
     * @param $params array
     * @return string
     */
    public static function translate($key, $params = array()) {
        if(array_key_exists($key, Language::$keys)) {
            $return = Language::$keys[$key];
            // Go throw params and replace
            foreach($params as $name => $value) {
                $return = str_replace('%' . $name . '%', $value, $return);
            }

            return $return;
        }
        return $key;
    }

}