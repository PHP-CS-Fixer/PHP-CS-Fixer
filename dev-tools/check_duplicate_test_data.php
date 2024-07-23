#!/usr/bin/env php
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

require_once __DIR__.'/../vendor/autoload.php';

$testClassNames = array_filter(
    array_keys(require __DIR__.'/../vendor/composer/autoload_classmap.php'),
    static fn (string $className): bool => str_starts_with($className, 'PhpCsFixer\Tests\\')
);

if ([] === $testClassNames) {
    echo 'Run: composer dump-autoload --optimize --working-dir=', realpath(__DIR__.'/..'), PHP_EOL;

    exit(1);
}

$duplicatesFound = false;

foreach ($testClassNames as $testClassName) {
    if (!str_ends_with($testClassName, 'Test')) {
        continue;
    }

    foreach ((new ReflectionClass($testClassName))->getMethods() as $method) {
        if (!$method->isPublic()) {
            continue;
        }
        if ($method->getDeclaringClass()->getName() !== $testClassName) {
            continue;
        }
        if (!str_starts_with($method->getName(), 'provide')) {
            continue;
        }

        $exceptionsBecauseOfSerialization = [ // should only shrink
            'PhpCsFixer\Tests\AutoReview\CommandTest::provideCommandHasNameConstCases',
            'PhpCsFixer\Tests\AutoReview\DocumentationTest::provideFixerDocumentationFileIsUpToDateCases',
            'PhpCsFixer\Tests\AutoReview\FixerFactoryTest::providePriorityIntegrationTestFilesAreListedInPriorityGraphCases',
            'PhpCsFixer\Tests\AutoReview\ProjectCodeTest::provideDataProviderMethodCases',
            'PhpCsFixer\Tests\Console\Command\DescribeCommandTest::provideExecuteOutputCases',
            'PhpCsFixer\Tests\Console\Command\HelpCommandTest::provideGetDisplayableAllowedValuesCases',
            'PhpCsFixer\Tests\Documentation\FixerDocumentGeneratorTest::provideGenerateRuleSetsDocumentationCases',
            'PhpCsFixer\Tests\Fixer\Basic\EncodingFixerTest::provideFixCases',
            'PhpCsFixer\Tests\UtilsTest::provideStableSortCases',
        ];
        if (in_array($testClassName.'::'.$method->getName(), $exceptionsBecauseOfSerialization, true)) {
            continue;
        }

        $exceptionsBecauseOfMoreThanOneDuplicate = [ // should only shrink
            'PhpCsFixer\Tests\Console\Command\SelfUpdateCommandTest::provideExecuteCases',
            'PhpCsFixer\Tests\Console\Output\Progress\DotsOutputTest::provideDotsProgressOutputCases',
            'PhpCsFixer\Tests\Fixer\ArrayNotation\TrimArraySpacesFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\Basic\BracesFixerTest::provideFixClassyBracesCases',
            'PhpCsFixer\Tests\Fixer\Basic\BracesPositionFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\Basic\CurlyBracesPositionFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\ClassNotation\FinalClassFixerTest::provideFix80Cases',
            'PhpCsFixer\Tests\Fixer\Basic\PsrAutoloadingFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\CastNotation\LowercaseCastFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\ClassNotation\FinalClassFixerTest::provideFix82Cases',
            'PhpCsFixer\Tests\Fixer\ControlStructure\NoUnneededControlParenthesesFixerTest::provideFixAllCases',
            'PhpCsFixer\Tests\Fixer\ControlStructure\NoUselessElseFixerTest::provideFixIfElseIfElseCases',
            'PhpCsFixer\Tests\Fixer\ControlStructure\NoUselessElseFixerTest::provideFixIfElseCases',
            'PhpCsFixer\Tests\Fixer\ControlStructure\YodaStyleFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\ControlStructure\YodaStyleFixerTest::providePHP71Cases',
            'PhpCsFixer\Tests\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixerTest::provideFixWithColonCases',
            'PhpCsFixer\Tests\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixerTest::provideFixAroundParenthesesOnlyCases',
            'PhpCsFixer\Tests\Fixer\LanguageConstruct\SingleSpaceAfterConstructFixerTest::provideFixWithUseCases',
            'PhpCsFixer\Tests\Fixer\LanguageConstruct\SingleSpaceAroundConstructFixerTest::provideFixWithUseCases',
            'PhpCsFixer\Tests\Fixer\PhpUnit\PhpUnitStrictFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocInlineTagNormalizerFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Tokenizer\Analyzer\AlternativeSyntaxAnalyzerTest::provideItThrowsOnInvalidAlternativeSyntaxBlockStartIndexCases',
            'PhpCsFixer\Tests\Tokenizer\Analyzer\FunctionsAnalyzerTest::provideIsGlobalFunctionCallCases',
            'PhpCsFixer\Tests\Tokenizer\TokenTest::provideIsMagicConstantCases',
            'PhpCsFixer\Tests\Tokenizer\TokensAnalyzerTest::provideIsBinaryOperatorCases',
        ];
        if (in_array($testClassName.'::'.$method->getName(), $exceptionsBecauseOfMoreThanOneDuplicate, true)) {
            continue;
        }

        $exceptionsBecauseOfOneDuplicate = [ // should only shrink
            'PhpCsFixer\Tests\DocBlock\TypeExpressionTest::provideGetTypesCases',
            'PhpCsFixer\Tests\DocBlock\TypeExpressionTest::provideGetConstTypesCases',
            'PhpCsFixer\Tests\DocBlock\TypeExpressionTest::provideParseInvalidExceptionCases',
            'PhpCsFixer\Tests\FixerNameValidatorTest::provideIsValidCases',
            'PhpCsFixer\Tests\Fixer\Alias\EregToPregFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\ArrayNotation\ArraySyntaxFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\Basic\BracesFixerTest::provideFixMultiLineStructuresCases',
            'PhpCsFixer\Tests\Fixer\Basic\BracesFixerTest::provideFunctionImportCases',
            'PhpCsFixer\Tests\Fixer\Comment\NoEmptyCommentFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\ConstantNotation\NativeConstantInvocationFixerTest::provideFixWithDefaultConfigurationCases',
            'PhpCsFixer\Tests\Fixer\ControlStructure\NoBreakCommentFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\ControlStructure\NoBreakCommentFixerTest::provideFixWithDifferentCommentTextCases',
            'PhpCsFixer\Tests\Fixer\ControlStructure\NoBreakCommentFixerTest::provideFixWithDifferentLineEndingCases',
            'PhpCsFixer\Tests\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixerTest::provideFixAllCases',
            'PhpCsFixer\Tests\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixerTest::provideFixAroundCommasOnlyCases',
            'PhpCsFixer\Tests\Fixer\FunctionNotation\PhpdocToParamTypeFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\Import\OrderedImportsFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\Operator\StandardizeIncrementFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\PhpUnit\PhpUnitTargetVersionTest::provideFulfillsCases',
            'PhpCsFixer\Tests\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocTypesOrderFixerTest::provideFixWithNullFirstCases',
            'PhpCsFixer\Tests\Fixer\StringNotation\SingleQuoteFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\Whitespace\MethodChainingIndentationFixerTest::provideFixCases',
            'PhpCsFixer\Tests\Fixer\Whitespace\SpacesInsideParenthesesFixerTest::provideDefaultFixCases',
            'PhpCsFixer\Tests\Fixer\Whitespace\SpacesInsideParenthesesFixerTest::provideSpacesFixCases',
            'PhpCsFixer\Tests\Tokenizer\Analyzer\AttributeAnalyzerTest::provideIsAttributeCases',
            'PhpCsFixer\Tests\Tokenizer\Analyzer\ClassyAnalyzerTest::provideIsClassyInvocationCases',
            'PhpCsFixer\Tests\Tokenizer\Transformer\ReturnRefTransformerTest::provideProcessCases',
        ];
        if (in_array($testClassName.'::'.$method->getName(), $exceptionsBecauseOfOneDuplicate, true)) {
            continue;
        }

        $duplicates = [];
        foreach ($method->invoke($method->getDeclaringClass()->newInstanceWithoutConstructor()) as $key => $data) {
            $data = serialize($data);
            $foundInDuplicates = false;
            foreach ($duplicates as $duplicateKey => $duplicateData) {
                if ($data === $duplicateData) {
                    printf(
                        'Duplicate in %s::%s: %s and %s.'.PHP_EOL,
                        $testClassName,
                        $method->getName(),
                        is_int($duplicateKey) ? '#'.$duplicateKey : '"'.$duplicateKey.'"',
                        is_int($key) ? '#'.$key : '"'.$key.'"',
                    );
                    $duplicatesFound = true;
                    $foundInDuplicates = true;
                }
            }
            if (!$foundInDuplicates) {
                $duplicates[$key] = $data;
            }
        }
    }
}

exit($duplicatesFound ? 1 : 0);
