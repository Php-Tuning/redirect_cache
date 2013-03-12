redirect_cache
==============

redirect Cache Class for PHP

Autor Andreas Höhne
      Webdesign Höhne


Install:

1) This Package should be installed in Root Dir, the class itself should be stored in includes/modules

2) chmod 777 writeperm

3) chmod 777 writeperm/redirect_cache

4) Place following Code in an general Include File:

  include ('includes/modules/redirect_cache.class.php');
  
  $redirect_cache = new redirect_cache;
  
  #$redirect_cache->cache_folder = 'writeperm/redirect_cache/'; // Cache File
  
  #$redirect_cache->cache_time = 60*60*24*31; // 31 days
  
  #$redirect_cache->debug = 1;
  
  $redirect_cache->do_redirect();
  
5) Set $target_url directly before you start your header("Location: xxx"); and add following code:

  $redirect_cache->write_cache($target_url);
  
