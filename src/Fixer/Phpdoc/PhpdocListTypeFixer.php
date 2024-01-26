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
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class PhpdocListTypeFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private const STYLE_ARRAY = 'array';
    private const STYLE_LIST = 'list';

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        $codeSample = <<<'PHP'
            <?php
            /**
             * @param bool[] $a
             * @param array<int> $b
             * @param array<string, int> $c
             * @param list<int> $d
             */

            PHP;

        return new FixerDefinition(
            'PHPDoc list types must be written in configured style.',
            [
                new CodeSample($codeSample),
                new CodeSample($codeSample, ['style' => self::STYLE_ARRAY]),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer, PhpdocTypesOrderFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority(): int
    {
        return 1;
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('style', sprintf('Whether to use `%s` or `%s` as type.', self::STYLE_ARRAY, self::STYLE_LIST)))
                ->setAllowedValues([self::STYLE_ARRAY, self::STYLE_LIST])
                ->setDefault(self::STYLE_LIST)
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if (!$tokens[$index]->isGivenKind([T_DOC_COMMENT])) {
                continue;
            }

            $docBlock = new DocBlock($tokens[$index]->getContent());

            foreach ($docBlock->getAnnotations() as $annotation) {
                if (!$annotation->supportTypes()) {
                    continue;
                }

                $typeExpression = $annotation->getTypeExpression();
                if (null === $typeExpression) {
                    continue;
                }

                $type = $typeExpression->toString();
                $type = $this->fixType($type);
                $annotation->setTypes([$type]);
            }

            $newContent = $docBlock->getContent();
            if ($newContent === $tokens[$index]->getContent()) {
                continue;
            }

            $tokens[$index] = new Token([T_DOC_COMMENT, $newContent]);
        }
    }

    private function fixType(string $type): string
    {
        $newType = Preg::replace('/([\\\\a-zA-Z0-9_>]+)\[\]/', 'array<$1>', $type);

        if ($newType !== $type) {
            return $this->fixType($newType);
        }

        return Preg::replace(
            '/(array|list)(?=<[^,]+(>|<|{|\\())/',
            $this->configuration['style'],
            $type
        );
    }
}
