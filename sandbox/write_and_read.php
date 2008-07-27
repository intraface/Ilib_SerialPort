<?php


unlink('device.txt');
touch('device.txt');


$handle = fopen('device.txt', 'r+b');

$content = '';

for($i = 0; $i < 4; $i++) {
    // $pointer = ftell($handle);
    // fseek($handle, 0);
    
    // fseek($handle, $pointer);
    fwrite($handle, 'TEST'.$i."\n");
    $content .= fread($handle, 128);
    rewind($handle);
    $content .= fread($handle, 128);
    // echo "written\n";
    sleep(1);
}
fclose($handle);

echo $content;

echo "Queue processed!\n";

?>