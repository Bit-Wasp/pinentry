<?php
require __DIR__ . "/../vendor/autoload.php";
use BitWasp\PinEntry\PinEntry;
use BitWasp\PinEntry\Process\Process;

$pinEntry = new PinEntry(new Process("/usr/bin/pinentry"));
$pin = $pinEntry->getInfo("version");
echo "got pin {$pin}\n";
