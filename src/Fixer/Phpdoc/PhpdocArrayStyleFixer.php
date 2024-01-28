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
use PhpCsFixer\DocBlock\TypeExpression;
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

final class PhpdocArrayStyleFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private const STRATEGY_FROM_ARRAY_TO_LIST = 'array_to_list';
    private const STRATEGY_FROM_BRACKETS_TO_ARRAY = 'brackets_to_array';
    private const STRATEGY_FROM_BRACKETS_TO_LIST = 'brackets_to_array_to_list';

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
             * @param list<int> $c
             * @param array<string, int> $d
             */

            PHP;

        return new FixerDefinition(
            'PHPDoc list types must be written in configured style.',
            [
                new CodeSample($codeSample),
                new CodeSample($codeSample, ['strategy' => self::STRATEGY_FROM_ARRAY_TO_LIST]),
                new CodeSample($codeSample, ['strategy' => self::STRATEGY_FROM_BRACKETS_TO_LIST]),
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
            (new FixerOptionBuilder('strategy', 'Which part of the conversion - brackets (`[]`) to `array` to `list` - to perform.'))
                ->setAllowedValues([self::STRATEGY_FROM_ARRAY_TO_LIST, self::STRATEGY_FROM_BRACKETS_TO_ARRAY, self::STRATEGY_FROM_BRACKETS_TO_LIST])
                ->setDefault(self::STRATEGY_FROM_BRACKETS_TO_ARRAY)
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

                $annotation->setTypes([$this->normalize($typeExpression->toString())]);
            }

            $newContent = $docBlock->getContent();
            if ($newContent === $tokens[$index]->getContent()) {
                continue;
            }

            $tokens[$index] = new Token([T_DOC_COMMENT, $newContent]);
        }
    }

    protected function normalize(string $type): string
    {
        $typeExpression = new TypeExpression($type, null, []);

        $typeExpression->walkTypes(function (TypeExpression $type): void {
            if ($type->isUnionType()) {
                return;
            }

            $value = $type->toString();

            if (Preg::match('/^\??\s*[\'"]/', $value)) {
                return;
            }

            if (str_starts_with($value, '?')) {
                $value = '?'.$this->fixType(substr($value, 1));
            } else {
                $value = $this->fixType($value);
            }

            \Closure::bind(static function () use ($type, $value): void {
                $type->value = $value;
            }, null, TypeExpression::class)();
        });

        return $typeExpression->toString();
    }

    private function fixType(string $type): string
    {
        if (self::STRATEGY_FROM_ARRAY_TO_LIST !== $this->configuration['strategy']) {
            do {
                $type = Preg::replace('/(.+)\[\]/', 'array<$1>', $type, -1, $count);
            } while ($count > 0);
        }

        if (self::STRATEGY_FROM_BRACKETS_TO_ARRAY !== $this->configuration['strategy']) {
            $type = Preg::replace('/array(?=<[^,]+(>|<|{|\\())/', 'list', $type);
        }

        return $type;
    }
}
