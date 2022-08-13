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
use PhpCsFixer\Fixer\Indentation;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\AlternativeSyntaxAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class StatementIndentationFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    use Indentation;

    private AlternativeSyntaxAnalyzer $alternativeSyntaxAnalyzer;

    private bool $bracesFixerCompatibility;

    public function __construct(bool $bracesFixerCompatibility = false)
    {
        parent::__construct();

        $this->bracesFixerCompatibility = $bracesFixerCompatibility;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Each statement must be indented.',
            [
                new CodeSample(
                    '<?php
if ($baz == true) {
  echo "foo";
}
else {
      echo "bar";
}
'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before HeredocIndentationFixer.
     * Must run after ClassAttributesSeparationFixer.
     */
    public function getPriority(): int
    {
        return parent::getPriority();
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->alternativeSyntaxAnalyzer = new AlternativeSyntaxAnalyzer();

        $blockSignatureFirstTokens = [
            T_USE,
            T_IF,
            T_ELSE,
            T_ELSEIF,
            T_FOR,
            T_FOREACH,
            T_WHILE,
            T_SWITCH,
            T_CASE,
            T_DEFAULT,
            T_TRY,
            T_FUNCTION,
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
            T_EXTENDS,
            T_IMPLEMENTS,
        ];
        if (\defined('T_MATCH')) { // @TODO: drop condition when PHP 8.0+ is required
            $blockSignatureFirstTokens[] = T_MATCH;
        }

        $blockFirstTokens = ['{', [CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN], [CT::T_USE_TRAIT], [CT::T_GROUP_IMPORT_BRACE_OPEN]];
        if (\defined('T_ATTRIBUTE')) { // @TODO: drop condition when PHP 8.0+ is required
            $blockFirstTokens[] = [T_ATTRIBUTE];
        }

        $endIndex = \count($tokens) - 1;
        if ($tokens[$endIndex]->isWhitespace()) {
            --$endIndex;
        }

        $lastIndent = $this->getLineIndentationWithBracesCompatibility(
            $tokens,
            0,
            $this->extractIndent($this->computeNewLineContent($tokens, 0)),
        );

        /**
         * @var list<array{
         *     type: 'block'|'block_signature'|'statement',
         *     skip: bool,
         *     end_index: int,
         *     end_index_inclusive: bool,
         *     initial_indent: string,
         *     is_indented_block: bool,
         * }> $scopes
         */
        $scopes = [
            [
                'type' => 'block',
                'skip' => false,
                'end_index' => $endIndex,
                'end_index_inclusive' => true,
                'initial_indent' => $lastIndent,
                'is_indented_block' => false,
            ],
        ];

        $previousLineInitialIndent = '';
        $previousLineNewIndent = '';
        $alternativeBlockStarts = [];
        $caseBlockStarts = [];

        foreach ($tokens as $index => $token) {
            $currentScope = \count($scopes) - 1;

            if (
                $token->equalsAny($blockFirstTokens)
                || ($token->equals('(') && !$tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(T_ARRAY))
                || isset($alternativeBlockStarts[$index])
                || isset($caseBlockStarts[$index])
            ) {
                $endIndexInclusive = true;

                if ($token->isGivenKind([T_EXTENDS, T_IMPLEMENTS])) {
                    $endIndex = $tokens->getNextTokenOfKind($index, ['{']);
                } elseif ($token->isGivenKind(CT::T_USE_TRAIT)) {
                    $endIndex = $tokens->getNextTokenOfKind($index, [';']);
                } elseif ($token->equals(':')) {
                    if (isset($caseBlockStarts[$index])) {
                        [$endIndex, $endIndexInclusive] = $this->findCaseBlockEnd($tokens, $index);
                    } else {
                        $endIndex = $this->alternativeSyntaxAnalyzer->findAlternativeSyntaxBlockEnd($tokens, $alternativeBlockStarts[$index]);
                    }
                } elseif ($token->isGivenKind(CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN)) {
                    $endIndex = $tokens->getNextTokenOfKind($index, [[CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE]]);
                } elseif ($token->isGivenKind(CT::T_GROUP_IMPORT_BRACE_OPEN)) {
                    $endIndex = $tokens->getNextTokenOfKind($index, [[CT::T_GROUP_IMPORT_BRACE_CLOSE]]);
                } elseif ($token->equals('{')) {
                    $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
                } elseif ($token->equals('(')) {
                    $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                } else {
                    $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ATTRIBUTE, $index);
                }

                if ('block_signature' === $scopes[$currentScope]['type']) {
                    $initialIndent = $scopes[$currentScope]['initial_indent'];
                } else {
                    $initialIndent = $this->getLineIndentationWithBracesCompatibility($tokens, $index, $lastIndent);
                }

                $skip = false;
                if ($this->bracesFixerCompatibility) {
                    $prevIndex = $tokens->getPrevMeaningfulToken($index);
                    if (null !== $prevIndex) {
                        $prevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
                    }
                    if (null !== $prevIndex && $tokens[$prevIndex]->isGivenKind([T_FUNCTION, T_FN])) {
                        $skip = true;
                    }
                }

                $scopes[] = [
                    'type' => 'block',
                    'skip' => $skip,
                    'end_index' => $endIndex,
                    'end_index_inclusive' => $endIndexInclusive,
                    'initial_indent' => $initialIndent,
                    'is_indented_block' => true,
                ];
                ++$currentScope;

                while ($index >= $scopes[$currentScope]['end_index']) {
                    array_pop($scopes);

                    --$currentScope;
                }

                continue;
            }

            if ($token->isGivenKind($blockSignatureFirstTokens)) {
                for ($endIndex = $index + 1, $max = \count($tokens); $endIndex < $max; ++$endIndex) {
                    if ($tokens[$endIndex]->equals('(')) {
                        $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $endIndex);

                        continue;
                    }

                    if ($tokens[$endIndex]->equalsAny(['{', ';', [T_DOUBLE_ARROW], [T_IMPLEMENTS]])) {
                        break;
                    }

                    if ($tokens[$endIndex]->equals(':')) {
                        if ($token->isGivenKind([T_CASE, T_DEFAULT])) {
                            $caseBlockStarts[$endIndex] = $index;
                        } else {
                            $alternativeBlockStarts[$endIndex] = $index;
                        }

                        break;
                    }
                }

                $scopes[] = [
                    'type' => 'block_signature',
                    'skip' => false,
                    'end_index' => $endIndex,
                    'end_index_inclusive' => true,
                    'initial_indent' => $this->getLineIndentationWithBracesCompatibility($tokens, $index, $lastIndent),
                    'is_indented_block' => $token->isGivenKind([T_EXTENDS, T_IMPLEMENTS]),
                ];

                continue;
            }

            if (
                $token->isWhitespace()
                || ($index > 0 && $tokens[$index - 1]->isGivenKind(T_OPEN_TAG))
            ) {
                $previousOpenTagContent = $tokens[$index - 1]->isGivenKind(T_OPEN_TAG)
                    ? Preg::replace('/\S/', '', $tokens[$index - 1]->getContent())
                    : ''
                ;

                $content = $previousOpenTagContent.($token->isWhitespace() ? $token->getContent() : '');

                if (!Preg::match('/\R/', $content)) {
                    continue;
                }

                $nextToken = $tokens[$index + 1] ?? null;

                if (
                    $this->bracesFixerCompatibility
                    && null !== $nextToken
                    && $nextToken->isComment()
                    && !$this->isCommentWithFixableIndentation($tokens, $index + 1)
                ) {
                    continue;
                }

                if ('block' === $scopes[$currentScope]['type'] || 'block_signature' === $scopes[$currentScope]['type']) {
                    $indent = false;

                    if ($scopes[$currentScope]['is_indented_block']) {
                        $firstNonWhitespaceTokenIndex = null;
                        $nextNewlineIndex = null;
                        for ($searchIndex = $index + 1, $max = \count($tokens); $searchIndex < $max; ++$searchIndex) {
                            $searchToken = $tokens[$searchIndex];

                            if (!$searchToken->isWhitespace()) {
                                if (null === $firstNonWhitespaceTokenIndex) {
                                    $firstNonWhitespaceTokenIndex = $searchIndex;
                                }

                                continue;
                            }

                            if (Preg::match('/\R/', $searchToken->getContent())) {
                                $nextNewlineIndex = $searchIndex;

                                break;
                            }
                        }

                        if (!$this->isCommentForControlSructureContinuation($tokens, $index + 1)) {
                            $endIndex = $scopes[$currentScope]['end_index'];

                            if (!$scopes[$currentScope]['end_index_inclusive']) {
                                ++$endIndex;
                            }

                            if (
                                (null !== $firstNonWhitespaceTokenIndex && $firstNonWhitespaceTokenIndex < $endIndex)
                                || (null !== $nextNewlineIndex && $nextNewlineIndex < $endIndex)
                            ) {
                                $indent = true;
                            }
                        }
                    }

                    $previousLineInitialIndent = $this->extractIndent($content);

                    if ($scopes[$currentScope]['skip']) {
                        $whitespaces = $previousLineInitialIndent;
                    } else {
                        $whitespaces = $scopes[$currentScope]['initial_indent'].($indent ? $this->whitespacesConfig->getIndent() : '');
                    }

                    $content = Preg::replace(
                        '/(\R+)\h*$/',
                        '$1'.$whitespaces,
                        $content
                    );

                    $previousLineNewIndent = $this->extractIndent($content);
                } else {
                    $content = Preg::replace(
                        '/(\R)'.$scopes[$currentScope]['initial_indent'].'(\h*)$/D',
                        '$1'.$scopes[$currentScope]['new_indent'].'$2',
                        $content
                    );
                }

                $lastIndent = $this->extractIndent($content);

                if ('' !== $previousOpenTagContent) {
                    $content = Preg::replace("/^{$previousOpenTagContent}/", '', $content);
                }

                if ('' !== $content) {
                    $tokens->ensureWhitespaceAtIndex($index, 0, $content);
                } elseif ($token->isWhitespace()) {
                    $tokens->clearAt($index);
                }

                if (null !== $nextToken && $nextToken->isComment()) {
                    $tokens[$index + 1] = new Token([
                        $nextToken->getId(),
                        Preg::replace(
                            '/(\R)'.preg_quote($previousLineInitialIndent, '/').'(\h*\S+.*)/',
                            '$1'.$previousLineNewIndent.'$2',
                            $nextToken->getContent()
                        ),
                    ]);
                }

                if ($token->isWhitespace()) {
                    continue;
                }
            }

            if ($this->isNewLineToken($tokens, $index)) {
                $lastIndent = $this->extractIndent($this->computeNewLineContent($tokens, $index));
            }

            while ($index >= $scopes[$currentScope]['end_index']) {
                array_pop($scopes);

                if ([] === $scopes) {
                    return;
                }

                --$currentScope;
            }

            if ($token->isComment() || $token->equalsAny([';', ',', '}', [T_OPEN_TAG], [T_CLOSE_TAG], [CT::T_ATTRIBUTE_CLOSE]])) {
                continue;
            }

            if ('statement' !== $scopes[$currentScope]['type'] && 'block_signature' !== $scopes[$currentScope]['type']) {
                $endIndex = $this->findStatementEndIndex($tokens, $index, $scopes[$currentScope]['end_index']);

                if ($endIndex === $index) {
                    continue;
                }

                $scopes[] = [
                    'type' => 'statement',
                    'skip' => false,
                    'end_index' => $endIndex,
                    'end_index_inclusive' => false,
                    'initial_indent' => $previousLineInitialIndent,
                    'new_indent' => $previousLineNewIndent,
                ];
            }
        }
    }

    private function findStatementEndIndex(Tokens $tokens, int $index, int $parentScopeEndIndex): int
    {
        $endIndex = null;

        for ($searchEndIndex = $index; $searchEndIndex <= $parentScopeEndIndex; ++$searchEndIndex) {
            $searchEndToken = $tokens[$searchEndIndex];

            if ($searchEndToken->equalsAny(['(', '{', [CT::T_ARRAY_SQUARE_BRACE_OPEN]])) {
                if ($searchEndToken->equals('(')) {
                    $blockType = Tokens::BLOCK_TYPE_PARENTHESIS_BRACE;
                } elseif ($searchEndToken->equals('{')) {
                    $blockType = Tokens::BLOCK_TYPE_CURLY_BRACE;
                } else {
                    $blockType = Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE;
                }

                $searchEndIndex = $tokens->findBlockEnd($blockType, $searchEndIndex);

                continue;
            }

            if ($searchEndToken->equalsAny([';', ',', '}', [T_CLOSE_TAG]])) {
                $endIndex = $tokens->getPrevNonWhitespace($searchEndIndex);

                break;
            }
        }

        return $endIndex ?? $tokens->getPrevMeaningfulToken($parentScopeEndIndex);
    }

    /**
     * @return array{int, bool}
     */
    private function findCaseBlockEnd(Tokens $tokens, int $index): array
    {
        for ($max = \count($tokens); $index < $max; ++$index) {
            if ($tokens[$index]->isGivenKind(T_SWITCH)) {
                $braceIndex = $tokens->getNextMeaningfulToken(
                    $tokens->findBlockEnd(
                        Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
                        $tokens->getNextMeaningfulToken($index)
                    )
                );

                if ($tokens[$braceIndex]->equals(':')) {
                    $index = $this->alternativeSyntaxAnalyzer->findAlternativeSyntaxBlockEnd($tokens, $index);
                } else {
                    $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $braceIndex);
                }

                continue;
            }

            if ($tokens[$index]->equals('{')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);

                continue;
            }

            if ($tokens[$index]->equalsAny([[T_CASE], [T_DEFAULT]])) {
                return [$index, true];
            }

            if ($tokens[$index]->equalsAny(['}', [T_ENDSWITCH]])) {
                return [$tokens->getPrevNonWhitespace($index), false];
            }
        }

        throw new \LogicException('End of case block not found.');
    }

    private function getLineIndentationWithBracesCompatibility(Tokens $tokens, int $index, string $regularIndent): string
    {
        if (
            $this->bracesFixerCompatibility
            && $tokens[$index]->isGivenKind(T_OPEN_TAG)
            && Preg::match('/\R/', $tokens[$index]->getContent())
            && isset($tokens[$index + 1])
            && $tokens[$index + 1]->isWhitespace()
            && Preg::match('/\h+$/D', $tokens[$index + 1]->getContent())
        ) {
            return Preg::replace('/.*?(\h+)$/D', '$1', $tokens[$index + 1]->getContent());
        }

        return $regularIndent;
    }

    private function isCommentForControlSructureContinuation(Tokens $tokens, int $index): bool
    {
        if (!isset($tokens[$index], $tokens[$index + 1])) {
            return false;
        }

        if (!$tokens[$index]->isComment() || 1 !== Preg::match('~^(//|#)~', $tokens[$index]->getContent())) {
            return false;
        }

        if (!$tokens[$index + 1]->isWhitespace() || 1 !== Preg::match('/\R/', $tokens[$index + 1]->getContent())) {
            return false;
        }

        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        if (null !== $prevIndex && $tokens[$prevIndex]->equals('{')) {
            return false;
        }

        $index = $tokens->getNextMeaningfulToken($index + 1);

        if (null === $index || !$tokens[$index]->equals('}')) {
            return false;
        }

        $index = $tokens->getNextMeaningfulToken($index);

        return null !== $index && $tokens[$index]->equalsAny([[T_ELSE], [T_ELSEIF], ',']);
    }

    /**
     * Returns whether the token at given index is a comment whose indentation
     * can be fixed.
     *
     * Indentation of a comment is not changed when the comment is part of a
     * multi-line message whose lines are all single-line comments and at least
     * one line has meaningful content.
     */
    private function isCommentWithFixableIndentation(Tokens $tokens, int $index): bool
    {
        if (!$tokens[$index]->isComment()) {
            return false;
        }

        if (str_starts_with($tokens[$index]->getContent(), '/*')) {
            return true;
        }

        $indent = preg_quote($this->whitespacesConfig->getIndent(), '~');

        if (1 === Preg::match("~^(//|#)({$indent}.*)?$~", $tokens[$index]->getContent())) {
            return false;
        }

        $firstCommentIndex = $index;
        while (true) {
            $i = $this->getSiblingContinuousSingleLineComment($tokens, $firstCommentIndex, false);
            if (null === $i) {
                break;
            }

            $firstCommentIndex = $i;
        }

        $lastCommentIndex = $index;
        while (true) {
            $i = $this->getSiblingContinuousSingleLineComment($tokens, $lastCommentIndex, true);
            if (null === $i) {
                break;
            }

            $lastCommentIndex = $i;
        }

        if ($firstCommentIndex === $lastCommentIndex) {
            return true;
        }

        for ($i = $firstCommentIndex + 1; $i < $lastCommentIndex; ++$i) {
            if (!$tokens[$i]->isWhitespace() && !$tokens[$i]->isComment()) {
                return false;
            }
        }

        return true;
    }

    private function getSiblingContinuousSingleLineComment(Tokens $tokens, int $index, bool $after): ?int
    {
        $siblingIndex = $index;
        do {
            if ($after) {
                $siblingIndex = $tokens->getNextTokenOfKind($siblingIndex, [[T_COMMENT]]);
            } else {
                $siblingIndex = $tokens->getPrevTokenOfKind($siblingIndex, [[T_COMMENT]]);
            }

            if (null === $siblingIndex) {
                return null;
            }
        } while (str_starts_with($tokens[$siblingIndex]->getContent(), '/*'));

        $newLines = 0;
        for ($i = min($siblingIndex, $index) + 1, $max = max($siblingIndex, $index); $i < $max; ++$i) {
            if ($tokens[$i]->isWhitespace() && Preg::match('/\R/', $tokens[$i]->getContent())) {
                if (1 === $newLines || Preg::match('/\R.*\R/', $tokens[$i]->getContent())) {
                    return null;
                }

                ++$newLines;
            }
        }

        return $siblingIndex;
    }
}
