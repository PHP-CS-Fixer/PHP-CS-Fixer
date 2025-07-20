<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Generator expects value type array\\<int, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\<int\\|string, PhpCsFixer\\\\Tokenizer\\\\Tokens\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedTraitsFixer.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
