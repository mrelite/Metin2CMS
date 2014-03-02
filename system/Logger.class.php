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

class Logger {

    /**
     * The log level:
     * 0 - Verbose & Warning & Error
     * 1 - Warning & Error
     * 2 - Error
     *
     * @var int
     */
    private static $logLevel;

    /**
     * Current log file pointer
     *
     * @var resource
     */
    private static $file;

    /**
     * Rotate all logs and open the current file
     *
     * @param int $logLevel The log level
     */
    public static function init($logLevel = 1) {
        Logger::$logLevel = $logLevel;

        // create log directory if not exists
        if(!is_dir(ROOT_DIR . "log")) {                     // Current log directory
            mkdir(ROOT_DIR . "log" . DS, 0777);
        }
        if(!is_dir(ROOT_DIR . "log" . DS . "archive")) {    // Rotation directory
            mkdir(ROOT_DIR . "log" . DS . "archive" . DS);
        }

        // do rotate
        Logger::checkRotate();

        // open current log
        Logger::$file = fopen(ROOT_DIR . "log" . DS . date("dmY") . ".log", "a+");
    }

    public static function close() {
        fclose(Logger::$file);
    }

    public static function error($message) {
        if(Logger::$logLevel > 2) {
            return;
        }
        Logger::writeLog("[ERROR]" . $message);
    }

    public static function warning($message) {
        if(Logger::$logLevel > 1) {
            return;
        }
        Logger::writeLog("[WARNING]" . $message);
    }

    public static function verbose($message) {
        if(Logger::$logLevel > 0) {
            return;
        }
        Logger::writeLog("[VERBOSE]" . $message);
    }

    private static function writeLog($message) {
        $prefix = "[" . $_SERVER["REMOTE_ADDR"] . "]" . "[" . date("H:i:s") . "]";
        fwrite(Logger::$file, $prefix . $message . PHP_EOL);
    }

    private static function checkRotate() {
        $logFiles = glob(ROOT_DIR . "log" . DS . "*.log");

        foreach($logFiles as $file) {
            $split = explode(".", basename($file));   // [0] = filename [1] = extension
            // Rotate if file is not from today or lager as 5 MB
            if($split[0] != date("dmY") || filesize($file) > 5 * 1024 * 1024) {
                Logger::logRotate($file);
            }
        }
    }

    /**
     * Rotate the file and add a counter (if more than 1 log for a day)
     *
     * @param $file The file which should be rotated
     * @return bool
     */
    private static function logRotate($file) {
        $sameNamedFiles = glob(ROOT_DIR . "log" . DS . "archive" . DS . '*' . basename($file));

        $counter = ((count($sameNamedFiles)) + 1);
        return rename(ROOT_DIR . "log" . DS . basename($file), ROOT_DIR . "log" . DS . "archive" . DS . $counter . '_' . basename($file));
    }

}