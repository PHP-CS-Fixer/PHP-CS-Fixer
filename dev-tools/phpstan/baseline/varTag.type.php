<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc tag @var with type DOMElement is not subtype of type ((TNode of DOMNode)|false).',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Console/Report/FixReport/CheckstyleReporter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc tag @var with type DOMElement is not subtype of type ((TNode of DOMNode)|false).',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Report/FixReport/JunitReporter.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
