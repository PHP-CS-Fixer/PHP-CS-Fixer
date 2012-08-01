<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;

/**
 * @author Ricard Clau <ricard.clau@gmail.com>
 */
class MethodArgumentsFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $content = $this->fixSingleLineArguments($content);

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        // defined in PSR-2 4.4
        return FixerInterface::PSR2_LEVEL;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the ControlSpacesFixer
        return -20;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'method_arguments';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return <<<'DESC'
In the argument list, there MUST NOT be a space before each comma, and there MUST be one space after each comma.

Argument lists MAY be split across multiple lines, where each subsequent line is indented once. When doing so, the first item in the list MUST be on the next line, and there MUST be only one argument per line.
When the argument list is split across multiple lines, the closing parenthesis and opening brace MUST be placed together on their own line with one space between them.
DESC;
    }

    /**
     * @param string $content
     * @return string
     */
    private function fixSingleLineArguments($content)
    {
        $pattern = '/function[\w \s]+\(.*\)/';

        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[0] as $match) {
                $proper = preg_replace(array('/\s*\(\s*/', '/\s*\)\s*/', '/[\s]*\,[\s]*/'), array('(', ')', ', '), $match);
                $content = str_replace($match, $proper, $content);
            }
        }

        return $content;
    }
}