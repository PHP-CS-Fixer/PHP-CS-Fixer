<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
    'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/NoBlankLinesAfterClassOpeningFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/GroupImportFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/NamespaceNotation/BlankLinesBeforeNamespaceFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Only numeric types are allowed in \\+, int\\<0, max\\>\\|false given on the left side\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
