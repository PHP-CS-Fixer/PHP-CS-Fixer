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

namespace PhpCsFixer\Tests\FixerDefinition;

use PhpCsFixer\FixerDefinition\CodeSampleInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerDefinition\FixerDefinition
 */
final class FixerDefinitionTest extends TestCase
{
    public function testGetSummary(): void
    {
        $definition = new FixerDefinition('Foo', []);

        self::assertSame('Foo', $definition->getSummary());
    }

    public function testGetCodeSamples(): void
    {
        $samples = [
            $this->createCodeSampleDouble(),
            $this->createCodeSampleDouble(),
        ];

        $definition = new FixerDefinition('', $samples);

        self::assertSame($samples, $definition->getCodeSamples());
    }

    public function testGetDescription(): void
    {
        $definition = new FixerDefinition('', []);

        self::assertNull($definition->getDescription());

        $definition = new FixerDefinition('', [], 'Foo');

        self::assertSame('Foo', $definition->getDescription());
    }

    public function testGetRiskyDescription(): void
    {
        $definition = new FixerDefinition('', []);

        self::assertNull($definition->getRiskyDescription());

        $definition = new FixerDefinition('', [], null, 'Foo');

        self::assertSame('Foo', $definition->getRiskyDescription());
    }

    private function createCodeSampleDouble(): CodeSampleInterface
    {
        return new class() implements CodeSampleInterface {
            public function getCode(): string
            {
                throw new \LogicException('Not implemented.');
            }

            public function getConfiguration(): ?array
            {
                throw new \LogicException('Not implemented.');
            }
        };
    }
}
