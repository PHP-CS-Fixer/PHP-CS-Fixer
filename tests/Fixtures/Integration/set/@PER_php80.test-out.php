<?php

class PER80
{
    public function trailingCommas(
        int|float $num,
    ) {
        $match = match ($num) {
            3 => 'three',
            M_PI => 'pi',
            default => 'other',
        };

        $sum = fn (
            int $x,
            int $y,
        ) => $x + $y;
    }
}
