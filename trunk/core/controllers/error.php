<?php
class error_controller extends rpd {

    function code($code)
    {
        if ($code=='404')
        {
                $page = '404';
        }
        else
        {
                $page = 'error';
        }
        echo $this->view('errors/'.$page);
    }


}
?>