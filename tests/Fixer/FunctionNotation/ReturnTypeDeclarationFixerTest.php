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
    public function testInvalidConfiguration()
    {
        $this->fixer->configure(array('s' => 9000));
    }

    /**
     * @dataProvider testFixProviderWithSpaceBeforeNone
     *
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 7.0
     */
    public function testFixWithDefaultConfiguration($expected, $input = null)
    {
        $this->fixer->configure(null);

        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider testFixProviderWithSpaceBeforeNone
     *
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 7.0
     */
    public function testFixWithSpaceBeforeNone($expected, $input = null)
    {
        $this->fixer->configure(array(
            'space_before' => 'none',
        ));

        $this->doTest($expected, $input);
    }

    public function testFixProviderWithSpaceBeforeNone()
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

    /**
     * @dataProvider testFixProviderWithSpaceBeforeOne
     *
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 7.0
     */
    public function testFixWithSpaceBeforeOne($expected, $input = null)
    {
        $this->fixer->configure(array(
            'space_before' => 'one',
        ));

        $this->doTest($expected, $input);
    }

    public function testFixProviderWithSpaceBeforeOne()
    {
        return array(
            array(
                '<?php function foo(int $a) {}',
            ),
            array(
                '<?php function foo(int $a) : string {}',
                '<?php function foo(int $a):string {}',
            ),
            array(
                '<?php function foo(int $a)/**/ : /**/string {}',
                '<?php function foo(int $a)/**/:/**/string {}',
            ),
            array(
                '<?php function foo(int $a) : string {}',
                '<?php function foo(int $a)  :  string {}',
            ),
            array(
                '<?php function foo(int $a) /**/ : /**/ string {}',
            ),
        );
    }
}
