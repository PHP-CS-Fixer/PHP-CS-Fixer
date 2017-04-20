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

namespace PhpCsFixer\Fixer\ListNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class ListSyntaxFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    private $candidateTokenKind;

    /**
     * Use 'syntax' => 'long'|'short'.
     *
     * @param array<string, string>|null $configuration
     *
     * @throws InvalidFixerConfigurationException
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);

        $this->candidateTokenKind = 'long' === $this->configuration['syntax'] ? CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN : T_LIST;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'List (`array` destructuring) assignment should be declared using the configured syntax. Requires PHP >= 7.1.',
            array(
                new VersionSpecificCodeSample(
                    "<?php\n[\$sample] = \$array;",
                    new VersionSpecification(70100),
                    array('syntax' => 'long')
                ),
                new VersionSpecificCodeSample(
                    "<?php\nlist(\$sample) = \$array;",
                    new VersionSpecification(70100),
                    array('syntax' => 'short')
                ),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before the BinaryOperatorSpacesFixer and TernaryOperatorSpacesFixer.
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return PHP_VERSION_ID >= 70100 && $tokens->isTokenKindFound($this->candidateTokenKind);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            if ($tokens[$index]->isGivenKind($this->candidateTokenKind)) {
                if (T_LIST === $this->candidateTokenKind) {
                    $this->fixToShortSyntax($tokens, $index);
                } else {
                    $this->fixToLongSyntax($tokens, $index);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $syntax = new FixerOptionBuilder('syntax', 'Whether to use the `long` or `short` `list` syntax.');
        $syntax = $syntax
            ->setAllowedValues(array('long', 'short'))
            ->setDefault('long')
            ->getOption()
        ;

        return new FixerConfigurationResolver(array($syntax));
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixToLongSyntax(Tokens $tokens, $index)
    {
        static $typesOfInterest = array(
            array(CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE),
            array(CT::T_ARRAY_SQUARE_BRACE_OPEN),
        );

        $closeIndex = $tokens->getNextTokenOfKind($index, $typesOfInterest);
        if (!$tokens[$closeIndex]->isGivenKind(CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE)) {
            return;
        }

        $tokens->overrideAt($index, '(');
        $tokens->overrideAt($closeIndex, ')');
        $tokens->insertAt($index, new Token(array(T_LIST, 'list')));
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixToShortSyntax(Tokens $tokens, $index)
    {
        $openIndex = $tokens->getNextTokenOfKind($index, array('('));
        $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);

        $tokens->overrideAt($openIndex, array(CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN, '['));
        $tokens->overrideAt($closeIndex, array(CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE, ']'));

        $tokens->clearTokenAndMergeSurroundingWhitespace($index);
    }
}
