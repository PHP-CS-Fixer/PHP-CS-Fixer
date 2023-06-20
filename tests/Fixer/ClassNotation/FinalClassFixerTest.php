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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\FinalClassFixer
 */
final class FinalClassFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        return [
            ['<?php /** @Entity */ class MyEntity {}'],
            ['<?php use Doctrine\ORM\Mapping as ORM; /** @ORM\Entity */ class MyEntity {}'],
            ['<?php use Doctrine\ORM\Mapping; /** @Mapping\Entity */ class MyEntity {}'],
            ['<?php use Doctrine\ORM; /** @ORM\Mapping\Entity */ class MyEntity {}'],
            ['<?php /** @Document */ class MyDocument {}'],
            ['<?php use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM; /** @ODM\Document */ class MyEntity {}'],
            ['<?php /** @entity */ class MyEntity {}'],
            ['<?php use Doctrine\ORM\Mapping as ORM; /** @orm\entity */ class MyEntity {}'],
            ['<?php abstract class MyAbstract {}'],
            ['<?php trait MyTrait {}'],
            ['<?php interface MyInterface {}'],
            ['<?php echo Exception::class;'],
            [
                '<?php final class MyClass {}',
                '<?php class MyClass {}',
            ],
            [
                '<?php final class MyClass extends MyAbstract {}',
                '<?php class MyClass extends MyAbstract {}',
            ],
            [
                '<?php final class MyClass implements MyInterface {}',
                '<?php class MyClass implements MyInterface {}',
            ],
            [
                '<?php /** @codeCoverageIgnore */ final class MyEntity {}',
                '<?php /** @codeCoverageIgnore */ class MyEntity {}',
            ],
            [
                '<?php final class A {} abstract class B {} final class C {}',
                '<?php class A {} abstract class B {} class C {}',
            ],
            [
                '<?php /** @internal Map my app to an @Entity */ final class MyMapper {}',
                '<?php /** @internal Map my app to an @Entity */ class MyMapper {}',
            ],
            ['<?php $anonymClass = new class {};'],
        ];
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

    public static function provideFix80Cases(): iterable
    {
        return [
            ['<?php #[Entity] class MyEntity {}'],
            ['<?php use Doctrine\ORM\Mapping as ORM; #[ORM\Entity] class MyEntity {}'],
            ['<?php use Doctrine\ORM; #[ORM\Mapping\Entity] class MyEntity {}'],
            ['<?php #[Document] class MyDocument {}'],
            ['<?php use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM; #[ODM\Document] class MyEntity {}'],
            ['<?php #[Entity] class MyEntity {}'],
            ['<?php use Doctrine\ORM\Mapping as ORM; #[ORM\entity] class MyEntity {}'],
            ['<?php #[IgnoredAttribute] #[Entity] class MyEntity {}'],
            ['<?php #[IgnoredAttribute("Some-Value"), Entity] class MyEntity {}'],

            // Test with comments in between attributes and class
            ['<?php #[Entity] /* some comment */ class MyEntity {}'],
            ['<?php use Doctrine\ORM\Mapping as ORM; #[ORM\Entity] /* some comment */ class MyEntity {}'],
            ['<?php use Doctrine\ORM; #[ORM\Mapping\Entity] /* some comment */ class MyEntity {}'],
            ['<?php #[Document] /* some comment */ class MyDocument {}'],
            ['<?php use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM; #[ODM\Document] /* some comment */ class MyEntity {}'],
            ['<?php #[Entity] /* some comment */ class MyEntity {}'],
            ['<?php use Doctrine\ORM\Mapping as ORM; #[ORM\entity] /* some comment */ class MyEntity {}'],
            ['<?php use Doctrine\ORM\Mapping as ORM; #[IgnoredAttribute] #[Entity] /* some comment */ class MyEntity {}'],
            ['<?php use Doctrine\ORM\Mapping as ORM; #[IgnoredAttribute("Some-Value"), Entity] /* some comment */ class MyEntity {}'],

            // Test with comments before the class
            ['<?php /* some comment */ #[Entity] class MyEntity {}'],
            ['<?php /* some comment */ use Doctrine\ORM\Mapping as ORM; #[ORM\Entity] class MyEntity {}'],
            ['<?php /* some comment */ use Doctrine\ORM; #[ORM\Mapping\Entity] class MyEntity {}'],
            ['<?php /* some comment */ #[Document] class MyDocument {}'],
            ['<?php /* some comment */ use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM; #[ODM\Document] class MyEntity {}'],
            ['<?php /* some comment */ #[Entity] class MyEntity {}'],
            ['<?php /* some comment */ use Doctrine\ORM\Mapping as ORM; #[ORM\entity] class MyEntity {}'],
            ['<?php /* some comment */ use Doctrine\ORM\Mapping as ORM; #[IgnoredAttribute] #[Entity] class MyEntity {}'],
            ['<?php /* some comment */ use Doctrine\ORM\Mapping as ORM; #[IgnoredAttribute, Entity] class MyEntity {}'],

            // Multiline tests
            [
                <<<'EOF'
                <?php
                use Doctrine\ORM;
                #[IgnoredAttribute("Some-Value"), IgnoredAttribute("Another-Value")]
                #[ORM\Mapping\Entity]
                /**
                 * multi
                 * line
                 */
                class MyEntity {}
                EOF,
            ],
            [
                <<<'EOF'
                <?php
                use Doctrine\ORM;
                #[
                    IgnoredAttribute("Some-Value"),
                    IgnoredAttribute("Another-Value"),#
                    ORM\Mapping\Entity,
                ]
                /**
                 * multi
                 * line
                 */
                class MyEntity {}
                EOF,
            ],
        ];
    }

    /**
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix82Cases(): iterable
    {
        return [
            ['<?php #[Entity] readonly class MyEntity {}'],
            ['<?php use Doctrine\ORM\Mapping as ORM; #[ORM\Entity] readonly class MyEntity {}'],
            ['<?php use Doctrine\ORM; #[ORM\Mapping\Entity] readonly class MyEntity {}'],
            ['<?php #[Document] readonly class MyDocument {}'],
            ['<?php use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM; #[ODM\Document] readonly class MyEntity {}'],
            ['<?php #[Entity] readonly class MyEntity {}'],
            ['<?php use Doctrine\ORM\Mapping as ORM; #[ORM\entity] readonly class MyEntity {}'],
            ['<?php use Doctrine\ORM; #[ORM\Mapping\Entity] readonly class MyEntity {}'],
            ['<?php use Doctrine\ORM; #[ORM\Mapping\Entity] readonly /* ... */ class MyEntity {}'],
            [
                <<<'EOF'
                <?php
                use Doctrine\ORM;
                #[ORM\Mapping\Entity]
                readonly
                /**
                 * multi
                 * line
                 */
                class MyEntity {}
                EOF,
            ],
        ];
    }
}
