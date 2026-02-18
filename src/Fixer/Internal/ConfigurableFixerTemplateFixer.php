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

namespace PhpCsFixer\Fixer\Internal;

use PhpCsFixer\AbstractDoctrineAnnotationFixer;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\AbstractPhpdocToTypeDeclarationFixer;
use PhpCsFixer\Console\Command\HelpCommand;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Doctrine\Annotation\Tokens as DoctrineAnnotationTokens;
use PhpCsFixer\Fixer\AttributeNotation\OrderedAttributesFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\ClassNotation\FinalInternalClassFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ControlStructure\NoBreakCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\InternalFixerInterface;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLinesBeforeNamespaceFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocTagRenameFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderByValueFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocReturnSelfReferenceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTagTypeFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\StdinFileInfo;
use PhpCsFixer\Tests\AbstractDoctrineAnnotationFixerTestCase;
use PhpCsFixer\Tests\AbstractFixerTest;
use PhpCsFixer\Tests\AbstractFunctionReferenceFixerTest;
use PhpCsFixer\Tests\AbstractProxyFixerTest;
use PhpCsFixer\Tests\Fixer\Whitespace\AbstractNullableTypeDeclarationFixerTestCase;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\FCT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @warning Does not support PHPUnit attributes
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ConfigurableFixerTemplateFixer extends AbstractFixer implements InternalFixerInterface
{
    private const MODIFIERS = [\T_PUBLIC, \T_PROTECTED, \T_PRIVATE, \T_FINAL, \T_ABSTRACT, \T_COMMENT, FCT::T_ATTRIBUTE, FCT::T_READONLY];

    public function getName(): string
    {
        return 'PhpCsFixerInternal/'.parent::getName();
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        $fileInfo = $this->getExampleFixerFile();
        $file = $fileInfo->openFile('r');
        $content = $file->fread($file->getSize());
        if (false === $content) {
            throw new \RuntimeException('Cannot read example file.');
        }
        $tokens = Tokens::fromCode($content);

        $generalPhpdocAnnotationRemoveFixer = new GeneralPhpdocAnnotationRemoveFixer();
        $generalPhpdocAnnotationRemoveFixer->configure([
            'annotations' => [
                'implements',
                'phpstan-type',
            ],
        ]);
        $generalPhpdocAnnotationRemoveFixer->applyFix($fileInfo, $tokens);

        return new FixerDefinition(
            'Configurable Fixers must declare Template type.',
            [
                new CodeSample(
                    $tokens->generateCode(),
                ),
            ],
            null,
            'This rule auto-adjust @implements and @phpstan-type, which heavily change information for SCA.',
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_CLASS);
    }

    public function isRisky(): bool
    {
        return true;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->applyFixForSrc($file, $tokens);
        $this->applyFixForTest($file, $tokens);
    }

    private function applyFixForTest(\SplFileInfo $file, Tokens $tokens): void
    {
        if (!$this->isTestForFixerFile($file)) {
            return;
        }

        $classIndex = $tokens->getNextTokenOfKind(0, [[\T_CLASS]]);

        $docBlockIndex = $this->getDocBlockIndex($tokens, $classIndex);
        if (!$tokens[$docBlockIndex]->isGivenKind(\T_DOC_COMMENT)) {
            $docBlockIndex = $tokens->getNextMeaningfulToken($docBlockIndex);
            $tokens->insertAt($docBlockIndex, [
                new Token([\T_DOC_COMMENT, "/**\n */"]),
                new Token([\T_WHITESPACE, "\n"]),
            ]);
        }

        $doc = new DocBlock($tokens[$docBlockIndex]->getContent());
        if (!$doc->isMultiLine()) {
            throw new \RuntimeException('Non-multiline docblock not expected, please convert it manually!');
        }

        $covers = array_map(
            static function ($annotation): string {
                $parts = explode(' ', $annotation->getContent());

                return trim(array_pop($parts));
            },
            $doc->getAnnotationsOfType(['covers']),
        );
        $covers = array_filter(
            $covers,
            static fn (string $className): bool => !str_contains($className, '\Abstract') && str_ends_with($className, 'Fixer'),
        );

        if (1 !== \count($covers)) {
            throw new \RuntimeException('Non-single covers annotation, please handle manually!');
        }

        $fixerName = array_pop($covers);

        $allowedBaseClasses = [
            AbstractDoctrineAnnotationFixerTestCase::class,
            AbstractNullableTypeDeclarationFixerTestCase::class,
            AbstractFixerTestCase::class,
        ];

        $currentClassName = str_replace('\PhpCsFixer', '\PhpCsFixer\Tests', $fixerName).'Test';
        $baseClassName = false;
        while (true) {
            $baseClassName = get_parent_class($currentClassName);
            if (false === $baseClassName || \in_array($baseClassName, $allowedBaseClasses, true)) {
                break;
            }
            $currentClassName = $baseClassName;
        }

        if (false === $baseClassName) {
            throw new \RuntimeException('Cannot find valid parent class!');
        }

        $baseClassName = self::getShortClassName($baseClassName);

        $expectedAnnotation = \sprintf('extends %s<%s>', $baseClassName, $fixerName);
        $expectedAnnotationPresent = false;
        $expectedTypeImport = \sprintf('phpstan-import-type _AutogeneratedInputConfiguration from %s', $fixerName);
        $expectedTypeImportPresent = false;

        foreach ($doc->getAnnotationsOfType(['extends']) as $annotation) {
            $annotationContent = $annotation->getContent();
            Preg::match('#^.*?(?P<annotation>@extends\s+?(?P<class>\w+)\<[^>]+?\>\S*)\s*?$#s', $annotationContent, $matches);

            if (
                ($matches['class'] ?? '') === $baseClassName
            ) {
                if (($matches['annotation'] ?? '') !== '@'.$expectedAnnotation) {
                    $annotationStart = $annotation->getStart();
                    $annotation->remove();
                    $doc->getLine($annotationStart)->setContent(' * @'.$expectedAnnotation."\n");
                }
                $expectedAnnotationPresent = true;

                break;
            }
        }

        $implements = class_implements($fixerName);
        if (isset($implements[ConfigurableFixerInterface::class])) {
            foreach ($doc->getAnnotationsOfType(['phpstan-import-type']) as $annotation) {
                $annotationContent = $annotation->getContent();

                Preg::match('#^.*?(@'.preg_quote($expectedTypeImport, '\\').')\s*?$#s', $annotationContent, $matches);

                if ([] !== $matches) {
                    $expectedTypeImportPresent = true;
                }
            }
        } else {
            $expectedTypeImportPresent = true;
        }

        if (!$expectedAnnotationPresent || !$expectedTypeImportPresent) {
            $lines = $doc->getLines();
            $lastLine = end($lines);
            \assert(false !== $lastLine);

            $lastLine->setContent(
                ''
                .(!$expectedAnnotationPresent ? ' * @'.$expectedAnnotation."\n" : '')
                .(!$expectedTypeImportPresent ? ' * @'.$expectedTypeImport."\n" : '')
                .$lastLine->getContent(),
            );
        }

        $tokens[$docBlockIndex] = new Token([\T_DOC_COMMENT, $doc->getContent()]);
    }

    private function applyFixForSrc(\SplFileInfo $file, Tokens $tokens): void
    {
        if ($file instanceof StdinFileInfo) {
            $file = $this->getExampleFixerFile();
        }

        $fixer = $this->getFixerForSrcFile($file);

        if (null === $fixer || !$fixer instanceof ConfigurableFixerInterface) {
            return;
        }

        $optionTypeInput = [];
        $optionTypeComputed = [];

        $configurationDefinition = $fixer->getConfigurationDefinition();
        foreach ($configurationDefinition->getOptions() as $option) {
            $optionName = $option->getName();
            $optionExistsAfterNormalization = true;
            $allowed = HelpCommand::getDisplayableAllowedValues($option);
            $allowedAfterNormalization = null;

            // manual handling of normalization
            if (null !== $option->getNormalizer()) {
                if ($fixer instanceof PhpdocOrderByValueFixer && 'annotations' === $optionName) {
                    $allowedAfterNormalization = 'array{'
                        .implode(
                            ', ',
                            array_map(
                                static fn ($value): string => \sprintf("'%s'?: '%s'", $value, strtolower($value)),
                                $allowed[0]->getAllowedValues(),
                            ),
                        )
                        .'}';
                } elseif ($fixer instanceof HeaderCommentFixer && \in_array($optionName, ['header', 'validator'], true)) {
                    // nothing to do
                } elseif ($fixer instanceof BlankLinesBeforeNamespaceFixer && \in_array($optionName, ['min_line_breaks', 'max_line_breaks'], true)) {
                    // nothing to do
                } elseif ($fixer instanceof PhpdocReturnSelfReferenceFixer && 'replacements' === $optionName) {
                    // nothing to do
                } elseif ($fixer instanceof GeneralPhpdocTagRenameFixer && 'replacements' === $optionName) {
                    // nothing to do
                } elseif ($fixer instanceof NoBreakCommentFixer && 'comment_text' === $optionName) {
                    // nothing to do
                } elseif ($fixer instanceof TrailingCommaInMultilineFixer && 'elements' === $optionName) {
                    // nothing to do
                } elseif ($fixer instanceof OrderedAttributesFixer && 'sort_algorithm' === $optionName) {
                    // nothing to do
                } elseif ($fixer instanceof OrderedAttributesFixer && 'order' === $optionName) {
                    $allowedAfterNormalization = 'array<string, int>';
                } elseif ($fixer instanceof FinalInternalClassFixer && \in_array($optionName, ['annotation_include', 'annotation_exclude', 'include', 'exclude'], true)) {
                    $allowedAfterNormalization = 'array<string, string>';
                } elseif ($fixer instanceof PhpdocTagTypeFixer && 'tags' === $optionName) {
                    // nothing to do
                } elseif ($fixer instanceof OrderedImportsFixer && 'sort_algorithm' === $optionName) {
                    // nothing to do
                } elseif ($fixer instanceof DeclareStrictTypesFixer && 'preserve_existing_declaration' === $optionName) {
                    $optionExistsAfterNormalization = false;
                } else {
                    throw new \LogicException(\sprintf('How to handle normalized types of "%s.%s" [`%s`]? Explicit instructions needed!', $fixer->getName(), $optionName, \get_class($fixer)));
                }
            }

            if (\is_array($allowed)) {
                // $allowed are allowed values
                $allowed = array_map(
                    static fn ($value): string => $value instanceof AllowedValueSubset
                        ? \sprintf('list<%s>', implode('|', array_map(static fn ($val) => "'".$val."'", $value->getAllowedValues())))
                        : Utils::toString($value),
                    $allowed,
                );
            } else {
                // $allowed will be allowed types
                $allowed = array_map(
                    static fn ($value): string => Utils::convertArrayTypeToList($value),
                    $option->getAllowedTypes(),
                );
            }

            sort($allowed);
            $allowed = implode('|', $allowed);

            if ('array' === $allowed) {
                $default = $option->getDefault();
                $getTypes = static fn ($values): array => array_unique(array_map(
                    static fn ($val) => \gettype($val),
                    $values,
                ));
                $defaultKeyTypes = $getTypes(array_keys($default));
                $defaultValueTypes = $getTypes(array_values($default));
                $allowed = \sprintf(
                    'array<%s, %s>',
                    [] !== $defaultKeyTypes ? implode('|', $defaultKeyTypes) : 'array-key',
                    [] !== $defaultValueTypes ? implode('|', $defaultValueTypes) : 'mixed',
                );
            }

            $optionTypeInput[] = \sprintf('%s%s: %s', $optionName, $option->hasDefault() ? '?' : '', $allowed);
            if (true === $optionExistsAfterNormalization) {
                $optionTypeComputed[] = \sprintf('%s: %s', $optionName, $allowedAfterNormalization ?? $allowed);
            }
        }

        $expectedTemplateTypeInputAnnotation = \sprintf("phpstan-type _AutogeneratedInputConfiguration array{\n *  %s,\n * }", implode(",\n *  ", $optionTypeInput));
        $expectedTemplateTypeComputedAnnotation = \sprintf("phpstan-type _AutogeneratedComputedConfiguration array{\n *  %s,\n * }", implode(",\n *  ", $optionTypeComputed));
        $expectedImplementsWithTypesAnnotation = 'implements ConfigurableFixerInterface<_AutogeneratedInputConfiguration, _AutogeneratedComputedConfiguration>';

        $classIndex = $tokens->getNextTokenOfKind(0, [[\T_CLASS]]);

        $docBlockIndex = $this->getDocBlockIndex($tokens, $classIndex);
        if (!$tokens[$docBlockIndex]->isGivenKind(\T_DOC_COMMENT)) {
            $docBlockIndex = $tokens->getNextMeaningfulToken($docBlockIndex);
            $tokens->insertAt($docBlockIndex, [
                new Token([\T_DOC_COMMENT, "/**\n */"]),
                new Token([\T_WHITESPACE, "\n"]),
            ]);
        }

        $doc = new DocBlock($tokens[$docBlockIndex]->getContent());
        if (!$doc->isMultiLine()) {
            throw new \RuntimeException('Non-multiline docblock not expected, please convert it manually!');
        }

        $templateTypeInputPresent = false;
        $templateTypeComputedPresent = false;
        $implementsWithTypesPresent = false;

        foreach ($doc->getAnnotationsOfType(['phpstan-type']) as $annotation) {
            $annotationContent = $annotation->getContent();
            $matches = [];
            Preg::match('#^.*?(?P<annotation>@phpstan-type\s+?(?P<typeName>.+?)\s+?(?P<typeContent>.+?))\s*?$#s', $annotationContent, $matches);

            if (
                ($matches['typeName'] ?? '') === '_AutogeneratedInputConfiguration'
            ) {
                if (($matches['annotation'] ?? '') !== '@'.$expectedTemplateTypeInputAnnotation) {
                    $annotationStart = $annotation->getStart();
                    $annotation->remove();
                    $doc->getLine($annotationStart)->setContent(' * @'.$expectedTemplateTypeInputAnnotation."\n");
                }

                $templateTypeInputPresent = true;

                continue;
            }

            if (
                ($matches['typeName'] ?? '') === '_AutogeneratedComputedConfiguration'
            ) {
                if (($matches['annotation'] ?? '') !== '@'.$expectedTemplateTypeComputedAnnotation) {
                    $annotationStart = $annotation->getStart();
                    $annotation->remove();
                    $doc->getLine($annotationStart)->setContent(' * @'.$expectedTemplateTypeComputedAnnotation."\n");
                }

                $templateTypeComputedPresent = true;

                continue;
            }
        }

        foreach ($doc->getAnnotationsOfType(['implements']) as $annotation) {
            $annotationContent = $annotation->getContent();
            Preg::match('#^.*?(?P<annotation>@implements\s+?(?P<class>\w+)\<[^>]+?\>\S*)\s*?$#s', $annotationContent, $matches);

            if (
                ($matches['class'] ?? '') === 'ConfigurableFixerInterface'
            ) {
                if (($matches['annotation'] ?? '') !== '@'.$expectedImplementsWithTypesAnnotation) {
                    $annotationStart = $annotation->getStart();
                    $annotation->remove();
                    $doc->getLine($annotationStart)->setContent(' * @'.$expectedImplementsWithTypesAnnotation."\n");
                }
                $implementsWithTypesPresent = true;

                break;
            }
        }

        if (!$templateTypeInputPresent || !$templateTypeComputedPresent || !$implementsWithTypesPresent) {
            $lines = $doc->getLines();
            $lastLine = end($lines);
            \assert(false !== $lastLine);

            $lastLine->setContent(
                ''
                .(!$templateTypeInputPresent ? ' * @'.$expectedTemplateTypeInputAnnotation."\n" : '')
                .(!$templateTypeComputedPresent ? ' * @'.$expectedTemplateTypeComputedAnnotation."\n" : '')
                .(!$implementsWithTypesPresent ? ' * @'.$expectedImplementsWithTypesAnnotation."\n" : '')
                .$lastLine->getContent(),
            );
        }

        $tokens[$docBlockIndex] = new Token([\T_DOC_COMMENT, $doc->getContent()]);
    }

    private function getDocBlockIndex(Tokens $tokens, int $index): int
    {
        do {
            $index = $tokens->getPrevNonWhitespace($index);

            if ($tokens[$index]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
                $index = $tokens->getPrevTokenOfKind($index, [[\T_ATTRIBUTE]]);
            }
        } while ($tokens[$index]->isGivenKind(self::MODIFIERS));

        return $index;
    }

    private function getExampleFixerFile(): \SplFileInfo
    {
        $reflection = new \ReflectionClass(ConstantCaseFixer::class);
        $fileName = $reflection->getFileName();
        if (false === $fileName) {
            throw new \RuntimeException('Cannot read example fileName.');
        }

        return new \SplFileInfo($fileName);
    }

    private static function getShortClassName(string $longClassName): string
    {
        return \array_slice(explode('\\', $longClassName), -1)[0];
    }

    private function isTestForFixerFile(\SplFileInfo $file): bool
    {
        $basename = $file->getBasename('.php');

        return str_ends_with($basename, 'FixerTest')
            && !\in_array($basename, [
                self::getShortClassName(AbstractFunctionReferenceFixerTest::class),
                self::getShortClassName(AbstractFixerTest::class),
                self::getShortClassName(AbstractProxyFixerTest::class),
            ], true);
    }

    private function getFixerForSrcFile(\SplFileInfo $file): ?FixerInterface
    {
        $basename = $file->getBasename('.php');

        if (!str_ends_with($basename, 'Fixer')) {
            return null;
        }

        Preg::match('#.+src/(.+)\.php#', $file->getPathname(), $matches);
        if (!isset($matches[1])) {
            return null;
        }

        $className = 'PhpCsFixer\\'.str_replace('/', '\\', $matches[1]);

        $implements = class_implements($className);
        if (false === $implements || !isset($implements[ConfigurableFixerInterface::class])) {
            return null;
        }

        if (AbstractPhpdocToTypeDeclarationFixer::class === $className) {
            return new class extends AbstractPhpdocToTypeDeclarationFixer {
                protected function isSkippedType(string $type): bool
                {
                    throw new \LogicException('Not implemented.');
                }

                protected function createTokensFromRawType(string $type): Tokens
                {
                    throw new \LogicException('Not implemented.');
                }

                public function getDefinition(): FixerDefinitionInterface
                {
                    throw new \LogicException('Not implemented.');
                }

                protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
                {
                    throw new \LogicException('Not implemented.');
                }

                public function isCandidate(Tokens $tokens): bool
                {
                    throw new \LogicException('Not implemented.');
                }
            };
        } elseif (AbstractDoctrineAnnotationFixer::class === $className) {
            return new class extends AbstractDoctrineAnnotationFixer {
                protected function isSkippedType(string $type): bool
                {
                    throw new \LogicException('Not implemented.');
                }

                protected function createTokensFromRawType(string $type): Tokens
                {
                    throw new \LogicException('Not implemented.');
                }

                public function getDefinition(): FixerDefinitionInterface
                {
                    throw new \LogicException('Not implemented.');
                }

                protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
                {
                    throw new \LogicException('Not implemented.');
                }

                public function isCandidate(Tokens $tokens): bool
                {
                    throw new \LogicException('Not implemented.');
                }

                public function configure(array $configuration): void
                {
                    // void
                }

                protected function fixAnnotations(DoctrineAnnotationTokens $doctrineAnnotationTokens): void
                {
                    throw new \LogicException('Not implemented.');
                }

                public function getConfigurationDefinition(): FixerConfigurationResolverInterface
                {
                    return $this->createConfigurationDefinition();
                }
            };
        }

        $fixer = new $className();

        \assert($fixer instanceof FixerInterface);

        return $fixer;
    }
}
