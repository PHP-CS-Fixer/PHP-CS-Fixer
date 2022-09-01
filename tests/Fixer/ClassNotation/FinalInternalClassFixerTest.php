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
            'fix multiple classes' => [
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
            'indent before `class`' => [
                '<?php /** @internal */
                    final class class1
                    {
                    }',
                '<?php /** @internal */
                    class class1
                    {
                    }',
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

    public function provideFixWithConfigCases(): iterable
    {
        yield [
            "<?php\n/** @CUSTOM */final class A{}",
            "<?php\n/** @CUSTOM */class A{}",
            [
                'include' => ['@Custom'],
            ],
        ];

        yield [
            '<?php
/**
* @CUSTOM
* @abc
*/
final class A2{}

/**
* @CUSTOM
*/
final class B2{}
',
            '<?php
/**
* @CUSTOM
* @abc
*/
class A2{}

/**
* @CUSTOM
*/
class B2{}
',
            [
                'include' => ['@Custom', '@abc'],
            ],
        ];

        yield [
            '<?php
/**
* @CUSTOM
* @internal
*/
final class A3{}

/**
* @CUSTOM
* @internal
* @other
*/
final class B3{}

/**
* @CUSTOM
* @internal
* @not-fix
*/
class C3{}
',
            '<?php
/**
* @CUSTOM
* @internal
*/
class A3{}

/**
* @CUSTOM
* @internal
* @other
*/
class B3{}

/**
* @CUSTOM
* @internal
* @not-fix
*/
class C3{}
',
            [
                'include' => ['@Custom', '@internal'],
                'exclude' => ['@not-fix'],
            ],
        ];

        yield [
            '<?php
/**
* @internal
*/
final class A4{}

/**
* @abc
*/
class B4{}
',
            '<?php
/**
* @internal
*/
class A4{}

/**
* @abc
*/
class B4{}
',
            [
                'exclude' => ['abc'],
            ],
        ];

        yield 'no includes, no excludes, no tags in PHPDoc' => [
            '<?php
/** only description */
final class Foo {}
            ',
            '<?php
/** only description */
class Foo {}
            ',
            [
                'exclude' => [],
                'include' => [],
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

        yield [
            '<?php $object = new /**/ class(){};',
        ];
    }

    public function testConfigureSameAnnotationInBothLists(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(sprintf('#^%s$#', preg_quote('[final_internal_class] Annotation cannot be used in both "include" and "exclude" list, got duplicates: "internal123".', '#')));

        $this->fixer->configure([
            'include' => ['@internal123', 'a'],
            'exclude' => ['@internal123', 'b'],
        ]);
    }

    /**
     * @group legacy
     */
    public function testConfigureBothNewAndOldSet(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(sprintf('#^%s$#', preg_quote('[final_internal_class] Configuration cannot contain deprecated option "annotation_include" and new option "include".', '#')));
        $this->expectDeprecation('Option "annotation_include" for rule "final_internal_class" is deprecated and will be removed in version 4.0. Use "internal" to configure PHPDoc annotations tags and attributes.');

        $this->fixer->configure([
            'annotation_include' => ['@internal', 'a'],
            'include' => ['@internal', 'b'],
        ]);
    }

    /**
     * @param array<string, list<string>> $config
     *
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input, array $config): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideFix80Cases(): iterable
    {
        yield 'multiple attributes, all configured as not to fix' => [
            '<?php
#[X]
#[A]
class Foo {}',
            null,
            ['exclude' => ['a', 'X']],
        ];

        yield 'multiple attributes, one configured as to fix, one as not to fix' => [
            '<?php
#[Internal]
#[A]
class Foo {}',
            null,
            [
                'include' => ['internal'],
                'exclude' => ['A'],
            ],
        ];

        yield 'multiple attributes, one configured as to fix' => [
            '<?php
#[Internal]
#[A]
final class Foo {}',
            '<?php
#[Internal]
#[A]
class Foo {}',
            ['include' => ['internal']],
        ];

        yield 'single attribute configured as to fix' => [
            '<?php
#[Internal]
final class Foo {}',
            '<?php
#[Internal]
class Foo {}',
            ['include' => ['internal']],
        ];

        yield [
            '<?php
#[StandWithUkraine]
class Foo {}',
            null,
            ['consider_absent_docblock_as_internal_class' => true],
        ];

        yield 'mixed bag of cases' => [
            '<?php
#[Entity(repositoryClass: PostRepository::class)]
class User
{}

#[ORM\Entity]
#[Index(name: "category_idx", columns: ["category"])]
final class Article
{}

#[A]
class ArticleB
{}

#[B]
final class Foo {}

#[C]
class FooX {}

$object1 = new #[ExampleAttribute] class(){};
$object2 = new /* */ class(){};
$object3 = new #[B] #[ExampleAttribute] class(){};

/**
 * @B
 */
final class PhpDocClass{}
',
            '<?php
#[Entity(repositoryClass: PostRepository::class)]
class User
{}

#[ORM\Entity]
#[Index(name: "category_idx", columns: ["category"])]
class Article
{}

#[A]
class ArticleB
{}

#[B]
class Foo {}

#[C]
class FooX {}

$object1 = new #[ExampleAttribute] class(){};
$object2 = new /* */ class(){};
$object3 = new #[B] #[ExampleAttribute] class(){};

/**
 * @B
 */
class PhpDocClass{}
',
            [
                'exclude' => ['Entity', 'A'],
                'include' => ['orm\entity', 'B'],
            ],
        ];

        yield 'positive on attribute, but negative on doc' => [
            '<?php
/** @final */
#[A]
class Foo {}',
            null,
            [
                'exclude' => ['final'],
                'include' => ['A'],
            ],
        ];
    }
}
