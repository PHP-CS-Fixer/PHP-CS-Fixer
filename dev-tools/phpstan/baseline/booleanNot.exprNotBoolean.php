<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a negated boolean, PhpCsFixer\\FileReader|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/FileReader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a negated boolean, PhpCsFixer\\Tokenizer\\Transformers|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Transformers.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
