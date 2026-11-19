<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\Fixer\\FunctionNotation\\MethodArgumentSpaceFixer::fixNewline() has an unused parameter $override.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/FunctionNotation/MethodArgumentSpaceFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\Fixer\\Operator\\BinaryOperatorSpacesFixer::replacePlaceholders() has an unused parameter $tokenContent.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
