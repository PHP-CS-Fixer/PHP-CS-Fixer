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

use Symfony\CS\FixerInterface;
use Symfony\CS\Tokens;

/**
 * @author Paweł Zaremba <pawzar@gmail.com>
 */
class OrderUseStatementsFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $allLines = explode("\n", $content);
        $tokens   = Tokens::fromCode($content);

        $unorderedLines = $this->findLines($allLines, $tokens);
        if (count($unorderedLines)) {
            $lineOrder = $this->getNewOrder($unorderedLines);

            $idx = 0;
            foreach (array_keys($unorderedLines) as $lineNumber) {
                $allLines[$lineNumber] = $unorderedLines[$lineOrder[$idx++]];
            }

            return implode("\n", $allLines);
        }

        return $content;
    }

    private function findLines($allLines, $tokens)
    {
        $lines = array();
        foreach ($tokens as $index => $token) {
            if (T_USE === $token->id) {
                $nextToken = $tokens->getNextNonWhitespace($index);
                if ($nextToken && $nextToken->id) {
                    $lines[$token->line - 1] = $allLines[$nextToken->line - 1];
                }
            }
        }

        return $lines;
    }

    private function getNewOrder(array $lines)
    {
        $newLines = array_map(function ($str) {
            return trim($str);
        }, $lines);
        asort($newLines);

        $lineOrder = array();
        foreach ($newLines as $k => $v) {
            $lineOrder[] = $k;
        }

        return $lineOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return FixerInterface::CONTRIB_LEVEL;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the MultipleUseStatementFixer
        return -10;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ordered_use';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Ordering use statements.';
    }
}
