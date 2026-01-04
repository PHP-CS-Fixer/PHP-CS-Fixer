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

namespace PhpCsFixer\Tests\Console\Output;

use PhpCsFixer\Console\Output\ErrorOutput;
use PhpCsFixer\Error\Error;
use PhpCsFixer\Linter\LintingException;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Output\ErrorOutput
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ErrorOutputTest extends TestCase
{
    /**
     * @param OutputInterface::VERBOSITY_* $verbosityLevel
     *
     * @dataProvider provideErrorOutputCases
     */
    public function testErrorOutput(Error $error, int $verbosityLevel, int $lineNumber, int $exceptionLineNumber, string $process): void
    {
        $source = $error->getSource();

        $output = $this->createStreamOutput($verbosityLevel);

        $errorOutput = new ErrorOutput($output);
        $errorOutput->listErrors($process, [$error]);

        $displayed = $this->readFullStreamOutput($output);

        $startWith = \sprintf(
            '
Files that were not fixed due to errors reported during %s:
   1) %s',
            $process,
            __FILE__,
        );

        if ($verbosityLevel >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $startWith .= \sprintf(
                '

                            '.'
        [%s]  '.'
        %s (%d)    '.'
                            '.'
',
                \get_class($source),
                $source->getMessage(),
                $source->getCode(),
            );
        }

        if ($verbosityLevel >= OutputInterface::VERBOSITY_DEBUG) {
            $startWith .= \sprintf(
                '
      PhpCsFixer\Tests\Console\Output\ErrorOutputTest::getErrorAndLineNumber()
        in %s at line %d
      PhpCsFixer\Tests\Console\Output\ErrorOutputTest::provideErrorOutputCases()
',
                __FILE__,
                $lineNumber,
            );
        }

        self::assertStringStartsWith($startWith, $displayed);
    }

    /**
     * @return iterable<int, array{Error, int, int, int, string}>
     */
    public static function provideErrorOutputCases(): iterable
    {
        $lineNumber = __LINE__;
        [$exceptionLineNumber, $error] = self::getErrorAndLineNumber(); // note: keep call and __LINE__ separated with one line break
        ++$lineNumber;

        yield [$error, OutputInterface::VERBOSITY_NORMAL, $lineNumber, $exceptionLineNumber, 'VN'];

        yield [$error, OutputInterface::VERBOSITY_VERBOSE, $lineNumber, $exceptionLineNumber, 'VV'];

        yield [$error, OutputInterface::VERBOSITY_VERY_VERBOSE, $lineNumber, $exceptionLineNumber, 'VVV'];

        yield [$error, OutputInterface::VERBOSITY_DEBUG, $lineNumber, $exceptionLineNumber, 'DEBUG'];
    }

    public function testLintingExceptionOutputsAppliedFixersAndDiff(): void
    {
        $fixerName = uniqid('braces_');
        $diffSpecificContext = uniqid('added_');
        $diff = <<<EOT
            --- Original
            +++ New
            @@ @@
             same line
            -deleted
            +{$diffSpecificContext}
            EOT;

        $lintError = new Error(Error::TYPE_LINT, __FILE__, new LintingException(), [$fixerName], $diff);

        $noDiffLintFixerName = uniqid('no_diff_');
        $noDiffLintError = new Error(Error::TYPE_LINT, __FILE__, new LintingException(), [$noDiffLintFixerName]);

        $invalidErrorFixerName = uniqid('line_ending_');
        $invalidDiff = uniqid('invalid_diff_');

        $invalidError = new Error(Error::TYPE_INVALID, __FILE__, new LintingException(), [$invalidErrorFixerName], $invalidDiff);

        $output = $this->createStreamOutput(OutputInterface::VERBOSITY_VERY_VERBOSE);

        $errorOutput = new ErrorOutput($output);
        $errorOutput->listErrors(uniqid('process_'), [
            $lintError,
            $noDiffLintError,
            $invalidError,
        ]);

        $displayed = $this->readFullStreamOutput($output);

        self::assertStringContainsString($fixerName, $displayed);
        self::assertStringContainsString($diffSpecificContext, $displayed);

        self::assertStringContainsString($noDiffLintFixerName, $displayed);

        self::assertStringNotContainsString($invalidErrorFixerName, $displayed);
        self::assertStringNotContainsString($invalidDiff, $displayed);
    }

    /**
     * @param OutputInterface::VERBOSITY_* $verbosityLevel
     */
    private function createStreamOutput(int $verbosityLevel): StreamOutput
    {
        $steam = fopen('php://memory', 'w', false);
        \assert(\is_resource($steam));

        $output = new StreamOutput($steam);
        $output->setDecorated(false);
        $output->setVerbosity($verbosityLevel);

        return $output;
    }

    private function readFullStreamOutput(StreamOutput $output): string
    {
        rewind($output->getStream());
        $displayed = stream_get_contents($output->getStream());
        \assert(\is_string($displayed));

        // normalize line breaks,
        // as we output using SF `writeln` we are not sure what line ending has been used as it is
        // based on the platform/console/terminal used
        return str_replace(\PHP_EOL, "\n", $displayed);
    }

    /**
     * @return array{int, Error}
     */
    private static function getErrorAndLineNumber(): array
    {
        $lineNumber = __LINE__;
        $exception = new \RuntimeException(// note: keep exception constructor and __LINE__ separated with one line break
            'PHPUnit RT',
            888,
            new \InvalidArgumentException('PHPUnit IAE'),
        );

        return [$lineNumber + 1, new Error(Error::TYPE_EXCEPTION, __FILE__, $exception)];
    }
}
