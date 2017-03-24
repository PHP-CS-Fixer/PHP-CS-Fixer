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
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Gregor Harlan <gharlan@web.de>
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class ArraySyntaxFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var array
     */
    private static $defaultConfiguration = array(
        'syntax' => 'long',
    );

    private $config;
    private $candidateTokenKind;
    private $fixCallback;

    /**
     * Use 'syntax' => 'long'|'short'.
     *
     * @param array<string, string>|null $configuration
     *
     * @throws InvalidFixerConfigurationException
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $this->config = 'long';
            $this->resolveCandidateTokenKind();
            $this->resolveFixCallback();

            return;
        }

        if (!array_key_exists('syntax', $configuration) || !in_array($configuration['syntax'], array('long', 'short'), true)) {
            throw new InvalidFixerConfigurationException(
                $this->getName(),
                sprintf('Configuration must define "syntax" being "short" or "long".')
            );
        }

        $this->config = $configuration['syntax'];
        if ('short' === $this->config && PHP_VERSION_ID < 50400) {
            throw new InvalidFixerConfigurationException($this->getName(), sprintf('Short array syntax is supported from PHP5.4 (your PHP version is %d).', PHP_VERSION_ID));
        }

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
            ),
            null,
            'The following can be configured: `syntax => "long"|"short"`',
            self::$defaultConfiguration
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
        $this->fixCallback = sprintf('fixTo%sArraySyntax', ucfirst($this->config));
    }

    private function resolveCandidateTokenKind()
    {
        $this->candidateTokenKind = 'long' === $this->config ? CT::T_ARRAY_SQUARE_BRACE_OPEN : T_ARRAY;
    }
}
