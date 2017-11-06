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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Tests\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\CT;

/**
 * @author Sebastiaans Stok <s.stok@rollerscapes.net>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\BraceClassInstantiationTransformer
 */
final class BraceClassInstantiationTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param string $source
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens, array $observedKinds = array())
    {
        $this->doTest(
            $source,
            $expectedTokens,
            $observedKinds
        );
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php echo (new Process())->getOutput();',
                array(
                    3 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    9 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
                array(
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php echo (new Process())::getOutput();',
                array(
                    3 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    9 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
                array(
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php return foo()->bar(new Foo())->bar();',
                array(
                    4 => '(',
                    5 => ')',
                    8 => '(',
                    12 => '(',
                    13 => ')',
                    14 => ')',
                    17 => '(',
                    18 => ')',
                ),
                array(
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php $foo[0](new Foo())->bar();',
                array(
                    5 => '(',
                    9 => '(',
                    10 => ')',
                    11 => ')',
                    14 => '(',
                    15 => ')',
                ),
                array(
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php $foo{0}(new Foo())->bar();',
                array(
                    5 => '(',
                    9 => '(',
                    10 => ')',
                    11 => ')',
                    14 => '(',
                    15 => ')',
                ),
                array(
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php $foo(new Foo())->bar();',
                array(
                    2 => '(',
                    6 => '(',
                    7 => ')',
                    8 => ')',
                    11 => '(',
                    12 => ')',
                ),
                array(
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php $$foo(new Foo())->bar();',
                array(
                    3 => '(',
                    7 => '(',
                    8 => ')',
                    9 => ')',
                    12 => '(',
                    13 => ')',
                ),
                array(
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php if ($foo){}(new Foo)->foo();',
                array(
                    8 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    12 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
                array(
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php echo (((new \stdClass()))->a);',
                array(
                    5 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    12 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
                array(
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php $foo = array(new Foo());',
                array(
                    6 => '(',
                    10 => '(',
                    11 => ')',
                    12 => ')',
                ),
                array(
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php if (new Foo()) { } elseif (new Bar()) { } else if (new Baz()) { }',
                array(
                    3 => '(',
                    7 => '(',
                    8 => ')',
                    9 => ')',
                    17 => '(',
                    21 => '(',
                    22 => ')',
                    23 => ')',
                    33 => '(',
                    37 => '(',
                    38 => ')',
                    39 => ')',
                ),
                array(
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php switch (new Foo()) { }',
                array(
                    3 => '(',
                    7 => '(',
                    8 => ')',
                    9 => ')',
                ),
                array(
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php for (new Foo();;) { }',
                array(
                    3 => '(',
                    7 => '(',
                    8 => ')',
                    11 => ')',
                ),
                array(
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php foreach (new Foo() as $foo) { }',
                array(
                    3 => '(',
                    7 => '(',
                    8 => ')',
                    13 => ')',
                ),
                array(
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php while (new Foo()) { }',
                array(
                    3 => '(',
                    7 => '(',
                    8 => ')',
                    9 => ')',
                ),
                array(
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php do { } while (new Foo());',
                array(
                    9 => '(',
                    13 => '(',
                    14 => ')',
                    15 => ')',
                ),
                array(
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php $static = new static(new \SplFileInfo(__FILE__));',
                array(
                    8 => '(',
                    13 => '(',
                    15 => ')',
                    16 => ')',
                ),
                array(
                    '(',
                    ')',
                    '(',
                    ')',
                ),
            ),
        );
    }

    /**
     * @param string $source
     *
     * @dataProvider provideProcessPhp70Cases
     */
    public function testProcessPhp70($source, array $expectedTokens, array $observedKinds = array())
    {
        $this->doTest(
            $source,
            $expectedTokens,
            $observedKinds
        );
    }

    public function provideProcessPhp70Cases()
    {
        return array(
            array(
                '<?php $foo = new class(new \stdClass()) {};',
                array(
                    8 => '(',
                    13 => '(',
                    14 => ')',
                    15 => ')',
                ),
                array(
                    '(',
                    ')',
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php $foo = (new class(new \stdClass()) {});',
                array(
                    5 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    20 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
                array(
                    CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
        );
    }
}
