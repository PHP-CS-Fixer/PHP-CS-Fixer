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

    public static function provideFixCases(): array
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

    public static function provideFix80Cases(): array
    {
        return [
            ['<?php #[Entity] class MyEntity {}'],
            ['<?php use Doctrine\ORM\Mapping as ORM; #[ORM\Entity] class MyEntity {}'],
            ['<?php use Doctrine\ORM; #[ORM\Mapping\Entity] class MyEntity {}'],
            ['<?php #[Document] class MyDocument {}'],
            ['<?php use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM; #[ODM\Document] class MyEntity {}'],
            ['<?php #[Entity] class MyEntity {}'],
            ['<?php use Doctrine\ORM\Mapping as ORM; #[ORM\entity] class MyEntity {}'],
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

    public static function provideFix82Cases(): array
    {
        return [
            [
                '<?php #[Entity] readonly final class MyEntity {}',
                '<?php #[Entity] readonly class MyEntity {}',
            ],
            [
                '<?php use Doctrine\ORM\Mapping as ORM; #[ORM\Entity] readonly final class MyEntity {}',
                '<?php use Doctrine\ORM\Mapping as ORM; #[ORM\Entity] readonly class MyEntity {}',
            ],
            [
                '<?php use Doctrine\ORM; #[ORM\Mapping\Entity] readonly final class MyEntity {}',
                '<?php use Doctrine\ORM; #[ORM\Mapping\Entity] readonly class MyEntity {}',
            ],
            [
                '<?php #[Document] readonly final class MyDocument {}',
                '<?php #[Document] readonly class MyDocument {}',
            ],
            [
                '<?php use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM; #[ODM\Document] readonly final class MyEntity {}',
                '<?php use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM; #[ODM\Document] readonly class MyEntity {}',
            ],
            [
                '<?php #[Entity] readonly final class MyEntity {}',
                '<?php #[Entity] readonly class MyEntity {}',
            ],
            [
                '<?php use Doctrine\ORM\Mapping as ORM; #[ORM\entity] readonly final class MyEntity {}',
                '<?php use Doctrine\ORM\Mapping as ORM; #[ORM\entity] readonly class MyEntity {}',
            ],
        ];
    }
}
