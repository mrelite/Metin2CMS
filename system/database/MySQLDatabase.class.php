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

class MySQLDatabase {

    /**
     * The mysqli instance of this connection
     * @var \mysqli
     */
    private $mysqli;

    /**
     * The hostname for the connection
     * @var string
     */
    private $host;

    /**
     * The username for the connection
     * @var string
     */
    private $username;

    /**
     * The database name for every query
     * @var string
     */
    private $database;

    /**
     * Create a database connection with mysqli
     *
     * @param $host string hostname for connection
     * @param $username string username for connection
     * @param $password string password for connection
     * @param $database string selected database
     * @throws SQLException if any error occur while connection
     */
    public function __construct($host, $username, $password, $database) {
        // Setting variables
        $this->host = $host;
        $this->username = $username;
        $this->database = $database;

        // Creating mysqli connection
        $this->mysqli = new \mysqli($this->host, $this->username, $this->password, $this->database);

        // Checking connection (No object style because of PHP bug in 5.2.9 and 5.3.0)
        if(mysqli_connect_error()) {
            throw new SQLException("Connection to database " . $this->database . " failed");
        }
    }

}