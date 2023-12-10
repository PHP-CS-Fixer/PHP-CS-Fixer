<?php

declare(strict_types=1);

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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
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
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);

        $this->fixer->configure(['assertions' => array_keys(self::getMethodsMap())]);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield ['<?php $self->foo();'];

        yield [self::generateTest('$self->foo();')];

        foreach (self::getMethodsMap() as $methodBefore => $methodAfter) {
            yield [self::generateTest("\$sth->{$methodBefore}(1, 1);")];

            yield [self::generateTest("\$sth->{$methodAfter}(1, 1);")];

            yield [self::generateTest("\$this->{$methodBefore}(1, 2, 'message', \$toMuch);")];

            yield [
                self::generateTest("\$this->{$methodAfter}(1, 2);"),
                self::generateTest("\$this->{$methodBefore}(1, 2);"),
            ];

            yield [
                self::generateTest("\$this->{$methodAfter}(1, 2); \$this->{$methodAfter}(1, 2);"),
                self::generateTest("\$this->{$methodBefore}(1, 2); \$this->{$methodBefore}(1, 2);"),
            ];

            yield [
                self::generateTest("\$this->{$methodAfter}(1, 2, 'description');"),
                self::generateTest("\$this->{$methodBefore}(1, 2, 'description');"),
            ];

            yield [
                self::generateTest("\$this->/*aaa*/{$methodAfter} \t /**bbb*/  ( /*ccc*/1  , 2);"),
                self::generateTest("\$this->/*aaa*/{$methodBefore} \t /**bbb*/  ( /*ccc*/1  , 2);"),
            ];

            yield [
                self::generateTest("\$this->{$methodAfter}(\$expectedTokens->count() + 10, \$tokens->count() ? 10 : 20 , 'Test');"),
                self::generateTest("\$this->{$methodBefore}(\$expectedTokens->count() + 10, \$tokens->count() ? 10 : 20 , 'Test');"),
            ];

            yield [
                self::generateTest("self::{$methodAfter}(1, 2);"),
                self::generateTest("self::{$methodBefore}(1, 2);"),
            ];

            yield [
                self::generateTest("static::{$methodAfter}(1, 2);"),
                self::generateTest("static::{$methodBefore}(1, 2);"),
            ];

            yield [
                self::generateTest("STATIC::{$methodAfter}(1, 2);"),
                self::generateTest("STATIC::{$methodBefore}(1, 2);"),
            ];
        }

        foreach (self::getMethodsMap() as $methodBefore => $methodAfter) {
            yield [
                self::generateTest("static::{$methodAfter}(1, 2,);"),
                self::generateTest("static::{$methodBefore}(1, 2,);"),
            ];

            yield [
                self::generateTest("self::{$methodAfter}(1, \$a, '', );"),
                self::generateTest("self::{$methodBefore}(1, \$a, '', );"),
            ];
        }
    }

    /**
     * Only method calls with 2 or 3 arguments should be fixed.
     *
     * @dataProvider provideNoFixWithWrongNumberOfArgumentsCases
     */
    public function testNoFixWithWrongNumberOfArguments(string $expected): void
    {
        $this->fixer->configure(['assertions' => array_keys(self::getMethodsMap())]);
        $this->doTest($expected);
    }

    public static function provideNoFixWithWrongNumberOfArgumentsCases(): iterable
    {
        foreach (self::getMethodsMap() as $candidate => $fix) {
            yield sprintf('do not change call to "%s" without arguments.', $candidate) => [
                self::generateTest(sprintf('$this->%s();', $candidate)),
            ];

            foreach ([1, 4, 5, 10] as $argumentCount) {
                yield sprintf('do not change call to "%s" with #%d arguments.', $candidate, $argumentCount) => [
                    self::generateTest(
                        sprintf(
                            '$this->%s(%s);',
                            $candidate,
                            substr(str_repeat('$a, ', $argumentCount), 0, -2)
                        )
                    ),
                ];
            }
        }
    }

    public function testInvalidConfig(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[php_unit_strict\] Invalid configuration: The option "assertions" .*\.$/');

        $this->fixer->configure(['assertions' => ['__TEST__']]);
    }

    /**
     * @return array<string, string>
     */
    private static function getMethodsMap(): array
    {
        return [
            'assertAttributeEquals' => 'assertAttributeSame',
            'assertAttributeNotEquals' => 'assertAttributeNotSame',
            'assertEquals' => 'assertSame',
            'assertNotEquals' => 'assertNotSame',
        ];
    }

    private static function generateTest(string $content): string
    {
        return "<?php final class FooTest extends \\PHPUnit_Framework_TestCase {\n    public function testSomething() {\n        ".$content."\n    }\n}\n";
    }
}
