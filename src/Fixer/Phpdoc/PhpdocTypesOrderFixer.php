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
use PhpCsFixer\DocBlock\Annotation;
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

final class PhpdocTypesOrderFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Sorts PHPDoc types.',
            [
                new CodeSample(
                    '<?php
/**
 * @param string|null $bar
 */
'
                ),
                new CodeSample(
                    '<?php
/**
 * @param null|string $bar
 */
',
                    ['null_adjustment' => 'always_last']
                ),
                new CodeSample(
                    '<?php
/**
 * @param null|string|int|\Foo $bar
 */
',
                    ['sort_algorithm' => 'alpha']
                ),
                new CodeSample(
                    '<?php
/**
 * @param null|string|int|\Foo $bar
 */
',
                    [
                        'sort_algorithm' => 'alpha',
                        'null_adjustment' => 'always_last',
                    ]
                ),
                new CodeSample(
                    '<?php
/**
 * @param null|string|int|\Foo $bar
 */
',
                    [
                        'sort_algorithm' => 'alpha',
                        'null_adjustment' => 'none',
                    ]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('sort_algorithm', 'The sorting algorithm to apply.'))
                ->setAllowedValues(['alpha', 'none'])
                ->setDefault('alpha')
                ->getOption(),
            (new FixerOptionBuilder('null_adjustment', 'Forces the position of `null` (overrides `sort_algorithm`).'))
                ->setAllowedValues(['always_first', 'always_last', 'none'])
                ->setDefault('always_first')
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());
            $annotations = $doc->getAnnotationsOfType(Annotation::getTagsWithTypes());

            if (0 === \count($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {
                // fix main types
                $annotation->setTypes(
                    $this->sortTypes(
                        $annotation->getTypeExpression()
                    )
                );

                // fix @method parameters types
                $line = $doc->getLine($annotation->getStart());
                $line->setContent(Preg::replaceCallback('/(@method\s+.+?\s+\w+\()(.*)\)/', function (array $matches) {
                    $sorted = Preg::replaceCallback('/([^\s,]+)([\s]+\$[^\s,]+)/', function (array $matches): string {
                        return $this->sortJoinedTypes($matches[1]).$matches[2];
                    }, $matches[2]);

                    return $matches[1].$sorted.')';
                }, $line->getContent()));
            }

            $tokens[$index] = new Token([T_DOC_COMMENT, $doc->getContent()]);
        }
    }

    /**
     * @return string[]
     */
    private function sortTypes(TypeExpression $typeExpression): array
    {
        $normalizeType = static function (string $type): string {
            return Preg::replace('/^\\??\\\?/', '', $type);
        };

        $typeExpression->sortTypes(
            function (TypeExpression $a, TypeExpression $b) use ($normalizeType): int {
                $a = $normalizeType($a->toString());
                $b = $normalizeType($b->toString());
                $lowerCaseA = strtolower($a);
                $lowerCaseB = strtolower($b);

                if ('none' !== $this->configuration['null_adjustment']) {
                    if ('null' === $lowerCaseA && 'null' !== $lowerCaseB) {
                        return 'always_last' === $this->configuration['null_adjustment'] ? 1 : -1;
                    }
                    if ('null' !== $lowerCaseA && 'null' === $lowerCaseB) {
                        return 'always_last' === $this->configuration['null_adjustment'] ? -1 : 1;
                    }
                }

                if ('alpha' === $this->configuration['sort_algorithm']) {
                    return strcasecmp($a, $b);
                }

                return 0;
            }
        );

        return $typeExpression->getTypes();
    }

    private function sortJoinedTypes(string $types): string
    {
        $typeExpression = new TypeExpression($types, null, []);

        return implode('|', $this->sortTypes($typeExpression));
    }
}
