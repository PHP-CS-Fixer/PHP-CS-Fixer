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
     * @param array<string, string> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        $input = $expected = '<?php ';

        for ($i = 1; $i < 10; ++$i) {
            $input .= sprintf("/** @internal */\nclass class%d\n{\n}\n", $i);
            $expected .= sprintf("/** @internal */\nfinal class class%d\n{\n}\n", $i);
        }

        yield 'fix multiple classes' => [
            $expected,
            $input,
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php
                    /**
                     * @ annotation_with_space_after_at_sign
                     */
                    class A {}
',
        ];

        yield 'indent before `class`' => [
            '<?php /** @internal */
                    final class class1
                    {
                    }',
            '<?php /** @internal */
                    class class1
                    {
                    }',
        ];

        yield 'multiple classes, first with internal annotation and second without internal annotation' => [
            '<?php

/** @internal */
final class Foo {}

class Bar {}
',
            '<?php

/** @internal */
class Foo {}

class Bar {}
',
        ];

        yield 'multiple classes, first without internal annotation and second with internal annotation' => [
            '<?php

class Foo {}

/** @internal */
final class Bar {}
',
            '<?php

class Foo {}

/** @internal */
class Bar {}
',
        ];

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
final class A{}

/**
 * @CUSTOM
 */
final class B{}
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
                'include' => ['@Custom', '@abc'],
            ],
        ];

        yield [
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
                'include' => ['@Custom', '@internal'],
                'exclude' => ['@not-fix'],
            ],
        ];

        yield [
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
                'exclude' => ['abc'],
            ],
        ];

        yield [
            '<?php final class A{}',
            '<?php class A{}',
            ['consider_absent_docblock_as_internal_class' => true],
        ];

        yield 'class with annotation with matching include and partial matching exclude' => [
            '<?php

/** @HelloWorld */
final class Foo {}
',
            '<?php

/** @HelloWorld */
class Foo {}
',
            [
                'include' => ['HelloWorld'],
                'exclude' => ['Hello'],
            ],
        ];

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

    /**
     * @group legacy
     *
     * @param array<string, mixed> $config
     *
     * @dataProvider provideInvalidConfigurationCases
     */
    public function testInvalidConfiguration(array $config, string $exceptionExpression, ?string $deprecationMessage = null): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches($exceptionExpression);
        if (null !== $deprecationMessage) {
            $this->expectDeprecation($deprecationMessage);
        }

        $this->fixer->configure($config);
    }

    /**
     * @return iterable<array{array<string, mixed>, string, 2?: string}>
     */
    public static function provideInvalidConfigurationCases(): iterable
    {
        yield 'same annotation in both lists' => [
            [
                'include' => ['@internal123', 'a'],
                'exclude' => ['@internal123', 'b'],
            ],
            sprintf('#^%s$#', preg_quote('[final_internal_class] Annotation cannot be used in both "include" and "exclude" list, got duplicates: "internal123".', '#')),
        ];

        yield 'both new and old include set' => [
            [
                'annotation_include' => ['@internal', 'a'],
                'include' => ['@internal', 'b'],
            ],
            sprintf('#^%s$#', preg_quote('[final_internal_class] Configuration cannot contain deprecated option "annotation_include" and new option "include".', '#')),
            'Option "annotation_include" for rule "final_internal_class" is deprecated and will be removed in version 4.0. Use "include" to configure PHPDoc annotations tags and attributes.',
        ];

        yield 'both new and old exclude set' => [
            [
                'annotation_exclude' => ['@internal', 'a'],
                'exclude' => ['@internal', 'b'],
            ],
            sprintf('#^%s$#', preg_quote('[final_internal_class] Configuration cannot contain deprecated option "annotation_exclude" and new option "exclude".', '#')),
            'Option "annotation_exclude" for rule "final_internal_class" is deprecated and will be removed in version 4.0. Use "exclude" to configure PHPDoc annotations tags and attributes.',
        ];
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

    /**
     * @return iterable<int|string, array{0: string, 1: null|string, 2: array{consider_absent_docblock_as_internal_class? : bool, exclude?: list<string>, include?: list<string>}}>
     */
    public static function provideFix80Cases(): iterable
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

        yield 'class that should be ignored as it has an attribute not included with absent docblock as true' => [
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

        yield 'multiple classes, first configured with attribute, second without attribute' => [
            '<?php
#[Internal]
final class Foo {}

class Bar {}',
            '<?php
#[Internal]
class Foo {}

class Bar {}',
            ['include' => ['internal']],
        ];

        yield 'multiple classes, first configured without attribute, second with attribute' => [
            '<?php
class Foo {}

#[Internal]
final class Bar {}',
            '<?php
class Foo {}

#[Internal]
class Bar {}',
            ['include' => ['internal']],
        ];

        yield 'include by attribute, but exclude by doc' => [
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

        yield 'include by phpDoc, but exclude by attribute' => [
            '<?php
/** @a */
#[Internal]
class Foo {}',
            null,
            [
                'exclude' => ['Internal'],
                'include' => ['A'],
            ],
        ];

        yield 'comment between attributes' => [
            '<?php
#[A]
/**
 * @B
 */
#[C]
final class Foo {}',
            '<?php
#[A]
/**
 * @B
 */
#[C]
class Foo {}',
            [
                'include' => ['A', 'C'],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input, array $config): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int|string, array{0: string, 1: null|string, 2: array{consider_absent_docblock_as_internal_class? : bool, exclude?: list<string>, include?: list<string>}}>
     */
    public static function provideFix82Cases(): iterable
    {
        yield 'readonly with enabled `consider_absent_docblock_as_internal_class`' => [
            '<?php readonly final class A{}',
            '<?php readonly class A{}',
            ['consider_absent_docblock_as_internal_class' => true],
        ];

        yield 'readonly with `internal` attribute and comment in-between' => [
            '<?php #[Internal] readonly /* comment */ final class A{}',
            '<?php #[Internal] readonly /* comment */ class A{}',
            ['consider_absent_docblock_as_internal_class' => true],
        ];
    }
}
