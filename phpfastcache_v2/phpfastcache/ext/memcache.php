<?php


/*
 * khoaofgod@yahoo.com
 * Website: http://www.phpfastcache.com
 * Example at our website, any bugs, problems, please visit http://www.codehelper.io
 */


class phpfastcache_memcache extends phpfastcache_method {

    var $instant;

    function checkMethod() {
        // Check memcache
        if(function_exists("memcache_connect")) {
            return true;
        }
        return false;
    }

    function __construct($option = array()) {
        $this->setOption($option);
        if(!$this->checkMethod()) {
            return false;
        }
        $this->instant = new Memcache();
    }

    function connectServer() {
        $server = $this->option['server'];
        if(count($server) < 1) {
            $server = array(
                array("127.0.0.1",11211),
            );
        }

        foreach($server as $s) {
            $name = $s[0]."_".$s[1];
            if(!isset($this->checked[$name])) {
                $this->instant->addserver($s[0],$s[1]);
                $this->checked[$name] = 1;
            }

        }
    }

    function set($keyword, $value = "", $time = 300, $option = array() ) {
        $this->connectServer();
        if(isset($option['skipExisting']) && $option['skipExisting'] == true) {
            return $this->instant->add($keyword, $value, false, $time );

        } else {
            return $this->instant->set($keyword, $value, false, $time );
        }

    }

    function get($keyword, $option = array()) {
        $this->connectServer();
        // return null if no caching
        // return value if in caching
        $x = $this->instant->get($keyword);
        if($x == false) {
            return null;
        } else {
            return $x;
        }
    }

    function delete($keyword, $option = array()) {
        $this->connectServer();
         $this->instant->delete($keyword);
    }

    function stats($option = array()) {
        $this->connectServer();
        $res = array(
            "info"  => "",
            "size"  =>  "",
            "data"  => $this->instant->getStats(),
        );

        return $res;

    }

    function clean($option = array()) {
        $this->connectServer();
        $this->instant->flush();
    }

    function isExisting($keyword) {
        $this->connectServer();
        $x = $this->get($keyword);
        if($x == null) {
            return false;
        } else {
            return true;
        }
    }

    function increment($keyword,$step =1 , $option = array()) {
        $this->connectServer();
        $this->instant->increment($keyword, $step);
    }

    function decrement($keyword,$step =1 , $option = array()) {
        $this->connectServer();
        $this->instant->decrement($keyword, $step);
    }

}