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

use system\core;
use system\SystemException;

class RegisterField {

    /**
     * The internal name for this field
     * @var string
     */
    private $name;

    /**
     * The type of the input field
     * @var string
     */
    private $type;

    /**
     * The display name for this field
     * @var string
     */
    private $display;

    /**
     * The RegEx pattern to check the string
     * @var string
     */
    private $pattern;

    /**
     * Is this field required
     * @var bool
     */
    private $required;

    /**
     * @param $name string
     * @param $type string
     * @param $display string
     * @param $pattern string
     * @param $required bool
     */
    public function __construct($name, $type, $display, $pattern, $required) {
        $this->name = $name;
        $this->type = $type;
        $this->display = $display;
        $this->pattern = $pattern;
        $this->required = $required;
    }

    /**
     * Get the internal name for this field
     * @return string
     */
    public function getName() { return $this->name; }

    /**
     * Get the type of the input field
     * @return string
     */
    public function getType() { return $this->type; }

    /**
     * Get the display name for this field
     * @return string
     */
    public function getDisplay() { return $this->display; }

    /**
     * Get the RegEx pattern to check the string
     * @return string
     */
    public function getPattern() { return $this->pattern; }

    /**
     * Is this field required
     * @return bool
     */
    public function isRequired() { return $this->required; }

    /**
     * Get the captcha html code
     * @return string
     */
    public function getCaptchaHtml() {
        if($this->type != 'captcha')
            throw new SystemException("Tried to get captcha html of an non captcha field.");

        if(!Core::$instance->getConfig("general_recaptcha_enable")) 
            throw new SystemException("Recaptcha isn't enabled. Please activate it or wait for other captchas");

        return recaptcha_get_html(Core::$instance->getConfig("general_recaptcha_pubkey"));
    }

    /**
     * Check if the value is valid for this field
     * @param $value string
     * @return bool
     */
    public function isValid($value) {
        // Is Captcha?
        if($this->type == 'captcha') {
            if(Core::$instance->getConfig("general_recaptcha_enable")) {
                $result = recaptcha_check_answer(Core::$instance->getConfig("general_recaptcha_privkey"), 
                    $_SERVER["REMOTE_ADDR"], 
                    $_POST["recaptcha_challenge_field"], 
                    $_POST["recaptcha_response_field"]);
                return $result->is_valid;
            } else {
                throw new SystemException("Recaptcha isn't enabled. Please activate it or wait for other captchas");
            }
        }

        // Check for empty
        if(empty($value) && $this->required)
            return false;

        // Check for pattern
        if(empty($this->pattern))
            return true;

        if(!preg_match($this->pattern, $value))
            return false;

        return true;
    }

}