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

use system\Logger;

class Login extends \system\Login {

    /**
     * @var User
     */
    private $currentUser;

    /**
     * Check if login request send and try data
     *
     * @param $core \system\Core
     * @return int error type
     */
    public function tryLogin($core)
    {
        if(!empty($_POST['login_user']) && !empty($_POST['login_pwd'])) {
            $account_sql = $core->getSql('account');

            $result = $account_sql->select('account', array('login', 'id', 'password', 'status', 'availDt'), '`login`="' . $_POST['login_user'] . '" and `password`=PASSWORD("' . $_POST['login_pwd'] . '")');
            if(count($result) != 0) {
                if($result[0]['status'] != 'OK') {
                    return -2;
                }
                // Updating session variables
                $_SESSION['login'] = $result[0]['login'];
                $_SESSION['id'] = $result[0]['id'];
                $_SESSION['salt'] = sha1($result[0]['id'] . $result[0]['login'] . $result[0]['password']);

                // Create current user instance
                $this->currentUser = new User($result[0]['id']);

                return 1;
            } else {
                Logger::warning('Try to login into account ' . $_POST['login_user']);
                return -1;
            }
        }

        return 0;
    }

    /**
     * Is currently a user logged in?
     *
     * @param $core \system\Core
     * @return bool
     */
    public function isLogin($core)
    {
        if(!empty($_SESSION['login']) && !empty($_SESSION['id']) && !empty($_SESSION['salt'])) {
            $account_sql = $core->getSql('account');
            $result = $account_sql->select('account', array('login', 'id', 'password', 'status', 'availDt'), '`id`="' . $_SESSION['id'] . '"');
            if(count($result) == 1) {
                if($result[0]['login'] == $_SESSION['login']) {
                    $salt = sha1($result[0]['id'] . $result[0]['login'] . $result[0]['password']);
                    if($_SESSION['salt'] == $salt) {
                        if($result[0]['status'] != 'OK') {
                            $this->clearLoginIdentify();
                            return false;
                        }
                        $this->currentUser = new User($result[0]['id']);
                        return true;
                    } else {
                        $this->clearLoginIdentify();
                        Logger::warning('Invalid salt?! Potential attack! Exit!');
                        exit();
                    }
                } else {
                    $this->clearLoginIdentify();
                    Logger::warning('Invalid login?! Potential attack! Exit!');
                    exit();
                }
            } else {
                $this->clearLoginIdentify();
            }

        }

        return false;
    }

    /**
     * Used for logout and on invalid login data
     */
    public function clearLoginIdentify() {
        unset($_SESSION['login']);
        unset($_SESSION['id']);
        unset($_SESSION['salt']);
    }

    /**
     * Has the current user right to do or see this?
     *
     * @param $right string right identifier
     * @return bool
     */
    public function hasRight($right)
    {
        // TODO: Implement hasRight() method.
    }

    /**
     * Returns the current user instance
     *
     * @return User
     */
    public function getCurrentUser()
    {
        return $this->currentUser;
    }

}