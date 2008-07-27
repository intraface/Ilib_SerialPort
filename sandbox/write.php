<?php


// ini_set('max_execution_time', MAX_EXECUTION_TIME);

/*
require_once('../src/Ilib/SerialPort.php');
$serial = new Ilib_SerialPort();
$serial->deviceSet('device.txt'); 
// $serial->confBaudRate('9600');
// $serial->confCharacterLength(8);
// $serial->confParity('none');
// $serial->confStopBits (1);
$serial->deviceOpen();
*/

unlink('device.txt');


$handle = fopen('device.txt', 'a');

for($i = 0; $i < 4; $i++) {
    fwrite($handle, 'TEST'.$i.chr(13));
    echo "written\n";
    sleep(2);
}
fclose($handle);

echo "Queue processed!\n";

?>