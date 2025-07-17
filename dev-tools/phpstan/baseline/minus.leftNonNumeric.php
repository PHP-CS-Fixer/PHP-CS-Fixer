<?php declare(strict_types = 1);

// total 1 error

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Only numeric types are allowed in \\-, int\\|false given on the left side\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/CheckCommand.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
