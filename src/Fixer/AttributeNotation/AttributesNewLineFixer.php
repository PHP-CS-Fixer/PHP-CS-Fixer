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

namespace PhpCsFixer\Fixer\AttributeNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Oleksandr Bredikhin <olbresoft@gmail.com>
 */
final class AttributesNewLineFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    public function isCandidate(Tokens $tokens): bool
    {
        return \PHP_VERSION_ID >= 8_00_00 && $tokens->isTokenKindFound(T_ATTRIBUTE);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Attributes should be on their own line.',
            [
                new VersionSpecificCodeSample(
                    "<?php
#[Foo] #[Bar] class Baz
{
}\n",
                    new VersionSpecification(8_00_00)
                ),
                new VersionSpecificCodeSample(
                    "<?php
#[Foo] class Bar
{
    #[Baz] public function foo() {}
}\n",
                    new VersionSpecification(8_00_00)
                ),
                new VersionSpecificCodeSample(
                    "<?php
#[Foo] class Bar
{
    #[Test] public const TEST = 'Test';
}\n",
                    new VersionSpecification(8_00_00)
                ),
            ],
        );
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $count = $tokens->count();
        for ($index = $count - 1; $index >= 0; --$index) {
            if ($tokens[$index]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
                $this->fixNewline($tokens, $index);
            }
        }
    }

    private function fixNewline(Tokens $tokens, int $endIndex): void
    {
        $nextIndex = $endIndex + 1;
        if ($tokens[$nextIndex]->isWhitespace()) {
            $whitespace = $tokens[$nextIndex]->getContent();

            if (str_contains($whitespace, "\n") || str_contains($whitespace, "\r")) {
                return;
            }

            $tokens->clearAt($nextIndex);
        }

        $indentation = $this->getIndentation($tokens, $endIndex);

        $tokens->ensureWhitespaceAtIndex(
            $endIndex + 1,
            0,
            $this->whitespacesConfig->getLineEnding().$indentation
        );
    }

    private function getIndentation(Tokens $tokens, int $attributeEndIndex): string
    {
        $nextMeaningfulIndex = $tokens->getNextMeaningfulToken($attributeEndIndex);

        if (null === $nextMeaningfulIndex || $tokens[$nextMeaningfulIndex]->isGivenKind([T_CLASS])) {
            return '';
        }

        $searchIndex = $nextMeaningfulIndex;

        do {
            $prevWhitespaceIndex = $tokens->getPrevTokenOfKind(
                $searchIndex,
                [[T_ENCAPSED_AND_WHITESPACE], [T_INLINE_HTML], [T_WHITESPACE]],
            );

            $searchIndex = $prevWhitespaceIndex;
        } while (null !== $prevWhitespaceIndex
        && !str_contains($tokens[$prevWhitespaceIndex]->getContent(), "\n")
        );

        if (null === $prevWhitespaceIndex) {
            return '';
        }

        $whitespaceContent = $tokens[$prevWhitespaceIndex]->getContent();

        if (str_contains($whitespaceContent, "\n")) {
            $lastNewLinePos = strrpos($whitespaceContent, "\n");

            if (false === $lastNewLinePos) {
                return '';
            }

            return substr($whitespaceContent, $lastNewLinePos + 1);
        }

        return $whitespaceContent;
    }
}
