<?php

class Foo {
    public const string BAR = 'bar';
}
$ar = 'AR';
echo Foo::{"B${ar}"};
