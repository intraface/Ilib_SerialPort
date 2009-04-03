<?php

ini_set('max_execution_time', 120);

define('SERIAL_DEVICE', 'COM4');
define('PHONE_NUMBER', '51906011');

require_once('Ilib/SerialPort.php');
$serial = new Ilib_SerialPort();
$serial->deviceSet(SERIAL_DEVICE); 
$serial->confBaudRate('9600');
$serial->confCharacterLength(8);
$serial->confParity('none');
$serial->confStopBits (1);
$serial->deviceOpen();

// sets the way to send message.
$serial->sendMessage('AT+CMGF=1'.chr(13));

$this->serial->sendMessage('AT+CMGS="+45'.PHONE_NUMBER.'"'.chr(13));
$this->serial->sendMessage('TEST'.chr(26).chr(13), 1);

echo  $this->serial->readPort();

$serial->deviceClose();
?>