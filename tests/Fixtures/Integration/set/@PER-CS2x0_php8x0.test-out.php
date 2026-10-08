<?php

function parametersMultilineWithoutComma(
    $x,
    $y,
) {}

parametersMultilineWithoutComma(x: 3, y: 4);

$matchMultilineWithoutComma = match ($a) {
    1 => 0,
    2 => 1,
};
