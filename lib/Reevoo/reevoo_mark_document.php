<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade reevoo to newer
 * versions in the future. If you wish to customize the code for your
 * needs please refer to https://github.com/reevoo/ for more information.
 *
 * @category    Reeovoo Magento Connect
 * @package     Com_Reevoo
 * @copyright   Copyright (c) 20015 Reevoo Limited. (www.reevoo.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Reevoo Package Module.
 *
 * @category   Reeovoo Magento Connect
 * @package    Com_Reevoo
 * @author     Haroon Chaudhry<haroon.chaudhry@innovadeltech.com>
 */
 
class ReevooMarkDocument {

    protected $head, $body, $headers;

    function __construct($data, $mtime) {
        if ($this->data = $data)
            list($this->head, $this->body) = explode("\n\n", $this->data, 2);
        else
            $this->head = $this->body = null;
        $this->mtime = $mtime;
    }

    function body() {
        if ($this->isValidResponse())
            return $this->body;
        else
            return "";
    }

    function statusCode() {
        $headers = explode("\n", $this->head, 2);
        $status_line = $headers[0];
        if (preg_match("/^HTTP\/[^ ]* ([0-9]{3})/", $status_line, $matches))
            return $matches[1];
        else
            return 500;
    }

    function header($name) {
        $headers = $this->headers();
        $name = strtolower($name);
        if (array_key_exists($name, $headers))
            return $headers[$name];
        else
            return null;
    }

    function headers() {
        if ($this->headers)
            return $this->headers;
        else {
            $headers = explode("\n", $this->head);
            array_shift($headers); // Status line is no use here
            $parsed_headers = Array();
            foreach ($headers as $header) {
                list($key, $value) = explode(":", $header, 2);
                $parsed_headers[strtolower($key)] = trim($value);
            }
            $this->headers = $parsed_headers;
            return $this->headers;
        }
    }

    function isValidResponse() {
        if (!$this->data)
            return false;
        return 200 == $this->statusCode();
    }

    function isCachableResponse() {
        return $this->statusCode() < 500;
    }

    function hasExpired() {
        if (!$this->data)
            return true;
        return $this->maxAge() < $this->currentAge();
    }

    function maxAge() {
        if (preg_match("/max-age=([0-9]+)/", $this->header("Cache-Control"), $matches))
            return $matches[1];
    }

    function currentAge() {
        return time() - $this->mtime + $this->header("Age");
    }

}

;
