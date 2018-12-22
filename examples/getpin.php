<?php
declare(strict_types=1);
require __DIR__ . "/../vendor/autoload.php";
use BitWasp\PinEntry\PinEntry;
use BitWasp\PinEntry\PinValidation\PinValidatorInterface;
use BitWasp\PinEntry\Process\DebugDecorator;
use BitWasp\PinEntry\Process\Process;
use BitWasp\PinEntry\PinRequest;


$request = new PinRequest();
$request->withTitle("Enter that secure pin of yours");
$request->withDesc("Your pin is required in order to proceed");
$request->withPrompt("promptpromptprompt");

$pinEntry = new PinEntry(new DebugDecorator(new Process("/usr/bin/pinentry")));

class SixDigitPinValidator implements PinValidatorInterface
{
    public function validate(string $input, string &$error = null): bool
    {
        if ($input !== (string)(int)$input) {
            $error = "Pin should be an integer, with no special characters";
            return false;
        }
        if (strlen($input) != 6) {
            $error = "PIN must be 6 digits";
            return false;
        }
        return true;
    }
}

$validator = new SixDigitPinValidator();
echo "call getpin\n";
$pin = $pinEntry->getPin($request, $validator);
echo "got pin {$pin}\n";
