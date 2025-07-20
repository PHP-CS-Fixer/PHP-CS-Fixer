<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Only booleans are allowed in a negated boolean, bool\\|int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
