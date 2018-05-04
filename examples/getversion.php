<?php
require "vendor/autoload.php";
use BitWasp\PinEntry\PinEntry;
use BitWasp\PinEntry\PinRequest;

$pinEntry = new PinEntry("/usr/bin/pinentry");
$pin = $pinEntry->getInfo("version");
echo "got pin {$pin}\n";
