<?php

declare(strict_types=1);

namespace BitWasp\PinEntry\PinValidation;

/**
 * This interface must be implemented for the desired type of PIN.
 * It uses the return value of validate to indicate a positive/negative
 * outcome, and upon failure, error message will be written to $errorMsg
 */
interface PinValidatorInterface
{
    public function validate(string $input, string &$errorMsg = null): bool;
}
