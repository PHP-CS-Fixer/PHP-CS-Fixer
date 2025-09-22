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
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassNotation\FinalClassFixer>
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\FinalClassFixer
 *
 * @author Filippo Tessarotto <zoeslam@gmail.com>
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

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php /** @Entity */ class MyEntity {}'];

        yield ['<?php use Doctrine\ORM\Mapping as ORM; /** @ORM\Entity */ class MyEntity {}'];

        yield ['<?php use Doctrine\ORM\Mapping as ORM; /** @ORM\Entity(repositoryClass="MyRepository") */ class MyEntity {}'];

        yield ['<?php use Doctrine\ORM\Mapping; /** @Mapping\Entity */ class MyEntity {}'];

        yield ['<?php use Doctrine\ORM; /** @ORM\Mapping\Entity */ class MyEntity {}'];

        yield ['<?php /** @Document */ class MyDocument {}'];

        yield ['<?php use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM; /** @ODM\Document */ class MyEntity {}'];

        yield ['<?php /** @entity */ class MyEntity {}'];

        yield ['<?php use Doctrine\ORM\Mapping as ORM; /** @orm\entity */ class MyEntity {}'];

        yield ['<?php abstract class MyAbstract {}'];

        yield ['<?php trait MyTrait {}'];

        yield ['<?php interface MyInterface {}'];

        yield ['<?php echo Exception::class;'];

        yield [
            '<?php final class MyClass {}',
            '<?php class MyClass {}',
        ];

        yield [
            '<?php final class MyClass extends MyAbstract {}',
            '<?php class MyClass extends MyAbstract {}',
        ];

        yield [
            '<?php final class MyClass implements MyInterface {}',
            '<?php class MyClass implements MyInterface {}',
        ];

        yield [
            '<?php /** @codeCoverageIgnore */ final class MyEntity {}',
            '<?php /** @codeCoverageIgnore */ class MyEntity {}',
        ];

        yield [
            '<?php final class A {} abstract class B {} final class C {}',
            '<?php class A {} abstract class B {} class C {}',
        ];

        yield [
            '<?php /** @internal Map my app to an @Entity */ final class MyMapper {}',
            '<?php /** @internal Map my app to an @Entity */ class MyMapper {}',
        ];

        yield ['<?php $anonymClass = new class {};'];
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

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield ['<?php #[Entity] class MyEntity {}'];

        yield ['<?php use Doctrine\ORM\Mapping as ORM; #[ORM\Entity] class MyEntity {}'];

        yield ['<?php use Doctrine\ORM\Mapping as ORM; #[ORM\Entity(repositoryClass:"MyRepository")] class MyEntity {}'];

        yield ['<?php use Doctrine\ORM; #[ORM\Mapping\Entity] class MyEntity {}'];

        yield ['<?php #[Document] class MyDocument {}'];

        yield ['<?php use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM; #[ODM\Document] class MyEntity {}'];

        yield ['<?php use Doctrine\ORM\Mapping as ORM; #[ORM\entity] class MyEntity {}'];

        yield ['<?php #[IgnoredAttribute] #[Entity] class MyEntity {}'];

        yield ['<?php #[IgnoredAttribute("Some-Value"), Entity] class MyEntity {}'];

        // Test with comments in between attributes and class
        yield ['<?php #[Entity] /* some comment */ class MyEntity {}'];

        yield ['<?php use Doctrine\ORM\Mapping as ORM; #[ORM\Entity] /* some comment */ class MyEntity {}'];

        yield ['<?php use Doctrine\ORM; #[ORM\Mapping\Entity] /* some comment */ class MyEntity {}'];

        yield ['<?php #[Document] /* some comment */ class MyDocument {}'];

        yield ['<?php use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM; #[ODM\Document] /* some comment */ class MyEntity {}'];

        yield ['<?php use Doctrine\ORM\Mapping as ORM; #[IgnoredAttribute] #[Entity] /* some comment */ class MyEntity {}'];

        yield ['<?php use Doctrine\ORM\Mapping as ORM; #[IgnoredAttribute("Some-Value"), Entity] /* some comment */ class MyEntity {}'];

        // Test with comments before the class
        yield ['<?php /* some comment */ #[Entity] class MyEntity {}'];

        yield ['<?php /* some comment */ use Doctrine\ORM\Mapping as ORM; #[ORM\Entity] class MyEntity {}'];

        yield ['<?php /* some comment */ use Doctrine\ORM; #[ORM\Mapping\Entity] class MyEntity {}'];

        yield ['<?php /* some comment */ #[Document] class MyDocument {}'];

        yield ['<?php /* some comment */ use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM; #[ODM\Document] class MyEntity {}'];

        yield ['<?php /* some comment */ use Doctrine\ORM\Mapping as ORM; #[ORM\entity] class MyEntity {}'];

        yield ['<?php /* some comment */ use Doctrine\ORM\Mapping as ORM; #[IgnoredAttribute] #[Entity] class MyEntity {}'];

        yield ['<?php /* some comment */ use Doctrine\ORM\Mapping as ORM; #[IgnoredAttribute, Entity] class MyEntity {}'];

        // Multiline tests
        yield [
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
        ];

        yield [
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

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFix82Cases(): iterable
    {
        yield ['<?php #[Entity] readonly class MyEntity {}'];

        yield ['<?php use Doctrine\ORM\Mapping as ORM; #[ORM\Entity] readonly class MyEntity {}'];

        yield ['<?php use Doctrine\ORM; #[ORM\Mapping\Entity] readonly class MyEntity {}'];

        yield ['<?php #[Document] readonly class MyDocument {}'];

        yield ['<?php use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM; #[ODM\Document] readonly class MyEntity {}'];

        yield ['<?php use Doctrine\ORM; #[ORM\Mapping\Entity] readonly /* ... */ class MyEntity {}'];

        yield [
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
        ];
    }
}
