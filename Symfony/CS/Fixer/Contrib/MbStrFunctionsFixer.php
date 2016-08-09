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
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class MbStrFunctionsFixer extends AbstractFixer
{
    /**
     * @var array the list of the string-related function names and their mb_ equivalent
     */
    private static $functions = array(
        'strlen' => 'mb_strlen',
        'strpos' => 'mb_strpos',
        'strrpos' => 'mb_strrpos',
        'substr' => 'mb_substr',
        'strtolower' => 'mb_strtolower',
        'strtoupper' => 'mb_strtoupper',
        'stripos' => 'mb_stripos',
        'strripos' => 'mb_strripos',
        'strstr' => 'mb_strstr',
        'stristr' => 'mb_stristr',
        'strrchr' => 'mb_strrchr',
        'substr_count' => 'mb_substr_count',
    );

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $end = $tokens->count() - 1;

        foreach (self::$functions as $originalName => $mbFunctionName) {
            // the sequence is the function name, followed by "(" and a quoted string
            $seq = array(array(T_STRING, $originalName), '(');

            $currIndex = 0;
            while (null !== $currIndex) {
                $match = $tokens->findSequence($seq, $currIndex, $end, false);

                // did we find a match?
                if (null === $match) {
                    break;
                }

                // findSequence also returns the tokens, but we're only interested in the indexes, i.e.:
                // 0 => function name,
                // 1 => bracket "("
                $match = array_keys($match);

                // advance tokenizer cursor
                $currIndex = $match[1];

                // ensure it's a function call (not a method / static call)
                $prev = $tokens->getPrevMeaningfulToken($match[0]);
                if (null === $prev || $tokens[$prev]->isGivenKind(array(T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_NEW))) {
                    continue;
                }
                if ($tokens[$prev]->isGivenKind(T_NS_SEPARATOR)) {
                    $nsPrev = $tokens->getPrevMeaningfulToken($prev);
                    if ($tokens[$nsPrev]->isGivenKind(array(T_STRING, T_NEW))) {
                        continue;
                    }
                }

                // modify function and argument
                $tokens[$match[0]]->setContent($mbFunctionName);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Replace non multibyte-safe functions with corresponding mb function. Warning! This could change code behavior.';
    }
}
