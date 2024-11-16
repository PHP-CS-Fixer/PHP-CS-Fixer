<?php

declare(strict_types=1);

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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
final class DeclareStrictTypesFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Force strict types declaration in all files.',
            [
                new CodeSample(
                    "<?php\n"
                ),
            ],
            null,
            'Forcing strict types will stop non strict code from working.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before BlankLineAfterOpeningTagFixer, DeclareEqualNormalizeFixer, HeaderCommentFixer.
     */
    public function getPriority(): int
    {
        return 2;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isMonolithicPhp() && !$tokens->isTokenKindFound(T_OPEN_TAG_WITH_ECHO);
    }

    public function isRisky(): bool
    {
        return true;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $openTagIndex = $tokens[0]->isGivenKind(T_INLINE_HTML) ? 1 : 0;

        $sequenceLocation = $tokens->findSequence([[T_DECLARE, 'declare'], '(', [T_STRING, 'strict_types'], '=', [T_LNUMBER], ')'], $openTagIndex, null, false);
        if (null === $sequenceLocation) {
            $this->insertSequence($openTagIndex, $tokens); // declaration not found, insert one

            return;
        }

        $this->fixStrictTypesCasingAndValue($tokens, $sequenceLocation);
    }

    /**
     * @param array<int, Token> $sequence
     */
    private function fixStrictTypesCasingAndValue(Tokens $tokens, array $sequence): void
    {
        /** @var int $index */
        /** @var Token $token */
        foreach ($sequence as $index => $token) {
            if ($token->isGivenKind(T_STRING)) {
                $tokens[$index] = new Token([T_STRING, strtolower($token->getContent())]);

                continue;
            }
            if ($token->isGivenKind(T_LNUMBER)) {
                $tokens[$index] = new Token([T_LNUMBER, '1']);

                break;
            }
        }
    }

    private function insertSequence(int $openTagIndex, Tokens $tokens): void
    {
        $sequence = [
            new Token([T_DECLARE, 'declare']),
            new Token('('),
            new Token([T_STRING, 'strict_types']),
            new Token('='),
            new Token([T_LNUMBER, '1']),
            new Token(')'),
            new Token(';'),
        ];
        $nextIndex = $openTagIndex + \count($sequence) + 1;

        $tokens->insertAt($openTagIndex + 1, $sequence);

        // transform "<?php" or "<?php\n" to "<?php " if needed
        $content = $tokens[$openTagIndex]->getContent();
        if (!str_contains($content, ' ') || str_contains($content, "\n")) {
            $tokens[$openTagIndex] = new Token([$tokens[$openTagIndex]->getId(), trim($tokens[$openTagIndex]->getContent()).' ']);
        }

        if (\count($tokens) === $nextIndex) {
            return; // no more tokens after sequence, single_blank_line_at_eof might add a line
        }

        $lineEnding = $this->whitespacesConfig->getLineEnding();
        if ($tokens[$nextIndex]->isWhitespace()) {
            $content = $tokens[$nextIndex]->getContent();
            $tokens[$nextIndex] = new Token([T_WHITESPACE, $lineEnding.ltrim($content, " \t")]);
        } else {
            $tokens->insertAt($nextIndex, new Token([T_WHITESPACE, $lineEnding]));
        }
    }
}
