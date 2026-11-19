<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $expectString is assigned value true but it already has that value.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
