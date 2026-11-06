<?php

function parametersMultilineWithoutComma(
    $x,
    $y,
) {}
$matchMultilineWithoutComma = match ($a) {
    1 => 0,
    2 => 1,
};
$closureUseMultilineWithoutComma = function () use (
    $x,
    $y,
) {};
