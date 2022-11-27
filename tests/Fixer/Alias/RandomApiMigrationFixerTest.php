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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractFunctionReferenceFixer
 * @covers \PhpCsFixer\Fixer\Alias\RandomApiMigrationFixer
 */
final class RandomApiMigrationFixerTest extends AbstractFixerTestCase
{
    public function testConfigureCheckSearchFunction(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[random_api_migration\] Invalid configuration: Function "is_null" is not handled by the fixer\.$#');

        $this->fixer->configure(['replacements' => ['is_null' => 'random_int']]);
    }

    public function testConfigureCheckReplacementType(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[random_api_migration\] Invalid configuration: Replacement for function "rand" must be a string, "null" given\.$#');

        $this->fixer->configure(['replacements' => ['rand' => null]]);
    }

    public function testConfigure(): void
    {
        $this->fixer->configure(['replacements' => ['rand' => 'random_int']]);

        $reflectionProperty = new \ReflectionProperty($this->fixer, 'configuration');
        $reflectionProperty->setAccessible(true);

        static::assertSame(
            ['replacements' => [
                'rand' => ['alternativeName' => 'random_int', 'argumentCount' => [0, 2]], ],
            ],
            $reflectionProperty->getValue($this->fixer)
        );
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @return array[]
     */
    public static function provideFixCases(): array
    {
        return [
            [
                '<?php random_int(0, getrandmax());',
                '<?php rand();',
                ['replacements' => ['rand' => 'random_int']],
            ],
            [
                '<?php random_int#1
                #2
                (0, getrandmax()#3
                #4
                )#5
                ;',
                '<?php rand#1
                #2
                (#3
                #4
                )#5
                ;',
                ['replacements' => ['rand' => 'random_int']],
            ],
            ['<?php $smth->srand($a);'],
            ['<?php srandSmth($a);'],
            ['<?php smth_srand($a);'],
            ['<?php new srand($a);'],
            ['<?php new Smth\\srand($a);'],
            ['<?php Smth\\srand($a);'],
            ['<?php namespace\\srand($a);'],
            ['<?php Smth::srand($a);'],
            ['<?php new srand\\smth($a);'],
            ['<?php srand::smth($a);'],
            ['<?php srand\\smth($a);'],
            ['<?php "SELECT ... srand(\$a) ...";'],
            ['<?php "SELECT ... SRAND($a) ...";'],
            ["<?php 'test'.'srand' . 'in concatenation';"],
            ['<?php "test" . "srand"."in concatenation";'],
            [
                '<?php
class SrandClass
{
    const srand = 1;
    public function srand($srand)
    {
        if (!defined("srand") || $srand instanceof srand) {
            echo srand;
        }
    }
}

class srand extends SrandClass{
    const srand = "srand";
}
',
            ],
            ['<?php mt_srand($a);', '<?php srand($a);'],
            ['<?php \\mt_srand($a);', '<?php \\srand($a);'],
            ['<?php $a = &mt_srand($a);', '<?php $a = &srand($a);'],
            ['<?php $a = &\\mt_srand($a);', '<?php $a = &\\srand($a);'],
            ['<?php /* foo */ mt_srand /** bar */ ($a);', '<?php /* foo */ srand /** bar */ ($a);'],
            ['<?php a(mt_getrandmax ());', '<?php a(getrandmax ());'],
            ['<?php a(mt_rand());', '<?php a(rand());'],
            ['<?php a(mt_srand());', '<?php a(srand());'],
            ['<?php a(\\mt_srand());', '<?php a(\\srand());'],
            [
                '<?php rand(rand($a));',
                null,
                ['replacements' => ['rand' => 'random_int']],
            ],
            [
                '<?php random_int($d, random_int($a,$b));',
                '<?php rand($d, rand($a,$b));',
                ['replacements' => ['rand' => 'random_int']],
            ],
            [
                '<?php random_int($a, \Other\Scope\mt_rand($a));',
                '<?php rand($a, \Other\Scope\mt_rand($a));',
                ['replacements' => ['rand' => 'random_int']],
            ],
            [
                '<?php $a = random_int(1,2) + random_int(3,4);',
                '<?php $a = rand(1,2) + mt_rand(3,4);',
                ['replacements' => ['rand' => 'random_int', 'mt_rand' => 'random_int']],
            ],
            [
                '<?php
                interface Test
                {
                    public function getrandmax();
                    public function &rand();
                }',
                null,
                ['replacements' => ['rand' => 'random_int']],
            ],
            [
                '<?php rand($d, rand($a,$b));',
                null,
                ['replacements' => ['rand' => 'rand']],
            ],
            [
                '<?php $a = random_int(1,2,) + random_int(3,4,);',
                '<?php $a = rand(1,2,) + mt_rand(3,4,);',
                ['replacements' => ['rand' => 'random_int', 'mt_rand' => 'random_int']],
            ],
            [
                '<?php mt_srand($a,);',
                '<?php srand($a,);',
            ],
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'simple 8.1' => [
            '<?php $f = srand(...);',
        ];
    }
}
