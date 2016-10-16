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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ReturnTypeDeclarationFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider testFixProvider
     * @requires PHP 7.0
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function testFixProvider()
    {
        return array(
            array(
                '<?php function foo(int $a) {}',
            ),
            array(
                '<?php function foo(int $a): string {}',
                '<?php function foo(int $a):string {}',
            ),
            array(
                '<?php function foo(int $a)/**/: /**/string {}',
                '<?php function foo(int $a)/**/:/**/string {}',
            ),
            array(
                '<?php function foo(int $a): string {}',
                '<?php function foo(int $a)  :  string {}',
            ),
            array(
                '<?php function foo(int $a) /**/: /**/ string {}',
                '<?php function foo(int $a) /**/ : /**/ string {}',
            ),
        );
    }
}
