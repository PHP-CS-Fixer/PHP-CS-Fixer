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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\DeprecatedFixerOption;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use Symfony\Component\OptionsResolver\Options;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class FinalInternalClassFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private const DEFAULTS = [
        'include' => [
            'internal',
        ],
        'exclude' => [
            'final',
            'Entity',
            'ORM\Entity',
            'ORM\Mapping\Entity',
            'Mapping\Entity',
            'Document',
            'ODM\Document',
        ],
    ];

    private bool $checkAnnotations;

    public function __construct()
    {
        parent::__construct();

        $this->checkAnnotations = \PHP_VERSION_ID >= 80000;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->assertConfigHasNoConflicts();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Internal classes should be `final`.',
            [
                new CodeSample("<?php\n/**\n * @internal\n */\nclass Sample\n{\n}\n"),
                new CodeSample(
                    "<?php\n/**\n * @CUSTOM\n */\nclass A{}\n\n/**\n * @CUSTOM\n * @not-fix\n */\nclass B{}\n",
                    [
                        'include' => ['@Custom'],
                        'exclude' => ['@not-fix'],
                    ]
                ),
            ],
            null,
            'Changing classes to `final` might cause code execution to break.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before ProtectedToPrivateFixer, SelfStaticAccessorFixer.
     * Must run after PhpUnitInternalClassFixer.
     */
    public function getPriority(): int
    {
        return 67;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_CLASS);
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
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            if (!$tokens[$index]->isGivenKind(T_CLASS) || !$this->isClassCandidate($tokensAnalyzer, $tokens, $index)) {
                continue;
            }

            // make class 'final'
            $tokens->insertSlices([
                $index => [
                    new Token([T_FINAL, 'final']),
                    new Token([T_WHITESPACE, ' ']),
                ],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $annotationsAsserts = [static function (array $values): bool {
            foreach ($values as $value) {
                if (!\is_string($value) || '' === $value) {
                    return false;
                }
            }

            return true;
        }];

        $annotationsNormalizer = static function (Options $options, array $value): array {
            $newValue = [];
            foreach ($value as $key) {
                if (str_starts_with($key, '@')) {
                    $key = substr($key, 1);
                }

                $newValue[strtolower($key)] = true;
            }

            return $newValue;
        };

        $oldAnnotationIncludeOption = new FixerOptionBuilder('annotation_include', 'Class level PHPDoc annotations tags that must be set in order to fix the class. (case insensitive).');
        $oldAnnotationIncludeOption = $oldAnnotationIncludeOption
            ->setAllowedTypes(['array'])
            ->setAllowedValues($annotationsAsserts)
            ->setDefault(['@internal'])
            ->setNormalizer($annotationsNormalizer)
            ->getOption()
        ;

        $oldAnnotationExcludeOption = new FixerOptionBuilder('annotation_exclude', 'Class level PHPDoc annotations tags that must be omitted to fix the class, even if all of the white list ones are used as well. (case insensitive).');
        $oldAnnotationExcludeOption = $oldAnnotationExcludeOption
            ->setAllowedTypes(['array'])
            ->setAllowedValues($annotationsAsserts)
            ->setDefault([
                '@final',
                '@Entity',
                '@ORM\Entity',
                '@ORM\Mapping\Entity',
                '@Mapping\Entity',
                '@Document',
                '@ODM\Document',
            ])
            ->setNormalizer($annotationsNormalizer)
            ->getOption()
        ;

        return new FixerConfigurationResolver([
            new DeprecatedFixerOption($oldAnnotationIncludeOption, 'Use `include` to configure PHPDoc annotations tags and attributes.'),
            new DeprecatedFixerOption($oldAnnotationExcludeOption, 'Use `exclude` to configure PHPDoc annotations tags and attributes.'),
            (new FixerOptionBuilder('include', 'Class level PHPDoc annotations tags or attributes of which one or more must be set in order to fix the class. (case insensitive).'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues($annotationsAsserts)
                ->setDefault(self::DEFAULTS['include'])
                ->setNormalizer($annotationsNormalizer)
                ->getOption(),
            (new FixerOptionBuilder('exclude', 'Class level PHPDoc annotations tags or attributes which must all be omitted to fix the class. (case insensitive).'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues($annotationsAsserts)
                ->setDefault(self::DEFAULTS['exclude'])
                ->setNormalizer($annotationsNormalizer)
                ->getOption(),
            (new FixerOptionBuilder('consider_absent_docblock_as_internal_class', 'Whether classes without any DocBlock should be fixed to final.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    /**
     * @param int $index T_CLASS index
     */
    private function isClassCandidate(TokensAnalyzer $tokensAnalyzer, Tokens $tokens, int $index): bool
    {
        if ($tokensAnalyzer->isAnonymousClass($index)) {
            return false;
        }

        $modifiers = $tokensAnalyzer->getClassyModifiers($index);

        if (isset($modifiers['final']) || isset($modifiers['abstract'])) {
            return false; // ignore class; it is abstract or already final
        }

        $index = $tokens->getPrevNonWhitespace($index);

        if ($this->checkAnnotations && $tokens[$index]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
            if (!$this->isClassCandidateBasedOnAttribute($tokens, $index)) {
                return false;
            }

            while ($tokens[$index]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
                $index = $tokens->findBlockStart(Tokens::BLOCK_TYPE_ATTRIBUTE, $index);
            }

            $index = $tokens->getPrevNonWhitespace($index);

            return !$tokens[$index]->isGivenKind(T_DOC_COMMENT) || $this->isClassCandidateBasedOnPhpDoc($tokens, $index);
        }

        if ($tokens[$index]->isGivenKind(T_DOC_COMMENT)) {
            return $this->isClassCandidateBasedOnPhpDoc($tokens, $index);
        }

        return $this->configuration['consider_absent_docblock_as_internal_class'];
    }

    private function isClassCandidateBasedOnPhpDoc(Tokens $tokens, int $index): bool
    {
        $doc = new DocBlock($tokens[$index]->getContent());
        $tags = [];

        foreach ($doc->getAnnotations() as $annotation) {
            if (1 !== Preg::match('/@\S+(?=\s|$)/', $annotation->getContent(), $matches)) {
                continue;
            }
            $tag = strtolower(substr(array_shift($matches), 1));

            foreach ($this->configuration['exclude'] as $tagStart => $true) {
                if (str_starts_with($tag, $tagStart)) {
                    return false; // ignore class: class-level PHPDoc contains tag that has been excluded through configuration
                }
            }

            $tags[$tag] = true;
        }

        return $this->isConfiguredAsInclude($tags);
    }

    private function isClassCandidateBasedOnAttribute(Tokens $tokens, int $attributeCloseIndex): bool
    {
        $attributeCandidates = [];

        while ($tokens[$attributeCloseIndex]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
            $attributeStartIndex = $index = $tokens->findBlockStart(Tokens::BLOCK_TYPE_ATTRIBUTE, $attributeCloseIndex);
            $attributeString = '';

            do {
                $index = $tokens->getNextMeaningfulToken($index);

                if (!$tokens[$index]->isGivenKind([T_STRING, T_NS_SEPARATOR])) {
                    break;
                }

                $attributeString .= strtolower($tokens[$index]->getContent());
            } while ($index < $attributeCloseIndex);

            if (isset($this->configuration['exclude'][$attributeString])) {
                return false;
            }

            $attributeCandidates[$attributeString] = true;
            $attributeCloseIndex = $tokens->getPrevNonWhitespace($attributeStartIndex);
        }

        return $this->isConfiguredAsInclude($attributeCandidates);
    }

    /**
     * @param array<string, bool> $attributes
     */
    private function isConfiguredAsInclude(array $attributes): bool
    {
        if (0 === \count($this->configuration['include'])) {
            return true;
        }

        foreach ($this->configuration['include'] as $tag => $true) {
            if (isset($attributes[$tag])) {
                return true;
            }
        }

        return false;
    }

    private function assertConfigHasNoConflicts(): void
    {
        foreach (['include', 'exclude'] as $newConfigKey) {
            $oldConfigKey = 'annotation_'.$newConfigKey;
            $defaults = [];

            foreach (self::DEFAULTS[$newConfigKey] as $foo) {
                $defaults[strtolower($foo)] = true;
            }

            $newConfigIsSet = $this->configuration[$newConfigKey] !== $defaults;
            $oldConfigIsSet = $this->configuration[$oldConfigKey] !== $defaults;

            if ($newConfigIsSet && $oldConfigIsSet) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Configuration cannot contain deprecated option "%s" and new option "%s".', $oldConfigKey, $newConfigKey));
            }

            if ($oldConfigIsSet) {
                $this->configuration[$newConfigKey] = $this->configuration[$oldConfigKey];
                $this->checkAnnotations = false; // run in old mode
            }

            // if ($newConfigIsSet) - only new config is set, all good
            // if (!$newConfigIsSet && !$oldConfigIsSet) - both are set as to default values, all good

            unset($this->configuration[$oldConfigKey]);
        }

        $intersect = array_intersect_assoc($this->configuration['include'], $this->configuration['exclude']);

        if (\count($intersect) > 0) {
            throw new InvalidFixerConfigurationException($this->getName(), sprintf('Annotation cannot be used in both "include" and "exclude" list, got duplicates: "%s".', implode('", "', array_keys($intersect))));
        }
    }
}
