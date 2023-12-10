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

namespace PhpCsFixer\Fixer\DoctrineAnnotation;

use PhpCsFixer\AbstractDoctrineAnnotationFixer;
use PhpCsFixer\Doctrine\Annotation\DocLexer;
use PhpCsFixer\Doctrine\Annotation\Tokens;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;

final class DoctrineAnnotationIndentationFixer extends AbstractDoctrineAnnotationFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Doctrine annotations must be indented with four spaces.',
            [
                new CodeSample("<?php\n/**\n *  @Foo(\n *   foo=\"foo\"\n *  )\n */\nclass Bar {}\n"),
                new CodeSample(
                    "<?php\n/**\n *  @Foo({@Bar,\n *   @Baz})\n */\nclass Bar {}\n",
                    ['indent_mixed_lines' => true]
                ),
            ]
        );
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            ...parent::createConfigurationDefinition()->getOptions(),
            (new FixerOptionBuilder('indent_mixed_lines', 'Whether to indent lines that have content before closing parenthesis.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    protected function fixAnnotations(Tokens $doctrineAnnotationTokens): void
    {
        $annotationPositions = [];
        for ($index = 0, $max = \count($doctrineAnnotationTokens); $index < $max; ++$index) {
            if (!$doctrineAnnotationTokens[$index]->isType(DocLexer::T_AT)) {
                continue;
            }

            $annotationEndIndex = $doctrineAnnotationTokens->getAnnotationEnd($index);
            if (null === $annotationEndIndex) {
                return;
            }

            $annotationPositions[] = [$index, $annotationEndIndex];
            $index = $annotationEndIndex;
        }

        $indentLevel = 0;
        foreach ($doctrineAnnotationTokens as $index => $token) {
            if (!$token->isType(DocLexer::T_NONE) || !str_contains($token->getContent(), "\n")) {
                continue;
            }

            if (!$this->indentationCanBeFixed($doctrineAnnotationTokens, $index, $annotationPositions)) {
                continue;
            }

            $braces = $this->getLineBracesCount($doctrineAnnotationTokens, $index);
            $delta = $braces[0] - $braces[1];
            $mixedBraces = 0 === $delta && $braces[0] > 0;
            $extraIndentLevel = 0;

            if ($indentLevel > 0 && ($delta < 0 || $mixedBraces)) {
                --$indentLevel;

                if (true === $this->configuration['indent_mixed_lines'] && $this->isClosingLineWithMeaningfulContent($doctrineAnnotationTokens, $index)) {
                    $extraIndentLevel = 1;
                }
            }

            $token->setContent(Preg::replace(
                '/(\n( +\*)?) *$/',
                '$1'.str_repeat(' ', 4 * ($indentLevel + $extraIndentLevel) + 1),
                $token->getContent()
            ));

            if ($delta > 0 || $mixedBraces) {
                ++$indentLevel;
            }
        }
    }

    /**
     * @return int[]
     */
    private function getLineBracesCount(Tokens $tokens, int $index): array
    {
        $opening = 0;
        $closing = 0;

        while (isset($tokens[++$index])) {
            $token = $tokens[$index];
            if ($token->isType(DocLexer::T_NONE) && str_contains($token->getContent(), "\n")) {
                break;
            }

            if ($token->isType([DocLexer::T_OPEN_PARENTHESIS, DocLexer::T_OPEN_CURLY_BRACES])) {
                ++$opening;

                continue;
            }

            if (!$token->isType([DocLexer::T_CLOSE_PARENTHESIS, DocLexer::T_CLOSE_CURLY_BRACES])) {
                continue;
            }

            if ($opening > 0) {
                --$opening;
            } else {
                ++$closing;
            }
        }

        return [$opening, $closing];
    }

    private function isClosingLineWithMeaningfulContent(Tokens $tokens, int $index): bool
    {
        while (isset($tokens[++$index])) {
            $token = $tokens[$index];
            if ($token->isType(DocLexer::T_NONE)) {
                if (str_contains($token->getContent(), "\n")) {
                    return false;
                }

                continue;
            }

            return !$token->isType([DocLexer::T_CLOSE_PARENTHESIS, DocLexer::T_CLOSE_CURLY_BRACES]);
        }

        return false;
    }

    /**
     * @param array<array<int>> $annotationPositions Pairs of begin and end indices of main annotations
     */
    private function indentationCanBeFixed(Tokens $tokens, int $newLineTokenIndex, array $annotationPositions): bool
    {
        foreach ($annotationPositions as $position) {
            if ($newLineTokenIndex >= $position[0] && $newLineTokenIndex <= $position[1]) {
                return true;
            }
        }

        for ($index = $newLineTokenIndex + 1, $max = \count($tokens); $index < $max; ++$index) {
            $token = $tokens[$index];

            if (str_contains($token->getContent(), "\n")) {
                return false;
            }

            return $tokens[$index]->isType(DocLexer::T_AT);
        }

        return false;
    }
}
