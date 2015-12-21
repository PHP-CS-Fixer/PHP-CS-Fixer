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

use Symfony\CS\AbstractFunctionReferenceFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 */
final class RandomApiMigrationFixer extends AbstractFunctionReferenceFixer
{
    /**
     * @var string[]
     */
    private static $replacements = array(
        'rand' => 'mt_rand',
        'srand' => 'mt_srand',
        'getrandmax' => 'mt_getrandmax',
    );

    /**
     * Static analog of 'public function configure(array $configuration = null)',
     * which can not be overridden in favor of static.
     *
     * @param string[]|null $customReplacements
     */
    public static function configureReplacement(array $customReplacements = null)
    {
        if (null !== $customReplacements) {
            foreach (self::$replacements as $pattern => &$replacement) {
                if (array_key_exists($pattern, $customReplacements) && is_string($customReplacements[$pattern])) {
                    $replacement = $customReplacements[$pattern];
                }
            }
        }
    }

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
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach (self::$replacements as $functionIdentity => $newName) {
            $isFunctionDefinedInScope = $this->isDefinedInScope($tokens, $functionIdentity);

            $currIndex = 0;
            while (null !== $currIndex) {
                // try getting function reference and translate boundaries for humans
                $boundaries = $this->find($functionIdentity, $tokens, $currIndex, $tokens->count() - 1);
                if (null === $boundaries) {
                    // next function search, as current one not found
                    continue 2;
                }
                list($functionName, $openParenthesis, $closeParenthesis) = $boundaries;

                // analysing cursor shift, so nested calls could be processed
                $currIndex = $openParenthesis;

                // analyse namespace specification (root one or none) and decide what to do
                $prevTokenIndex = $tokens->getPrevMeaningfulToken($functionName);
                if ($tokens[$prevTokenIndex]->isGivenKind(T_NS_SEPARATOR)) {
                    // do nothing with namespace identity
                } elseif ($isFunctionDefinedInScope) {
                    // skip analysis if function is defined in the scope, so this is a referenced call
                    continue;
                }

                $tokens[$functionName]->setContent($newName);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Replaces rand, srand, getrandmax functions calls with their mt_* analogs.';
    }
}
