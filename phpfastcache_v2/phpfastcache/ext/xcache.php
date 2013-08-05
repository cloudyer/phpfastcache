<?php

/*
 * khoaofgod@yahoo.com
 * Website: http://www.phpfastcache.com
 * Example at our website, any bugs, problems, please visit http://www.codehelper.io
 */

class phpfastcache_xcache extends phpfastcache_method {

    function checkMethod() {
        // Check xcache
        if(extension_loaded('xcache') && function_exists("xcache_get"))
        {
           return true;
        }
        return false;

    }

    function __construct($option = array()) {
        $this->setOption($option);
        if(!$this->checkMethod()) {
            return false;
        }

    }

    function set($keyword, $value = "", $time = 300, $option = array() ) {

        if(isset($option['skipExisting']) && $option['skipExisting'] == true) {
            if(!$this->isExisting($keyword)) {
                return xcache_set($keyword,$value,$time);
            }
        } else {
            return xcache_set($keyword,$value,$time);
        }
        return false;
    }

    function get($keyword, $option = array()) {
        // return null if no caching
        // return value if in caching
        $data = xcache_get($keyword);
        if($data === false || $data == "") {
            return null;
        }
        return $data;
    }

    function delete($keyword, $option = array()) {
        return xcache_unset($keyword);
    }

    function stats($option = array()) {
        $res = array(
            "info"  =>  "",
            "size"  =>  "",
            "data"  =>  "",
        );

        try {
            $res['data'] = xcache_list(XC_TYPE_VAR,100);
        } catch(Exception $e) {
            $res['data'] = array();
        }
        return $res;
    }

    function clean($option = array()) {
        $cnt = xcache_count(XC_TYPE_VAR);
        for ($i=0; $i < $cnt; $i++) {
            xcache_clear_cache(XC_TYPE_VAR, $i);
        }
        return true;
    }

    function isExisting($keyword) {
        if(xcache_isset($keyword)) {
            return true;
        } else {
            return false;
        }
    }

    function increment($keyword,$step = 1 , $option = array()) {

        $ret =xcache_inc($keyword, $step);
        if($ret === false) {
            $this->set($keyword,$step,3600);
            return $step;
        } else {
            return $ret;
        }
    }

    function decrement($keyword,$step = 1 , $option = array()) {
        $ret = xcache_dec($keyword, $step);
        if($ret === false) {
            $this->set($keyword,$step,3600);
            return $step;
        } else {
            return $ret;
        }
    }


}