<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $innerValues might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
