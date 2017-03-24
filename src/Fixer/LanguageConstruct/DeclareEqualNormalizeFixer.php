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

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class DeclareEqualNormalizeFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var array<string, string>
     */
    private static $defaultConfiguration = array('space' => 'none');

    /**
     * @var string
     */
    private $callback;

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $configuration = self::$defaultConfiguration;
        } elseif (
            1 !== count($configuration)
            || !isset($configuration['space'])
            || ('none' !== $configuration['space'] && 'single' !== $configuration['space'])
        ) {
            throw new InvalidFixerConfigurationException($this->getName(), 'Configuration must define "space" being "single" or "none".');
        }

        $this->callback = 'none' === $configuration['space'] ? 'removeWhitespaceAroundToken' : 'ensureWhitespaceAroundToken';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Equal sign in declare statement should be surrounded by spaces or not following configuration.',
            array(new CodeSample("<?php\ndeclare(ticks =  1);")),
            null,
            'Configure `[\'space\' => \'none\']` or `[\'space\' => \'single\']`.',
            self::$defaultConfiguration
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_DECLARE);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $callback = $this->callback;
        for ($index = 0, $count = $tokens->count(); $index < $count - 6; ++$index) {
            if (!$tokens[$index]->isGivenKind(T_DECLARE)) {
                continue;
            }

            while (!$tokens[++$index]->equals('='));

            $this->$callback($tokens, $index);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  of `=` token
     */
    private function ensureWhitespaceAroundToken(Tokens $tokens, $index)
    {
        if ($tokens[$index + 1]->isWhitespace()) {
            $tokens[$index + 1]->setContent(' ');
        } else {
            $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
        }

        if ($tokens[$index - 1]->isWhitespace()) {
            $tokens[$index - 1]->setContent(' ');
        } else {
            $tokens->insertAt($index, new Token(array(T_WHITESPACE, ' ')));
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  of `=` token
     */
    private function removeWhitespaceAroundToken(Tokens $tokens, $index)
    {
        $tokens->removeLeadingWhitespace($index);
        $tokens->removeTrailingWhitespace($index);
    }
}
