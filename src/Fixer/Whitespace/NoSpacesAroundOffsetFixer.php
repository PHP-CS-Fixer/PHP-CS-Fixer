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

namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class NoSpacesAroundOffsetFixer extends AbstractFixer
{
    /**
     * @var string[]
     */
    private $configuration = array();

    /**
     * Default target/configuration.
     *
     * @var string[]
     */
    private static $defaultConfiguration = array(
        'inside',
        'outside',
    );

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        if (!$configuration) {
            if (null === $configuration) {
                $this->configuration = self::$defaultConfiguration;

                return;
            }

            throw new InvalidFixerConfigurationException($this->getName(), 'Configuration must be provided.');
        }

        foreach ($configuration as $name) {
            if (!in_array($name, self::$defaultConfiguration, true)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Unknown configuration option "%s".', $name));
            }
        }

        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array('[', CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN));
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->equalsAny(array('[', array(CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN)))) {
                continue;
            }

            if (in_array('inside', $this->configuration, true)) {
                if ($token->equals('[')) {
                    $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE, $index);
                } else {
                    $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE, $index);
                }

                // remove space after opening `[` or `{`
                $this->removeWhitespaceToken($tokens[$index + 1]);

                // remove space before closing `]` or `}`
                $this->removeWhitespaceToken($tokens[$endIndex - 1]);
            }

            if (in_array('outside', $this->configuration, true)) {
                $prevNonWhitespaceIndex = $tokens->getPrevNonWhitespace($index);
                if ($tokens[$prevNonWhitespaceIndex]->isComment()) {
                    continue;
                }

                $tokens->removeLeadingWhitespace($index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There MUST NOT be spaces around offset braces.';
    }

    /**
     * Removes the token if it is single line whitespace.
     *
     * @param Token $token
     */
    private function removeWhitespaceToken(Token $token)
    {
        if ($token->isWhitespace(" \t")) {
            $token->clear();
        }
    }
}
