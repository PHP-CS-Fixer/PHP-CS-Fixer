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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.4.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class IndentationTypeFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /**
     * Width (in columns) of one indentation level in the source being fixed,
     * also used as the tab stop width when expanding tabs.
     */
    private const SOURCE_INDENT_WIDTH = 4;

    private string $indent;

    private bool $useTabs;

    /**
     * Memoized results of {@see convertIndent()}; only a handful of distinct
     * indentation strings occur in a file.
     *
     * @var array<string, string>
     */
    private array $convertedIndents = [];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Code MUST use configured indentation type.',
            [
                new CodeSample("<?php\n\nif (true) {\n\techo 'Hello!';\n}\n"),
            ],
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocIndentFixer.
     * Must run after ClassAttributesSeparationFixer.
     */
    public function getPriority(): int
    {
        return 50;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([\T_COMMENT, \T_DOC_COMMENT, \T_WHITESPACE]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->indent = $this->whitespacesConfig->getIndent();
        $this->useTabs = "\t" === $this->indent;
        $this->convertedIndents = [];

        foreach ($tokens as $index => $token) {
            // Comparing ids avoids Token::isWhitespace() (a trim() on every
            // punctuation token) and Token::isComment() (an in_array()).
            $id = $token->getId();

            if (\T_WHITESPACE === $id) {
                $content = $this->fixIndentInWhitespace($tokens, $index, $token->getContent());
            } elseif (\T_COMMENT === $id || \T_DOC_COMMENT === $id) {
                $content = $this->fixIndentInComment($token->getContent());
            } else {
                continue;
            }

            if (null !== $content) {
                $tokens[$index] = new Token([$id, $content]);
            }
        }
    }

    /**
     * @return null|string new content, or null when nothing needs to change
     */
    private function fixIndentInComment(string $content): ?string
    {
        // The indentation of a comment's first line is a separate whitespace
        // token, so only comments spanning several lines can contain any.
        if (false === strpbrk($content, "\r\n")) {
            return null;
        }

        // Without a configured tab there is nothing to do for space-only indentation.
        if (!$this->useTabs && !str_contains($content, "\t")) {
            return null;
        }

        $newContent = Preg::replaceCallback(
            '/^[ \t]+/m',
            fn (array $matches): string => $this->convertIndent($matches[0]),
            $content,
        );

        return $newContent === $content ? null : $newContent;
    }

    /**
     * @return null|string new content, or null when nothing needs to change
     */
    private function fixIndentInWhitespace(Tokens $tokens, int $index, string $content): ?string
    {
        // Most whitespace tokens are single spaces between tokens.
        if (!$this->useTabs) {
            // Spaces are kept as they are, so only tabs can need conversion.
            if (!str_contains($content, "\t")) {
                return null;
            }
        } elseif (\strlen($content) === strspn($content, "\r\n\t")) {
            // Tab indentation that is already tabs only.
            return null;
        }

        $previousTokenEndsWithLinebreak = false;

        if ($index > 0) {
            $previousContent = $tokens[$index - 1]->getContent();

            // whitespace is indentation only when it starts a line, i.e. the
            // previous token ENDS with a line break (not merely contains one)
            if ('' !== $previousContent && ("\n" === $previousContent[-1] || "\r" === $previousContent[-1])) {
                $previousTokenEndsWithLinebreak = true;
            }
        }

        // mid-line whitespace: no line break inside and not at the start of a line
        if (!$previousTokenEndsWithLinebreak && false === strpbrk($content, "\r\n")) {
            return null;
        }

        $subject = $previousTokenEndsWithLinebreak ? "\n".$content : $content;
        $newContent = Preg::replaceCallback(
            '/(\R)([ \t]+)/', // find indent
            fn (array $matches): string => $matches[1].$this->convertIndent($matches[2]),
            $subject,
        );

        if ($previousTokenEndsWithLinebreak) {
            $newContent = substr($newContent, 1);
        }

        return $newContent === $content ? null : $newContent;
    }

    /**
     * Converts leading whitespace (any mix of spaces and tabs, starting at
     * column 0) to the configured indentation type.
     *
     * Tabs are first expanded to the next tab stop. When the configured indent
     * is a tab, each whole level becomes a tab and any remainder is kept as
     * alignment spaces. For any other configured indent, the result is plain
     * spaces of the same width: existing spaces are left untouched and a tab
     * becomes one level of SOURCE_INDENT_WIDTH spaces.
     */
    private function convertIndent(string $leadingWhitespace): string
    {
        return $this->convertedIndents[$leadingWhitespace] ??= $this->computeIndent($leadingWhitespace);
    }

    private function computeIndent(string $leadingWhitespace): string
    {
        $width = 0;

        for ($i = 0, $length = \strlen($leadingWhitespace); $i < $length; ++$i) {
            $width += "\t" === $leadingWhitespace[$i]
                ? self::SOURCE_INDENT_WIDTH - ($width % self::SOURCE_INDENT_WIDTH)
                : 1;
        }

        if (!$this->useTabs) {
            return str_repeat(' ', $width);
        }

        return str_repeat("\t", intdiv($width, self::SOURCE_INDENT_WIDTH))
            .str_repeat(' ', $width % self::SOURCE_INDENT_WIDTH);
    }
}
