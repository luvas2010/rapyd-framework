# Controller Sample #

A controller is a simple php class it must extend rpd superclass (so it can inherit $this->db, $this->url  and some other system libraries).

Conventions/rules:

  * Class name must end with _controller
  * Filename must be minuscase, with the same class name (without_controller suffix)
  * File must be deployed in /application/controllers/  (or in /modules/modulename/controllers/)


```

<?php

class welcome_controller extends rpd {

  function index()
  {
    $vars = array('varname'=>'value');
    echo $this->view('welcome',$vars);
  }
  
  function test()
  {
    echo 'test';
  }

}


```

this request:
```
http://website/welcome
```
will parse the [View](View.md) 'welcome' then the result will be printed

this other request:
```
http://website/welcome/test
```
will output 'test'