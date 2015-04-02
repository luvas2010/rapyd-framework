![http://www.rapyd.com/assets/images/flow.gif](http://www.rapyd.com/assets/images/flow.gif)

# application flow #


  * index.php (system gateway) include a configuration file and the rpd superclass
  * the superclass include and instance a controller, then invoke one of its methods (following the requested uri)
  * the method call a view to send an output

## use case ##

  * user request http://myhost/welcome/
  * apache parse .htaccess in application root then call index.php if /controller/method/ isn't a valid file or folder
  * index include framework files, then the application controller: /controllers/welcome.php
  * superclass instance "Welcome" class then invoke $welcome->index()
  * index method call a view (include a php-html file and print an output)