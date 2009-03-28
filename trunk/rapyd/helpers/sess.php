<?php if (!defined('RAPYD_PATH')) exit('No direct script access allowed');



class rpd_sess_helper {


  public static function get_persistence()
  {
    $self = $_SERVER['PHP_SELF'];
    $session  = @$_SESSION['rapyd'];

    if ($session===FALSE)
      return array();
      return (isset($session[$self])) ? $session[$self] : array();

  }

	// --------------------------------------------------------------------

  public static function save_persistence()
  {
    $self = $_SERVER['PHP_SELF'];
    $page = self::get_persistence();

    if (count($_POST)<1)
    {
      if ( isset($page["back_post"]) )
      {
        $_POST = $page["back_post"];
      }
    } else {
      $page["back_post"]= $_POST;
    }

    $page["back_url"]= rawurldecode(rpd_url_helper::get_url());
    $_SESSION['rapyd'][$self] = $page;
  }

	// --------------------------------------------------------------------

  public static function clear_persistence()
  {
    $self = $_SERVER['PHP_SELF'];
    unset($_SESSION['rapyd'][$self]);
  }



}
