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

namespace PhpCsFixer\Fixer\Import;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * Fixer for rules defined in PSR2 ¶3.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class SingleImportPerStatementFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'There MUST be one use keyword per declaration.',
            [
                new CodeSample(
                    '<?php
use Foo, Sample, Sample\Sample as Sample2;
'
                ),
                new CodeSample(
                    '<?php
use Space\Models\ {
    TestModelA,
    TestModelB,
    TestModel,
};
',
                    ['group_to_single_imports' => true]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before MultilineWhitespaceBeforeSemicolonsFixer, NoLeadingImportSlashFixer, NoSinglelineWhitespaceBeforeSemicolonsFixer, NoUnusedImportsFixer, SpaceAfterSemicolonFixer.
     */
    public function getPriority(): int
    {
        return 1;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_USE);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        foreach (array_reverse($tokensAnalyzer->getImportUseIndexes()) as $index) {
            $endIndex = $tokens->getNextTokenOfKind($index, [';', [T_CLOSE_TAG]]);
            $groupClose = $tokens->getPrevMeaningfulToken($endIndex);

            if ($tokens[$groupClose]->isGivenKind(CT::T_GROUP_IMPORT_BRACE_CLOSE)) {
                if ($this->configuration['group_to_single_imports']) {
                    $this->fixGroupUse($tokens, $index, $endIndex);
                }
            } else {
                $this->fixMultipleUse($tokens, $index, $endIndex);
            }
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('group_to_single_imports', 'Whether to change group imports into single imports.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    private function getGroupDeclaration(Tokens $tokens, int $index): array
    {
        $groupPrefix = '';
        $comment = '';
        $groupOpenIndex = null;

        for ($i = $index + 1;; ++$i) {
            if ($tokens[$i]->isGivenKind(CT::T_GROUP_IMPORT_BRACE_OPEN)) {
                $groupOpenIndex = $i;

                break;
            }

            if ($tokens[$i]->isComment()) {
                $comment .= $tokens[$i]->getContent();
                if (!$tokens[$i - 1]->isWhitespace() && !$tokens[$i + 1]->isWhitespace()) {
                    $groupPrefix .= ' ';
                }

                continue;
            }

            if ($tokens[$i]->isWhitespace()) {
                $groupPrefix .= ' ';

                continue;
            }

            $groupPrefix .= $tokens[$i]->getContent();
        }

        return [
            rtrim($groupPrefix),
            $groupOpenIndex,
            $tokens->findBlockEnd(Tokens::BLOCK_TYPE_GROUP_IMPORT_BRACE, $groupOpenIndex),
            $comment,
        ];
    }

    /**
     * @return string[]
     */
    private function getGroupStatements(Tokens $tokens, string $groupPrefix, int $groupOpenIndex, int $groupCloseIndex, string $comment): array
    {
        $statements = [];
        $statement = $groupPrefix;

        for ($i = $groupOpenIndex + 1; $i <= $groupCloseIndex; ++$i) {
            $token = $tokens[$i];

            if ($token->equals(',') && $tokens[$tokens->getNextMeaningfulToken($i)]->isGivenKind(CT::T_GROUP_IMPORT_BRACE_CLOSE)) {
                continue;
            }

            if ($token->equalsAny([',', [CT::T_GROUP_IMPORT_BRACE_CLOSE]])) {
                $statements[] = 'use'.$statement.';';
                $statement = $groupPrefix;

                continue;
            }

            if ($token->isWhitespace()) {
                $j = $tokens->getNextMeaningfulToken($i);

                if ($tokens[$j]->isGivenKind(T_AS)) {
                    $statement .= ' as ';
                    $i += 2;
                } elseif ($tokens[$j]->isGivenKind(CT::T_FUNCTION_IMPORT)) {
                    $statement = ' function'.$statement;
                    $i += 2;
                } elseif ($tokens[$j]->isGivenKind(CT::T_CONST_IMPORT)) {
                    $statement = ' const'.$statement;
                    $i += 2;
                }

                if ($token->isWhitespace(" \t") || !str_starts_with($tokens[$i - 1]->getContent(), '//')) {
                    continue;
                }
            }

            $statement .= $token->getContent();
        }

        if ('' !== $comment) {
            $statements[0] .= ' '.$comment;
        }

        return $statements;
    }

    private function fixGroupUse(Tokens $tokens, int $index, int $endIndex): void
    {
        [$groupPrefix, $groupOpenIndex, $groupCloseIndex, $comment] = $this->getGroupDeclaration($tokens, $index);
        $statements = $this->getGroupStatements($tokens, $groupPrefix, $groupOpenIndex, $groupCloseIndex, $comment);

        if (\count($statements) < 2) {
            return;
        }

        $tokens->clearRange($index, $groupCloseIndex);
        if ($tokens[$endIndex]->equals(';')) {
            $tokens->clearAt($endIndex);
        }

        $ending = $this->whitespacesConfig->getLineEnding();
        $importTokens = Tokens::fromCode('<?php '.implode($ending, $statements));
        $importTokens->clearAt(0);
        $importTokens->clearEmptyTokens();

        $tokens->insertAt($index, $importTokens);
    }

    private function fixMultipleUse(Tokens $tokens, int $index, int $endIndex): void
    {
        $nextTokenIndex = $tokens->getNextMeaningfulToken($index);

        if ($tokens[$nextTokenIndex]->isGivenKind(CT::T_FUNCTION_IMPORT)) {
            $leadingTokens = [
                new Token([CT::T_FUNCTION_IMPORT, 'function']),
                new Token([T_WHITESPACE, ' ']),
            ];
        } elseif ($tokens[$nextTokenIndex]->isGivenKind(CT::T_CONST_IMPORT)) {
            $leadingTokens = [
                new Token([CT::T_CONST_IMPORT, 'const']),
                new Token([T_WHITESPACE, ' ']),
            ];
        } else {
            $leadingTokens = [];
        }

        $ending = $this->whitespacesConfig->getLineEnding();

        for ($i = $endIndex - 1; $i > $index; --$i) {
            if (!$tokens[$i]->equals(',')) {
                continue;
            }

            $tokens[$i] = new Token(';');
            $i = $tokens->getNextMeaningfulToken($i);

            $tokens->insertAt($i, new Token([T_USE, 'use']));
            $tokens->insertAt($i + 1, new Token([T_WHITESPACE, ' ']));

            foreach ($leadingTokens as $offset => $leadingToken) {
                $tokens->insertAt($i + 2 + $offset, clone $leadingTokens[$offset]);
            }

            $indent = WhitespacesAnalyzer::detectIndent($tokens, $index);

            if ($tokens[$i - 1]->isWhitespace()) {
                $tokens[$i - 1] = new Token([T_WHITESPACE, $ending.$indent]);
            } elseif (!str_contains($tokens[$i - 1]->getContent(), "\n")) {
                $tokens->insertAt($i, new Token([T_WHITESPACE, $ending.$indent]));
            }
        }
    }
}
