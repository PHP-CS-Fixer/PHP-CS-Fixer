<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $cwd of class PhpCsFixer\\Console\\ConfigurationResolver constructor expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $cwd of class PhpCsFixer\\Console\\ConfigurationResolver constructor expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $basePath of static method Symfony\\Component\\Filesystem\\Path::makeRelative() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/ListFilesCommand.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $cwd of class PhpCsFixer\\Console\\ConfigurationResolver constructor expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/ListFilesCommand.php',
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

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
