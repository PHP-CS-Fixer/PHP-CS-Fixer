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

namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author siad007
 */
final class NativeClassCasingFixer extends AbstractFixer
{
    private static $nativeClassNames;

    public function __construct()
    {
        parent::__construct();

        if (null === self::$nativeClassNames) {
            foreach (get_declared_classes() as $class) {
                self::$nativeClassNames[strtolower($class)] = $class;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Classes should be called using the correct casing.',
            [new CodeSample("<?php\nnew STDCLASS();\n")]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_STRING);
    }

    /**
     * @param \SplFileInfo $file
     * @param Tokens       $tokens
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if ($name = $this->nativeClass($index, $token, $tokens)) {
                $tokens[$index] = new Token([T_STRING, self::$nativeClassNames[$name]]);
            }
        }
    }

    /**
     * Get the lower case name of the native class or null.
     *
     * @param $index
     * @param Token  $token
     * @param Tokens $tokens
     *
     * @return bool|string
     */
    private function nativeClass($index, Token $token, Tokens $tokens)
    {
        $beforeClassName = $tokens->getPrevMeaningfulToken($index);
        $lower = strtolower($token->getContent());

        return array_key_exists($lower, self::$nativeClassNames)
            &&
            !$tokens[$beforeClassName]->isGivenKind(
                [
                    T_CLASS,
                    T_AS,
                    T_DOUBLE_COLON,
                    T_OBJECT_OPERATOR,
                    T_FUNCTION,
                    T_CONST,
                    T_TRAIT,
                    T_USE,
                    CT::T_USE_TRAIT,
                ]
            )
            &&
            !(
                $tokens[$beforeClassName]->isGivenKind(T_NS_SEPARATOR)
                &&
                $tokens[$tokens->getPrevMeaningfulToken($beforeClassName)]->isGivenKind([T_STRING])
            )
            ? $lower
            : null;
    }
}
