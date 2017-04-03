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

namespace PhpCsFixer\Fixer\ArrayNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;

/**
 * @author Gregor Harlan <gharlan@web.de>
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class ArraySyntaxFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    private $candidateTokenKind;
    private $fixCallback;

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);

        $this->resolveCandidateTokenKind();
        $this->resolveFixCallback();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'PHP arrays should be declared using the configured syntax (requires PHP >= 5.4 for short syntax).',
            array(
                new CodeSample(
                    "<?php\n[1,2];",
                    array('syntax' => 'long')
                ),
                new VersionSpecificCodeSample(
                    "<?php\narray(1,2);",
                    new VersionSpecification(50400),
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
        return $tokens->isTokenKindFound($this->candidateTokenKind);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $callback = $this->fixCallback;
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            if ($tokens[$index]->isGivenKind($this->candidateTokenKind)) {
                $this->$callback($tokens, $index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $syntax = new FixerOptionBuilder('syntax', 'Whether to use the `long` or `short` array syntax.');
        $syntax = $syntax
            ->setAllowedValues(array('long', 'short'))
            ->setNormalizer(function (Options $options, $value) {
                if (PHP_VERSION_ID < 50400 && 'short' === $value) {
                    throw new InvalidOptionsException(sprintf(
                        'Short array syntax is supported from PHP5.4 (your PHP version is %d).',
                        PHP_VERSION_ID
                    ));
                }

                return $value;
            })
            ->setDefault('long')
            ->getOption()
        ;

        return new FixerConfigurationResolver(array($syntax));
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixToLongArraySyntax(Tokens $tokens, $index)
    {
        $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);

        $tokens->overrideAt($index, '(');
        $tokens->overrideAt($closeIndex, ')');

        $tokens->insertAt($index, new Token(array(T_ARRAY, 'array')));
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixToShortArraySyntax(Tokens $tokens, $index)
    {
        $openIndex = $tokens->getNextTokenOfKind($index, array('('));
        $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);

        $tokens->overrideAt($openIndex, array(CT::T_ARRAY_SQUARE_BRACE_OPEN, '['));
        $tokens->overrideAt($closeIndex, array(CT::T_ARRAY_SQUARE_BRACE_CLOSE, ']'));

        $tokens->clearTokenAndMergeSurroundingWhitespace($index);
    }

    private function resolveFixCallback()
    {
        $this->fixCallback = sprintf('fixTo%sArraySyntax', ucfirst($this->configuration['syntax']));
    }

    private function resolveCandidateTokenKind()
    {
        $this->candidateTokenKind = 'long' === $this->configuration['syntax'] ? CT::T_ARRAY_SQUARE_BRACE_OPEN : T_ARRAY;
    }
}
