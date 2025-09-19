<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Trait PhpCsFixer\\RuleSet\\AutomaticMigrationSetTrait is used zero times and is not analysed.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/AutomaticMigrationSetTrait.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
