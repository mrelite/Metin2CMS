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
use system\SystemException;

class User {

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $data = array();

    /**
     * @var bool
     */
    private $dataExists = true;

    /**
     * Create a account / user instance and load all needed data from database
     *
     * @param $id int
     * @throws \system\SystemException
     */
    public function __construct($id) {

        $account_sql = Core::$instance->getSql('account');
        $homepage_sql = Core::$instance->getSql('homepage');

        $this->id = $account_sql->escape($id);

        $result = $account_sql->select('account', array('login', 'email'), '`id`="' . $this->id . '"');
        if(count($result) == 1) {
            $this->login = $result[0]['login'];
            $this->email = $result[0]['email'];

            $data = $homepage_sql->select('mt2base_account_data', array('data'), '`id`="' . $this->id . '"');
            if(count($data) == 1) {
                $this->data = unserialize($data[0]['data']);
            } else {
                $this->dataExists = false;
            }
        } else {
            throw new SystemException('Invalid account id');
        }
    }

    /**
     * Save data changes to database
     */
    public function save() {
        $homepage_sql = Core::$instance->getSql('homepage');

        if($this->dataExists) {
            $homepage_sql->update('mt2base_account_data', array('data'), array(serialize($this->data)), '`id`="' . $this->id . '"');
        } else {
            $homepage_sql->insert('mt2base_account_data', array('id', 'data'), array($this->id, serialize($this->data)));
        }
    }

    public function getID() { return $this->id; }
    public function getLogin() { return $this->login; }
    public function getEmail() { return $this->email; }
    public function getData($dataName, $default) {
        if(array_key_exists($dataName, $this->data)) {
            return $this->data[$dataName];
        }
        $this->data[$dataName] = $default;

        return $default;
    }

}