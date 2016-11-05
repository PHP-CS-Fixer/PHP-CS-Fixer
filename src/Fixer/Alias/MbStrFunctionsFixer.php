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
        'strlen' => array('alternativeName' => 'mb_strlen', 'argumentCount' => array(1)),
        'strpos' => array('alternativeName' => 'mb_strpos', 'argumentCount' => array(2, 3)),
        'strrpos' => array('alternativeName' => 'mb_strrpos', 'argumentCount' => array(2, 3)),
        'substr' => array('alternativeName' => 'mb_substr', 'argumentCount' => array(2, 3)),
        'strtolower' => array('alternativeName' => 'mb_strtolower', 'argumentCount' => array(1)),
        'strtoupper' => array('alternativeName' => 'mb_strtoupper', 'argumentCount' => array(1)),
        'stripos' => array('alternativeName' => 'mb_stripos', 'argumentCount' => array(2, 3)),
        'strripos' => array('alternativeName' => 'mb_strripos', 'argumentCount' => array(2, 3)),
        'strstr' => array('alternativeName' => 'mb_strstr', 'argumentCount' => array(2, 3)),
        'stristr' => array('alternativeName' => 'mb_stristr', 'argumentCount' => array(2, 3)),
        'strrchr' => array('alternativeName' => 'mb_strrchr', 'argumentCount' => array(2)),
        'substr_count' => array('alternativeName' => 'mb_substr_count', 'argumentCount' => array(2, 3, 4)),
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
        foreach (self::$functions as $functionIdentity => $functionReplacement) {
            $currIndex = 0;
            while (null !== $currIndex) {
                // try getting function reference and translate boundaries for humans
                $boundaries = $this->find($functionIdentity, $tokens, $currIndex, $tokens->count() - 1);
                if (null === $boundaries) {
                    // next function search, as current one not found
                    continue 2;
                }

                list($functionName, $openParenthesis, $closeParenthesis) = $boundaries;
                $count = $this->countArguments($tokens, $openParenthesis, $closeParenthesis);
                if (!in_array($count, $functionReplacement['argumentCount'], true)) {
                    continue 2;
                }

                // analysing cursor shift, so nested calls could be processed
                $currIndex = $openParenthesis;

                $tokens[$functionName]->setContent($functionReplacement['alternativeName']);
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
