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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\FinalInternalClassFixer
 */
final class FinalInternalClassFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected PHP source code
     * @param null|string $input    PHP source code
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        $input = $expected = '<?php ';

        for ($i = 1; $i < 10; ++$i) {
            $input .= sprintf("/** @internal */\nclass class%d\n{\n}\n", $i);
            $expected .= sprintf("/** @internal */\nfinal class class%d\n{\n}\n", $i);
        }

        return [
            [
                $expected,
                $input,
            ],
            [
                '<?php

/** @internal */
final class class1
{
}

interface A {}
trait B{}

/** @internal */
final class class2
{
}
',
                '<?php

/** @internal */
class class1
{
}

interface A {}
trait B{}

/** @internal */
class class2
{
}
',
            ],
            [
                '<?php
/** @internal */
final class class1
{
}

/** @internal */
final class class2
{
}

/**
 * @internal
 * @final
 */
class class3
{
}

/**
 * @internal
 */
abstract class class4 {}
',
                '<?php
/** @internal */
final class class1
{
}

/** @internal */
class class2
{
}

/**
 * @internal
 * @final
 */
class class3
{
}

/**
 * @internal
 */
abstract class class4 {}
',
            ],
            [
                '<?php
                    /**
                     * @ annotation_with_space_after_at_sign
                     */
                    class A {}
',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixWithConfigCases
     */
    public function testFixWithConfig(string $expected, string $input, array $config): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideFixWithConfigCases(): array
    {
        return [
            [
                "<?php\n/** @CUSTOM */final class A{}",
                "<?php\n/** @CUSTOM */class A{}",
                [
                    'annotation_include' => ['@Custom'],
                ],
            ],
            [
                '<?php
/**
 * @CUSTOM
 * @abc
 */
final class A{}

/**
 * @CUSTOM
 */
class B{}
',
                '<?php
/**
 * @CUSTOM
 * @abc
 */
class A{}

/**
 * @CUSTOM
 */
class B{}
',
                [
                    'annotation_include' => ['@Custom', '@abc'],
                ],
            ],
            [
                '<?php
/**
 * @CUSTOM
 * @internal
 */
 final class A{}

/**
 * @CUSTOM
 * @internal
 * @other
 */
 final class B{}

/**
 * @CUSTOM
 * @internal
 * @not-fix
 */
 class C{}
',
                '<?php
/**
 * @CUSTOM
 * @internal
 */
 class A{}

/**
 * @CUSTOM
 * @internal
 * @other
 */
 class B{}

/**
 * @CUSTOM
 * @internal
 * @not-fix
 */
 class C{}
',
                [
                    'annotation_include' => ['@Custom', '@internal'],
                    'annotation_exclude' => ['@not-fix'],
                ],
            ],
            [
                '<?php
/**
 * @internal
 */
final class A{}

/**
 * @abc
 */
class B{}
',
                '<?php
/**
 * @internal
 */
class A{}

/**
 * @abc
 */
class B{}
',
                [
                    'annotation_exclude' => ['abc'],
                ],
            ],
        ];
    }

    /**
     * @param string      $expected PHP source code
     * @param null|string $input    PHP source code
     *
     * @dataProvider provideAnonymousClassesCases
     */
    public function testAnonymousClassesCases(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideAnonymousClassesCases(): iterable
    {
        yield [
            '<?php
/** @internal */
$a = new class (){};',
        ];

        yield [
            '<?php
/** @internal */
$a = new class{};',
        ];
    }

    public function testConfigureSameAnnotationInBothLists(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(
            sprintf('#^%s$#', preg_quote('[final_internal_class] Annotation cannot be used in both the include and exclude list, got duplicates: "internal123".', '#'))
        );

        $this->fixer->configure([
            'annotation_include' => ['@internal123', 'a'],
            'annotation_exclude' => ['@internal123', 'b'],
        ]);
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix80Cases(): iterable
    {
        yield [
            '<?php
#[Internal]
class Foo {}',
        ];
    }
}
