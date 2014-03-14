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

namespace plugins\mt2base\pages;

use plugins\mt2base\RegisterField;
use system\Core;
use system\Language;
use system\Logger;
use system\pages\Page;

class Register implements Page {

    /**
     * @var array(RegisterField)
     */
    private static $fields = array();

    public function __construct() {
        Register::$fields[] = new RegisterField('login', 'text', Language::translate('register_login'), '/^([a-zA-Z0-9_]{4,16})$/', true);
        Register::$fields[] = new RegisterField('password', 'password', Language::translate('register_pwd'), '/^([\w\d]{8,24})$/', true);
        Register::$fields[] = new RegisterField('password_repeat', 'password', Language::translate('register_pwd_repeat'), '', true);
        Register::$fields[] = new RegisterField('email', 'text', Language::translate('register_email'), '/^[\w][\w-.]+@[\w-.]+\.[a-z]{2,4}$/U', true);
        Register::$fields[] = new RegisterField('email_repeat', 'text', Language::translate('register_email_repeat'), '', true);
        Register::$fields[] = new RegisterField('delete_code', 'text', Language::translate('register_delete_code'), '/^([a-zA-Z0-9]{4,16})$/', true);

        // For adding fields with plugins
        Core::$instance->getEventHandler()->triggerEvent('create_register_dialog', $this);
    }

    /**
     * Get the template file for this page
     *
     * @return string
     */
    public function getTemplateName()
    {
        return "mt2base/register.tpl";
    }

    /**
     * Prepare smarty for use this template.
     * Assign variables and do queries here.
     *
     * @param $core \system\Core
     * @param $smarty \Smarty
     */
    public function prepare($core, $smarty)
    {
        // Check for register request
        if(!empty($_POST['do_register']) && $_POST['do_register'] == 1) {
            $success = true;
            $errors = array();
            foreach(Register::$fields as $field) {
                if($field->isValid($_POST['register'][$field->getName()])) {

                } else {
                    $success = false;
                    $errors[] = Language::translate('register_error_' . $field->getName());
                }
            }

            // Check password and email repeat
            if($_POST['register']['password'] != $_POST['register']['password_repeat']) {
                $success = false;
                $errors[] = Language::translate('register_error_password_repeat');
            }
            if($_POST['register']['email'] != $_POST['register']['email_repeat']) {
                $success = false;
                $errors[] = Language::translate('register_error_email_repeat');
            }

            // All fields okay? Check username && run query
            if($success) {
                // Check username
                $account_sql = $core->getSql('account');
                $count = $account_sql->selectCount('account', '`login`="' . $account_sql->escape($_POST['register']['login']) . '" or ' .
                    '`email`="' . $account_sql->escape($_POST['register']['email']) . '"');
                if($count > 0) {
                    $success = false;
                    $errors[] = Language::translate('register_error_login_email');
                } else {
                    // Create account
                    $account_sql->insert('account',
                        array(
                            'login',
                            'password',
                            'social_id',
                            'email',
                            'status',
                            'create_time'
                        ),
                        array(
                            $_POST['register']['login'],
                            'PASSWORD("' . $account_sql->escape($_POST['register']['password']) . '")',
                            $_POST['register']['delete_code'],
                            $_POST['register']['email'],
                            'OK',
                            'NOW()'
                        )
                    );
                    Logger::verbose('Create account ' . $_POST['register']['login']);
                    $insert_id = $account_sql->lastInsertId();
                    Logger::verbose('Account has id '. $insert_id);

                    $core->getEventHandler()->triggerEvent('finish_create_account', $this, array($insert_id));
                }
            }

            $smarty->assign('request', true);
            $smarty->assign('success', $success);
            $smarty->assign('errors', $errors);
        }

        $smarty->assign('fields', Register::$fields);
    }

}