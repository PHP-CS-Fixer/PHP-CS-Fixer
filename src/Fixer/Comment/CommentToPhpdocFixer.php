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

namespace PhpCsFixer\Fixer\Comment;

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
use PhpCsFixer\Tokenizer\Analyzer\CommentsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class CommentToPhpdocFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @var string[]
     */
    private array $ignoredTags = [];

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * Must run before GeneralPhpdocAnnotationRemoveFixer, GeneralPhpdocTagRenameFixer, NoBlankLinesAfterPhpdocFixer, NoEmptyPhpdocFixer, NoSuperfluousPhpdocTagsFixer, PhpdocAddMissingParamAnnotationFixer, PhpdocAlignFixer, PhpdocAnnotationWithoutDotFixer, PhpdocInlineTagNormalizerFixer, PhpdocLineSpanFixer, PhpdocNoAccessFixer, PhpdocNoAliasTagFixer, PhpdocNoEmptyReturnFixer, PhpdocNoPackageFixer, PhpdocNoUselessInheritdocFixer, PhpdocOrderByValueFixer, PhpdocOrderFixer, PhpdocReturnSelfReferenceFixer, PhpdocSeparationFixer, PhpdocSingleLineVarSpacingFixer, PhpdocSummaryFixer, PhpdocTagCasingFixer, PhpdocTagTypeFixer, PhpdocToCommentFixer, PhpdocToParamTypeFixer, PhpdocToPropertyTypeFixer, PhpdocToReturnTypeFixer, PhpdocTrimConsecutiveBlankLineSeparationFixer, PhpdocTrimFixer, PhpdocTypesOrderFixer, PhpdocVarAnnotationCorrectOrderFixer, PhpdocVarWithoutNameFixer.
     * Must run after AlignMultilineCommentFixer.
     */
    public function getPriority(): int
    {
        // Should be run before all other PHPDoc fixers
        return 26;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Comments with annotation should be docblock when used on structural elements.',
            [
                new CodeSample("<?php /* header */ \$x = true; /* @var bool \$isFoo */ \$isFoo = true;\n"),
                new CodeSample("<?php\n// @todo do something later\n\$foo = 1;\n\n// @var int \$a\n\$a = foo();\n", ['ignored_tags' => ['todo']]),
            ],
            null,
            'Risky as new docblocks might mean more, e.g. a Doctrine entity might have a new column in database.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->ignoredTags = array_map(
            static function (string $tag): string {
                return strtolower($tag);
            },
            $this->configuration['ignored_tags']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('ignored_tags', 'List of ignored tags'))
                ->setAllowedTypes(['array'])
                ->setDefault([])
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $commentsAnalyzer = new CommentsAnalyzer();

        for ($index = 0, $limit = \count($tokens); $index < $limit; ++$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_COMMENT)) {
                continue;
            }

            if ($commentsAnalyzer->isHeaderComment($tokens, $index)) {
                continue;
            }

            if (!$commentsAnalyzer->isBeforeStructuralElement($tokens, $index)) {
                continue;
            }

            $commentIndices = $commentsAnalyzer->getCommentBlockIndices($tokens, $index);

            if ($this->isCommentCandidate($tokens, $commentIndices)) {
                $this->fixComment($tokens, $commentIndices);
            }

            $index = max($commentIndices);
        }
    }

    /**
     * @param int[] $indices
     */
    private function isCommentCandidate(Tokens $tokens, array $indices): bool
    {
        return array_reduce(
            $indices,
            function (bool $carry, int $index) use ($tokens): bool {
                if ($carry) {
                    return true;
                }
                if (1 !== Preg::match('~(?:#|//|/\*+|\R(?:\s*\*)?)\s*\@([a-zA-Z0-9_\\\\-]+)(?=\s|\(|$)~', $tokens[$index]->getContent(), $matches)) {
                    return false;
                }

                return !\in_array(strtolower($matches[1]), $this->ignoredTags, true);
            },
            false
        );
    }

    /**
     * @param int[] $indices
     */
    private function fixComment(Tokens $tokens, array $indices): void
    {
        if (1 === \count($indices)) {
            $this->fixCommentSingleLine($tokens, reset($indices));
        } else {
            $this->fixCommentMultiLine($tokens, $indices);
        }
    }

    private function fixCommentSingleLine(Tokens $tokens, int $index): void
    {
        $message = $this->getMessage($tokens[$index]->getContent());

        if ('' !== trim(substr($message, 0, 1))) {
            $message = ' '.$message;
        }

        if ('' !== trim(substr($message, -1))) {
            $message .= ' ';
        }

        $tokens[$index] = new Token([T_DOC_COMMENT, '/**'.$message.'*/']);
    }

    /**
     * @param int[] $indices
     */
    private function fixCommentMultiLine(Tokens $tokens, array $indices): void
    {
        $startIndex = reset($indices);
        $indent = Utils::calculateTrailingWhitespaceIndent($tokens[$startIndex - 1]);

        $newContent = '/**'.$this->whitespacesConfig->getLineEnding();
        $count = max($indices);

        for ($index = $startIndex; $index <= $count; ++$index) {
            if (!$tokens[$index]->isComment()) {
                continue;
            }
            if (str_contains($tokens[$index]->getContent(), '*/')) {
                return;
            }
            $message = $this->getMessage($tokens[$index]->getContent());
            if ('' !== trim(substr($message, 0, 1))) {
                $message = ' '.$message;
            }
            $newContent .= $indent.' *'.$message.$this->whitespacesConfig->getLineEnding();
        }

        for ($index = $startIndex; $index <= $count; ++$index) {
            $tokens->clearAt($index);
        }

        $newContent .= $indent.' */';

        $tokens->insertAt($startIndex, new Token([T_DOC_COMMENT, $newContent]));
    }

    private function getMessage(string $content): string
    {
        if (str_starts_with($content, '#')) {
            return substr($content, 1);
        }
        if (str_starts_with($content, '//')) {
            return substr($content, 2);
        }

        return rtrim(ltrim($content, '/*'), '*/');
    }
}
