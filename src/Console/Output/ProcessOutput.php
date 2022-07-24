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

namespace PhpCsFixer\Console\Output;

use PhpCsFixer\FixerFileProcessedEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Output writer to show the process of a FixCommand.
 *
 * @internal
 */
final class ProcessOutput implements ProcessOutputInterface
{
    /**
     * File statuses map.
     */
    private static array $eventStatusMap = [
        FixerFileProcessedEvent::STATUS_NO_CHANGES => ['symbol' => '.', 'format' => '%s', 'description' => 'no changes'],
        FixerFileProcessedEvent::STATUS_FIXED => ['symbol' => 'F', 'format' => '<fg=green>%s</fg=green>', 'description' => 'fixed'],
        FixerFileProcessedEvent::STATUS_SKIPPED => ['symbol' => 'S', 'format' => '<fg=cyan>%s</fg=cyan>', 'description' => 'skipped (cached or empty file)'],
        FixerFileProcessedEvent::STATUS_INVALID => ['symbol' => 'I', 'format' => '<bg=red>%s</bg=red>', 'description' => 'invalid file syntax (file ignored)'],
        FixerFileProcessedEvent::STATUS_EXCEPTION => ['symbol' => 'E', 'format' => '<bg=red>%s</bg=red>', 'description' => 'error'],
        FixerFileProcessedEvent::STATUS_LINT => ['symbol' => 'E', 'format' => '<bg=red>%s</bg=red>', 'description' => 'error'],
    ];

    private OutputInterface $output;

    private EventDispatcherInterface $eventDispatcher;

    private int $files;

    private int $processedFiles = 0;

    /**
     * @var int
     */
    private $symbolsPerLine;

    public function __construct(OutputInterface $output, EventDispatcherInterface $dispatcher, int $width, int $nbFiles)
    {
        $this->output = $output;
        $this->eventDispatcher = $dispatcher;
        $this->eventDispatcher->addListener(FixerFileProcessedEvent::NAME, [$this, 'onFixerFileProcessed']);
        $this->files = $nbFiles;

        // max number of characters per line
        // - total length x 2 (e.g. "  1 / 123" => 6 digits and padding spaces)
        // - 11               (extra spaces, parentheses and percentage characters, e.g. " x / x (100%)")
        $this->symbolsPerLine = max(1, $width - \strlen((string) $this->files) * 2 - 11);
    }

    public function __destruct()
    {
        $this->eventDispatcher->removeListener(FixerFileProcessedEvent::NAME, [$this, 'onFixerFileProcessed']);
    }

    /**
     * This class is not intended to be serialized,
     * and cannot be deserialized (see __wakeup method).
     */
    public function __sleep(): array
    {
        throw new \BadMethodCallException('Cannot serialize '.__CLASS__);
    }

    /**
     * Disable the deserialization of the class to prevent attacker executing
     * code by leveraging the __destruct method.
     *
     * @see https://owasp.org/www-community/vulnerabilities/PHP_Object_Injection
     */
    public function __wakeup(): void
    {
        throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
    }

    public function onFixerFileProcessed(FixerFileProcessedEvent $event): void
    {
        $status = self::$eventStatusMap[$event->getStatus()];
        $this->output->write($this->output->isDecorated() ? sprintf($status['format'], $status['symbol']) : $status['symbol']);

        ++$this->processedFiles;

        $symbolsOnCurrentLine = $this->processedFiles % $this->symbolsPerLine;
        $isLast = $this->processedFiles === $this->files;

        if (0 === $symbolsOnCurrentLine || $isLast) {
            $this->output->write(sprintf(
                '%s %'.\strlen((string) $this->files).'d / %d (%3d%%)',
                $isLast && 0 !== $symbolsOnCurrentLine ? str_repeat(' ', $this->symbolsPerLine - $symbolsOnCurrentLine) : '',
                $this->processedFiles,
                $this->files,
                round($this->processedFiles / $this->files * 100)
            ));

            if (!$isLast) {
                $this->output->writeln('');
            }
        }
    }

    public function printLegend(): void
    {
        $symbols = [];

        foreach (self::$eventStatusMap as $status) {
            $symbol = $status['symbol'];
            if ('' === $symbol || isset($symbols[$symbol])) {
                continue;
            }

            $symbols[$symbol] = sprintf('%s-%s', $this->output->isDecorated() ? sprintf($status['format'], $symbol) : $symbol, $status['description']);
        }

        $this->output->write(sprintf("\nLegend: %s\n", implode(', ', $symbols)));
    }
}
