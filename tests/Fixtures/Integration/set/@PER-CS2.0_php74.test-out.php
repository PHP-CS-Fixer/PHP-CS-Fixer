<?php

$arrayMultilineWithoutComma = [
    'foo' => 'bar',
    'foo2' => 'bar',
];
$heredocMultilineWithoutComma = [
    'foo',
    <<<EOD
        bar
        EOD,
];
argumentsMultilineWithoutComma(
    1,
    2,
);
function parametersMultilineWithoutComma(
    $x,
    $y
) {}
