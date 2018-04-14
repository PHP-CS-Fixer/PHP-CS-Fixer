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

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer
 */
final class PhpUnitStrictFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @group legacy
     * @dataProvider provideTestFixCases
     * @expectedDeprecation Passing "assertions" at the root of the configuration is deprecated and will not be supported in 3.0, use "assertions" => array(...) option instead.
     */
    public function testLegacyFix($expected, $input = null)
    {
        $this->fixer->configure(array_keys($this->getMethodsMap()));
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);

        $this->fixer->configure(array('assertions' => array_keys($this->getMethodsMap())));
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        $cases = array(
            array('<?php $self->foo();'),
        );

        foreach ($this->getMethodsMap() as $methodBefore => $methodAfter) {
            $cases[] = array("<?php \$sth->${methodBefore}(1, 1);");
            $cases[] = array("<?php \$sth->${methodAfter}(1, 1);");
            $cases[] = array(
                "<?php \$this->${methodAfter}(1, 2);",
                "<?php \$this->${methodBefore}(1, 2);",
            );
            $cases[] = array(
                "<?php \$this->${methodAfter}(1, 2); \$this->${methodAfter}(1, 2);",
                "<?php \$this->${methodBefore}(1, 2); \$this->${methodBefore}(1, 2);",
            );
            $cases[] = array(
                "<?php \$this->${methodAfter}(1, 2, 'descr');",
                "<?php \$this->${methodBefore}(1, 2, 'descr');",
            );
            $cases[] = array(
                "<?php \$this->/*aaa*/${methodAfter} \t /**bbb*/  ( /*ccc*/1  , 2);",
                "<?php \$this->/*aaa*/${methodBefore} \t /**bbb*/  ( /*ccc*/1  , 2);",
            );
            $cases[] = array(
                "<?php \$this->${methodAfter}(\$expectedTokens->count() + 10, \$tokens->count() ? 10 : 20 , 'Test');",
                "<?php \$this->${methodBefore}(\$expectedTokens->count() + 10, \$tokens->count() ? 10 : 20 , 'Test');",
            );
        }

        return $cases;
    }

    /**
     * Only method calls with 2 or 3 arguments should be fixed.
     *
     * @param string $expected
     *
     * @dataProvider provideTestNoFixWithWrongNumberOfArgumentsCases
     */
    public function testNoFixWithWrongNumberOfArguments($expected)
    {
        $this->fixer->configure(array('assertions' => array_keys($this->getMethodsMap())));
        $this->doTest($expected);
    }

    public function provideTestNoFixWithWrongNumberOfArgumentsCases()
    {
        $cases = array();
        foreach ($this->getMethodsMap() as $candidate => $fix) {
            $cases[sprintf('do not change call to "%s" without arguments.', $candidate)] = array(
                sprintf('<?php $this->%s();', $candidate),
            );

            foreach (array(1, 4, 5, 10) as $argumentCount) {
                $cases[sprintf('do not change call to "%s" with #%d arguments.', $candidate, $argumentCount)] = array(
                    sprintf(
                        '<?php $this->%s(%s);',
                        $candidate,
                        substr(str_repeat('$a, ', $argumentCount), 0, -2)
                    ),
                );
            }
        }

        return $cases;
    }

    public function testInvalidConfig()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '/^\[php_unit_strict\] Invalid configuration: The option "assertions" .*\.$/'
        );

        $this->fixer->configure(array('assertions' => array('__TEST__')));
    }

    /**
     * @return array<string, string>
     */
    private function getMethodsMap()
    {
        return array(
            'assertAttributeEquals' => 'assertAttributeSame',
            'assertAttributeNotEquals' => 'assertAttributeNotSame',
            'assertEquals' => 'assertSame',
            'assertNotEquals' => 'assertNotSame',
        );
    }
}
