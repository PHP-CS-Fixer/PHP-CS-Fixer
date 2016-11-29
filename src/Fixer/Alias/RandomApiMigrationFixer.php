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
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 */
final class RandomApiMigrationFixer extends AbstractFunctionReferenceFixer implements ConfigurableFixerInterface
{
    /**
     * @var array
     */
    private $configuration;

    private static $defaultConfiguration = array(
        'getrandmax' => array('alternativeName' => 'mt_getrandmax', 'argumentCount' => array(0)),
        'mt_rand' => array('alternativeName' => 'mt_rand', 'argumentCount' => array(1, 2)),
        'rand' => array('alternativeName' => 'mt_rand', 'argumentCount' => array(0, 2)),
        'srand' => array('alternativeName' => 'mt_srand', 'argumentCount' => array(0, 1)),
    );

    /**
     * @param string[]|null $configuration
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $this->configuration = self::$defaultConfiguration;

            return;
        }

        foreach ($configuration as $functionName => $replacement) {
            if (!array_key_exists($functionName, self::$defaultConfiguration)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('"%s" is not handled by the fixer.', $functionName));
            }

            if (!is_string($replacement)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Expected string got "%s".', is_object($replacement) ? get_class($replacement) : gettype($replacement)));
            }

            $configuration[$functionName] = array('alternativeName' => $replacement, 'argumentCount' => self::$defaultConfiguration[$functionName]['argumentCount']);
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
        foreach ($this->configuration as $functionIdentity => $functionReplacement) {
            if ($functionIdentity === $functionReplacement['alternativeName']) {
                continue;
            }

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
    protected function getDescription()
    {
        return 'Replaces rand, srand, getrandmax functions calls with their mt_* analogs.';
    }
}
