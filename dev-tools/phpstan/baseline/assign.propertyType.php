<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Property PhpCsFixer\\FixerConfiguration\\FixerOption::$allowedValues (non-empty-list<bool|(callable(mixed): bool)|float|int|string|null>|null) does not accept non-empty-array<int<0, max>, bool|(callable(mixed): bool)|Closure|float|int|string|null>|null.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/FixerConfiguration/FixerOption.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
