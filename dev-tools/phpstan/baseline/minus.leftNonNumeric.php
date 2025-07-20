<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Only numeric types are allowed in \\-, int\\|false given on the left side\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/CheckCommand.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
