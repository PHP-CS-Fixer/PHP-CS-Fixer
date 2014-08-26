<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\All;

use Symfony\CS\FixerInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ReturnStatementsFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // [Structure] Add a blank line before return statements
        return preg_replace_callback('/(^.*$)\n+(^ +return\b)/m', function ($match) {
            // don't add it if the previous line is ...
            if (
                preg_match('/\{$/m',                       $match[1]) || // ... ending with an opening brace
                preg_match('/\:$/m',                       $match[1]) || // ... ending with a colon (e.g. a case statement)
                preg_match('%^ *//%m',                     $match[1]) || // ... an inline comment
                preg_match('/^$/m',                        $match[1]) || // ... already blank
                preg_match('/^(?!.*\{) *(if .+|else.*)/m', $match[1])    // ... if/else/else if/elseif without an opening brace
            ) {
                return $match[1]."\n".$match[2];
            }

            return $match[1]."\n\n".$match[2];
        }, $content);
    }

    public function getLevel()
    {
        return FixerInterface::ALL_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'return';
    }

    public function getDescription()
    {
        return 'An empty line feed should precede a return statement.';
    }
}
