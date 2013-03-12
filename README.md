redirect_cache
==============

Redirect Cache Class for PHP

      Autor: Andreas Höhne
      www.webdesign-hoehne.de


Install:

1)    This Package should be installed in Root Dir, the class itself should be stored in includes/modules

2)    chmod 777 writeperm

3)    chmod 777 writeperm/redirect_cache

4)    Place following Code in an general Include File:
      
      include ('includes/modules/redirect_cache.class.php');
      $redirect_cache = new redirect_cache;
      $redirect_cache->do_redirect();
  
5)    Set $target_url directly before you start your header("Location: xxx"); and add following code:
      
      $redirect_cache->write_cache($target_url);
  
