<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Property PhpCsFixer\\Fixer\\Phpdoc\\PhpdocTypesFixer::$typesSetToFix (array<string, true>) does not accept array<\'$this\'|\'array\'|\'bool\'|\'boolean\'|\'callable\'|\'double\'|\'false\'|\'float\'|\'int\'|\'integer\'|\'iterable\'|\'mixed\'|\'null\'|\'object\'|\'parent\'|\'resource\'|\'scalar\'|\'self\'|\'static\'|\'string\'|\'true\'|\'void\', true>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocTypesFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Static property PhpCsFixer\\Tests\\AutoReview\\ProjectCodeTest::$srcClassCases (array<string, array{class-string}>|null) does not accept array<class-string, array{class-string}>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Static property PhpCsFixer\\Tests\\AutoReview\\ProjectCodeTest::$testClassCases (array<string, array{class-string<PhpCsFixer\\Tests\\TestCase>}>|null) does not accept array<class-string<PhpCsFixer\\Tests\\TestCase>, array{class-string<PhpCsFixer\\Tests\\TestCase>}>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/AutoReview/ProjectCodeTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
