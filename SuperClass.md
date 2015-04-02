# rpd the superclass #

Rapyd framework has a "superclass" (like other mvc frameworks):

[rpd](http://code.google.com/p/rapyd-framework/source/browse/trunk/rapyd/libraries/rapyd.php)

In the application flow, the superclass is used statically in the system gateway (the main index.php file which handle all request).

rpd class has all methods needed in a (minimalistic) mvc:

  * router (to detect current/requested uri)
  * controller (to instance the class that will perform actions to serve an uri request)
  * view (called by controller, it include/parse an output file, usually a simple php file with html tags and some loop and echo).