<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool) of method PhpCsFixer\\Tokenizer\\Tokens::setSize() should be covariant with return type (true) of method SplFixedArray<PhpCsFixer\\Tokenizer\\Token>::setSize()',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Tokens.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
