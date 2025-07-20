<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getPriority\\(\\) on PhpCsFixer\\\\Fixer\\\\FixerInterface\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/AbstractProxyFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method isGivenKind\\(\\) on PhpCsFixer\\\\Tokenizer\\\\Token\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getEnd\\(\\) on PhpCsFixer\\\\DocBlock\\\\Annotation\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getStart\\(\\) on PhpCsFixer\\\\DocBlock\\\\Annotation\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
