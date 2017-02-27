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

namespace PhpCsFixer\Console\Output;

use PhpCsFixer\Error\Error;
use PhpCsFixer\Linter\LintingException;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class ErrorOutput
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var bool
     */
    private $isDecorated;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
        $this->isDecorated = $output->isDecorated();
    }

    /**
     * @param string  $process
     * @param Error[] $errors
     */
    public function listErrors($process, array $errors)
    {
        $this->output->writeln(array('', sprintf(
            'Files that were not fixed due to errors reported during %s:',
            $process
        )));

        $showDetails = $this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;
        $showTrace = $this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE;
        foreach ($errors as $i => $error) {
            $this->output->writeln(sprintf('%4d) %s', $i + 1, $error->getFilePath()));
            if ($showDetails && null !== $e = $error->getSource()) {
                $this->output->writeln(array(
                    '      <bg=yellow;fg=black;>Details</>',
                    sprintf('      <comment>Class</comment>    %s', $this->prepareOutput(get_class($e))),
                    sprintf('      <comment>Message</comment>  %s', $this->prepareOutput($e->getMessage())),
                    sprintf('      <comment>Code</comment>     %d', $e->getCode()),
                    sprintf('      <comment>File</comment>     %s:%d', $this->prepareOutput($e->getFile()), $e->getLine()),
                ));

                if ($showTrace && !$e instanceof LintingException) { // stack trace of lint exception is of no interest
                    $this->output->writeln('      <bg=yellow;fg=black;>Stack trace</>');
                    $stackTrace = $e->getTrace();
                    foreach ($stackTrace as $trace) {
                        $this->outputTrace($trace, '      ');
                    }
                }
            }
        }
    }

    /**
     * @param array  $trace
     * @param string $indent
     */
    private function outputTrace(array $trace, $indent)
    {
        if (isset($trace['file'])) {
            $this->output->writeln(sprintf('%s<comment>File</comment>      %s:%d', $indent, $this->prepareOutput($trace['file']), $trace['line']));
        }

        if (isset($trace['class'], $trace['type'], $trace['function'])) {
            $this->output->writeln(sprintf(
                '%s<comment> Method</comment>   %s%s%s',
                $indent,
                $this->prepareOutput($trace['class']),
                $this->prepareOutput($trace['type']),
                $this->prepareOutput($trace['function'])
            ));
        } elseif (isset($trace['function'])) {
            $this->output->writeln(sprintf('%s<comment> Function</comment> %s', $indent, $this->prepareOutput($trace['function'])));
        }
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function prepareOutput($string)
    {
        return $this->isDecorated
            ? OutputFormatter::escape($string)
            : $string
        ;
    }
}
