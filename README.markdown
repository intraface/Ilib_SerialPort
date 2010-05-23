Communicate with a serial port from PHP
==

This is a PHP Version 5 port of [phpSerial](http://www.phpclasses.org/package/3679-PHP-Communicate-with-a-serial-port.html) written by Rémy Sanchez.

Introduction
--

This class can be used to communicate with a serial port using PHP under Linux or Windows.

It takes the path (like "/dev/ttyS0" for linux or "COM1" for windows) of serial device and checks whether it is valid before opening a connection to it.

Once the connection is opened, it can send data to the serial port, and read answers (reading is only implemented for linux).

The class may also change connection parameters for the given serial device.

/!\ WARNING /!\ 
--

It works with linux for r/w, but with windows I've only been able to make write working. If you're a windows user, try to access the serial port through network with serproxy instead.
The class have been reported to work fine with Mac OS X, but i've not tested it, so I've left the OS test in the sourcecode, but feel free to add macos in it.