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

namespace PhpCsFixer\Fixer\Alias;

use PhpCsFixer\AbstractFunctionReferenceFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class MbStrFunctionsFixer extends AbstractFunctionReferenceFixer
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
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_STRING);
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach (self::$functions as $functionIdentity => $newName) {
            $currIndex = 0;
            while (null !== $currIndex) {
                // try getting function reference and translate boundaries for humans
                $boundaries = $this->find($functionIdentity, $tokens, $currIndex, $tokens->count() - 1);
                if (null === $boundaries) {
                    // next function search, as current one not found
                    continue 2;
                }

                list($functionName, $openParenthesis) = $boundaries;

                // analysing cursor shift, so nested calls could be processed
                $currIndex = $openParenthesis;

                $tokens[$functionName]->setContent($newName);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Replace non multibyte-safe functions with corresponding mb function.';
    }
}
