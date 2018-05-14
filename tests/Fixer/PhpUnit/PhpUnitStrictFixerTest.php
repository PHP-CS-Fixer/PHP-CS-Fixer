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

        $this->fixer->configure(['assertions' => array_keys($this->getMethodsMap())]);
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        $cases = [
            ['<?php $self->foo();'],
        ];

        foreach ($this->getMethodsMap() as $methodBefore => $methodAfter) {
            $cases[] = ["<?php \$sth->${methodBefore}(1, 1);"];
            $cases[] = ["<?php \$sth->${methodAfter}(1, 1);"];
            $cases[] = [
                "<?php \$this->${methodAfter}(1, 2);",
                "<?php \$this->${methodBefore}(1, 2);",
            ];
            $cases[] = [
                "<?php \$this->${methodAfter}(1, 2); \$this->${methodAfter}(1, 2);",
                "<?php \$this->${methodBefore}(1, 2); \$this->${methodBefore}(1, 2);",
            ];
            $cases[] = [
                "<?php \$this->${methodAfter}(1, 2, 'descr');",
                "<?php \$this->${methodBefore}(1, 2, 'descr');",
            ];
            $cases[] = [
                "<?php \$this->/*aaa*/${methodAfter} \t /**bbb*/  ( /*ccc*/1  , 2);",
                "<?php \$this->/*aaa*/${methodBefore} \t /**bbb*/  ( /*ccc*/1  , 2);",
            ];
            $cases[] = [
                "<?php \$this->${methodAfter}(\$expectedTokens->count() + 10, \$tokens->count() ? 10 : 20 , 'Test');",
                "<?php \$this->${methodBefore}(\$expectedTokens->count() + 10, \$tokens->count() ? 10 : 20 , 'Test');",
            ];
            $cases[] = [
                "<?php self::${methodAfter}(1, 2);",
                "<?php self::${methodBefore}(1, 2);",
            ];
            $cases[] = [
                "<?php static::${methodAfter}(1, 2);",
                "<?php static::${methodBefore}(1, 2);",
            ];
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
        $this->fixer->configure(['assertions' => array_keys($this->getMethodsMap())]);
        $this->doTest($expected);
    }

    public function provideTestNoFixWithWrongNumberOfArgumentsCases()
    {
        $cases = [];
        foreach ($this->getMethodsMap() as $candidate => $fix) {
            $cases[sprintf('do not change call to "%s" without arguments.', $candidate)] = [
                sprintf('<?php $this->%s();', $candidate),
            ];

            foreach ([1, 4, 5, 10] as $argumentCount) {
                $cases[sprintf('do not change call to "%s" with #%d arguments.', $candidate, $argumentCount)] = [
                    sprintf(
                        '<?php $this->%s(%s);',
                        $candidate,
                        substr(str_repeat('$a, ', $argumentCount), 0, -2)
                    ),
                ];
            }
        }

        return $cases;
    }

    public function testInvalidConfig()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('/^\[php_unit_strict\] Invalid configuration: The option "assertions" .*\.$/');

        $this->fixer->configure(['assertions' => ['__TEST__']]);
    }

    /**
     * @return array<string, string>
     */
    private function getMethodsMap()
    {
        return [
            'assertAttributeEquals' => 'assertAttributeSame',
            'assertAttributeNotEquals' => 'assertAttributeNotSame',
            'assertEquals' => 'assertSame',
            'assertNotEquals' => 'assertNotSame',
        ];
    }
}
