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

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class PhpdocScalarFixer extends AbstractPhpdocTypesFixer implements ConfigurableFixerInterface
{
    /**
     * The types to fix.
     *
     * @var array<string, string>
     */
    private static array $types = [
        'boolean' => 'bool',
        'callback' => 'callable',
        'double' => 'float',
        'integer' => 'int',
        'real' => 'float',
        'str' => 'string',
    ];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Scalar types should always be written in the same form. `int` not `integer`, `bool` not `boolean`, `float` not `real` or `double`.',
            [
                new CodeSample('<?php
/**
 * @param integer $a
 * @param boolean $b
 * @param real $c
 *
 * @return double
 */
function sample($a, $b, $c)
{
    return sample2($a, $b, $c);
}
'),
                new CodeSample(
                    '<?php
/**
 * @param integer $a
 * @param boolean $b
 * @param real $c
 */
function sample($a, $b, $c)
{
    return sample2($a, $b, $c);
}
',
                    ['types' => ['boolean']]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before GeneralPhpdocAnnotationRemoveFixer, GeneralPhpdocTagRenameFixer, NoBlankLinesAfterPhpdocFixer, NoEmptyPhpdocFixer, NoSuperfluousPhpdocTagsFixer, PhpdocAddMissingParamAnnotationFixer, PhpdocAlignFixer, PhpdocInlineTagNormalizerFixer, PhpdocLineSpanFixer, PhpdocNoAccessFixer, PhpdocNoAliasTagFixer, PhpdocNoEmptyReturnFixer, PhpdocNoPackageFixer, PhpdocNoUselessInheritdocFixer, PhpdocOrderByValueFixer, PhpdocOrderFixer, PhpdocParamOrderFixer, PhpdocReadonlyClassCommentToKeywordFixer, PhpdocReturnSelfReferenceFixer, PhpdocSeparationFixer, PhpdocSingleLineVarSpacingFixer, PhpdocSummaryFixer, PhpdocTagCasingFixer, PhpdocTagTypeFixer, PhpdocToParamTypeFixer, PhpdocToPropertyTypeFixer, PhpdocToReturnTypeFixer, PhpdocTrimConsecutiveBlankLineSeparationFixer, PhpdocTrimFixer, PhpdocTypesOrderFixer, PhpdocVarAnnotationCorrectOrderFixer, PhpdocVarWithoutNameFixer.
     * Must run after PhpdocTypesFixer.
     */
    public function getPriority(): int
    {
        /*
         * Should be run before all other docblock fixers apart from the
         * phpdoc_to_comment and phpdoc_indent fixer to make sure all fixers
         * apply correct indentation to new code they add. This should run
         * before alignment of params is done since this fixer might change
         * the type and thereby un-aligning the params. We also must run after
         * the phpdoc_types_fixer because it can convert types to things that
         * we can fix.
         */
        return 15;
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $types = array_keys(self::$types);

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('types', 'A list of types to fix.'))
                ->setAllowedValues([new AllowedValueSubset($types)])
                ->setDefault($types)
                ->getOption(),
        ]);
    }

    protected function normalize(string $type): string
    {
        if (\in_array($type, $this->configuration['types'], true)) {
            return self::$types[$type];
        }

        return $type;
    }
}
