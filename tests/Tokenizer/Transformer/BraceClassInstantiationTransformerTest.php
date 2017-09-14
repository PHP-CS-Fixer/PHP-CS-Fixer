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
                    8 => '(',
                    14 => ')',
                ),
            ),
            array(
                '<?php $foo[0](new Foo())->bar();',
                array(
                    5 => '(',
                    11 => ')',
                ),
            ),
            array(
                '<?php $foo{0}(new Foo())->bar();',
                array(
                    5 => '(',
                    11 => ')',
                ),
            ),
            array(
                '<?php $foo(new Foo())->bar();',
                array(
                    2 => '(',
                    8 => ')',
                ),
            ),
            array(
                '<?php $$foo(new Foo())->bar();',
                array(
                    3 => '(',
                    9 => ')',
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
                    12 => ')',
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
                    15 => ')',
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
