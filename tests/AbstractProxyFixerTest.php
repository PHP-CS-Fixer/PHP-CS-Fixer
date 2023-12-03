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
use PhpCsFixer\Tests\Double\FixerDoubleFactory;
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
        $proxyFixer = $this->buildProxyFixer([FixerDoubleFactory::createSimple(true)]);
        self::assertTrue($proxyFixer->isCandidate(new Tokens()));

        $proxyFixer = $this->buildProxyFixer([FixerDoubleFactory::createSimple(false)]);
        self::assertFalse($proxyFixer->isCandidate(new Tokens()));

        $proxyFixer = $this->buildProxyFixer([
            FixerDoubleFactory::createSimple(false),
            FixerDoubleFactory::createSimple(true),
        ]);

        self::assertTrue($proxyFixer->isCandidate(new Tokens()));
    }

    public function testRisky(): void
    {
        $proxyFixer = $this->buildProxyFixer([FixerDoubleFactory::createSimple(true, false)]);
        self::assertFalse($proxyFixer->isRisky());

        $proxyFixer = $this->buildProxyFixer([FixerDoubleFactory::createSimple(true, true)]);
        self::assertTrue($proxyFixer->isRisky());

        $proxyFixer = $this->buildProxyFixer([
            FixerDoubleFactory::createSimple(true, false),
            FixerDoubleFactory::createSimple(true, true),
            FixerDoubleFactory::createSimple(true, false),
        ]);

        self::assertTrue($proxyFixer->isRisky());
    }

    public function testSupports(): void
    {
        $file = new \SplFileInfo(__FILE__);

        $proxyFixer = $this->buildProxyFixer([FixerDoubleFactory::createSimple(true, false, false)]);
        self::assertFalse($proxyFixer->supports($file));

        $proxyFixer = $this->buildProxyFixer([FixerDoubleFactory::createSimple(true, true, true)]);
        self::assertTrue($proxyFixer->supports($file));

        $proxyFixer = $this->buildProxyFixer([
            FixerDoubleFactory::createSimple(true, false, false),
            FixerDoubleFactory::createSimple(true, true, false),
            FixerDoubleFactory::createSimple(true, false, true),
        ]);

        self::assertTrue($proxyFixer->supports($file));
    }

    public function testPrioritySingleFixer(): void
    {
        $proxyFixer = $this->buildProxyFixer([FixerDoubleFactory::createSimple(true, false, false, 123)]);
        self::assertSame(123, $proxyFixer->getPriority());
    }

    public function testPriorityMultipleFixersNotSet(): void
    {
        $proxyFixer = $this->buildProxyFixer([
            FixerDoubleFactory::createSimple(true),
            FixerDoubleFactory::createSimple(true, true),
            FixerDoubleFactory::createSimple(true, false, true),
        ]);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('You need to override this method to provide the priority of combined fixers.');

        $proxyFixer->getPriority();
    }

    public function testWhitespacesConfig(): void
    {
        $config = new WhitespacesFixerConfig();
        $whitespacesAwareFixer = FixerDoubleFactory::createWhitespacesAwareFixer();

        $proxyFixer = $this->buildProxyFixer([
            FixerDoubleFactory::createSimple(true, true),
            $whitespacesAwareFixer,
            FixerDoubleFactory::createSimple(true, false, true),
        ]);

        $proxyFixer->setWhitespacesConfig($config);

        self::assertSame($config, $whitespacesAwareFixer->extraBehavior());
    }

    public function testApplyFixInPriorityOrder(): void
    {
        $fixer1 = FixerDoubleFactory::createSimple(true, false, true, 1);
        $fixer2 = FixerDoubleFactory::createSimple(true, false, true, 10);

        $proxyFixer = $this->buildProxyFixer([$fixer1, $fixer2]);
        $proxyFixer->fix(new \SplFileInfo(__FILE__), Tokens::fromCode('<?php echo 1;'));

        self::assertSame(2, $fixer1->extraBehavior());
        self::assertSame(1, $fixer2->extraBehavior());
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
