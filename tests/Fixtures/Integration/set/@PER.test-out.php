<?php

abstract class PER
{
    protected static string $keywords;

    abstract public static function keywords();

    public function heredocAndNowdoc()
    {
        $notAllowed = <<<'COUNTEREXAMPLE'
            Wrong indentation.
            Also, should be nowdoc.
            COUNTEREXAMPLE;
    }

    public function shortClosures()
    {
        $identity = fn (int $x): int => $x;

        $sum = fn (int $x, int $y): int
            => $x + $y;
    }

    public function trailingCommas()
    {
        $min = min(3, M_PI);
        $min = min(
            3,
            M_PI,
        );

        [$foo, $bar] = ['foo', 'bar'];
        [
            $foo,
            $bar,
        ] = [
            'foo',
            'bar',
        ];

        list($foo, $bar) = array('foo', 'bar');
        list(
            $foo,
            $bar,
        ) = array(
            'foo',
            'bar',
        );
    }

    public function chaining()
    {
        $this
            ->foo()
            ->bar()
            ->baz();
    }
}
