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
    private $configuration = array(
        'rand' => 'mt_rand',
        'srand' => 'mt_srand',
        'getrandmax' => 'mt_getrandmax',
    );

    /**
     * @param string[]|null $configuration
     */
    public function configure(array $configuration = null)
    {
        static $validFunctions = array('rand', 'srand', 'getrandmax');

        if (null === $configuration) {
            return;
        }

        foreach ($configuration as $pattern => $replacement) {
            if (!in_array($pattern, $validFunctions, true)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('"%s" is not handled by the fixer.', $pattern));
            }

            if (!is_string($replacement)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Expected string got "%s".', is_object($replacement) ? get_class($replacement) : gettype($replacement)));
            }
        }

        $this->configuration = $configuration;
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
        foreach ($this->configuration as $functionIdentity => $newName) {
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
        return 'Replaces rand, srand, getrandmax functions calls with their mt_* analogs.';
    }
}
