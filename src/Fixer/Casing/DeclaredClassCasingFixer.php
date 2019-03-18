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
final class DeclaredClassCasingFixer extends AbstractFixer
{
    /**
     * @var string[]
     */
    private static $declaredClassNames;

    public function __construct()
    {
        parent::__construct();

        if (null === self::$declaredClassNames) {
            foreach (get_declared_classes() as $class) {
                self::$declaredClassNames[strtolower($class)] = $class;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Classes should be referred to using the correct casing.',
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
            if ($name = $this->declaredClass($index, $token, $tokens)) {
                $tokens[$index] = new Token([T_STRING, self::$declaredClassNames[$name]]);
            }
        }
    }

    /**
     * Get the lower case name of the declared class or null.
     *
     * @param int    $index
     * @param Token  $token
     * @param Tokens $tokens
     *
     * @return null|string
     */
    private function declaredClass($index, Token $token, Tokens $tokens)
    {
        $beforeClassName = $tokens->getPrevMeaningfulToken($index);
        $lower = strtolower($token->getContent());

        $declaredClass = null;

        if (
            array_key_exists($lower, self::$declaredClassNames)
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
        ) {
            $declaredClass = $lower;
        }

        return $declaredClass;
    }
}
