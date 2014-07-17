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
use Symfony\CS\Tokens;

/**
 * @author Paweł Zaremba <pawzar@gmail.com>
 */
class OrderUseStatementsFixer implements FixerInterface
{

    public function fix(\SplFileInfo $file, $content)
    {
        $unorderedLines = array();
        $allLines       = explode("\n", $content);

        $allTokens = Tokens::fromCode($content);

        foreach ($allTokens as $key => $token) {
            if ($token->id === T_USE) {
                $nextToken = $allTokens->getNextNonWhitespace($key);
                if ($nextToken && $nextToken->id) {
                    $unorderedLines[$token->line - 1] = $allLines[$nextToken->line - 1];
                }
            }
        }

        $orderedLines = $unorderedLines;
        sort($orderedLines);

        $idx = 0;
        foreach ($unorderedLines as $key => $value) {
            $allLines[$key] = $orderedLines[$idx++];
        }

        return implode("\n", $allLines);
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
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'ordered_use';
    }

    public function getDescription()
    {
        return 'Ordering use statements.';
    }

}
