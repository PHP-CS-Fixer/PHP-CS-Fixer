<?php

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

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Preg;

/**
 * Case sensitive tag replace fixer (does not process inline tags like {@inheritdoc}).
 *
 * @author Graham Campbell <graham@alt-three.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class PhpdocNoAliasTagFixer extends AbstractProxyFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'No alias PHPDoc tags should be used.',
            [
                new CodeSample(
                    '<?php
/**
 * @property string $foo
 * @property-read string $bar
 *
 * @link baz
 */
final class Example
{
}
'
                ),
                new CodeSample(
                    '<?php
/**
 * @property string $foo
 * @property-read string $bar
 *
 * @link baz
 */
final class Example
{
}
',
                    ['replacements' => ['link' => 'website']]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAddMissingParamAnnotationFixer, PhpdocAlignFixer, PhpdocSingleLineVarSpacingFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority()
    {
        return parent::getPriority();
    }

    public function configure(array $configuration = null)
    {
        parent::configure($configuration);

        /** @var GeneralPhpdocTagRenameFixer $generalPhpdocTagRenameFixer */
        $generalPhpdocTagRenameFixer = $this->proxyFixers['general_phpdoc_tag_rename'];

        try {
            $generalPhpdocTagRenameFixer->configure([
                'fix_annotation' => true,
                'fix_inline' => false,
                'replacements' => $this->configuration['replacements'],
                'case_sensitive' => true,
            ]);
        } catch (InvalidConfigurationException $exception) {
            throw new InvalidFixerConfigurationException(
                $this->getName(),
                Preg::replace('/^\[.+?\] /', '', $exception->getMessage()),
                $exception
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolverRootless('replacements', [
            (new FixerOptionBuilder('replacements', 'Mapping between replaced annotations with new ones.'))
                ->setAllowedTypes(['array'])
                ->setDefault([
                    'property-read' => 'property',
                    'property-write' => 'property',
                    'type' => 'var',
                    'link' => 'see',
                ])
                ->getOption(),
        ], $this->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers()
    {
        return [new GeneralPhpdocTagRenameFixer()];
    }
}
