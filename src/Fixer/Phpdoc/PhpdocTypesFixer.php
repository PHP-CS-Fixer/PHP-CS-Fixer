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

use PhpCsFixer\AbstractPhpdocTypesFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpdocTypesFixer extends AbstractPhpdocTypesFixer implements ConfigurableFixerInterface
{
    /**
     * Available types, grouped.
     *
     * @var array<string,string[]>
     */
    private const POSSIBLE_TYPES = [
        'simple' => [
            'array',
            'bool',
            'callable',
            'float',
            'int',
            'iterable',
            'null',
            'object',
            'string',
        ],
        'alias' => [
            'boolean',
            'callback',
            'double',
            'integer',
            'real',
        ],
        'meta' => [
            '$this',
            'false',
            'mixed',
            'parent',
            'resource',
            'scalar',
            'self',
            'static',
            'true',
            'void',
        ],
    ];

    /**
     * @var string
     */
    private $patternToFix = '';

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $typesToFix = array_merge(...array_map(static function (string $group): array {
            return self::POSSIBLE_TYPES[$group];
        }, $this->configuration['groups']));

        $this->patternToFix = sprintf(
            '/(?<![a-zA-Z0-9_\x80-\xff]\\\\)(\b|.(?=\$))(%s)\b(?!\\\\)/i',
            implode(
                '|',
                array_map(
                    static function (string $type): string {
                        return preg_quote($type, '/');
                    },
                    $typesToFix
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'The correct case must be used for standard PHP types in PHPDoc.',
            [
                new CodeSample(
                    '<?php
/**
 * @param STRING|String[] $bar
 *
 * @return inT[]
 */
'
                ),
                new CodeSample(
                    '<?php
/**
 * @param BOOL $foo
 *
 * @return MIXED
 */
',
                    ['groups' => ['simple', 'alias']]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before GeneralPhpdocAnnotationRemoveFixer, GeneralPhpdocTagRenameFixer, NoBlankLinesAfterPhpdocFixer, NoEmptyPhpdocFixer, NoSuperfluousPhpdocTagsFixer, PhpdocAddMissingParamAnnotationFixer, PhpdocAlignFixer, PhpdocAlignFixer, PhpdocInlineTagNormalizerFixer, PhpdocLineSpanFixer, PhpdocNoAccessFixer, PhpdocNoAliasTagFixer, PhpdocNoEmptyReturnFixer, PhpdocNoPackageFixer, PhpdocNoUselessInheritdocFixer, PhpdocOrderByValueFixer, PhpdocOrderFixer, PhpdocReturnSelfReferenceFixer, PhpdocScalarFixer, PhpdocSeparationFixer, PhpdocSingleLineVarSpacingFixer, PhpdocSummaryFixer, PhpdocTagCasingFixer, PhpdocTagTypeFixer, PhpdocToParamTypeFixer, PhpdocToPropertyTypeFixer, PhpdocToReturnTypeFixer, PhpdocToReturnTypeFixer, PhpdocTrimConsecutiveBlankLineSeparationFixer, PhpdocTrimFixer, PhpdocTypesOrderFixer, PhpdocVarAnnotationCorrectOrderFixer, PhpdocVarWithoutNameFixer.
     * Must run after PhpdocAnnotationWithoutDotFixer, PhpdocIndentFixer.
     */
    public function getPriority(): int
    {
        /*
         * Should be run before all other docblock fixers apart from the
         * phpdoc_to_comment and phpdoc_indent fixer to make sure all fixers
         * apply correct indentation to new code they add. This should run
         * before alignment of params is done since this fixer might change
         * the type and thereby un-aligning the params. We also must run before
         * the phpdoc_scalar_fixer so that it can make changes after us.
         */
        return 16;
    }

    /**
     * {@inheritdoc}
     */
    protected function normalize(string $type): string
    {
        return Preg::replaceCallback(
            $this->patternToFix,
            function (array $matches): string {
                return strtolower($matches[0]);
            },
            $type
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $possibleGroups = array_keys(self::POSSIBLE_TYPES);

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('groups', 'Type groups to fix.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset($possibleGroups)])
                ->setDefault($possibleGroups)
                ->getOption(),
        ]);
    }
}
