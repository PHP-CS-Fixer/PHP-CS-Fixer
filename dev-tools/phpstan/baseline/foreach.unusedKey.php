<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach key variable $rule is never read.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/RuleSet/Sets/AutoPHPUnitMigrationRiskySetTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
