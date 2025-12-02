<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int|false given on the right side.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/AbstractRuleSetDefinition.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
