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
    'rawMessage' => 'Parameter #2 $offset of function substr expects int, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function str_replace expects array<string>|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $offset of function substr expects int, int|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Documentation/RuleSetDocumentationGenerator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $index of method PhpCsFixer\\Tokenizer\\Tokens::getNextTokenOfKind() expects int, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Alias/PowToExponentiationFixer.php',
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
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, int|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $offset of function substr expects int, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/NoUnusedImportsFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $indices of method PhpCsFixer\\Fixer\\Import\\OrderedImportsFixer::sortByAlgorithm() expects array<int, array{namespace: non-empty-string, startIndex: int, endIndex: int, importType: \'class\'|\'const\'|\'function\', group: bool}>, array<\'\'|int, array{namespace: string, startIndex: int|null, endIndex: int, importType: \'class\'|\'const\'|\'function\', group: bool}> given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $indices of method PhpCsFixer\\Fixer\\Import\\OrderedImportsFixer::sortByAlgorithm() expects array<int, array{namespace: non-empty-string, startIndex: int, endIndex: int, importType: \'class\'|\'const\'|\'function\', group: bool}>, non-empty-array<\'\'|int, array{namespace: string, startIndex: int|null, endIndex: int, importType: \'class\'|\'const\'|\'function\', group: bool}> given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $offset of function substr expects int, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $name of static method PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitAttributesFixer::toClassConstant() expects non-empty-string, string given.',
    'count' => 5,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitAttributesFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $lines of method PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitTestAnnotationFixer::splitUpDocBlock() expects non-empty-list<PhpCsFixer\\DocBlock\\Line>, list<PhpCsFixer\\DocBlock\\Line> given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $offset of function substr expects int, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/NoBlankLinesAfterPhpdocFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $str of function preg_quote expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $parentScopeEndIndex of method PhpCsFixer\\Fixer\\Whitespace\\ArrayIndentationFixer::findExpressionEndIndex() expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
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
    'rawMessage' => 'Parameter #1 $analysis of static method PhpCsFixer\\Tokenizer\\Analyzer\\ControlCaseStructuresAnalyzer::buildControlCaseStructureAnalysis() expects array{kind: int, index: int, open: int, end: int, cases: list<array{index: int, open: int}>, default: array{index: int, open: int}|null}, non-empty-array<literal-string&non-falsy-string, mixed> given.',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $name of static method PhpCsFixer\\Tokenizer\\Processor\\ImportProcessor::tokenizeName() expects non-empty-string, string given.',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Tokenizer/Transformer/NameQualifiedTransformer.php',
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
    'rawMessage' => 'Parameter #1 $expected of method PhpCsFixer\\Tests\\Console\\ConfigurationResolverTest::testResolveIntersectionOfPaths() expects Exception|list<string>, array<string> given.',
    'count' => 10,
    'path' => __DIR__ . '/../../../tests/Console/ConfigurationResolverTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $expectedClass of method PhpCsFixer\\Tests\\Console\\ConfigurationResolverTest::testResolveConfigFileChooseFile() expects class-string<PhpCsFixer\\ConfigInterface>, string given.',
    'count' => 5,
    'path' => __DIR__ . '/../../../tests/Console/ConfigurationResolverTest.php',
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
    'rawMessage' => 'Parameter #1 $wrongConfig of method PhpCsFixer\\Tests\\Fixer\\Alias\\NoMixedEchoPrintFixerTest::testInvalidConfiguration() expects array<string, mixed>, array<int, int> given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Alias/NoMixedEchoPrintFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $configuration of method PhpCsFixer\\Tests\\Fixer\\Alias\\RandomApiMigrationFixerTest::testInvalidConfiguration() expects array{replacements?: array<string, string>}, array{replacements: array{rand: null}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Alias/RandomApiMigrationFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $configuration of method PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\GeneralAttributeRemoveFixerTest::testFix() expects array{attributes?: list<class-string>}, array{attributes: array{\'A\\\\B\\\\Bar\', \'Test\\\\AB\\\\Baz\', \'A\\\\B\\\\Quux\', \'A\\\\B\\\\Baz\', \'A\\\\B\\\\Foo\', \'\\\\AB\\\\Baz\'}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/AttributeNotation/GeneralAttributeRemoveFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $configuration of method PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\GeneralAttributeRemoveFixerTest::testFix() expects array{attributes?: list<class-string>}, array{attributes: array{\'A\\\\B\\\\Bar\', \'Test\\\\A\\\\B\\\\Quux\'}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/AttributeNotation/GeneralAttributeRemoveFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $configuration of method PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\GeneralAttributeRemoveFixerTest::testFix() expects array{attributes?: list<class-string>}, array{attributes: array{\'A\\\\B\\\\Foo\', \'Test\\\\A\\\\B\\\\Quux\', \'A\\\\B\\\\Baz\'}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/AttributeNotation/GeneralAttributeRemoveFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $configuration of method PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\GeneralAttributeRemoveFixerTest::testFix() expects array{attributes?: list<class-string>}, array{attributes: array{\'A\\\\B\\\\Foo\', \'Test\\\\Corge\'}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/AttributeNotation/GeneralAttributeRemoveFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $configuration of method PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\GeneralAttributeRemoveFixerTest::testFix() expects array{attributes?: list<class-string>}, array{attributes: array{\'A\\\\B\\\\Foo\', \'\\\\A\\\\B\\\\Qux\', \'A\\\\B\\\\Baz\', \'Test\\\\A\\\\B\\\\Quux\', \'A\\\\B\\\\Bar\', \'Test\\\\Corge\'}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/AttributeNotation/GeneralAttributeRemoveFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $configuration of method PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\GeneralAttributeRemoveFixerTest::testFix() expects array{attributes?: list<class-string>}, array{attributes: array{\'A\\\\B\\\\Foo\', \'\\\\A\\\\B\\\\Qux\'}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/AttributeNotation/GeneralAttributeRemoveFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $configuration of method PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\GeneralAttributeRemoveFixerTest::testFix() expects array{attributes?: list<class-string>}, array{attributes: array{\'Test\\\\A\\\\B\\\\Quux\', \'A\\\\B\\\\Bar\', \'Test\\\\Corge\', \'A\\\\B\\\\Baz\', \'A\\\\B\\\\Foo\', \'\\\\A\\\\B\\\\Qux\'}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/AttributeNotation/GeneralAttributeRemoveFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $configuration of method PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\GeneralAttributeRemoveFixerTest::testFix() expects array{attributes?: list<class-string>}, array{attributes: array{\'Test\\\\Corge\', \'\\\\A\\\\B\\\\Qux\'}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/AttributeNotation/GeneralAttributeRemoveFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $configuration of method PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\GeneralAttributeRemoveFixerTest::testFix() expects array{attributes?: list<class-string>}, array{attributes: array{\'\\\\A\\\\B\\\\Qux\', \'\\\\Corge\', \'A\\\\B\\\\Bar\'}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/AttributeNotation/GeneralAttributeRemoveFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $config of method PhpCsFixer\\Tests\\Fixer\\CastNotation\\CastSpacesFixerTest::testInvalidConfiguration() expects array{space?: \'none\'|\'single\'}, array{space: \'double\'} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/CastNotation/CastSpacesFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $config of method PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ClassDefinitionFixerTest::testInvalidConfiguration() expects array{inline_constructor_arguments?: bool, multi_line_extends_each_single_line?: bool, single_item_single_line?: bool, single_line?: bool, space_before_parenthesis?: bool}, array{single_line: \'z\'} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ClassNotation/ClassDefinitionFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $expected of method PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ClassDefinitionFixerTest::testClassyInheritanceInfo() expects array{start: int, classy: int, open: int, extends: array{start: int, count: int, multiLine: bool}|false, implements: array{start: int, count: int, multiLine: bool}|false, anonymousClass: bool, final: int|false, abstract: int|false, ...}, array{start: 12, count: 1, multiLine: true} given.',
    'count' => 2,
    'path' => __DIR__ . '/../../../tests/Fixer/ClassNotation/ClassDefinitionFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $expected of method PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ClassDefinitionFixerTest::testClassyInheritanceInfo() expects array{start: int, classy: int, open: int, extends: array{start: int, count: int, multiLine: bool}|false, implements: array{start: int, count: int, multiLine: bool}|false, anonymousClass: bool, final: int|false, abstract: int|false, ...}, array{start: 16, count: 2, multiLine: false} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ClassNotation/ClassDefinitionFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $expected of method PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ClassDefinitionFixerTest::testClassyInheritanceInfo() expects array{start: int, classy: int, open: int, extends: array{start: int, count: int, multiLine: bool}|false, implements: array{start: int, count: int, multiLine: bool}|false, anonymousClass: bool, final: int|false, abstract: int|false, ...}, array{start: 36, count: 2, multiLine: false} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ClassNotation/ClassDefinitionFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $expected of method PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ClassDefinitionFixerTest::testClassyInheritanceInfo() expects array{start: int, classy: int, open: int, extends: array{start: int, count: int, multiLine: bool}|false, implements: array{start: int, count: int, multiLine: bool}|false, anonymousClass: bool, final: int|false, abstract: int|false, ...}, array{start: 5, count: 1, multiLine: false} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ClassNotation/ClassDefinitionFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $expected of method PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ClassDefinitionFixerTest::testClassyInheritanceInfo() expects array{start: int, classy: int, open: int, extends: array{start: int, count: int, multiLine: bool}|false, implements: array{start: int, count: int, multiLine: bool}|false, anonymousClass: bool, final: int|false, abstract: int|false, ...}, array{start: 5, count: 2, multiLine: true} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ClassNotation/ClassDefinitionFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $expected of method PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ClassDefinitionFixerTest::testClassyInheritanceInfo() expects array{start: int, classy: int, open: int, extends: array{start: int, count: int, multiLine: bool}|false, implements: array{start: int, count: int, multiLine: bool}|false, anonymousClass: bool, final: int|false, abstract: int|false, ...}, array{start: 5, count: 3, multiLine: false} given.',
    'count' => 3,
    'path' => __DIR__ . '/../../../tests/Fixer/ClassNotation/ClassDefinitionFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $expected of method PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ClassDefinitionFixerTest::testClassyInheritanceInfoPre80() expects array{start: int, classy: int, open: int, extends: array{start: int, count: int, multiLine: bool}|false, implements: array{start: int, count: int, multiLine: bool}|false, anonymousClass: bool, final: int|false, abstract: int|false, ...}, array{start: 36, count: 2, multiLine: true} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ClassNotation/ClassDefinitionFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $config of method PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ModifierKeywordsFixerTest::testInvalidConfiguration() expects array{elements?: list<\'const\'|\'method\'|\'property\'>}, array{elements: array{\'_unknown_\'}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ClassNotation/ModifierKeywordsFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $config of method PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ModifierKeywordsFixerTest::testInvalidConfiguration() expects array{elements?: list<\'const\'|\'method\'|\'property\'>}, array{elements: array{null}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ClassNotation/ModifierKeywordsFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\Comment\\HeaderCommentFixerTest::testInvalidConfiguration() expects array{comment_type?: \'comment\'|\'PHPDoc\', header: string, location?: \'after_declare_strict\'|\'after_open\', separate?: \'both\'|\'bottom\'|\'none\'|\'top\', validator?: string|null}|null, array{header: \'\', comment_type: \'foo\'} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Comment/HeaderCommentFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\Comment\\HeaderCommentFixerTest::testInvalidConfiguration() expects array{comment_type?: \'comment\'|\'PHPDoc\', header: string, location?: \'after_declare_strict\'|\'after_open\', separate?: \'both\'|\'bottom\'|\'none\'|\'top\', validator?: string|null}|null, array{header: \'\', comment_type: stdClass} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Comment/HeaderCommentFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\Comment\\HeaderCommentFixerTest::testInvalidConfiguration() expects array{comment_type?: \'comment\'|\'PHPDoc\', header: string, location?: \'after_declare_strict\'|\'after_open\', separate?: \'both\'|\'bottom\'|\'none\'|\'top\', validator?: string|null}|null, array{header: \'\', location: stdClass} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Comment/HeaderCommentFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\Comment\\HeaderCommentFixerTest::testInvalidConfiguration() expects array{comment_type?: \'comment\'|\'PHPDoc\', header: string, location?: \'after_declare_strict\'|\'after_open\', separate?: \'both\'|\'bottom\'|\'none\'|\'top\', validator?: string|null}|null, array{header: \'\', separate: stdClass} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Comment/HeaderCommentFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\Comment\\HeaderCommentFixerTest::testInvalidConfiguration() expects array{comment_type?: \'comment\'|\'PHPDoc\', header: string, location?: \'after_declare_strict\'|\'after_open\', separate?: \'both\'|\'bottom\'|\'none\'|\'top\', validator?: string|null}|null, array{header: 1} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Comment/HeaderCommentFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\Comment\\HeaderCommentFixerTest::testInvalidConfiguration() expects array{comment_type?: \'comment\'|\'PHPDoc\', header: string, location?: \'after_declare_strict\'|\'after_open\', separate?: \'both\'|\'bottom\'|\'none\'|\'top\', validator?: string|null}|null, array{} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Comment/HeaderCommentFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\ConstantNotation\\NativeConstantInvocationFixerTest::testInvalidConfiguration() expects array{exclude?: list<string>, fix_built_in?: bool, include?: list<string>, scope?: \'all\'|\'namespaced\', strict?: bool}, array{include: array{0.1}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ConstantNotation/NativeConstantInvocationFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\ConstantNotation\\NativeConstantInvocationFixerTest::testInvalidConfiguration() expects array{exclude?: list<string>, fix_built_in?: bool, include?: list<string>, scope?: \'all\'|\'namespaced\', strict?: bool}, array{include: array{1}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ConstantNotation/NativeConstantInvocationFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\ConstantNotation\\NativeConstantInvocationFixerTest::testInvalidConfiguration() expects array{exclude?: list<string>, fix_built_in?: bool, include?: list<string>, scope?: \'all\'|\'namespaced\', strict?: bool}, array{include: array{array{}}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ConstantNotation/NativeConstantInvocationFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\ConstantNotation\\NativeConstantInvocationFixerTest::testInvalidConfiguration() expects array{exclude?: list<string>, fix_built_in?: bool, include?: list<string>, scope?: \'all\'|\'namespaced\', strict?: bool}, array{include: array{false}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ConstantNotation/NativeConstantInvocationFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\ConstantNotation\\NativeConstantInvocationFixerTest::testInvalidConfiguration() expects array{exclude?: list<string>, fix_built_in?: bool, include?: list<string>, scope?: \'all\'|\'namespaced\', strict?: bool}, array{include: array{null}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ConstantNotation/NativeConstantInvocationFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\ConstantNotation\\NativeConstantInvocationFixerTest::testInvalidConfiguration() expects array{exclude?: list<string>, fix_built_in?: bool, include?: list<string>, scope?: \'all\'|\'namespaced\', strict?: bool}, array{include: array{stdClass}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ConstantNotation/NativeConstantInvocationFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\ConstantNotation\\NativeConstantInvocationFixerTest::testInvalidConfiguration() expects array{exclude?: list<string>, fix_built_in?: bool, include?: list<string>, scope?: \'all\'|\'namespaced\', strict?: bool}, array{include: array{true}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ConstantNotation/NativeConstantInvocationFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $config of method PhpCsFixer\\Tests\\Fixer\\ControlStructure\\YodaStyleFixerTest::testInvalidConfiguration() expects array{always_move_variable?: bool, equal?: bool|null, identical?: bool|null, less_and_greater?: bool|null}, array{equal: 2} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ControlStructure/YodaStyleFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\FunctionDeclarationFixerTest::testInvalidConfiguration() expects array{closure_fn_spacing?: \'none\'|\'one\', closure_function_spacing?: \'none\'|\'one\', trailing_comma_single_line?: bool}, array{closure_fn_spacing: \'neither\'} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/FunctionNotation/FunctionDeclarationFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\FunctionDeclarationFixerTest::testInvalidConfiguration() expects array{closure_fn_spacing?: \'none\'|\'one\', closure_function_spacing?: \'none\'|\'one\', trailing_comma_single_line?: bool}, array{closure_function_spacing: \'neither\'} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/FunctionNotation/FunctionDeclarationFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $config of method PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\DeclareEqualNormalizeFixerTest::testInvalidConfiguration() expects array{space?: \'none\'|\'single\'}, array{space: \'tab\'} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/LanguageConstruct/DeclareEqualNormalizeFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $config of method PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\FunctionToConstantFixerTest::testInvalidConfiguration() expects array{functions?: list<\'get_called_class\'|\'get_class\'|\'get_class_this\'|\'php_sapi_name\'|\'phpversion\'|\'pi\'>}, array{functions: array{\'a\'}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/LanguageConstruct/FunctionToConstantFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $config of method PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\FunctionToConstantFixerTest::testInvalidConfiguration() expects array{functions?: list<\'get_called_class\'|\'get_class\'|\'get_class_this\'|\'php_sapi_name\'|\'phpversion\'|\'pi\'>}, array{functions: array{1}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/LanguageConstruct/FunctionToConstantFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $config of method PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\FunctionToConstantFixerTest::testInvalidConfiguration() expects array{functions?: list<\'get_called_class\'|\'get_class\'|\'get_class_this\'|\'php_sapi_name\'|\'phpversion\'|\'pi\'>}, array{functions: array{abc: true}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/LanguageConstruct/FunctionToConstantFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\NamespaceNotation\\BlankLinesBeforeNamespaceFixerTest::testInvalidConfiguration() expects array{max_line_breaks?: int, min_line_breaks?: int}, array{min_line_breaks: 1, max_line_breaks: \'two and a half\'} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/NamespaceNotation/BlankLinesBeforeNamespaceFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\NamespaceNotation\\BlankLinesBeforeNamespaceFixerTest::testInvalidConfiguration() expects array{max_line_breaks?: int, min_line_breaks?: int}, array{min_line_breaks: true, max_line_breaks: 2} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/NamespaceNotation/BlankLinesBeforeNamespaceFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $config of method PhpCsFixer\\Tests\\Fixer\\Operator\\BinaryOperatorSpacesFixerTest::testInvalidConfiguration() expects array{default?: \'align\'|\'align_by_scope\'|\'align_single_space\'|\'align_single_space…\'|\'at_least_single…\'|\'no_space\'|\'single_space\'|null, operators?: array<string, string|null>}, array{operators: array{123: 1}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Operator/BinaryOperatorSpacesFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $config of method PhpCsFixer\\Tests\\Fixer\\Operator\\BinaryOperatorSpacesFixerTest::testInvalidConfiguration() expects array{default?: \'align\'|\'align_by_scope\'|\'align_single_space\'|\'align_single_space…\'|\'at_least_single…\'|\'no_space\'|\'single_space\'|null, operators?: array<string, string|null>}, array{operators: true} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Operator/BinaryOperatorSpacesFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\Operator\\ConcatSpaceFixerTest::testInvalidConfiguration() expects array{spacing?: \'none\'|\'one\'}, array{spacing: \'tabs\'} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Operator/ConcatSpaceFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $config of method PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocNoAliasTagFixerTest::testInvalidConfiguration() expects array{replacements?: array<string, string>}, array{replacements: array{1: \'abc\'}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Phpdoc/PhpdocNoAliasTagFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $config of method PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocNoAliasTagFixerTest::testInvalidConfiguration() expects array{replacements?: array<string, string>}, array{replacements: array{a: null}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Phpdoc/PhpdocNoAliasTagFixerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocReturnSelfReferenceFixerTest::testInvalidConfiguration() expects array{replacements?: array<string, string>}, array{replacements: array{1: \'a\'}} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Phpdoc/PhpdocReturnSelfReferenceFixerTest.php',
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
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be string by placeholder #1 ("%%s"), false given.',
    'count' => 5,
    'path' => __DIR__ . '/../../../tests/Test/AbstractIntegrationCaseFactory.php',
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
    'rawMessage' => 'Parameter #2 $sequence of method PhpCsFixer\\Tests\\Tokenizer\\TokensTest::testFindSequenceException() expects non-empty-list<array{0: int, 1?: string}|PhpCsFixer\\Tokenizer\\Token|string>, array{} given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Tokenizer/TokensTest.php',
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
