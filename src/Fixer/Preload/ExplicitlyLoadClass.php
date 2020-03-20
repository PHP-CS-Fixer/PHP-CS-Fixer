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

namespace PhpCsFixer\Fixer\Preload;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ExplicitlyLoadClass extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Adds extra `class_exists` to help PHP 7.4 preloading.',
            [
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_CLASS) || $tokens->isTokenKindFound(T_TRAIT);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $candidates = $this->parse($tokens, '__construct');
        $classesNotToLoad = $this->getPreloadedClasses($file, $tokens);

        $classesToLoad = array_diff($candidates, $classesNotToLoad);
        $this->injectClasses($tokens, $classesToLoad);
    }

    /**
     * @param string $functionName
     *
     * @return string[] classes
     */
    private function parse(Tokens $tokens, $functionName)
    {
        $classes = [];
        $index = $this->findFunction($tokens, $functionName);
        // if not public, get the types.
        // TODO there may be other keyword but public/private/protected, eg "static"
        if ('public' !== $tokens[$tokens->getPrevMeaningfulToken($index)]->getContent()) {
            // Get argument types
            $startedParsingArguments = false;
            for ($i = $index; $i < \count($tokens); ++$i) {
                $token = $tokens[$i];
                // Look for when the arguments begin
                if ('(' === $token->getContent()) {
                    $startedParsingArguments = true;

                    continue;
                }

                // If we have not reached the arguments yet
                if (!$startedParsingArguments) {
                    continue;
                }

                // Are all arguments parsed?
                if (')' === $token->getContent()) {
                    break;
                }

                if ($token->isGivenKind(T_STRING) && !$token->isKeyword() && !\in_array($token->getContent(), ['string', 'bool', 'array', 'float', 'int'], true)) {
                    $classes[] = $token->getContent();
                }
            }
        }

        // TODO parse body.

        return $classes;
    }

    /**
     * Get classes that are found by the preloader. Ie classes we shouldn't include in `class_exists`.
     *
     * @return string[]
     */
    private function getPreloadedClasses(\SplFileInfo $file, Tokens $tokens)
    {
        $classes = [];

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_STRING)) {
                continue;
            }

            // TODO rework so it is way better
            if ('class_exists' === $token->getContent()) {
                $nextToken = $tokens[$index + 2];
                $classes[] = $nextToken->getContent();
            }
        }

        return $classes;
    }

    /**
     * Find a function in the tokens.
     *
     * @param string $name
     *
     * @return null|int the index or null. The index is to the "function" token.
     */
    private function findFunction(Tokens $tokens, $name)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $nextTokenIndex = $tokens->getNextMeaningfulToken($index);
            $nextToken = $tokens[$nextTokenIndex];

            if ($nextToken->getContent() !== $name) {
                continue;
            }

            return $index;
        }

        return null;
    }

    private function injectClasses(Tokens $tokens, array $classes)
    {
        $insertAfter = null;
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_CLASS)) {
                continue;
            }

            $insertAfter = $tokens->getPrevMeaningfulToken($index);

            break;
        }

        if (null === $insertAfter) {
            return;
        }

        $newTokens = [];
        foreach ($classes as $class) {
            //$newTokens[] = new Token([T_STRING, 'class_exists('.$class.'::class);'."\n"]);
            $newTokens[] = new Token([T_STRING, 'class_exists']);
            $newTokens[] = new Token('(');
            $newTokens[] = new Token([T_STRING, $class]);
            $newTokens[] = new Token([T_DOUBLE_COLON, '::']);
            $newTokens[] = new Token([CT::T_CLASS_CONSTANT, 'class']);
            $newTokens[] = new Token(')');
            $newTokens[] = new Token(';');
            $newTokens[] = new Token([T_WHITESPACE, "\n"]);
        }

        $tokens->insertAt($insertAfter + 2, $newTokens);
    }
}
