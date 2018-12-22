<?php
$required = [1=>0, 2=>0];

$tests = [
    [0=>0, 1=>0, 2=>0],
    [1=>0, 2=>0, 3=>0],
];

foreach ($tests as $test) {
    $missingKeys = array_diff_key($required, $test);
    if (count($missingKeys) > 0) {
        echo "Missing keys " . implode(" ", $missingKeys) . PHP_EOL;
    }
}
