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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 * @author John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer
 */
final class UnaryOperatorSpacesFixerTest extends AbstractFixerTestCase
{
    public function testWrongConfigItem(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '/^\[unary_operator_spaces\] Invalid configuration: The option "foo" does not exist\. Defined options are: "default", "operators"\.$/'
        );

        $this->fixer->configure(['foo' => true]);
    }

    public function testWrongConfigTypeForOperators(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '/^\[unary_operator_spaces\] Invalid configuration: The option "operators" with value true is expected to be of type "array", but is of type "(bool|boolean)"\.$/'
        );

        $this->fixer->configure(['operators' => true]);
    }

    public function testWrongConfigTypeForOperatorsKey(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[unary_operator_spaces\] Invalid configuration: Unexpected "operators" key, expected any of ".*", got "123" of type "int"\.$/');

        $this->fixer->configure(['operators' => [123 => 1]]);
    }

    public function testWrongConfigTypeForOperatorsKeyValue(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[unary_operator_spaces\] Invalid configuration: Unexpected value for operator "\+\+", expected any of ".*", got "\'abc\'" of type "string"\.$/');

        $this->fixer->configure(['operators' => ['++' => 'abc']]);
    }

    /**
     * @dataProvider provideFixDefaultCases
     */
    public function testFixDefault(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array<int, string>>
     */
    public static function provideFixDefaultCases(): iterable
    {
        yield from [
            [
                "<?php \$a= 1;\$a#\n++#\n;#",
            ],
            [
                '<?php $a++;',
                '<?php $a ++;',
            ],
            [
                '<?php $a--;',
                '<?php $a --;',
            ],
            [
                '<?php ++$a;',
                '<?php ++ $a;',
            ],
            [
                '<?php --$a;',
                '<?php -- $a;',
            ],
            [
                '<?php $a = !$b;',
                '<?php $a = ! $b;',
            ],
            [
                '<?php $a = !!$b;',
                '<?php $a = ! ! $b;',
            ],
            [
                '<?php $a = ~$b;',
                '<?php $a = ~ $b;',
            ],
            [
                '<?php $a = &$b;',
                '<?php $a = & $b;',
            ],
            [
                '<?php $a=&$b;',
            ],
            [
                '<?php $a * -$b;',
                '<?php $a * - $b;',
            ],
            [
                '<?php $a *-$b;',
                '<?php $a *- $b;',
            ],
            [
                '<?php $a*-$b;',
            ],
            [
                '<?php $a / -$b;',
                '<?php $a / - $b;',
            ],
            [
                '<?php $a /-$b;',
                '<?php $a /- $b;',
            ],
            [
                '<?php $a/-$b;',
            ],
            [
                '<?php $a ^ -$b;',
                '<?php $a ^ - $b;',
            ],
            [
                '<?php $a ^-$b;',
                '<?php $a ^- $b;',
            ],
            [
                '<?php $a^-$b;',
            ],
            [
                '<?php function &foo(){}',
                '<?php function & foo(){}',
            ],
            [
                '<?php function &foo(){}',
                '<?php function &   foo(){}',
            ],
            [
                '<?php function foo(&$a, array &$b, Bar &$c) {}',
                '<?php function foo(& $a, array & $b, Bar & $c) {}',
            ],
            [
                '<?php function foo($a, ...$b) {}',
                '<?php function foo($a, ... $b) {}',
            ],
            [
                '<?php function foo(&...$a) {}',
                '<?php function foo(& ... $a) {}',
            ],
            [
                '<?php function foo(array ...$a) {}',
            ],
            [
                '<?php foo(...$a);',
                '<?php foo(... $a);',
            ],
            [
                '<?php foo($a, ...$b);',
                '<?php foo($a, ... $b);',
            ],
            [
                '<?php !foo();',
                '<?php ! foo();',
            ],
            [
                '<?php if (!bar()) {}',
                '<?php if (! bar()) {}',
            ],
            [
                '<?php if ( !bar()) {}',
                '<?php if ( ! bar()) {}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixOperatorsAsConfiguredCases
     *
     * @param null|array<string, string> $config
     */
    public function testFixOperatorsAsConfigured(string $expected, ?string $input = null, ?array $config = null): void
    {
        if (null !== $config) {
            $this->fixer->configure(['operators' => $config]);
        }

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array<int, null|array<string, string>|string>>
     */
    public static function provideFixOperatorsAsConfiguredCases(): iterable
    {
        yield from [
            'post-inc, leading' => [
                '<?php $a ++;',
                '<?php $a++;',
                ['++' => 'leading_space'],
            ],
            'post-inc, trailing' => [
                '<?php $a++ ;',
                '<?php $a++;',
                ['++' => 'trailing_space'],
            ],
            'post-inc, leading and trailing' => [
                '<?php $a ++ ;',
                '<?php $a++;',
                ['++' => 'leading_and_trailing_spaces'],
            ],
            'post-inc, no spaces' => [
                '<?php $a++; $b++ ; $c++ ;',
                '<?php $a ++; $b ++ ; $c ++ ;',
            ],
            'post-dec, leading' => [
                '<?php $a --;',
                '<?php $a--;',
                ['--' => 'leading_space'],
            ],
            'post-dec, trailing' => [
                '<?php $a-- ;',
                '<?php $a--;',
                ['--' => 'trailing_space'],
            ],
            'post-dec, leading and trailing' => [
                '<?php $a -- ;',
                '<?php $a--;',
                ['--' => 'leading_and_trailing_spaces'],
            ],
            'post-dec, no spaces' => [
                '<?php $a--; $b-- ; $c-- ;',
                '<?php $a --; $b-- ; $c -- ;',
            ],
            'pre-inc, leading' => [
                '<?php ++$a;',
                null,
                ['++' => 'leading_space'],
            ],
            'pre-inc, trailing' => [
                '<?php ++ $a;',
                '<?php ++$a;',
                ['++' => 'trailing_space'],
            ],
            'pre-inc, leading and trailing' => [
                '<?php ++ $a; ++ $b;',
                '<?php ++$a;++$b;',
                ['++' => 'leading_and_trailing_spaces'],
            ],
            'pre-inc, no spaces' => [
                '<?php ++$a; ++$b;',
                '<?php ++ $a; ++   $b;',
            ],
            'pre-dec, leading' => [
                '<?php --$a;',
                null,
                ['--' => 'leading_space'],
            ],
            'pre-dec, trailing' => [
                '<?php -- $a;',
                '<?php --$a;',
                ['--' => 'trailing_space'],
            ],
            'pre-dec, leading and trailing' => [
                '<?php -- $a;',
                '<?php --$a;',
                ['--' => 'leading_and_trailing_spaces'],
            ],
            'pre-dec, no spaces' => [
                '<?php --$a; --$b;',
                '<?php -- $a; --   $b;',
            ],
            'not operator, leading' => [
                '<?php $i = 0; $i++; $foo = !false || ( !true || ! !false && (2 === (7 - 5)));',
                '<?php $i = 0; $i++; $foo =!false || (!true || !!false && (2 === (7 - 5)));',
                ['!' => 'leading_space'],
            ],
            'not operator, trailing' => [
                '<?php $i = 0; $i++; $foo =! false || (! true || ! ! false && (2 === (7 - 5)));',
                '<?php $i = 0; $i++; $foo =!false || (!true || !!false && (2 === (7 - 5)));',
                ['!' => 'trailing_space'],
            ],
            'not operator, leading and trailing' => [
                '<?php $i = 0; $i++; $foo = ! false || ( ! true || ! ! false && (2 === (7 - 5)));',
                '<?php $i = 0; $i++; $foo =!false || (!true || !!false && (2 === (7 - 5)));',
                ['!' => 'leading_and_trailing_spaces'],
            ],
            'not operator, no spaces' => [
                '<?php $i = 0; $i++; $foo = !false || ( !true || !!false && (2 === (7 - 5)));',
                '<?php $i = 0; $i++; $foo = ! false || ( ! true || ! ! false && (2 === (7 - 5)));',
            ],
            'at operator, leading' => [
                '<?php @trigger_error(\'Foo\', E_USER_DEPRECATED); @mkdir($path); @fopen($pathname, \'rb+\');',
                '<?php @trigger_error(\'Foo\', E_USER_DEPRECATED);@mkdir($path);   @fopen($pathname, \'rb+\');',
                ['@' => 'leading_space'],
            ],
            'at operator, trailing' => [
                '<?php @ trigger_error(\'Foo\', E_USER_DEPRECATED);@ mkdir($path);   @ fopen($pathname, \'rb+\');',
                '<?php @trigger_error(\'Foo\', E_USER_DEPRECATED);@mkdir($path);   @fopen($pathname, \'rb+\');',
                ['@' => 'trailing_space'],
            ],
            'at operator, leading and trailing' => [
                '<?php @ trigger_error(\'Foo\', E_USER_DEPRECATED); @ mkdir($path); @ fopen($pathname, \'rb+\');',
                '<?php @trigger_error(\'Foo\', E_USER_DEPRECATED);@mkdir($path);   @fopen($pathname, \'rb+\');',
                ['@' => 'leading_and_trailing_spaces'],
            ],
            'at operator, no spaces' => [
                '<?php @trigger_error(\'Foo\', E_USER_DEPRECATED); @mkdir($path);   @fopen($pathname, \'rb+\');',
                '<?php @  trigger_error(\'Foo\', E_USER_DEPRECATED); @  mkdir($path);   @     fopen($pathname, \'rb+\');',
            ],
            'tilde, leading' => [
                '<?php $mask = E_ALL & ~E_DEPRECATED; $masks = []; $masks[] = ~$mask;',
                '<?php $mask = E_ALL &   ~E_DEPRECATED; $masks = []; $masks[] =~$mask;',
                ['~' => 'leading_space'],
            ],
            'tilde, trailing' => [
                '<?php $mask = E_ALL &   ~ E_DEPRECATED; $masks = []; $masks[] =~ $mask;',
                '<?php $mask = E_ALL &   ~E_DEPRECATED; $masks = []; $masks[] =~$mask;',
                ['~' => 'trailing_space'],
            ],
            'tilde, leading and trailing' => [
                '<?php $mask = E_ALL & ~ E_DEPRECATED; $masks = []; $masks[] = ~ $mask;',
                '<?php $mask = E_ALL &   ~E_DEPRECATED; $masks = []; $masks[] =~$mask;',
                ['~' => 'leading_and_trailing_spaces'],
            ],
            'tilde, no spaces' => [
                '<?php $mask = E_ALL & ~E_DEPRECATED; $masks = []; $masks[] = ~$mask;',
                '<?php $mask = E_ALL & ~   E_DEPRECATED; $masks = []; $masks[] = ~ $mask;',
            ],
            '&, leading' => [
                '<?php function &   foo(array &$config) {} function bar(int $a, &$b) {}',
                '<?php function&   foo(array&$config) {} function bar(int $a,&$b) {}',
                ['&' => 'leading_space'],
            ],
            '&, trailing' => [
                '<?php function & foo(array & $config) {} function bar(int $a, & $b) {}',
                '<?php function &   foo(array &$config) {} function bar(int $a, &   $b) {}',
                ['&' => 'trailing_space'],
            ],
            '&, leading and trailing' => [
                '<?php function & foo(array & $config) {} function bar(int $a, & $b) {}',
                '<?php function &   foo(array&$config) {} function bar(int $a,    &   $b) {}',
                ['&' => 'leading_and_trailing_spaces'],
            ],
            '&, no spaces' => [
                '<?php function &foo(array &$config) {} function bar(int $a, &$b) {}',
                '<?php function &   foo(array & $config) {} function bar(int $a, &   $b) {}',
            ],
        ];
    }
}
