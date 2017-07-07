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

namespace PhpCsFixer\Fixer\Strict;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Aidan Woods
 */
final class DeclareStrictTypesPositionFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @internal
     */
    const LINE_NEXT = 'next';

    /**
     * @internal
     */
    const LINE_SAME = 'same';

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Move the strict type declaration to the configured location. Requires PHP >= 7.0.',
            [
                new VersionSpecificCodeSample(
                    '<?php declare(strict_types=1);',
                    new VersionSpecification(70000)
                ),
            ],
            null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must ran before SingleBlankLineBeforeNamespaceFixer, BlankLineAfterOpeningTagFixer and DeclareEqualNormalizeFixer.
        return 2;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return PHP_VERSION_ID >= 70000 && $tokens[0]->isGivenKind(T_OPEN_TAG);
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return false;
    }

    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('relocate_to', 'Whether ``declare(strict_types=1)`` should be placed on "next" or "same" line, after the opening ``<?php`` tag.'))
                ->setAllowedValues([self::LINE_NEXT, self::LINE_SAME])
                ->setDefault(self::LINE_NEXT)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        // check if the declaration is already done
        $searchIndex = $tokens->getNextMeaningfulToken(0);
        $sequence = $this->getDeclareStrictTypeSequence();
        $sequenceLocation = $tokens->findSequence(array_merge($sequence, [new Token(';')]), $searchIndex, null, false);

        if (null === $sequenceLocation) {
            $sequenceLocation = $tokens->findSequence($sequence, $searchIndex, null, false);
        }

        if (null !== $sequenceLocation) {
            $this->fixLocation($tokens, $sequenceLocation);
        }
    }

    /**
     * @return Token[]
     */
    protected function getDeclareStrictTypeSequence()
    {
        static $sequence = null;

        // do not look for open tag, closing semicolon or empty lines;
        // - open tag is tested by isCandidate
        // - semicolon or end tag must be there to be valid PHP
        // - empty tokens and comments are dealt with later
        if (null === $sequence) {
            $sequence = [
                new Token([T_DECLARE, 'declare']),
                new Token('('),
                new Token([T_STRING, 'strict_types']),
                new Token('='),
                new Token([T_LNUMBER, '1']),
                new Token(')'),
            ];
        }

        return $sequence;
    }

    private function insertSequence(Tokens $tokens, array $sequence = null)
    {
        // ensure there is a newline after php open tag
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        $tokens[0] = new Token([$tokens[0]->getId(), rtrim($tokens[0]->getContent()).$lineEnding]);

        if (null === $sequence) {
            $sequence = $this->getDeclareStrictTypeSequence();
            $sequence[] = new Token(';');
        }

        $endIndex = count($sequence);

        $tokens->insertAt(1, $sequence);

        if (!isset($tokens[$endIndex + 1])) {
            return; // no more tokens afters sequence, single_blank_line_at_eof might add a line
        }

        $nextToken = $tokens[$endIndex + 1];
        $nextLine = $nextToken->getContent();
        $trailingContent = ltrim($nextLine);
        $extraWhitespace = substr($nextLine, 0, strlen($nextLine) - strlen($trailingContent));

        $tokens->ensureWhitespaceAtIndex($endIndex + 1, 0, $lineEnding.$extraWhitespace);
    }

    /**
     * @param Tokens            $tokens
     * @param array<int, Token> $sequence
     */
    private function fixLocation(Tokens $tokens, array $sequence)
    {
        reset($sequence);
        $start = key($sequence);
        end($sequence);
        $end = key($sequence);

        $lineEnding = $this->whitespacesConfig->getLineEnding();

        if (1 !== $start) {
            $seq = [];
            for ($i = $start; $i <= $end; ++$i) {
                $seq[$i] = clone $tokens[$i];
                $tokens->clearTokenAndMergeSurroundingWhitespace($i);
            }

            $sequence = $seq;

            $tokens->clearEmptyTokens();

            $this->insertSequence($tokens, array_values(array_filter($sequence, function ($token) {return $token->getContent() !== ''; })));
        }

        $end = self::LINE_NEXT === $this->configuration['relocate_to'] ? $lineEnding : ' ';
        $tokens[0] = new Token([$tokens[0]->getId(), rtrim($tokens[0]->getContent()).$end]);
    }
}
