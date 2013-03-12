<?php

/**
 * Redirect Cache Klasse
 * Autor: Andreas Höhne
 * 		  Webdesign Höhne
 *
 * Feel free to use this Code on your website, reselling is not allowed
 *
 * Aufruf:
 include ('includes/modules/redirect_cache.class.php');
 $redirect_cache = new redirect_cache;
 $redirect_cache->cache_folder = 'writeperm/redirect_cache/';
 $redirect_cache->cache_time = 60*60*24;
 #$redirect_cache->debug = 1;
 $redirect_cache->do_redirect();

 * Cache schreiben:
 $redirect_cache->write_cache($target_url);

 */

class redirect_cache {
	var $cache_folder;
	var $request_domain;
	var $request_path;
	var $request_hash_subfolder;
	var $request_hash_filename;
	var $cachefile;
	var $cache_time = 2678400; // Standard 31 Tage
	var $redirect_type = 'permanent';
	var $debug = 0;
	var $hash_requested = FALSE;

    function redirect_cache() {
    	$this->request_domain = $_SERVER['SERVER_NAME'];
    	$this->request_path = $_SERVER['REQUEST_URI'];
    }

    function critical_error(){
    	die('Kritischer Umleitungsfehler, bitte besuchen Sie unsere Hauptseite: ' .
    	'<a href="http://'.$_SERVER['SERVER_NAME'].'/">'.$_SERVER['SERVER_NAME'].'</a>');
    }

    function write_cache($target){
    	$this->request_hash();
    	if ($target != 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']){
    		if (!is_dir($this->cache_folder.$this->request_hash_subfolder)){
	    		mkdir($this->cache_folder.$this->request_hash_subfolder);
	    	}
	    	$f = fopen($this->cachefile, 'w');
			fwrite($f, $target);
			fclose($f);
    	}else{
    		$this->critical_error();
    	}
    }

    function read_file(){
		return file_get_contents($this->cachefile);
    }

    function check_file(){
		if (is_dir($this->cache_folder.$this->request_hash_subfolder)){
			if (is_file($this->cachefile)){
				if (filemtime($this->cachefile) >= ( time() - $this->cache_time )
				&& filesize($this->cachefile) >= 1){
					#if ($this->debug==1)die('Dateigrösse und Zeit stimmt');
					return TRUE;
				}else{
					#if ($this->debug==1)die('Dateigrösse und Zeit stimmt nicht');
				}
			}else{
				#if ($this->debug==1)die('Datei nicht vorhanden');
			}
		}else{
			#if ($this->debug==1)die('Verzeichnis nicht vorhanden');
		}
		return FALSE;
    }

    function request_hash(){
    	if ($this->hash_requested == FALSE){
    		$this->request_hash_subfolder = md5($this->request_domain);
	    	$this->request_hash_filename = md5($this->request_path).'.url';
	    	$this->cachefile = $this->cache_folder.
	    		$this->request_hash_subfolder.
				'/'.$this->request_hash_filename;
			$this->hash_requested = TRUE;
    	}
    }

    function do_redirect(){
    	$this->request_hash();
    	if ($this->check_file() == TRUE){
    		$target_url = $this->read_file();
    		if ($target_url != ''){
    			if ($this->redirect_type == 'permanent' || $this->redirect_type == 301){
		    		header("HTTP/1.1 301 Moved Permanently");
		    	}
			    header("Location: ".$target_url);
			    die();
    		}else{
    			$this->critical_error();
    		}
    	}else{
    		#if ($this->debug==1)die('Check_File stimmt nicht');
    	}
    }

}
?>