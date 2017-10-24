<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Jack Cherng <jfcherng@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\FunctionCompactNullableTypehintFixer
 */
final class FunctionCompactNullableTypehintFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php function foo(/**? int*/$param) {}',
            ],
            [
                '<?php function foo(?callable $param) {}',
                '<?php function foo(? callable $param) {}',
            ],
            [
                '<?php function foo(?array &$param) {}',
                '<?php function foo(? array &$param) {}',
            ],
            [
                '<?php function foo(?Bar $param) {}',
                '<?php function foo(? Bar $param) {}',
            ],
            [
                '<?php function foo(?Bar\Baz $param) {}',
                '<?php function foo(? Bar\Baz $param) {}',
            ],
            [
                '<?php function foo(?Bar\Baz &$param) {}',
                '<?php function foo(? Bar\Baz &$param) {}',
            ],
            [
                '<?php $foo = function(?Bar\Baz $param) {};',
                '<?php $foo = function(? Bar\Baz $param) {};',
            ],
            [
                '<?php $foo = function(?Bar\Baz &$param) {};',
                '<?php $foo = function(? Bar\Baz &$param) {};',
            ],
            [
                '<?php class Test { public function foo(?Bar\Baz $param) {} }',
                '<?php class Test { public function foo(? Bar\Baz $param) {} }',
            ],
            [
                '<?php $foo = function(?array $a,
                    ?array $b) {};',
                '<?php $foo = function(?
                    array $a,
                    ? array $b) {};',
            ],
            [
                '<?php function foo(?array ...$param) {}',
                '<?php function foo(? array ...$param) {}',
            ],
        ];
    }
}
