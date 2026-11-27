<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $expectString is assigned value true but it already has that value.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Offset $attributes[\'visibility\'] is assigned value null but it already has that value.',
    'count' => 2,
    'path' => __DIR__ . '/../../../tests/Tokenizer/TokensAnalyzerTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
