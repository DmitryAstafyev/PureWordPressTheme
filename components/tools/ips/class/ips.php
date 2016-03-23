<?php
namespace Pure\Components\Tools\IPs{
    class Core {
        public function getClientIP() {
            $ipaddress = '';
            $ipaddress = ($ipaddress === '' ? (isset($_SERVER['HTTP_CLIENT_IP']         ) ? $_SERVER['HTTP_CLIENT_IP']          : '') : $ipaddress);
            $ipaddress = ($ipaddress === '' ? (isset($_SERVER['HTTP_X_FORWARDED_FOR']   ) ? $_SERVER['HTTP_X_FORWARDED_FOR']    : '') : $ipaddress);
            $ipaddress = ($ipaddress === '' ? (isset($_SERVER['HTTP_X_FORWARDED']       ) ? $_SERVER['HTTP_X_FORWARDED']        : '') : $ipaddress);
            $ipaddress = ($ipaddress === '' ? (isset($_SERVER['HTTP_FORWARDED_FOR']     ) ? $_SERVER['HTTP_FORWARDED_FOR']      : '') : $ipaddress);
            $ipaddress = ($ipaddress === '' ? (isset($_SERVER['HTTP_FORWARDED']         ) ? $_SERVER['HTTP_FORWARDED']          : '') : $ipaddress);
            $ipaddress = ($ipaddress === '' ? (isset($_SERVER['REMOTE_ADDR']            ) ? $_SERVER['REMOTE_ADDR']             : '') : $ipaddress);
            $ipaddress = ($ipaddress === '' ? (isset($_SERVER['HTTP_CLIENT_IP']         ) ? $_SERVER['HTTP_CLIENT_IP']          : '') : $ipaddress);
            $ipaddress = ($ipaddress === '' ? 'UNKNOWN' : $ipaddress);
            return $ipaddress;
        }
    }
}
?>