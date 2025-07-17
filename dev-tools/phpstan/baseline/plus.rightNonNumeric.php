<?php declare(strict_types = 1);

// total 1 error

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the right side\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/AbstractRuleSetDescription.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
