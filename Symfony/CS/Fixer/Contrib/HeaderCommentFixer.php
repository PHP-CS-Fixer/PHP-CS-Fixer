<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\ConfigAwareInterface;
use Symfony\CS\ConfigInterface;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class HeaderCommentFixer extends AbstractFixer implements ConfigAwareInterface
{
    private $config;
    private $header;

    public function fix(\SplFileInfo $file, $content)
    {
        $header = $this->getHeader();
        if ('' === $header) {
            throw new \RuntimeException("The config must have a header text set.");
        }

        $tokens = Tokens::fromCode($content);

        if (!count($tokens) || $tokens[0]->getId() !== T_OPEN_TAG || '' === $header) {
            return $content;
        }

        $newContent  = $tokens[0]->getContent();
        $newContent .= PHP_EOL;
        $newContent .= $header;
        $newContent .= PHP_EOL;

        if (null !== $firstNonWhitespace = $tokens->getNextNonWhitespace(0)) {
            $indexStart = $firstNonWhitespace;
            if ($tokens[$firstNonWhitespace]->getId() === T_COMMENT) {
                $indexStart = $tokens->getNextNonWhitespace($firstNonWhitespace);
            }
        }

        if (null !== $indexStart) {
            $newContent .= $tokens->generatePartialCode($indexStart, $tokens->getSize()-1);
        }

        return $newContent;
    }

    public function getDescription()
    {
        return 'Add or replace header comment.';
    }

    public function supports(\SplFileInfo $file)
    {
        if ('php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION)) {
            return true;
        }

        return false;
    }

    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
    }

    private function getHeader()
    {
        if ($this->header === null) {
            $trimmedHeader = trim($this->config->getHeader());
            if (strlen($trimmedHeader) === 0) {
                $this->header = '';
            } else {
                $this->header = $this->encloseTextInComment($this->config->getHeader());
            }
        }

        return $this->header;
    }

    private function encloseTextInComment($header)
    {
        $comment = '/*'.PHP_EOL;
        $lines = explode("\n", str_replace("\r", '', $header));
        foreach ($lines as $line) {
            $comment .= rtrim(' * '.$line).PHP_EOL;
        }
        $comment .= ' */'.PHP_EOL;

        return $comment;
    }
}
