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

namespace PhpCsFixer\Console\Output\Progress;

use PhpCsFixer\Console\Output\OutputContext;
use PhpCsFixer\FixerFileProcessedEvent;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 *
 * @phpstan-type _StatusMap array<FixerFileProcessedEvent::STATUS_*, array{symbol: string, format: string, description: string}>
 */
abstract class OneCharLegendOutput implements ProgressOutputInterface
{
    /**
     * File statuses map.
     *
     * @var _StatusMap
     */
    private const EVENT_STATUS_MAP = [
        FixerFileProcessedEvent::STATUS_NO_CHANGES => ['symbol' => '.', 'format' => '%s', 'description' => 'no changes'],
        FixerFileProcessedEvent::STATUS_FIXED => ['symbol' => 'F', 'format' => '<fg=green>%s</fg=green>', 'description' => 'fixed'],
        FixerFileProcessedEvent::STATUS_SKIPPED => ['symbol' => 'S', 'format' => '<fg=cyan>%s</fg=cyan>', 'description' => 'skipped (cached or empty file)'],
        FixerFileProcessedEvent::STATUS_INVALID => ['symbol' => 'I', 'format' => '<bg=red>%s</bg=red>', 'description' => 'invalid file syntax (file ignored)'],
        FixerFileProcessedEvent::STATUS_EXCEPTION => ['symbol' => 'E', 'format' => '<bg=red>%s</bg=red>', 'description' => 'error'],
        FixerFileProcessedEvent::STATUS_LINT => ['symbol' => 'E', 'format' => '<bg=red>%s</bg=red>', 'description' => 'error'],
    ];
    private OutputContext $context;

    public function __construct(OutputContext $context)
    {
        $this->context = $context;
    }

    /**
     * This class is not intended to be serialized,
     * and cannot be deserialized (see __wakeup method).
     */
    public function __sleep(): array
    {
        throw new \BadMethodCallException('Cannot serialize '.static::class);
    }

    /**
     * Disable the deserialization of the class to prevent attacker executing
     * code by leveraging the __destruct method.
     *
     * @see https://owasp.org/www-community/vulnerabilities/PHP_Object_Injection
     */
    public function __wakeup(): void
    {
        throw new \BadMethodCallException('Cannot unserialize '.static::class);
    }

    public function printLegend(): void
    {
        $symbols = [];

        foreach ($this->getStatusMap() as $status) {
            $symbol = $status['symbol'];
            if ('' === $symbol || isset($symbols[$symbol])) {
                continue;
            }

            $symbols[$symbol] = \sprintf(
                '%s-%s',
                $this->getOutput()->isDecorated() ? \sprintf($status['format'], $symbol) : $symbol,
                $status['description']
            );
        }

        $this->getOutput()->write(\sprintf("\nLegend: %s\n", implode(', ', $symbols)));
    }

    /**
     * @return _StatusMap
     */
    protected function getStatusMap(): array
    {
        return self::EVENT_STATUS_MAP;
    }

    final protected function getContext(): OutputContext
    {
        return $this->context;
    }

    final protected function getOutput(): OutputInterface
    {
        return $this->context->getOutput();
    }
}
