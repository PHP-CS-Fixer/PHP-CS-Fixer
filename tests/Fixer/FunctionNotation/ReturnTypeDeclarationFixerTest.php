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
 *
 * @requires PHP 7.0
 * @covers \PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer
 */
final class ReturnTypeDeclarationFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfiguration()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[return_type_declaration\] Invalid configuration: The option "s" does not exist. (Known|Defined) options are: "space_before".$#'
        );

        $this->fixer->configure(array('s' => 9000));
    }

    /**
     * @group legacy
     * @dataProvider testFixProviderWithSpaceBeforeNone
     * @expectedDeprecation Passing NULL to set default configuration is deprecated and will not be supported in 3.0, use an empty array instead.
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testLegacyFixWithDefaultConfiguration($expected, $input = null)
    {
        $this->fixer->configure(null);

        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider testFixProviderWithSpaceBeforeNone
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithDefaultConfiguration($expected, $input = null)
    {
        $this->fixer->configure(array());

        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider testFixProviderWithSpaceBeforeNone
     *
     * @param string      $expected
     * @param null|string $input
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
                '<?php function foo1(int $a) {}',
            ),
            array(
                '<?php function foo2(int $a): string {}',
                '<?php function foo2(int $a):string {}',
            ),
            array(
                '<?php function foo3(int $c)/**/ : /**/ string {}',
            ),
            array(
                '<?php function foo4(int $a): string {}',
                '<?php function foo4(int $a)  :  string {}',
            ),
            array(
                '<?php function foo5(int $e)#
: #
#
string {}',
                '<?php function foo5(int $e)#
:#
#
string {}',
            ),
            array(
                '<?php
                    function foo1(int $a): string {}
                    function foo2(int $a): string {}
                    function foo3(int $a): string {}
                    function foo4(int $a): string {}
                    function foo5(int $a): string {}
                    function foo6(int $a): string {}
                    function foo7(int $a): string {}
                    function foo8(int $a): string {}
                    function foo9(int $a): string {}
                ',
                '<?php
                    function foo1(int $a):string {}
                    function foo2(int $a):string {}
                    function foo3(int $a):string {}
                    function foo4(int $a):string {}
                    function foo5(int $a):string {}
                    function foo6(int $a):string {}
                    function foo7(int $a):string {}
                    function foo8(int $a):string {}
                    function foo9(int $a):string {}
                ',
            ),
        );
    }

    /**
     * @dataProvider testFixProviderWithSpaceBeforeOne
     *
     * @param string      $expected
     * @param null|string $input
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
                '<?php function fooA(int $a) {}',
            ),
            array(
                '<?php function fooB(int $a) : string {}',
                '<?php function fooB(int $a):string {}',
            ),
            array(
                '<?php function fooC(int $a)/**/ : /**/string {}',
                '<?php function fooC(int $a)/**/:/**/string {}',
            ),
            array(
                '<?php function fooD(int $a) : string {}',
                '<?php function fooD(int $a)  :  string {}',
            ),
            array(
                '<?php function fooE(int $a) /**/ : /**/ string {}',
            ),
        );
    }
}
