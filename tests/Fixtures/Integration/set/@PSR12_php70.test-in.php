<?php

declare(strict_types = 1 );

function foo() : void
{
    $a =& $b;
    $c =  &  $d;
}

$class = new class() {};
