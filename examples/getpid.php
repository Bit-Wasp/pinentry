<?php
require "vendor/autoload.php";
use BitWasp\PinEntry\PinEntry;

$pinEntry = new PinEntry("/usr/bin/pinentry");
$pin = $pinEntry->getInfo("pid");
echo "got pin {$pin}\n";
