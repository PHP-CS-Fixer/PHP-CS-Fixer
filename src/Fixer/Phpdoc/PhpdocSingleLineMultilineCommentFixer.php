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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer to convert multi-line PHPDoc comments with only a single statement to a single line.
 *
 * @author Liam Hammett <liam@liamhammett.com>
 */
final class PhpdocSingleLineMultilineCommentFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Multi-line PHPDoc comments with only a single statement should be converted to a single line.',
            [
                new CodeSample(
                    '<?php
/**
 * @return string
 */
function foo()
{
    return "bar";
}
'
                ),
                new CodeSample(
                    '<?php
/**
 * @param string $name
 */
function setName($name)
{
    $this->name = $name;
}
'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     * Must run before PhpdocAlignFixer.
     */
    public function getPriority(): int
    {
        return -11;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_DOC_COMMENT);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }

            $content = $token->getContent();
            $fixedContent = $this->fixTokenContent($content);

            if ($content !== $fixedContent) {
                $tokens[$index] = new Token([\T_DOC_COMMENT, $fixedContent]);
            }
        }
    }

    private function fixTokenContent(string $content): string
    {
        if (!$this->isMultilineComment($content)) {
            return $content;
        }

        $lines = explode("\n", $content);
        $meaningfulLines = [];

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            if ($trimmedLine === '/**' || $trimmedLine === '*/' || $trimmedLine === '*' || $trimmedLine === '') {
                continue;
            }
            
            if (str_starts_with($trimmedLine, '*')) {
                $lineContent = trim(substr($trimmedLine, 1));
                if ($lineContent !== '') {
                    $meaningfulLines[] = $lineContent;
                }
            } else {
                $meaningfulLines[] = $trimmedLine;
            }
        }

        if (\count($meaningfulLines) === 1) {
            return '/** ' . $meaningfulLines[0] . ' */';
        }

        return $content;
    }

    private function isMultilineComment(string $content): bool
    {
        return str_contains($content, "\n");
    }
}
