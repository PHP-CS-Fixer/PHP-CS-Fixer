<?php declare(strict_types = 1);

// total 4 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Only booleans are allowed in a negated boolean, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Cache/Signature.php',
];
$ignoreErrors[] = [
    'message' => '#^Only booleans are allowed in a negated boolean, bool\\|int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Only booleans are allowed in a negated boolean, int\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
