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
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\Tests\\DocBlock\\TypeExpressionTest::parseTypeExpression() has an unused parameter $namespace.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/DocBlock/TypeExpressionTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\Tests\\DocBlock\\TypeExpressionTest::parseTypeExpression() has an unused parameter $namespaceUses.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/DocBlock/TypeExpressionTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
