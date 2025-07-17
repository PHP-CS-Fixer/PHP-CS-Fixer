<?php declare(strict_types = 1);

// total 1 error

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Only iterables can be unpacked, int\\|list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given in argument \\#3\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
