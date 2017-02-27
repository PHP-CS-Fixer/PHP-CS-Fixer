<?php

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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class ErrorOutputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param Error  $error
     * @param int    $verbosityLevel
     * @param int    $lineNumber
     * @param int    $exceptionLineNumber
     * @param string $process
     *
     * @dataProvider provideTestCases
     */
    public function testErrorOutput(Error $error, $verbosityLevel, $lineNumber, $exceptionLineNumber, $process)
    {
        $source = $error->getSource();

        $output = new StreamOutput(fopen('php://memory', 'w', false));
        $output->setDecorated(false);
        $output->setVerbosity($verbosityLevel);

        $errorOutput = new ErrorOutput($output);
        $errorOutput->listErrors($process, array($error));

        rewind($output->getStream());
        $displayed = stream_get_contents($output->getStream());

        $startWith = sprintf('
Files that were not fixed due to errors reported during %s:
   1) %s',
            $process,
            __FILE__
        );

        if ($verbosityLevel >= OutputInterface::VERBOSITY_VERBOSE) {
            $startWith .= sprintf('
      Details
      Class    %s
      Message  %s
      Code     %d
      File     %s:%s
',
                get_class($source),
                $source->getMessage(),
                $source->getCode(),
                __FILE__,
                $exceptionLineNumber);
        }

        if ($verbosityLevel >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $startWith .= sprintf('      Stack trace
      File      %s:%d
       Method   PhpCsFixer\Tests\Console\Output\ErrorOutputTest->getErrorAndLineNumber
       Method   PhpCsFixer\Tests\Console\Output\ErrorOutputTest->provideTestCases
',
                __FILE__,
                $lineNumber
            );
        }

        $this->assertStringStartsWith($startWith, $displayed);
    }

    public function provideTestCases()
    {
        $lineNumber = __LINE__;
        list($exceptionLineNumber, $error) = $this->getErrorAndLineNumber(); // note: keep call and __LINE__ separated with one line break
        ++$lineNumber;

        return array(
            array($error, OutputInterface::VERBOSITY_NORMAL, $lineNumber, $exceptionLineNumber, 'VN'),
            array($error, OutputInterface::VERBOSITY_VERBOSE, $lineNumber, $exceptionLineNumber, 'VV'),
            array($error, OutputInterface::VERBOSITY_VERY_VERBOSE, $lineNumber, $exceptionLineNumber, 'VVV'),
        );
    }

    private function getErrorAndLineNumber()
    {
        $lineNumber = __LINE__;
        $exception = new \RuntimeException(// note: keep exception constructor and __LINE__ separated with one line break
            'PHPUnit RT',
            888,
            new \InvalidArgumentException('PHPUnit IAE')
        );

        return array($lineNumber + 1, new Error(Error::TYPE_EXCEPTION, __FILE__, $exception));
    }
}
