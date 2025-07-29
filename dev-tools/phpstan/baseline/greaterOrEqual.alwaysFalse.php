<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Comparison operation "\\>\\=" between int\\<70400, 80499\\> and 80500 is always false\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Tokenizer/FCT.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
