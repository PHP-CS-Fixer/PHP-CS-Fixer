<?php declare(strict_types = 1);

// total 67 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, array\\<non\\-falsy\\-string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$time of class PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\ReportSummary constructor expects int, float\\|int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$basePath of static method Symfony\\\\Component\\\\Filesystem\\\\Path\\:\\:makeRelative\\(\\) expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/ListFilesCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/ListFilesCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$filename of function is_file expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array\\<int\\<0, max\\>, string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$diffs of static method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\GitlabReporter\\:\\:getLines\\(\\) expects list\\<SebastianBergmann\\\\Diff\\\\Diff\\>, array\\<SebastianBergmann\\\\Diff\\\\Diff\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Report/FixReport/GitlabReporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$reporter of method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\ReporterFactory\\:\\:registerReporter\\(\\) expects PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\ReporterInterface, object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Report/FixReport/ReporterFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$reporter of method PhpCsFixer\\\\Console\\\\Report\\\\ListSetsReport\\\\ReporterFactory\\:\\:registerReporter\\(\\) expects PhpCsFixer\\\\Console\\\\Report\\\\ListSetsReport\\\\ReporterInterface, object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Report/ListSetsReport/ReporterFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, array\\<non\\-falsy\\-string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\<string\\>\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Documentation/RuleSetDocumentationGenerator.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getNextTokenOfKind\\(\\) expects int, int\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Alias/PowToExponentiationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$slices of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:insertSlices\\(\\) expects array\\<int, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\<\'\'\\|int, array\\{PhpCsFixer\\\\Tokenizer\\\\Token, PhpCsFixer\\\\Tokenizer\\\\Token\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ArrayNotation/YieldFromArrayToYieldsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function strlen expects string, string\\|false given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Fixer/Basic/PsrAutoloadingFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$class of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:getFirstTokenIndexOfClassElement\\(\\) expects array\\{index\\: int, open\\: int, close\\: int, elements\\: non\\-empty\\-list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\>\\}, array\\{index\\: int, open\\: int\\|null, close\\: int\\<0, max\\>, elements\\: list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'promoted_property\'\\|\'property\'\\|\'trait_import\', index\\: int, start\\: int, end\\: int\\}\\>\\}\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$class of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:getLastTokenIndexOfClassElement\\(\\) expects array\\{index\\: int, open\\: int, close\\: int, elements\\: non\\-empty\\-list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\>\\}, array\\{index\\: int, open\\: int\\|null, close\\: int\\<0, max\\>, elements\\: list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'promoted_property\'\\|\'property\'\\|\'trait_import\', index\\: int, start\\: int, end\\: int\\}\\>\\}\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$sequence of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) expects non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, list\\<array\\{int, string\\}\\|int\\|string\\> given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$a of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:sortGroupElements\\(\\) expects array\\{start\\: int, visibility\\: string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\: string, name\\: string, end\\: int\\}, array&T given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$b of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:sortGroupElements\\(\\) expects array\\{start\\: int, visibility\\: string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\: string, name\\: string, end\\: int\\}, array&T given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function strlen expects string, int\\|list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string1 of function strcasecmp expects string, int\\|list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string2 of function strcasecmp expects string, int\\|list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$elements of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedTraitsFixer\\:\\:sort\\(\\) expects array\\<int, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\<int\\<0, max\\>\\|string, PhpCsFixer\\\\Tokenizer\\\\Tokens\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedTraitsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$possibleKind of method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:isGivenKind\\(\\) expects int\\|list\\<int\\>, list\\<int\\|string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$types of method PhpCsFixer\\\\DocBlock\\\\Annotation\\:\\:setTypes\\(\\) expects list\\<string\\>, array\\<int\\<0, max\\>, string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/NoUnusedImportsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$indices of method PhpCsFixer\\\\Fixer\\\\Import\\\\OrderedImportsFixer\\:\\:sortByAlgorithm\\(\\) expects array\\<int, array\\{namespace\\: non\\-empty\\-string, startIndex\\: int, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\>, array\\<\'\'\\|int, array\\{namespace\\: string, startIndex\\: int\\|null, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$indices of method PhpCsFixer\\\\Fixer\\\\Import\\\\OrderedImportsFixer\\:\\:sortByAlgorithm\\(\\) expects array\\<int, array\\{namespace\\: non\\-empty\\-string, startIndex\\: int, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\>, non\\-empty\\-array\\<\'\'\\|int, array\\{namespace\\: string, startIndex\\: int\\|null, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array\\<0\\|1\\|2, \'class\'\\|\'const\'\\|\'function\'\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getNextMeaningfulToken\\(\\) expects int, int\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$name of static method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitAttributesFixer\\:\\:toClassConstant\\(\\) expects class\\-string, string given\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitAttributesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/NoBlankLinesAfterPhpdocFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$content of method PhpCsFixer\\\\DocBlock\\\\Line\\:\\:setContent\\(\\) expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function preg_quote expects string, int\\|string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$parentScopeEndIndex of method PhpCsFixer\\\\Fixer\\\\Whitespace\\\\ArrayIndentationFixer\\:\\:findExpressionEndIndex\\(\\) expects int, int\\|string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$fixerConflicts of method PhpCsFixer\\\\FixerFactory\\:\\:generateConflictMessage\\(\\) expects array\\<string, list\\<string\\>\\>, non\\-empty\\-array\\<string, non\\-empty\\-array\\<int\\<0, max\\>, string\\>\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/FixerFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array\\<int\\<0, max\\>, string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/FixerFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$code of static method PhpCsFixer\\\\Hasher\\:\\:calculate\\(\\) expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Linter/CachingLinter.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, array\\<string, mixed\\>\\|bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/RuleSet.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$analysis of static method PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\ControlCaseStructuresAnalyzer\\:\\:buildControlCaseStructureAnalysis\\(\\) expects array\\{kind\\: int, index\\: int, open\\: int, end\\: int, cases\\: list\\<array\\{index\\: int, open\\: int\\}\\>, default\\: array\\{index\\: int, open\\: int\\}\\|null\\}, non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\> given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$possibleKind of method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:isGivenKind\\(\\) expects int\\|list\\<int\\>, non\\-empty\\-array\\<int\\<0, max\\>, int\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, iterable\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Tokens given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$name of static method PhpCsFixer\\\\Tokenizer\\\\Processor\\\\ImportProcessor\\:\\:tokenizeName\\(\\) expects class\\-string, string given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Tokenizer/Transformer/NameQualifiedTransformer.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$json of function json_decode expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/ToolInfo.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
