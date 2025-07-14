<?php declare(strict_types = 1);

// total 1 error

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type array\\<int\\<0, max\\>, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ControlStructure/IncludeFixer.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
