<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class Request {
    private $ajax = null;
    private $ip = null;
    private $server_aliases = array();

    public function stripSlashes(&$data) {
        return is_array($data) ? array_map(array($this, 'stripSlashes'), $data) : stripslashes($data);
    }

    public function get($name, $type = 'str', $default=null) {
        return $this->getParam($name, $type, $default);
    }

    public function getParam($name, $type = 'str', $default=null) {
        if (isset($_REQUEST[$name])) {
            $val = $_REQUEST[$name];
            if ($type == 'int') {
                $val = preg_replace('~[^\d\-]~', '', $val);
                $val = intval($val, 10);
            }
            elseif ($type == 'float') {
                $val = preg_replace('~[^\d\.,\-]~', '', $val);
                $val = floatval(str_replace(',', '.', $val));
            }
            elseif ($type == 'bool')
                $val = $val == 1 || $val == 'true';
            elseif ($type == 'array') {
                if (! is_array($val))
                    $val = array($val);
            }

            return $val;
        }

        return $default;
    }

    public function isAjax() {
        if ($this->ajax !== null)
            return $this->ajax;

        $this->ajax = false;
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
          && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'
          && isset($_SERVER['HTTP_REFERER'])
          && strlen($_SERVER['HTTP_REFERER']))
            foreach ($this->server_aliases as $alias)
                if (strpos($_SERVER['HTTP_REFERER'], $alias) !== false) {
                    $this->ajax = true;
                    break;
                }

        return $this->ajax;
    }

    public function getUserAgent() {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    public function getUserIP($as_long = false) {
        if ($this->ip !== null)
            return $as_long ? sprintf('%u', ip2long($this->ip)) : $this->ip;

        if (isset($_SERVER['HTTP_CLIENT_IP']) && strlen($_SERVER['HTTP_CLIENT_IP']))
            $this->ip = $_SERVER['HTTP_CLIENT_IP'];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strlen($_SERVER['HTTP_X_FORWARDED_FOR']))
            $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else
            $this->ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';

        return $as_long ? sprintf('%u', ip2long($this->ip)) : $this->ip;
    }

    public function redirect($url, $terminate = true, $statusCode = 302) {
        if (! headers_sent())
            header('Location: ' . $url, true, $statusCode);
        else
            echo "<script type='text/javascript'>window.location.href='{$url}';</script><noscript><meta http-equiv='refresh' content='0;url={$url}' /></noscript>";

        if ($terminate)
            exit;
    }
}
