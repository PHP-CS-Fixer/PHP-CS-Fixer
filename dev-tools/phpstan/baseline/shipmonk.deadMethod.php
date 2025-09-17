<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Unused PhpCsFixer\\RuleSet\\AutomaticMigrationSetTrait::calculateActualVersion',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/AutomaticMigrationSetTrait.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Unused PhpCsFixer\\RuleSet\\AutomaticMigrationSetTrait::calculateCandidateSets',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/AutomaticMigrationSetTrait.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Unused PhpCsFixer\\RuleSet\\AutomaticMigrationSetTrait::calculateTargetSet',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/AutomaticMigrationSetTrait.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Unused PhpCsFixer\\RuleSet\\AutomaticMigrationSetTrait::getMigrationSets',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/AutomaticMigrationSetTrait.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
