<?php

/**
 * Redirect Cache Klasse
 * Autor: Andreas Höhne
 * 		  Webdesign Höhne
 * v1.0.6 - 02.08.2016
 *
 * Feel free to use this Code on your website, reselling is not allowed
 *
 * Aufruf:
 * include ('includes/modules/redirect_cache.class.php');
 * $redirect_cache = new redirect_cache;
 * $redirect_cache->set_cache_folder('writeperm/redirect_cache/');
 * $redirect_cache->set_cache_time(60*60*24);
 * #$redirect_cache->set_debug(1);
 * $redirect_cache->do_redirect();
 * Cache schreiben:
 * $redirect_cache->write_cache($target_url);
 */

class redirect_cache {
    private $defect = 0;
    private $cache_folder;
    private $request_domain;
    private $request_path;
    private $request_hash_subfolder;
    private $request_hash_filename;
    private $cachefile;
    private $cache_time;
    private $redirect_type = 'permanent';
    private $debug = 0;
    private $hash_requested = FALSE;
    private $request_sheme = 'http';

    function __construct() {
        if (isset($_SERVER['SERVER_NAME'])) {
            $this->request_domain = $_SERVER['SERVER_NAME'];
        }
        if (isset($_SERVER['REQUEST_URI'])) {
            $this->request_path = $_SERVER['REQUEST_URI'];
        }
        $this -> set_debug();
        $this -> set_cache_folder();
        $this -> set_cache_time();
        $this -> set_redirect_type();
        $this->setRequestSheme();
    }

    function set_debug($debug = 0) {
        $this -> debug = $debug;
    }

    function set_cache_folder($folder = 'writeperm/redirect_cache/') {
        #if (is_dir($folder)){
        // 2do right check
        $this -> cache_folder = $folder;
        #}else{
        #	$this->defect = 1;
        #}
    }

    function set_cache_time($cachetime = 2678400) {
        $this->cache_time = $cachetime;
    }

    function set_redirect_type($redirect_type = 'permanent') {
        if (strtolower($redirect_type) == 'permanent' || $redirect_type == 301) {
            $this->redirect_type = 'permanent';
        } elseif (strtolower($redirect_type) == 'temporary' || strtolower($redirect_type) == 'found' || $redirect_type == 302) {
            $this->redirect_type = 'temporary';
        }
    }

    function setRequestSheme($sheme = 'detect') {
        if ($sheme == 'detect') {
            if (isset($_SERVER['REQUEST_SCHEME']) && ($_SERVER['REQUEST_SCHEME'] == 'http' || $_SERVER['REQUEST_SCHEME'] == 'https' ) ) {
                $this->request_sheme = $_SERVER['REQUEST_SCHEME'];
            }
        } elseif ($sheme == 'http' || $sheme == 'https') {
            $this->request_sheme = $sheme;
        }
    }

    function write_cache($target) {
        if ($this -> defect != 1) {
            $this -> request_hash();
            if (isset($_SERVER['SERVER_NAME']) && isset($_SERVER['REQUEST_URI'])) {
                if ($target != $this->request_sheme . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']) {
                    if (!is_dir($this->cache_folder . $this->request_hash_subfolder)) {
                        mkdir($this->cache_folder . $this->request_hash_subfolder);
                    }
                    // Only write when no $_POST variables are set
                    if (!isset($_POST) || (is_array($_POST) && count($_POST) == 0)) {
                        $f = fopen($this->cachefile, 'w');
                        fwrite($f, $target);
                        fclose($f);
                    }
                } else {
                    $this->critical_error();
                }
            }
        } elseif ($this -> defect == 1) {
            $this -> critical_error('defect');
        }
    }

    function request_hash() {
        if ($this -> hash_requested === FALSE) {
            $this -> request_hash_subfolder = md5($this->request_sheme . $this->request_domain);
            $this -> request_hash_filename = md5($this -> request_path) . '.url';
            $this -> cachefile = $this -> cache_folder . $this -> request_hash_subfolder . '/' . $this -> request_hash_filename;
            $this -> hash_requested = TRUE;
        }
    }

    function critical_error($type = 'redir') {
        if ($this->debug == 1) {
            if ($type == 'redir') {
                die('Kritischer Umleitungsfehler, bitte besuchen Sie unsere Hauptseite: ' . '<a href="//' . $_SERVER['SERVER_NAME'] . '/">' . $_SERVER['SERVER_NAME'] . '</a>');
            } elseif ($type == 'defect') {
                die('Variablen defekt gesetzt');
            }
        }
    }

    function do_redirect() {
        if ($this -> defect != 1) {
            $this -> request_hash();
            if ($this -> check_file() === TRUE) {
                $target_url = $this -> read_file();
                if ($target_url != '') {
                    // Only redirect when no $_POST variables are set
                    if (!isset($_POST)) {
                        if ($this->redirect_type == 'permanent') {
                            header("HTTP/1.1 301 Moved Permanently");
                        } elseif ($this->redirect_type == 'temporary') {
                            header("HTTP/1.1 302 Found");
                        }
                        header("Location: " . $target_url);
                        die();
                    }
                } else {
                    $this -> critical_error();
                }
            } else {
                #if ($this->debug==1)die('Check_File stimmt nicht');
            }
        }
    }

    function check_file() {
        if (is_dir($this->cache_folder . $this->request_hash_subfolder)) {
            if (is_file($this->cachefile)) {
                if (filemtime($this->cachefile) >= (time() - $this->cache_time) && filesize($this->cachefile) >= 1) {
                    #if ($this->debug==1)die('Dateigroesse und Zeit stimmt');
                    return TRUE;
                } else {
                    #if ($this->debug==1)die('Dateigroesse und Zeit stimmt nicht');
                }
            } else {
                #if ($this->debug==1)die('Datei nicht vorhanden');
            }
        } else {
            #if ($this->debug==1)die('Verzeichnis nicht vorhanden');
        }
        return FALSE;
    }

    function read_file() {
        return file_get_contents($this->cachefile);
    }

}