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

namespace PhpCsFixer\Tests;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tests\Fixtures\Test\AbstractProxyFixerTest\SimpleFixer;
use PhpCsFixer\Tests\Fixtures\Test\AbstractProxyFixerTest\SimpleWhitespacesAwareFixer;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\AbstractProxyFixer
 */
final class AbstractProxyFixerTest extends TestCase
{
    public function testCandidate(): void
    {
        $proxyFixer = $this->buildProxyFixer([new SimpleFixer(true)]);
        static::assertTrue($proxyFixer->isCandidate(new Tokens()));

        $proxyFixer = $this->buildProxyFixer([new SimpleFixer(false)]);
        static::assertFalse($proxyFixer->isCandidate(new Tokens()));

        $proxyFixer = $this->buildProxyFixer([
            new SimpleFixer(false),
            new SimpleFixer(true),
        ]);

        static::assertTrue($proxyFixer->isCandidate(new Tokens()));
    }

    public function testRisky(): void
    {
        $proxyFixer = $this->buildProxyFixer([new SimpleFixer(true, false)]);
        static::assertFalse($proxyFixer->isRisky());

        $proxyFixer = $this->buildProxyFixer([new SimpleFixer(true, true)]);
        static::assertTrue($proxyFixer->isRisky());

        $proxyFixer = $this->buildProxyFixer([
            new SimpleFixer(true, false),
            new SimpleFixer(true, true),
            new SimpleFixer(true, false),
        ]);

        static::assertTrue($proxyFixer->isRisky());
    }

    public function testSupports(): void
    {
        $file = new \SplFileInfo(__FILE__);

        $proxyFixer = $this->buildProxyFixer([new SimpleFixer(true, false, false)]);
        static::assertFalse($proxyFixer->supports($file));

        $proxyFixer = $this->buildProxyFixer([new SimpleFixer(true, true, true)]);
        static::assertTrue($proxyFixer->supports($file));

        $proxyFixer = $this->buildProxyFixer([
            new SimpleFixer(true, false, false),
            new SimpleFixer(true, true, false),
            new SimpleFixer(true, false, true),
        ]);

        static::assertTrue($proxyFixer->supports($file));
    }

    public function testPrioritySingleFixer(): void
    {
        $proxyFixer = $this->buildProxyFixer([new SimpleFixer(true, false, false, 123)]);
        static::assertSame(123, $proxyFixer->getPriority());
    }

    public function testPriorityMultipleFixersNotSet(): void
    {
        $proxyFixer = $this->buildProxyFixer([
            new SimpleFixer(true),
            new SimpleFixer(true, true),
            new SimpleFixer(true, false, true),
        ]);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('You need to override this method to provide the priority of combined fixers.');

        $proxyFixer->getPriority();
    }

    public function testWhitespacesConfig(): void
    {
        $config = new WhitespacesFixerConfig();
        $whitespacesAwareFixer = new SimpleWhitespacesAwareFixer();

        $proxyFixer = $this->buildProxyFixer([
            new SimpleFixer(true, true),
            $whitespacesAwareFixer,
            new SimpleFixer(true, false, true),
        ]);

        $proxyFixer->setWhitespacesConfig($config);

        static::assertSame($config, $whitespacesAwareFixer->getWhitespacesFixerConfig());
    }

    public function testApplyFixInPriorityOrder(): void
    {
        $fixer1 = new SimpleFixer(true, false, true, 1);
        $fixer2 = new SimpleFixer(true, false, true, 10);

        $proxyFixer = $this->buildProxyFixer([$fixer1, $fixer2]);
        $proxyFixer->fix(new \SplFileInfo(__FILE__), Tokens::fromCode('<?php echo 1;'));

        static::assertSame(2, $fixer1->isFixCalled());
        static::assertSame(1, $fixer2->isFixCalled());
    }

    /**
     * @param FixerInterface[] $fixers
     */
    private function buildProxyFixer(array $fixers): AbstractProxyFixer
    {
        return new class($fixers) extends AbstractProxyFixer implements WhitespacesAwareFixerInterface {
            /**
             * @var list<FixerInterface>
             */
            private array $fixers;

            /**
             * @param list<FixerInterface> $fixers
             */
            public function __construct(array $fixers)
            {
                $this->fixers = $fixers;

                parent::__construct();
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \BadMethodCallException('Not implemented.');
            }

            protected function createProxyFixers(): array
            {
                return $this->fixers;
            }
        };
    }
}
