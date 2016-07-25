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

use PhpCsFixer\Test\AbstractTransformerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 */
final class ImportTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @dataProvider provideProcessCases
     * @requires PHP 5.6
     */
    public function testProcess($source, array $expectedTokens = array())
    {
        $this->doTest($source, $expectedTokens);
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php const FOO = 1;',
                array(
                    1 => 'T_CONST',
                ),
            ),
            array(
                '<?php use Foo; const FOO = 1;',
                array(
                    6 => 'T_CONST',
                ),
            ),
            array(
                '<?php class Foo { const BAR = 1; }',
                array(
                    7 => 'T_CONST',
                ),
            ),
            array(
                '<?php use const Foo\\BAR;',
                array(
                    3 => 'CT_CONST_IMPORT',
                ),
            ),
            array(
                '<?php function foo() {}',
                array(
                    1 => 'T_FUNCTION',
                ),
            ),
            array(
                '<?php $a = function () {};',
                array(
                    5 => 'T_FUNCTION',
                ),
            ),
            array(
                '<?php class Foo { function foo() {} }',
                array(
                    7 => 'T_FUNCTION',
                ),
            ),
            array(
                '<?php use function Foo\\bar;',
                array(
                    3 => 'CT_FUNCTION_IMPORT',
                ),
            ),
        );
    }
}
