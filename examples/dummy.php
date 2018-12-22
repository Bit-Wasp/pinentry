<?php
require __DIR__ . "/../vendor/autoload.php";
use BitWasp\PinEntry\PinEntry;
use BitWasp\PinEntry\Process\DebugDecorator;
use BitWasp\PinEntry\Process\Process;

$pinEntry = new PinEntry(new DebugDecorator(new Process("/usr/bin/pinentry")));
$pin = $pinEntry->getInfo("dummy");
echo "got pin {$pin}\n";
