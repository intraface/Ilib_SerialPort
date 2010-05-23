<?php
/**
 *  Simple serial relay script for turning my sprinkler system on and off from the web!
 *
 * Utilizes the PHP Serial class by Rémy Sanchez <thenux@gmail.com>
 * (Thanks you rule!!) to communicate with the QK108/CK1610 serial relay board!
 */

//check the GET action var to see if an action is to be performed
if (isset($_GET['action'])) {
    //Action required

    //Load the serial port class
    require("../src/Ilib/SerialPort.php");

    //Initialize the class
    $serial = new Ilib_SerialPort();

    //Specify the serial port to use... in this case COM1
    $serial->deviceSet("COM1");

    //Set the serial port parameters. The documentation says 9600 8-N-1, so
    $serial->confBaudRate(9600); //Baud rate: 9600
    $serial->confParity("none");  //Parity (this is the "N" in "8-N-1")
    $serial->confCharacterLength(8); //Character length (this is the "8" in "8-N-1")
    $serial->confStopBits(1);  //Stop bits (this is the "1" in "8-N-1")
    $serial->confFlowControl("none");
//Device does not support flow control of any kind,
//so set it to none.

    //Now we "open" the serial port so we can write to it
    $serial->deviceOpen();

    //Issue the appropriate command according to the serial relay
    //board documentation
    if ($_GET['action'] == "on") {
        //to turn relay number 1 on, we issue the command
        $serial->sendMessage("N1\r");

    } else if ($_GET['action'] == "off") {
        //to turn relay number 1 off, we issue this command
        $serial->sendMessage("F1\r");
    }

    //We're done, so close the serial port again
    $serial->deviceClose();

}


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Sprinkler System Controller</title>
</head>
<body>

<h1>Sprinkler System Controller</h1>
<p><a href="<?=$_SERVER['PHP_SELF'] . "?action=on" ?>">
Click here to turn the system on.</a></p>
<p><a href="<?=$_SERVER['PHP_SELF'] . "?action=off" ?>">
Click here to turn the system off.</a></p>
</body>
</html>