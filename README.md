redirect_cache
==============

Redirect Cache Class for PHP

      Autor: Andreas Hoehne
      www.php-tuning.de / www.webdesign-hoehne.de
      (c) 2013 Webdesign Hoehne

Install:

1)    This Package should be installed in Root Dir, the class itself should be stored in includes/modules

2)    get sure you have write access to writeperm/redirect_cache

3)    Place following Code in an general Include File:

      include ('includes/modules/redirect_cache.class.php');
      $redirect_cache = new redirect_cache;
      $redirect_cache->do_redirect();

4)    Set $target_url directly before you start your header("Location: xxx"); and add following code:

      $redirect_cache->write_cache($target_url);

