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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class ArrayIndentationFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /** @var int */
    private $newlineTokenIndexCache;

    /** @var int */
    private $newlineTokenPositionCache;

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Each element of an array must be indented exactly once.',
            [
                new CodeSample("<?php\n\$foo = [\n   'bar' => [\n    'baz' => true,\n  ],\n];\n"),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN]);
    }

    /**
     * {@inheritdoc}
     *
     * Must run before AlignMultilineCommentFixer, BinaryOperatorSpacesFixer.
     * Must run after BracesFixer, MethodArgumentSpaceFixer, MethodChainingIndentationFixer.
     */
    public function getPriority()
    {
        return 29;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $this->returnWithUpdateCache(0, null);

        $scopes = [];
        $previousLineInitialIndent = '';
        $previousLineNewIndent = '';

        foreach ($tokens as $index => $token) {
            $currentScope = [] !== $scopes ? \count($scopes) - 1 : null;

            if ($token->isComment()) {
                continue;
            }

            if (
                $token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)
                || ($token->equals('(') && $tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(T_ARRAY))
            ) {
                $endIndex = $tokens->findBlockEnd(
                    $token->equals('(') ? Tokens::BLOCK_TYPE_PARENTHESIS_BRACE : Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE,
                    $index
                );

                $scopes[] = [
                    'type' => 'array',
                    'end_index' => $endIndex,
                    'initial_indent' => $this->getLineIndentation($tokens, $index),
                ];

                continue;
            }

            if (null === $currentScope) {
                continue;
            }

            if ($token->isWhitespace()) {
                if (!Preg::match('/\R/', $token->getContent())) {
                    continue;
                }

                if ('array' === $scopes[$currentScope]['type']) {
                    $indent = false;

                    for ($searchEndIndex = $index + 1; $searchEndIndex < $scopes[$currentScope]['end_index']; ++$searchEndIndex) {
                        $searchEndToken = $tokens[$searchEndIndex];

                        if (
                            (!$searchEndToken->isWhitespace() && !$searchEndToken->isComment())
                            || ($searchEndToken->isWhitespace() && Preg::match('/\R/', $searchEndToken->getContent()))
                        ) {
                            $indent = true;

                            break;
                        }
                    }

                    $content = Preg::replace(
                        '/(\R+)\h*$/',
                        '$1'.$scopes[$currentScope]['initial_indent'].($indent ? $this->whitespacesConfig->getIndent() : ''),
                        $token->getContent()
                    );

                    $previousLineInitialIndent = $this->extractIndent($token->getContent());
                    $previousLineNewIndent = $this->extractIndent($content);
                } else {
                    $content = Preg::replace(
                        '/(\R)'.preg_quote($scopes[$currentScope]['initial_indent'], '/').'(\h*)$/',
                        '$1'.$scopes[$currentScope]['new_indent'].'$2',
                        $token->getContent()
                    );
                }

                $tokens[$index] = new Token([T_WHITESPACE, $content]);

                continue;
            }

            if ($index === $scopes[$currentScope]['end_index']) {
                while ([] !== $scopes && $index === $scopes[$currentScope]['end_index']) {
                    array_pop($scopes);
                    --$currentScope;
                }

                continue;
            }

            if ($token->equals(',')) {
                continue;
            }

            if ('expression' !== $scopes[$currentScope]['type']) {
                $endIndex = $this->findExpressionEndIndex($tokens, $index, $scopes[$currentScope]['end_index']);

                if ($endIndex === $index) {
                    continue;
                }

                $scopes[] = [
                    'type' => 'expression',
                    'end_index' => $endIndex,
                    'initial_indent' => $previousLineInitialIndent,
                    'new_indent' => $previousLineNewIndent,
                ];
            }
        }
    }

    private function findExpressionEndIndex(Tokens $tokens, $index, $parentScopeEndIndex)
    {
        $endIndex = null;

        for ($searchEndIndex = $index + 1; $searchEndIndex < $parentScopeEndIndex; ++$searchEndIndex) {
            $searchEndToken = $tokens[$searchEndIndex];

            if ($searchEndToken->equalsAny(['(', '{']) || $searchEndToken->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                $type = Tokens::detectBlockType($searchEndToken);
                $searchEndIndex = $tokens->findBlockEnd(
                    $type['type'],
                    $searchEndIndex
                );

                continue;
            }

            if ($searchEndToken->equals(',')) {
                $endIndex = $tokens->getPrevMeaningfulToken($searchEndIndex);

                break;
            }
        }

        if (null !== $endIndex) {
            return $endIndex;
        }

        return $tokens->getPrevMeaningfulToken($parentScopeEndIndex);
    }

    private function getLineIndentation(Tokens $tokens, $index)
    {
        $newlineTokenIndex = $this->getPreviousNewlineTokenIndex($tokens, $index);

        if (null === $newlineTokenIndex) {
            return '';
        }

        return $this->extractIndent($this->computeNewLineContent($tokens, $newlineTokenIndex));
    }

    private function extractIndent($content)
    {
        if (Preg::match('/\R(\h*)[^\r\n]*$/D', $content, $matches)) {
            return $matches[1];
        }

        return '';
    }

    private function getPreviousNewlineTokenIndex(Tokens $tokens, $startIndex)
    {
        $index = $startIndex;
        while ($index > 0) {
            $index = $tokens->getPrevTokenOfKind($index, [[T_WHITESPACE], [T_INLINE_HTML]]);

            if ($this->newlineTokenIndexCache > $index) {
                return $this->returnWithUpdateCache($startIndex, $this->newlineTokenPositionCache);
            }

            if (null === $index) {
                break;
            }

            if ($this->isNewLineToken($tokens, $index)) {
                return $this->returnWithUpdateCache($startIndex, $index);
            }
        }

        return $this->returnWithUpdateCache($startIndex, null);
    }

    private function isNewLineToken(Tokens $tokens, $index)
    {
        if (!$tokens[$index]->isGivenKind([T_WHITESPACE, T_INLINE_HTML])) {
            return false;
        }

        return (bool) Preg::match('/\R/', $this->computeNewLineContent($tokens, $index));
    }

    private function computeNewLineContent(Tokens $tokens, $index)
    {
        $content = $tokens[$index]->getContent();

        if (0 !== $index && $tokens[$index - 1]->equalsAny([[T_OPEN_TAG], [T_CLOSE_TAG]])) {
            $content = Preg::replace('/\S/', '', $tokens[$index - 1]->getContent()).$content;
        }

        return $content;
    }

    /**
     * @param int      $index
     * @param null|int $position
     */
    private function returnWithUpdateCache($index, $position)
    {
        $this->newlineTokenIndexCache = $index;
        $this->newlineTokenPositionCache = $position;

        return $position;
    }
}
