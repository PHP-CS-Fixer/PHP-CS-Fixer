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

use PhpCsFixer\AbstractOrderFixer;
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

final class PhpdocTypesOrderFixer extends AbstractOrderFixer implements ConfigurableFixerInterface
{
    private const OPTION_SORT_ALGORITHM = 'sort_algorithm';

    private const OPTION_NULL_ADJUSTMENT = 'null_adjustment';

    private const NULL_ADJUSTMENT_ALWAYS_FIRST = 'always_first';

    private const NULL_ADJUSTMENT_ALWAYS_LAST = 'always_last';

    private const NULL_ADJUSTMENT_NONE = 'none';

    /**
     * Array of supported sort orders in configuration.
     *
     * @var string[]
     */
    private const SUPPORTED_SORT_ORDER_OPTIONS = [
        AbstractOrderFixer::SORT_ORDER_ALPHA,
        AbstractOrderFixer::SORT_ORDER_NONE,
    ];

    /**
     * Array of supported sort orders in configuration.
     *
     * @var string[]
     */
    private const SUPPORTED_NULL_ADJUSTMENT_OPTIONS = [
        self::NULL_ADJUSTMENT_ALWAYS_FIRST,
        self::NULL_ADJUSTMENT_ALWAYS_LAST,
        self::NULL_ADJUSTMENT_NONE,
    ];

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
                    [self::OPTION_NULL_ADJUSTMENT => self::NULL_ADJUSTMENT_ALWAYS_LAST]
                ),
                new CodeSample(
                    '<?php
/**
 * @param null|string|int|\Foo $bar
 */
',
                    [self::OPTION_SORT_ALGORITHM => AbstractOrderFixer::SORT_ORDER_ALPHA]
                ),
                new CodeSample(
                    '<?php
/**
 * @param null|string|int|\Foo $bar
 */
',
                    [
                        self::OPTION_SORT_ALGORITHM => AbstractOrderFixer::SORT_ORDER_ALPHA,
                        self::OPTION_NULL_ADJUSTMENT => self::NULL_ADJUSTMENT_ALWAYS_LAST,
                    ]
                ),
                new CodeSample(
                    '<?php
/**
 * @param null|string|int|\Foo $bar
 */
',
                    [
                        self::OPTION_SORT_ALGORITHM => AbstractOrderFixer::SORT_ORDER_ALPHA,
                        self::OPTION_NULL_ADJUSTMENT => self::NULL_ADJUSTMENT_NONE,
                    ]
                ),
                new CodeSample(
                    '<?php
/**
 * @param Aaa|AA $bar
 */
',
                    [AbstractOrderFixer::OPTION_CASE_SENSITIVE => true]
                ),
                new CodeSample(
                    '<?php
/**
 * @param string|array|int $bar
 */
',
                    [AbstractOrderFixer::OPTION_DIRECTION => self::DIRECTION_DESCEND]
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
            (new FixerOptionBuilder(self::OPTION_SORT_ALGORITHM, 'The sorting algorithm to apply.'))
                ->setAllowedValues(self::SUPPORTED_SORT_ORDER_OPTIONS)
                ->setDefault(AbstractOrderFixer::SORT_ORDER_ALPHA)
                ->getOption(),
            (new FixerOptionBuilder(AbstractOrderFixer::OPTION_DIRECTION, 'Which direction the types should be sorted.'))
                ->setAllowedValues(AbstractOrderFixer::SUPPORTED_DIRECTION_OPTIONS)
                ->setDefault(AbstractOrderFixer::DIRECTION_ASCEND)
                ->getOption(),
            (new FixerOptionBuilder(AbstractOrderFixer::OPTION_CASE_SENSITIVE, 'Whether the sorting should be case sensitive.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder(self::OPTION_NULL_ADJUSTMENT, 'Forces the position of `null` (overrides `sort_algorithm`).'))
                ->setAllowedValues(self::SUPPORTED_NULL_ADJUSTMENT_OPTIONS)
                ->setDefault(self::NULL_ADJUSTMENT_ALWAYS_FIRST)
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
                if (null !== $annotation->getTypeExpression()) {
                    $annotation->setTypes(
                        $this->sortTypes(
                            $annotation->getTypeExpression()
                        )
                    );
                }

                // fix @method parameters types
                $line = $doc->getLine($annotation->getStart());
                $line->setContent(Preg::replaceCallback('/@method\s+'.TypeExpression::REGEX_TYPES.'\s+\K(?&callable)/', function (array $matches) {
                    $typeExpression = new TypeExpression($matches[0], null, []);

                    return implode('|', $this->sortTypes($typeExpression));
                }, $line->getContent()));
            }

            $tokens[$index] = new Token([T_DOC_COMMENT, $doc->getContent()]);
        }
    }

    protected function getSortOrderOptionName(): string
    {
        return self::OPTION_SORT_ALGORITHM;
    }

    /**
     * @return string[]
     */
    private function sortTypes(TypeExpression $typeExpression): array
    {
        $normalizeType = static fn (string $type): string => Preg::replace('/^\\??\\\?/', '', $type);

        $typeExpression->sortTypes(
            function (TypeExpression $a, TypeExpression $b) use ($normalizeType): int {
                $a = $normalizeType($a->toString());
                $b = $normalizeType($b->toString());
                $lowerCaseA = strtolower($a);
                $lowerCaseB = strtolower($b);

                if (self::NULL_ADJUSTMENT_NONE !== $this->configuration[self::OPTION_NULL_ADJUSTMENT]) {
                    if ('null' === $lowerCaseA && 'null' !== $lowerCaseB) {
                        return self::NULL_ADJUSTMENT_ALWAYS_LAST === $this->configuration[self::OPTION_NULL_ADJUSTMENT]
                            ? 1
                            : -1;
                    }
                    if ('null' !== $lowerCaseA && 'null' === $lowerCaseB) {
                        return self::NULL_ADJUSTMENT_ALWAYS_LAST === $this->configuration[self::OPTION_NULL_ADJUSTMENT]
                            ? -1
                            : 1;
                    }
                }

                return $this->getScoreWithSortAlgorithm($a, $b);
            }
        );

        return $typeExpression->getTypes();
    }
}
