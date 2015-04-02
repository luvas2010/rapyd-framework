# File System #

![http://www.rapyd.com/assets/images/filesystem.jpg](http://www.rapyd.com/assets/images/filesystem.jpg)

Rapyd Filesystem is clean, “system” is separated from “application” (usually you can upgrade
by replacing “rapyd” folder).
Furthermore, you can isolate application pieces in “modules” folder (so you can organize a
cms in modules).

If you need to customize “rapyd/libraries” you can build your extended version in “application/
libraries”

Autoload: you do not have to worry about load classes before instancing or extending, Rapyd
autoload will find and include all needed files (even if they are in a different folder/module).