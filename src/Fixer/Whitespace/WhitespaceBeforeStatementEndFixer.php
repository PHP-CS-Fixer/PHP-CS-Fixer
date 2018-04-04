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

namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class WhitespaceBeforeStatementEndFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Forbid multi-line whitespace before a statement end (comma or semicolon) or moves it to the next line for multiline statements.',
            [
                new CodeSample(
                    '<?php
$bar = [
    $foo
        ->bar()
        ->baz(),
];

return $bar
    ->bar()
    ->baz();
'
                ),
                new CodeSample(
                    '<?php
return $foo
    ->bar()
    ->baz()  ;
',
                    ['semicolon_strategy' => 'no_whitespace']
                ),
                new CodeSample(
                    '<?php
return [
    $foo
        ->bar()
        ->baz()  ,
];
',
                    ['comma_strategy' => 'no_whitespace']
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return
            ('none' !== $this->configuration['semicolon_strategy'] && $tokens->isTokenKindFound(';'))
            || ('none' !== $this->configuration['comma_strategy'] && $tokens->isTokenKindFound(','))
        ;
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('semicolon_strategy', 'Strategy to apply to semicolon.'))
                ->setAllowedValues(['none', 'no_whitespace', 'new_line_for_multiline_statement'])
                ->setDefault('new_line_for_multiline_statement')
                ->getOption()
            ,
            (new FixerOptionBuilder('comma_strategy', 'Strategy to apply to comma.'))
                ->setAllowedValues(['none', 'no_whitespace', 'new_line_for_multiline_statement'])
                ->setDefault('new_line_for_multiline_statement')
                ->getOption()
            ,
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $characterStategies = [
            'none' => [],
            'no_whitespace' => [],
            'new_line_for_multiline_statement' => [],
        ];

        foreach ([
            'semicolon_strategy' => ';',
            'comma_strategy' => ',',
        ] as $option => $character) {
            $characterStategies[$this->configuration[$option]][] = $character;
        }

        $this->applyNoWhitespaceFix($tokens, $characterStategies['no_whitespace']);
        $this->applyNewLineForMultilineStatementFix($tokens, $characterStategies['new_line_for_multiline_statement']);
    }

    private function applyNoWhitespaceFix(Tokens $tokens, array $characters): void
    {
        if ([] === $characters) {
            return;
        }

        $lineEnding = $this->whitespacesConfig->getLineEnding();

        foreach ($tokens as $index => $token) {
            if (!$token->equalsAny($characters)) {
                continue;
            }

            if (
                \PHP_VERSION_ID < 70300
                && $token->equals(',')
                && $tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(T_END_HEREDOC)
            ) {
                continue;
            }

            $previousIndex = $index - 1;
            $previous = $tokens[$previousIndex];

            if (!$previous->isWhitespace()) {
                continue;
            }

            $content = $previous->getContent();
            if (str_starts_with($content, $lineEnding) && $tokens[$index - 2]->isComment()) {
                $tokens->ensureWhitespaceAtIndex($previousIndex, 0, $lineEnding);
            } else {
                $tokens->clearAt($previousIndex);
            }
        }
    }

    private function applyNewLineForMultilineStatementFix(Tokens $tokens, array $characters): void
    {
        if ([] === $characters) {
            return;
        }

        $lineEnding = $this->whitespacesConfig->getLineEnding();

        for ($index = \count($tokens) - 1; $index >= 0; --$index) {
            $characterToken = $tokens[$index];

            if (!$characterToken->equalsAny($characters)) {
                continue;
            }

            $indent = $this->findWhitespaceBeforeFirstCall($index - 1, $tokens);

            if (null === $indent) {
                continue;
            }

            $tokens->clearAt($index);
            $tokens->insertAt(
                $this->getNewLineIndex($index, $tokens),
                [
                    new Token([T_WHITESPACE, $lineEnding.$indent]),
                    $characterToken,
                ]
            );
        }
    }

    /**
     * Find the index for the new line. Return the given index when there's no new line.
     */
    private function getNewLineIndex(int $index, Tokens $tokens): int
    {
        for ($count = \count($tokens); $index < $count; ++$index) {
            if (str_contains($tokens[$index]->getContent(), "\n")) {
                return $index;
            }
        }

        return $index;
    }

    /**
     * Checks if the character closes a chained call and returns the whitespace of the first call at $index.
     * i.e. it will return the whitespace marked with '____' in the example underneath.
     *
     * ..
     * ____$this->methodCall()
     *          ->anotherCall();
     * ..
     */
    private function findWhitespaceBeforeFirstCall(int $index, Tokens $tokens): ?string
    {
        // character followed by a closing bracket?
        if (!$tokens[$index]->equals(')')) {
            return null;
        }

        // find opening bracket
        $openingBrackets = 1;
        for (--$index; $index > 0; --$index) {
            if ($tokens[$index]->equals(')')) {
                ++$openingBrackets;

                continue;
            }

            if ($tokens[$index]->equals('(')) {
                if (1 === $openingBrackets) {
                    break;
                }
                --$openingBrackets;
            }
        }

        // method name
        if (!$tokens[--$index]->isGivenKind(T_STRING)) {
            return null;
        }

        // -> or ::
        if (!$tokens[--$index]->isGivenKind([T_OBJECT_OPERATOR, T_DOUBLE_COLON])) {
            return null;
        }

        // white space
        if (!$tokens[--$index]->isGivenKind(T_WHITESPACE)) {
            return null;
        }

        $closingBrackets = 0;
        for ($index; $index >= 0; --$index) {
            if ($tokens[$index]->equals(')')) {
                ++$closingBrackets;
            }

            if ($tokens[$index]->equals('(')) {
                --$closingBrackets;
            }

            // must be the variable of the first call in the chain
            if ($tokens[$index]->isGivenKind([T_VARIABLE, T_RETURN, T_STRING]) && 0 === $closingBrackets) {
                if ($tokens[--$index]->isGivenKind(T_WHITESPACE)
                    || $tokens[$index]->isGivenKind(T_OPEN_TAG)) {
                    return $this->getIndentAt($tokens, $index);
                }
            }
        }

        return null;
    }

    private function getIndentAt(Tokens $tokens, int $index): ?string
    {
        $content = '';
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        // find line ending token
        for (; $index > 0; --$index) {
            if (false !== strstr($tokens[$index]->getContent(), $lineEnding)) {
                break;
            }
        }

        if ($tokens[$index]->isWhitespace()) {
            $content = $tokens[$index]->getContent();
            --$index;
        }

        if ($tokens[$index]->isGivenKind(T_OPEN_TAG)) {
            $content = $tokens[$index]->getContent().$content;
        }

        if (1 === Preg::match('/\R{1}([ \t]*)$/', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
