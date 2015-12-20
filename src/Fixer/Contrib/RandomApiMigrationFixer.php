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

namespace PhpCsFixer\Fixer\Contrib;

use PhpCsFixer\AbstractFunctionReferenceFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tokenizer\Tokens;

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
     * @param string[]|null $customReplacements
     */
    public function configure(array $customReplacements = null)
    {
        if (null !== $customReplacements) {
            foreach ($customReplacements as $pattern => $replacement) {
                if (!array_key_exists($pattern, self::$replacements)) {
                    throw new InvalidFixerConfigurationException($this->getName(), sprintf('"%s" is not handled by the fixer', $pattern));
                }
                if (!is_string($replacement)) {
                    throw new InvalidFixerConfigurationException($this->getName(), sprintf('Expected string got "%s"', gettype($replacement)));
                }

                self::$replacements[$pattern] = $replacement;
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
