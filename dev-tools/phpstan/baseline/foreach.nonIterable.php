<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Argument of an invalid type array<string, PhpCsFixer\\RuleSet\\RuleSet>|false supplied for foreach, only iterables are supported.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Documentation/FixerDocumentGenerator.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
