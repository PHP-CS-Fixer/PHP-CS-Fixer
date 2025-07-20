<?php declare(strict_types = 1);

// total 1 error

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Fixer\\\\Import\\\\FullyQualifiedStrictTypesFixer\\:\\:\\$reservedIdentifiersByLevel \\(array\\<int\\<0, max\\>, array\\<string, true\\>\\>\\) does not accept non\\-empty\\-array\\<int, array\\<string, true\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
