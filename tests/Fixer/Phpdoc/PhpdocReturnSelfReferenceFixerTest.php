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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class PhpdocReturnSelfReferenceFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected      PHP code
     * @param string|null $input         PHP code
     * @param array|null  $configuration
     *
     * @dataProvider provideTestCases
     */
    public function testFix($expected, $input = null, array $configuration = null)
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public function provideTestCases()
    {
        return array(
            array(
                '<?php interface A{/** @return    $this */public function test();}',
                '<?php interface A{/** @return    this */public function test();}',
            ),
            array(
                '<?php interface B{/** @return self|int */function test();}',
                '<?php interface B{/** @return $SELF|int */function test();}',
            ),
            array(
                '<?php interface C{/** @return $self|int */function test();}',
                null,
                array('$static' => 'static'),
            ),
            array(
                '<?php class D {} /** @return {@this} */ require_once($a);echo 1;echo 1;echo 1;echo 1;echo 1;echo 1;echo 1;echo 1;',
            ),
            array(
                '<?php /** @return this */ require_once($a);echo 1;echo 1;echo 1;echo 1;echo 1;echo 1;echo 1;echo 1; class E {}',
            ),
        );
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideGeneratedFixCases
     */
    public function testGeneratedFix($expected, $input)
    {
        $config = array($input => $expected);
        $this->fixer->configure($config);

        $expected = sprintf('<?php
/**
 * Please do not use @return %s|static|self|this|$static|$self|@static|@self|@this as return type hint
 */
class F
{
    /**
     * @param %s
     *
     * @return %s
     */
     public function AB($self)
     {
        return $this; // %s
     }
}
', $input, $input, $expected, $input);

        $input = sprintf('<?php
/**
 * Please do not use @return %s|static|self|this|$static|$self|@static|@self|@this as return type hint
 */
class F
{
    /**
     * @param %s
     *
     * @return %s
     */
     public function AB($self)
     {
        return $this; // %s
     }
}
', $input, $input, $input, $input);

        $this->doTest($expected, $input);
    }

    /**
     * Expected after fixing, return type to fix.
     *
     * @return array<array<string, string>
     */
    public function provideGeneratedFixCases()
    {
        return array(
            array('$this', 'this'),
            array('$this', '@this'),
            array('self', '$self'),
            array('self', '@self'),
            array('static', '$static'),
            array('static', '@STATIC'),
        );
    }

    /**
     * @param array  $configuration
     * @param string $message
     *
     * @dataProvider provideInvalidConfiguration
     */
    public function testInvalidConfiguration(array $configuration, $message)
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            sprintf('/^\[phpdoc_return_self_reference\] %s$/', preg_quote($message, '/'))
        );

        $this->fixer->configure($configuration);
    }

    public function provideInvalidConfiguration()
    {
        return array(
            array(
                array(1 => 'a'),
                'Unknown key "integer#1", expected any of "this", "@this", "$self", "@self", "$static", "@static".',
            ),
            array(
                array(
                    'this' => 'foo',
                ),
                'Unknown value "string#foo", expected any of "$this", "static", "self".',
            ),
        );
    }
}
