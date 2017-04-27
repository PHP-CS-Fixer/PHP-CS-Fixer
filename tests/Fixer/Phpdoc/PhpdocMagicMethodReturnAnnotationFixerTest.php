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
final class PhpdocMagicMethodReturnAnnotationFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array|null  $config
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix(array $config = null, $expected, $input = null)
    {
        if (null !== $config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            'Minimal candidate' => array(
                null,
                '<?php class A{function __construct(){}}',
                '<?php class A{/** @return A*/function __construct(){}}',
            ),
            'Single class' => array(
                null,
'<?php class B
{
    /**
     */
    private function __construct()
    {
        return 1;
    }

    /**
     */
    public function __destruct()
    {
        return 1;
    }
}',
'<?php class B
{
    /**
     * @return B
     */
    private function __construct()
    {
        return 1;
    }

    /**
     * @return B
     */
    public function __destruct()
    {
        return 1;
    }
}',
            ),
            'Multiple classes in same file.' => array(
                null,
'<?php
class C
{
    /**
     */
    final public function __construct()
    {
        return 1;
    }
}
class D
{
    /**
     */
    protected function __construct()
    {
    }

    /**
     * @return D
     */
    public function construct()
    {
    }
}
',
'<?php
class C
{
    /**
     * @return int
     */
    final public function __construct()
    {
        return 1;
    }
}
class D
{
    /**
     * @return D
     */
    protected function __construct()
    {
    }

    /**
     * @return D
     */
    public function construct()
    {
    }
}
',
            ),
            'Comments, casing and missing visibility.' => array(
                null,
'<?php class E
{
    /**
     */
    FUNCTION/**/__CONstruct# All your rebase are belong to us.
    ()
    {
        return 1;
    }
}',
'<?php class E
{
    /**
     * @return E
     */
    FUNCTION/**/__CONstruct# All your rebase are belong to us.
    ()
    {
        return 1;
    }
}',
            ),
            'Abstract constructor.' => array(
                null,
'<?php
abstract class G
{
    '.'
    abstract public function __construct();
}',
'<?php
abstract class G
{
    /** @return G*/
    abstract public function __construct();
}',
            ),
            'Single interface multiple annotations.' => array(
                null,
'<?php interface I
{
    /**
     * Some test.
     *
     * @param int $a
     *
     *
     * @internal @final
     */
    function __construct($a = 1);
}',
'<?php interface I
{
    /**
     * Some test.
     *
     * @param int $a
     *
     * @return B
     *
     * @internal @final
     */
    function __construct($a = 1);
}',
            ),
            'Config test' => array(
                array('methods' => array('__clone', '__set')),
'<?php
class A
{
    /**
     * @return A
     */
    public function __construct(){}

    /**
     */
    public function __clone(){}

    /**
     */
    public function __set($a, $b){}
}
',
'<?php
class A
{
    /**
     * @return A
     */
    public function __construct(){}

    /**
     * @return A
     */
    public function __clone(){}

    /**
     * @return A
     */
    public function __set($a, $b){}
}
',
            ),
            'Do not touch functions.' => array(
                null,
'<?php
/**
 * @return F
 */
function __construct()
{
}
',
            ),
        );
    }

    /**
     * @requires PHP 5.4
     */
    public function testTraitFixing()
    {
        $this->doTest(
'<?php trait T
{
    /**
     */
    private function __construct()
    {
        return 1;
    }
}',
'<?php trait T
{
    /**
     * @return int
     */
    private function __construct()
    {
        return 1;
    }
}'
            );
    }

    /**
     * @param array  $config
     * @param string $exceptionMessageRegExp
     *
     * @dataProvider provideInvalidConfig
     */
    public function testInvalidConfiguration(array $config, $exceptionMessageRegExp)
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            $exceptionMessageRegExp
        );

        $this->fixer->configure($config);
    }

    public function provideInvalidConfig()
    {
        return array(
            array(
                array('a' => 1),
                '#^\[phpdoc_magic_method_return_annotation\] Configuration "methods" must be provided as array\.$#',
            ),
            array(
                array('methods' => 1),
                '#^\[phpdoc_magic_method_return_annotation\] Configuration "methods" must be provided as array\.$#',
            ),
            array(
                array('methods' => array('a')),
                '#^\[phpdoc_magic_method_return_annotation\] Only the following magic method names can be configured for fixing "__construct", "__clone", "__destruct", "__set", "__unset", "__wakeUp"\.$#',
            ),
        );
    }
}
