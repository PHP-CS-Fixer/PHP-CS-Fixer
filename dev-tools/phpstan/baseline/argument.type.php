<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%d"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/WorkerCommand.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #5 of function sprintf is expected to be string by placeholder #4 ("%%s"), bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $diffs of static method PhpCsFixer\\Console\\Report\\FixReport\\GitlabReporter::getLines() expects list<SebastianBergmann\\Diff\\Diff>, array<SebastianBergmann\\Diff\\Diff> given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Report/FixReport/GitlabReporter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $slices of method PhpCsFixer\\Tokenizer\\Tokens::insertSlices() expects array<int, list<PhpCsFixer\\Tokenizer\\Token>|PhpCsFixer\\Tokenizer\\Token|PhpCsFixer\\Tokenizer\\Tokens>, array<\'\'|int, array{PhpCsFixer\\Tokenizer\\Token, PhpCsFixer\\Tokenizer\\Token}|PhpCsFixer\\Tokenizer\\Token> given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ArrayNotation/YieldFromArrayToYieldsFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, string|false given.',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Fixer/Basic/PsrAutoloadingFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $a of method PhpCsFixer\\Fixer\\ClassNotation\\OrderedClassElementsFixer::sortGroupElements() expects array{start: int, visibility: string, abstract: bool, static: bool, readonly: bool, type: string, name: string, end: int}, array&T given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $b of method PhpCsFixer\\Fixer\\ClassNotation\\OrderedClassElementsFixer::sortGroupElements() expects array{start: int, visibility: string, abstract: bool, static: bool, readonly: bool, type: string, name: string, end: int}, array&T given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $possibleKind of method PhpCsFixer\\Tokenizer\\Token::isGivenKind() expects int|list<int>, list<int|string> given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $offset of function substr expects int, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/NoUnusedImportsFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $name of static method PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitAttributesFixer::toClassConstant() expects non-empty-string, string given.',
    'count' => 4,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitAttributesFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $lines of method PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitTestAnnotationFixer::splitUpDocBlock() expects non-empty-list<PhpCsFixer\\DocBlock\\Line>, list<PhpCsFixer\\DocBlock\\Line> given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $code of static method PhpCsFixer\\Hasher::calculate() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Linter/CachingLinter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$values of function sprintf expects bool|float|int|string|null, array<string, mixed>|bool given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/RuleSet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $json of function json_decode expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/ToolInfo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%d"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #2 ("%%d"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $testClassName of method PhpCsFixer\\Tests\\AutoReview\\ProjectCodeTest::testDataFromDataProviders() expects class-string<PhpCsFixer\\Tests\\TestCase>, class-string given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $testClassName of method PhpCsFixer\\Tests\\AutoReview\\ProjectCodeTest::testDataProvidersAreNonPhpVersionConditional() expects class-string<PhpCsFixer\\Tests\\TestCase>, class-string given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $testClassName of method PhpCsFixer\\Tests\\AutoReview\\ProjectCodeTest::testThatTestDataProvidersAreUsed() expects class-string<PhpCsFixer\\Tests\\TestCase>, class-string given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $dataProviderName of method PhpCsFixer\\Tests\\AutoReview\\ProjectCodeTest::testDataProvidersAreNonPhpVersionConditional() expects non-empty-string, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be string by placeholder #1 ("%%s"), string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Cache/FileHandlerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be string by placeholder #1 ("%%s"), class-string<Throwable>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Console/Output/ErrorOutputTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $expectedOutputClass of method PhpCsFixer\\Tests\\Console\\Output\\Progress\\ProgressOutputFactoryTest::testValidProcessOutputIsCreated() expects class-string<Throwable>, string given.',
    'count' => 4,
    'path' => __DIR__ . '/../../../tests/Console/Output/Progress/ProgressOutputFactoryTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $minimum of method PhpCsFixer\\Tests\\FixerDefinition\\VersionSpecificationTest::testConstructorRejectsInvalidValues() expects int<1, max>|null, -1 given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/FixerDefinition/VersionSpecificationTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $minimum of method PhpCsFixer\\Tests\\FixerDefinition\\VersionSpecificationTest::testConstructorRejectsInvalidValues() expects int<1, max>|null, 0 given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/FixerDefinition/VersionSpecificationTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $maximum of method PhpCsFixer\\Tests\\FixerDefinition\\VersionSpecificationTest::testConstructorRejectsInvalidValues() expects int<1, max>|null, -1 given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/FixerDefinition/VersionSpecificationTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $maximum of method PhpCsFixer\\Tests\\FixerDefinition\\VersionSpecificationTest::testConstructorRejectsInvalidValues() expects int<1, max>|null, 0 given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/FixerDefinition/VersionSpecificationTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%d"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/RuleSet/Sets/AbstractSetTestCase.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #2 ("%%d"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/RuleSet/Sets/AbstractSetTestCase.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $input of method PhpCsFixer\\Tests\\Runner\\Parallel\\ProcessFactoryTest::testCreate() expects array<string, string>, array<string, string|true> given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Runner/Parallel/ProcessFactoryTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $input of method PhpCsFixer\\Tests\\Runner\\Parallel\\ProcessFactoryTest::testGetCommandArgs() expects array<string, string>, array<string, string|true> given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Runner/Parallel/ProcessFactoryTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $input of method PhpCsFixer\\Tests\\Runner\\Parallel\\ProcessFactoryTest::testGetCommandArgs() expects array<string, string>, array<string, true> given.',
    'count' => 2,
    'path' => __DIR__ . '/../../../tests/Runner/Parallel/ProcessFactoryTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 of function sprintf is expected to be int by placeholder #3 ("%%d"), int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%d"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Test/AbstractTransformerTestCase.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $caseSensitive of method PhpCsFixer\\Tests\\Tokenizer\\TokenTest::testIsKeyCaseSensitive() expects bool|list<bool>, array{1: false} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Tokenizer/TokenTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $expected of method PhpCsFixer\\Tests\\Tokenizer\\TokensAnalyzerTest::testIsBinaryOperator() expects list<int>, array{0: 3, 5: false} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Tokenizer/TokensAnalyzerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #6 $caseSensitive of method PhpCsFixer\\Tests\\Tokenizer\\TokensTest::testFindSequence() expects bool|list<bool>, array{1: false} given.',
    'count' => 2,
    'path' => __DIR__ . '/../../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #6 $caseSensitive of method PhpCsFixer\\Tests\\Tokenizer\\TokensTest::testFindSequence() expects bool|list<bool>, array{1: true} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #6 $caseSensitive of method PhpCsFixer\\Tests\\Tokenizer\\TokensTest::testFindSequence() expects bool|list<bool>, array{2: false} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Tokenizer/TokensTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
