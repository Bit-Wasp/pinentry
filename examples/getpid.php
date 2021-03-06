<?php
require __DIR__ . "/../vendor/autoload.php";
use BitWasp\PinEntry\PinEntry;
use BitWasp\PinEntry\Process\Process;
use BitWasp\PinEntry\Process\DebugDecorator;

$pinEntry = new PinEntry(new DebugDecorator(new Process("/usr/bin/pinentry")));
$pin = $pinEntry->getPID();
echo "got PID {$pin}\n";

$pin = $pinEntry->getVersion();
echo "got version {$pin}\n";
