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

use PhpCsFixer\Tests\AutoReview\CommandTest;
use PhpCsFixer\Tests\AutoReview\DocumentationTest;
use PhpCsFixer\Tests\AutoReview\FixerFactoryTest;
use PhpCsFixer\Tests\AutoReview\ProjectCodeTest;
use PhpCsFixer\Tests\Console\Command\DescribeCommandTest;
use PhpCsFixer\Tests\Console\Command\HelpCommandTest;
use PhpCsFixer\Tests\Console\Command\SelfUpdateCommandTest;
use PhpCsFixer\Tests\Console\Output\Progress\DotsOutputTest;
use PhpCsFixer\Tests\DocBlock\TypeExpressionTest;
use PhpCsFixer\Tests\Documentation\FixerDocumentGeneratorTest;
use PhpCsFixer\Tests\Fixer\Alias\EregToPregFixerTest;
use PhpCsFixer\Tests\Fixer\ArrayNotation\ArraySyntaxFixerTest;
use PhpCsFixer\Tests\Fixer\ArrayNotation\TrimArraySpacesFixerTest;
use PhpCsFixer\Tests\Fixer\Basic\BracesFixerTest;
use PhpCsFixer\Tests\Fixer\Basic\BracesPositionFixerTest;
use PhpCsFixer\Tests\Fixer\Basic\CurlyBracesPositionFixerTest;
use PhpCsFixer\Tests\Fixer\Basic\EncodingFixerTest;
use PhpCsFixer\Tests\Fixer\Basic\PsrAutoloadingFixerTest;
use PhpCsFixer\Tests\Fixer\CastNotation\LowercaseCastFixerTest;
use PhpCsFixer\Tests\Fixer\ClassNotation\FinalClassFixerTest;
use PhpCsFixer\Tests\Fixer\Comment\NoEmptyCommentFixerTest;
use PhpCsFixer\Tests\Fixer\ConstantNotation\NativeConstantInvocationFixerTest;
use PhpCsFixer\Tests\Fixer\ControlStructure\NoBreakCommentFixerTest;
use PhpCsFixer\Tests\Fixer\ControlStructure\NoUnneededControlParenthesesFixerTest;
use PhpCsFixer\Tests\Fixer\ControlStructure\NoUselessElseFixerTest;
use PhpCsFixer\Tests\Fixer\ControlStructure\YodaStyleFixerTest;
use PhpCsFixer\Tests\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixerTest;
use PhpCsFixer\Tests\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixerTest;
use PhpCsFixer\Tests\Fixer\FunctionNotation\PhpdocToParamTypeFixerTest;
use PhpCsFixer\Tests\Fixer\Import\OrderedImportsFixerTest;
use PhpCsFixer\Tests\Fixer\LanguageConstruct\SingleSpaceAfterConstructFixerTest;
use PhpCsFixer\Tests\Fixer\LanguageConstruct\SingleSpaceAroundConstructFixerTest;
use PhpCsFixer\Tests\Fixer\Operator\StandardizeIncrementFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocInlineTagNormalizerFixerTest;
use PhpCsFixer\Tests\Fixer\Phpdoc\PhpdocTypesOrderFixerTest;
use PhpCsFixer\Tests\Fixer\PhpUnit\PhpUnitStrictFixerTest;
use PhpCsFixer\Tests\Fixer\PhpUnit\PhpUnitTargetVersionTest;
use PhpCsFixer\Tests\Fixer\StringNotation\SingleQuoteFixerTest;
use PhpCsFixer\Tests\Fixer\Whitespace\MethodChainingIndentationFixerTest;
use PhpCsFixer\Tests\Fixer\Whitespace\SpacesInsideParenthesesFixerTest;
use PhpCsFixer\Tests\FixerNameValidatorTest;
use PhpCsFixer\Tests\Tokenizer\Analyzer\AlternativeSyntaxAnalyzerTest;
use PhpCsFixer\Tests\Tokenizer\Analyzer\AttributeAnalyzerTest;
use PhpCsFixer\Tests\Tokenizer\Analyzer\ClassyAnalyzerTest;
use PhpCsFixer\Tests\Tokenizer\Analyzer\FunctionsAnalyzerTest;
use PhpCsFixer\Tests\Tokenizer\TokensAnalyzerTest;
use PhpCsFixer\Tests\Tokenizer\TokenTest;
use PhpCsFixer\Tests\Tokenizer\Transformer\ReturnRefTransformerTest;
use PhpCsFixer\Tests\UtilsTest;

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

        $exceptions = array_map(
            static function (Closure $dataProvider): string {
                $reflection = new ReflectionFunction($dataProvider);

                return $reflection->getClosureCalledClass()->getName().'::'.$reflection->getName();
            },
            [ // should only shrink
                // because of Serialization
                CommandTest::provideCommandHasNameConstCases(...),
                DocumentationTest::provideFixerDocumentationFileIsUpToDateCases(...),
                FixerFactoryTest::providePriorityIntegrationTestFilesAreListedInPriorityGraphCases(...),
                ProjectCodeTest::provideDataProviderMethodCases(...),
                DescribeCommandTest::provideExecuteOutputCases(...),
                HelpCommandTest::provideGetDisplayableAllowedValuesCases(...),
                FixerDocumentGeneratorTest::provideGenerateRuleSetsDocumentationCases(...),
                EncodingFixerTest::provideFixCases(...),
                UtilsTest::provideStableSortCases(...),
                // because of more than one duplicate
                SelfUpdateCommandTest::provideExecuteCases(...),
                DotsOutputTest::provideDotsProgressOutputCases(...),
                TrimArraySpacesFixerTest::provideFixCases(...),
                BracesFixerTest::provideFixClassyBracesCases(...),
                BracesPositionFixerTest::provideFixCases(...),
                CurlyBracesPositionFixerTest::provideFixCases(...),
                FinalClassFixerTest::provideFix80Cases(...),
                PsrAutoloadingFixerTest::provideFixCases(...),
                LowercaseCastFixerTest::provideFixCases(...),
                FinalClassFixerTest::provideFix82Cases(...),
                NoUnneededControlParenthesesFixerTest::provideFixAllCases(...),
                NoUselessElseFixerTest::provideFixIfElseIfElseCases(...),
                NoUselessElseFixerTest::provideFixIfElseCases(...),
                YodaStyleFixerTest::provideFixCases(...),
                YodaStyleFixerTest::providePHP71Cases(...),
                DoctrineAnnotationArrayAssignmentFixerTest::provideFixCases(...),
                DoctrineAnnotationArrayAssignmentFixerTest::provideFixWithColonCases(...),
                DoctrineAnnotationSpacesFixerTest::provideFixAroundParenthesesOnlyCases(...),
                SingleSpaceAfterConstructFixerTest::provideFixWithUseCases(...),
                SingleSpaceAroundConstructFixerTest::provideFixWithUseCases(...),
                PhpUnitStrictFixerTest::provideFixCases(...),
                PhpdocInlineTagNormalizerFixerTest::provideFixCases(...),
                AlternativeSyntaxAnalyzerTest::provideItThrowsOnInvalidAlternativeSyntaxBlockStartIndexCases(...),
                FunctionsAnalyzerTest::provideIsGlobalFunctionCallCases(...),
                TokenTest::provideIsMagicConstantCases(...),
                TokensAnalyzerTest::provideIsBinaryOperatorCases(...),
                // because of one duplicate
                TypeExpressionTest::provideGetTypesCases(...),
                TypeExpressionTest::provideGetConstTypesCases(...),
                TypeExpressionTest::provideParseInvalidExceptionCases(...),
                FixerNameValidatorTest::provideIsValidCases(...),
                EregToPregFixerTest::provideFixCases(...),
                ArraySyntaxFixerTest::provideFixCases(...),
                BracesFixerTest::provideFixMultiLineStructuresCases(...),
                BracesFixerTest::provideFunctionImportCases(...),
                NoEmptyCommentFixerTest::provideFixCases(...),
                NativeConstantInvocationFixerTest::provideFixWithDefaultConfigurationCases(...),
                NoBreakCommentFixerTest::provideFixCases(...),
                NoBreakCommentFixerTest::provideFixWithDifferentCommentTextCases(...),
                NoBreakCommentFixerTest::provideFixWithDifferentLineEndingCases(...),
                DoctrineAnnotationSpacesFixerTest::provideFixAllCases(...),
                DoctrineAnnotationSpacesFixerTest::provideFixAroundCommasOnlyCases(...),
                PhpdocToParamTypeFixerTest::provideFixCases(...),
                OrderedImportsFixerTest::provideFixCases(...),
                StandardizeIncrementFixerTest::provideFixCases(...),
                PhpUnitTargetVersionTest::provideFulfillsCases(...),
                NoSuperfluousPhpdocTagsFixerTest::provideFixCases(...),
                PhpdocTypesOrderFixerTest::provideFixWithNullFirstCases(...),
                SingleQuoteFixerTest::provideFixCases(...),
                MethodChainingIndentationFixerTest::provideFixCases(...),
                SpacesInsideParenthesesFixerTest::provideDefaultFixCases(...),
                SpacesInsideParenthesesFixerTest::provideSpacesFixCases(...),
                AttributeAnalyzerTest::provideIsAttributeCases(...),
                ClassyAnalyzerTest::provideIsClassyInvocationCases(...),
                ReturnRefTransformerTest::provideProcessCases(...),
            ],
        );
        if (in_array($testClassName.'::'.$method->getName(), $exceptions, true)) {
            continue;
        }

        $alreadyFoundCases = [];
        foreach ($method->invoke($method->getDeclaringClass()->newInstanceWithoutConstructor()) as $candidateKey => $candidateData) {
            $candidateData = serialize($candidateData);
            $foundInDuplicates = false;
            foreach ($alreadyFoundCases as $caseKey => $caseData) {
                if ($candidateData === $caseData) {
                    printf(
                        'Duplicate in %s::%s: %s and %s.'.PHP_EOL,
                        $testClassName,
                        $method->getName(),
                        is_int($caseKey) ? '#'.$caseKey : '"'.$caseKey.'"',
                        is_int($candidateKey) ? '#'.$candidateKey : '"'.$candidateKey.'"',
                    );
                    $duplicatesFound = true;
                    $foundInDuplicates = true;
                }
            }
            if (!$foundInDuplicates) {
                $alreadyFoundCases[$candidateKey] = $candidateData;
            }
        }
    }
}

exit($duplicatesFound ? 1 : 0);
