<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int<0, max>|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
