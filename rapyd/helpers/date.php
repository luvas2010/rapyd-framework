<?php if (!defined('RAPYD_PATH')) exit('No direct script access allowed');


class rpd_date_helper {

  public static function iso2human($date)
  {
    if ((strpos($date,"0000-00-00")!==false) || ($date==""))
      return "";
    return preg_replace('#^(\d{4})-(\d{2})-(\d{2})( \d{2}:\d{2}:\d{2})?#', '$3/$2/$1$4', $date);
  }

	// --------------------------------------------------------------------

  public static function human2iso($date)
  {
    return preg_replace('#^(\d{2})/(\d{2})/(\d{4})( \d{2}:\d{2}:\d{2})?#', '$3-$2-$1$4', $date);
  }

}
