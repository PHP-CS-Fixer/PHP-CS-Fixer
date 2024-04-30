<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPriority\\(\\) on PhpCsFixer\\\\Fixer\\\\FixerInterface\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/AbstractProxyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Cache/Signature.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Config\\:\\:getFinder\\(\\) should return PhpCsFixer\\\\Finder but returns iterable\\<SplFileInfo\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\-, int\\|false given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, array\\<non\\-falsy\\-string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$time of class PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\ReportSummary constructor expects int, float\\|int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$basePath of static method Symfony\\\\Component\\\\Filesystem\\\\Path\\:\\:makeRelative\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/ListFilesCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/ListFilesCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of function is_file expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array\\<int\\<0, max\\>, string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\ConfigurationResolver\\:\\:\\$path \\(list\\<string\\>\\|null\\) does not accept array\\<non\\-empty\\-string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'line\' might not exist on array\\{function\\?\\: string, line\\?\\: int, file\\: string, class\\?\\: class\\-string, type\\?\\: \'\\-\\>\'\\|\'\\:\\:\', args\\?\\: array, object\\?\\: object\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Output/ErrorOutput.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\CheckstyleReporter\\:\\:generate\\(\\) should return string but returns string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/CheckstyleReporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$text of static method Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\:\\:escape\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/CheckstyleReporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$diffs of static method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\GitlabReporter\\:\\:getLines\\(\\) expects list\\<SebastianBergmann\\\\Diff\\\\Diff\\>, array\\<SebastianBergmann\\\\Diff\\\\Diff\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/GitlabReporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\JunitReporter\\:\\:generate\\(\\) should return string but returns string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/JunitReporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$text of static method Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\:\\:escape\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/JunitReporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$reporter of method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\ReporterFactory\\:\\:registerReporter\\(\\) expects PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\ReporterInterface, object given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/ReporterFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\XmlReporter\\:\\:generate\\(\\) should return string but returns string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/XmlReporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$text of static method Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\:\\:escape\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/XmlReporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$reporter of method PhpCsFixer\\\\Console\\\\Report\\\\ListSetsReport\\\\ReporterFactory\\:\\:registerReporter\\(\\) expects PhpCsFixer\\\\Console\\\\Report\\\\ListSetsReport\\\\ReporterInterface, object given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/ListSetsReport/ReporterFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Console\\\\WarningsDetector\\:\\:getWarnings\\(\\) should return list\\<string\\> but returns non\\-empty\\-array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/WarningsDetector.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\DocBlock\\\\Annotation\\:\\:\\$end \\(int\\) does not accept int\\|string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, array\\<non\\-falsy\\-string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\|string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Documentation/RuleSetDocumentationGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Error\\\\ErrorsManager\\:\\:getExceptionErrors\\(\\) should return list\\<PhpCsFixer\\\\Error\\\\Error\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\Error\\\\Error\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Error/ErrorsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Error\\\\ErrorsManager\\:\\:getInvalidErrors\\(\\) should return list\\<PhpCsFixer\\\\Error\\\\Error\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\Error\\\\Error\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Error/ErrorsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Error\\\\ErrorsManager\\:\\:getLintErrors\\(\\) should return list\\<PhpCsFixer\\\\Error\\\\Error\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\Error\\\\Error\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Error/ErrorsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\AbstractPhpUnitFixer\\:\\:addInternalAnnotation\\(\\) should return list\\<PhpCsFixer\\\\DocBlock\\\\Line\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\DocBlock\\\\Line\\|non\\-falsy\\-string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/AbstractPhpUnitFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getNextTokenOfKind\\(\\) expects int, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/PowToExponentiationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$slices of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:insertSlices\\(\\) expects array\\<int, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\<\'\'\\|int, array\\{PhpCsFixer\\\\Tokenizer\\\\Token, PhpCsFixer\\\\Tokenizer\\\\Token\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ArrayNotation/YieldFromArrayToYieldsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'end\' on array\\{name\\: string, start\\: int, end\\: int\\}\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/AttributeNotation/OrderedAttributesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'end\' on array\\{start\\: int, end\\: int, name\\: string\\}\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/AttributeNotation/OrderedAttributesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function substr expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/AttributeNotation/OrderedAttributesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function strlen expects string, string\\|false given\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/Basic/PsrAutoloadingFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$tokens of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getPrevTokenOfKind\\(\\) expects list\\<array\\{int\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, list\\<list\\<int\\>\\|string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Casing/NativeTypeDeclarationCasingFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'elements\' on array\\{index\\: int, open\\: int\\|null, close\\: int\\<0, max\\>, elements\\: list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'property\'\\|\'trait_import\', index\\: int, start\\: int, end\\: int\\}\\>\\}\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'index\' on array\\{classIndex\\: int, token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'property\'\\|\'trait_import\'\\}\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'end\' might not exist on array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'start\' might not exist on array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\.$#',
	'count' => 11,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$class of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:getFirstTokenIndexOfClassElement\\(\\) expects array\\{index\\: int, open\\: int, close\\: int, elements\\: non\\-empty\\-list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\>\\}, array\\{index\\: int, open\\: int\\|null, close\\: int\\<0, max\\>, elements\\: list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'property\'\\|\'trait_import\', index\\: int, start\\: int, end\\: int\\}\\>\\}\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$class of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:getLastTokenIndexOfClassElement\\(\\) expects array\\{index\\: int, open\\: int, close\\: int, elements\\: non\\-empty\\-list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\>\\}, array\\{index\\: int, open\\: int\\|null, close\\: int\\<0, max\\>, elements\\: list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'property\'\\|\'trait_import\', index\\: int, start\\: int, end\\: int\\}\\>\\}\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:\\$classElementTypes \\(array\\<string, string\\>\\) does not accept array\\<int\\|string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassDefinitionFixer\\:\\:getClassyDefinitionInfo\\(\\) should return array\\{start\\: int, classy\\: int, open\\: int, extends\\: array\\{start\\: int, numberOfExtends\\: int, multiLine\\: bool\\}\\|false, implements\\: array\\{start\\: int, numberOfImplements\\: int, multiLine\\: bool\\}\\|false, anonymousClass\\: bool, final\\: int\\|false, abstract\\: int\\|false, \\.\\.\\.\\} but returns array\\{classy\\: int, open\\: int\\|null, extends\\: array\\<string, bool\\|int\\>\\|false, implements\\: array\\<string, bool\\|int\\>\\|false, anonymousClass\\: bool, final\\: int\\|false, abstract\\: int\\|false, readonly\\: int\\|false, \\.\\.\\.\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, bool\\|int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an elseif condition, array\\<string, bool\\|int\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an if condition, array\\<string, bool\\|int\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in pre\\-increment, bool\\|int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoBlankLinesAfterClassOpeningFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\NoPhp4ConstructorFixer\\:\\:findFunction\\(\\) should return array\\{nameIndex\\: int, startIndex\\: int, endIndex\\: int, bodyIndex\\: int, modifiers\\: list\\<int\\>\\}\\|null but returns array\\{nameIndex\\: int\\<0, max\\>, startIndex\\: int, endIndex\\: int\\|null, bodyIndex\\: int\\|null, modifiers\\: array\\<\'\'\\|int, int\\>\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\NoPhp4ConstructorFixer\\:\\:getWrapperMethodSequence\\(\\) should return array\\{list\\<list\\<array\\{int, string\\}\\|int\\|string\\>\\>, array\\{3\\: false\\}\\} but returns array\\{list\\<non\\-empty\\-list\\<\'\\(\'\\|\'\\)\'\\|\',\'\\|\';\'\\|\'\\{\'\\|\'\\}\'\\|array\\{0\\: int, 1\\?\\: string\\}\\>\\>, array\\{3\\: false\\}\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sequence of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) expects non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, list\\<array\\{int, string\\}\\|int\\|string\\> given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'type\' on array\\{classIndex\\: int, token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'property\'\\|\'trait_import\'\\}\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'class_is_final\' might not exist on array\\{classIndex\\: int, token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, class_is_final\\?\\: bool, method_final_index\\: int\\|null, method_is_constructor\\?\\: bool, method_is_private\\: bool, method_of_enum\\: false\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'method_is…\' might not exist on array\\{classIndex\\: int, token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, class_is_final\\: false, method_final_index\\: int\\|null, method_is_constructor\\?\\: bool, method_is_private\\: true, method_of_enum\\: false\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:getElements\\(\\) should return list\\<array\\{start\\: int, visibility\\: string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\: string, name\\: string, end\\: int\\}\\> but returns list\\<array\\{start\\: int, visibility\\: \'public\', abstract\\: false, static\\: false, readonly\\: bool, type\\: string, name\\?\\: string, end\\: int\\}\\|array\\{start\\: int, visibility\\: non\\-empty\\-string, abstract\\: bool, static\\: bool, readonly\\: bool\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'end\' might not exist on array\\{abstract\\: bool, end\\?\\: int, name\\?\\: string, readonly\\: bool, start\\: int, static\\: bool, type\\?\\: string, visibility\\: non\\-empty\\-string\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$a of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:sortGroupElements\\(\\) expects array\\{start\\: int, visibility\\: string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\: string, name\\: string, end\\: int\\}, array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$b of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:sortGroupElements\\(\\) expects array\\{start\\: int, visibility\\: string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\: string, name\\: string, end\\: int\\}, array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:\\$typePosition \\(array\\<string, int\\>\\) does not accept array\\<int\\|string, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only iterables can be unpacked, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|int\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given in argument \\#3\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function strlen expects string, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|int\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string1 of function strcasecmp expects string, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|int\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$string2 of function strcasecmp expects string, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|int\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Generator expects value type array\\<int, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\<int\\|string, PhpCsFixer\\\\Tokenizer\\\\Tokens\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedTraitsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$elements of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedTraitsFixer\\:\\:sort\\(\\) expects array\\<int, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\<int\\<0, max\\>\\|string, PhpCsFixer\\\\Tokenizer\\\\Tokens\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedTraitsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$types of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedTypesFixer\\:\\:runTypesThroughSortingAlgorithm\\(\\) expects list\\<array\\<string\\>\\|string\\>, array\\<list\\<array\\<string\\>\\|string\\>\\|string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedTypesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$types of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedTypesFixer\\:\\:runTypesThroughSortingAlgorithm\\(\\) expects list\\<array\\<string\\>\\|string\\>, array\\<string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedTypesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, int\\|false\\|null given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ProtectedToPrivateFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\-, int\\|false given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Comment/CommentToPhpdocFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in pre\\-increment, int\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Comment/CommentToPhpdocFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:clearAt\\(\\) expects int, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Comment/CommentToPhpdocFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:insertAt\\(\\) expects int, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Comment/CommentToPhpdocFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\.\\.\\.\\$arg1 of function max expects non\\-empty\\-array, array\\<int\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Comment/CommentToPhpdocFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\.\\.\\.\\$arg1 of function max expects non\\-empty\\-array, list\\<int\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Comment/CommentToPhpdocFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\<int\\<0, max\\>, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/IncludeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sequence of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) expects non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, array\\<int, list\\<int\\|string\\>\\|string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/SimplifiedIfReturnFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$possibleKind of method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:isGivenKind\\(\\) expects list\\<int\\>\\|int, list\\<int\\|string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'end\' on array\\{indices\\: list\\<int\\>, secondArgument\\?\\: int, levels\\: int, end\\: int\\}\\|true\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/CombineNestedDirnameFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'indices\' on array\\{indices\\: list\\<int\\>, secondArgument\\?\\: int, levels\\: int, end\\: int\\}\\|true\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/CombineNestedDirnameFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, array\\<string, array\\<int, int\\>\\|int\\>\\|bool given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/CombineNestedDirnameFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$dirnameInfoArray of method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\CombineNestedDirnameFixer\\:\\:combineDirnames\\(\\) expects list\\<array\\{indices\\: list\\<int\\>, secondArgument\\?\\: int, levels\\: int, end\\: int\\}\\>, non\\-empty\\-list\\<array\\{indices\\: list\\<int\\>, secondArgument\\?\\: int, levels\\: int, end\\: int\\}\\|true\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/CombineNestedDirnameFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$flags of method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\FopenFlagOrderFixer\\:\\:sortFlags\\(\\) expects list\\<string\\>, array\\<string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/FopenFlagOrderFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\ImplodeCallFixer\\:\\:getArgumentIndices\\(\\) should return array\\<int, int\\> but returns array\\<int\\|string, int\\|null\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/ImplodeCallFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\NativeFunctionInvocationFixer\\:\\:normalizeFunctionNames\\(\\) should return array\\<string, true\\> but returns array\\<string\\|true\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/NativeFunctionInvocationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$others of method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:equalsAny\\(\\) expects list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, array\\<int, array\\<int, int\\|string\\>\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/PhpdocToReturnTypeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$indexEnd of method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\RegularCallableCallFixer\\:\\:getTokensSubcollection\\(\\) expects int, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/RegularCallableCallFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#5 \\$firstArgEndIndex of method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\RegularCallableCallFixer\\:\\:replaceCallUserFuncWithCallback\\(\\) expects int, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/RegularCallableCallFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\Import\\\\FullyQualifiedStrictTypesFixer\\:\\:\\$discoveredSymbols \\(array\\{const\\?\\: list\\<class\\-string\\>, class\\?\\: list\\<class\\-string\\>, function\\?\\: list\\<class\\-string\\>\\}\\|null\\) does not accept array\\{const\\?\\: list\\<class\\-string\\>, class\\: non\\-empty\\-list\\<string\\>, function\\?\\: list\\<class\\-string\\>\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\Import\\\\FullyQualifiedStrictTypesFixer\\:\\:\\$reservedIdentifiersByLevel \\(array\\<int\\<0, max\\>, array\\<string, true\\>\\>\\) does not accept non\\-empty\\-array\\<int, array\\<string, true\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\Import\\\\FullyQualifiedStrictTypesFixer\\:\\:\\$symbolsForImport \\(array\\{const\\?\\: array\\<string, class\\-string\\>, class\\?\\: array\\<string, class\\-string\\>, function\\?\\: array\\<string, class\\-string\\>\\}\\) does not accept array\\{const\\?\\: array\\<string, string\\>, class\\?\\: array\\<string, string\\>, function\\?\\: array\\<string, string\\>\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Import\\\\GlobalNamespaceImportFixer\\:\\:prepareImports\\(\\) should return array\\<string, class\\-string\\> but returns array\\<string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$types of method PhpCsFixer\\\\DocBlock\\\\Annotation\\:\\:setTypes\\(\\) expects list\\<string\\>, array\\<int\\<0, max\\>, string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$imports of method PhpCsFixer\\\\Tokenizer\\\\Processor\\\\ImportProcessor\\:\\:insertImports\\(\\) expects array\\{const\\?\\: array\\<int\\|string, class\\-string\\>, class\\?\\: array\\<int\\|string, class\\-string\\>, function\\?\\: array\\<int\\|string, class\\-string\\>\\}, array\\{const\\?\\: array\\<string, string\\>, function\\?\\: array\\<string, string\\>, class\\?\\: array\\<string, string\\>\\}&non\\-empty\\-array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GroupImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/NoUnusedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Import\\\\OrderedImportsFixer\\:\\:getNewOrder\\(\\) should return array\\<int, array\\{namespace\\: non\\-empty\\-string, startIndex\\: int, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\> but returns array\\<\'\'\\|int, array\\{namespace\\: string, startIndex\\: int\\|null, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$indices of method PhpCsFixer\\\\Fixer\\\\Import\\\\OrderedImportsFixer\\:\\:sortByAlgorithm\\(\\) expects array\\<int, array\\{namespace\\: non\\-empty\\-string, startIndex\\: int, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\>, array\\<\'\'\\|int, array\\{namespace\\: string, startIndex\\: int\\|null, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$indices of method PhpCsFixer\\\\Fixer\\\\Import\\\\OrderedImportsFixer\\:\\:sortByAlgorithm\\(\\) expects array\\<int, array\\{namespace\\: non\\-empty\\-string, startIndex\\: int, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\>, non\\-empty\\-array\\<\'\'\\|int, array\\{namespace\\: string, startIndex\\: int\\|null, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array\\<0\\|1\\|2, \'class\'\\|\'const\'\\|\'function\'\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getNextMeaningfulToken\\(\\) expects int, int\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$indices of method PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\CombineConsecutiveIssetsFixer\\:\\:clearTokens\\(\\) expects list\\<int\\>, non\\-empty\\-array\\<int\\|null\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$indices of method PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\CombineConsecutiveIssetsFixer\\:\\:getTokenClones\\(\\) expects list\\<int\\>, array\\<int\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\FunctionToConstantFixer\\:\\:getReplacementTokenClones\\(\\) should return array\\{int, int, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\} but returns array\\{int, int, array\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$items of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:insertAt\\(\\) expects list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|PhpCsFixer\\\\Tokenizer\\\\Tokens, array\\<PhpCsFixer\\\\Tokenizer\\\\Token\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\FunctionToConstantFixer\\:\\:\\$functionsFixMap \\(array\\<string, array\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\>\\) does not accept array\\<int\\|string, array\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, bool\\|int given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, bool\\|int given on the right side\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, bool\\|int given\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an if condition, bool\\|int given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, bool\\|int given on the left side\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getNextTokenOfKind\\(\\) expects int, bool\\|int given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getPrevTokenOfKind\\(\\) expects int, bool\\|int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:insertAt\\(\\) expects int, bool\\|int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\SingleSpaceAroundConstructFixer\\:\\:\\$fixTokenMapContainASingleSpace \\(array\\<string, int\\>\\) does not accept array\\<int\\|string, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/SingleSpaceAroundConstructFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\SingleSpaceAroundConstructFixer\\:\\:\\$fixTokenMapFollowedByASingleSpace \\(array\\<string, int\\>\\) does not accept array\\<int\\|string, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/SingleSpaceAroundConstructFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\SingleSpaceAroundConstructFixer\\:\\:\\$fixTokenMapPrecededByASingleSpace \\(array\\<string, int\\>\\) does not accept array\\<int\\|string, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/SingleSpaceAroundConstructFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/NamespaceNotation/BlankLinesBeforeNamespaceFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Operator\\\\BinaryOperatorSpacesFixer\\:\\:resolveOperatorsFromConfig\\(\\) should return array\\<string, string\\> but returns array\\<int\\|string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, int\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\-, int\\<0, max\\>\\|false given on the right side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Operator\\\\TernaryToElvisOperatorFixer\\:\\:getAfterOperator\\(\\) should return array\\{start\\: int, end\\: int\\} but returns array\\{start\\: int\\|null, end\\?\\: int\\|null\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/TernaryToElvisOperatorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitAttributesFixer\\:\\:createAttributeTokens\\(\\) should return list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\> but returns non\\-empty\\-array\\<int\\|string, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitAttributesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$name of static method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitAttributesFixer\\:\\:toClassConstant\\(\\) expects class\\-string, string given\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitAttributesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$slices of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:insertSlices\\(\\) expects array\\<int, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\<\'\'\\|int, array\\{PhpCsFixer\\\\Tokenizer\\\\Token, PhpCsFixer\\\\Tokenizer\\\\Token\\}\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDataProviderStaticFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method isGivenKind\\(\\) on PhpCsFixer\\\\Tokenizer\\\\Token\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an if condition, bool\\|int\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$token of class PhpCsFixer\\\\Tokenizer\\\\Token constructor expects array\\{int, string\\}\\|string, array\\{262, int\\|string\\|true\\} given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitTestAnnotationFixer\\:\\:addTestAnnotation\\(\\) should return array\\<PhpCsFixer\\\\DocBlock\\\\Line\\> but returns array\\<PhpCsFixer\\\\DocBlock\\\\Line\\|string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitTestAnnotationFixer\\:\\:updateLines\\(\\) should return list\\<PhpCsFixer\\\\DocBlock\\\\Line\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\<0, max\\>\\|false given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/NoBlankLinesAfterPhpdocFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\NoSuperfluousPhpdocTagsFixer\\:\\:getArgumentsInfo\\(\\) should return array\\<non\\-empty\\-string, array\\{types\\: list\\<string\\>, allows_null\\: bool\\}\\> but returns array\\<string, array\\{types\\: list\\<string\\>, allows_null\\: bool\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/NoSuperfluousPhpdocTagsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\PhpdocAlignFixer\\:\\:getMatches\\(\\) should return array\\{indent\\: string\\|null, tag\\: string\\|null, hint\\: string, var\\: string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\|null but returns array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'desc\' might not exist on array\\{indent\\: string\\|null, tag\\: null, hint\\: string, var\\: string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'desc\' might not exist on array\\{indent\\: string\\|null, tag\\: string, hint\\: string, var\\: \'\', static\\: string, desc\\?\\: string\\|null\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'desc\' might not exist on array\\{indent\\: string\\|null, tag\\: string, hint\\: string, var\\: non\\-empty\\-string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an if condition, int\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$types of method PhpCsFixer\\\\DocBlock\\\\DocBlock\\:\\:getAnnotationsOfType\\(\\) expects list\\<string\\>\\|string, array\\<string\\> given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocOrderFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEnd\\(\\) on PhpCsFixer\\\\DocBlock\\\\Annotation\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getStart\\(\\) on PhpCsFixer\\\\DocBlock\\\\Annotation\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$content of method PhpCsFixer\\\\DocBlock\\\\Line\\:\\:setContent\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, array\\<string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocReturnSelfReferenceFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(int\\|string\\)\\: mixed\\)\\|null, Closure\\(string\\)\\: string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTagTypeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$parts of method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\PhpdocTagTypeFixer\\:\\:tagIsSurroundedByText\\(\\) expects list\\<string\\>, array\\<string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTagTypeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\PhpdocToCommentFixer\\:\\:\\$ignoredTags \\(list\\<string\\>\\) does not accept array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocToCommentFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Semicolon/MultilineWhitespaceBeforeSemicolonsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$str of function preg_quote expects string, int\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$parentScopeEndIndex of method PhpCsFixer\\\\Fixer\\\\Whitespace\\\\ArrayIndentationFixer\\:\\:findExpressionEndIndex\\(\\) expects int, int\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\Whitespace\\\\BlankLineBeforeStatementFixer\\:\\:\\$fixTokenMap \\(list\\<int\\>\\) does not accept non\\-empty\\-array\\<int\\|string, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/BlankLineBeforeStatementFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'new_indent\' might not exist on array\\{type\\: \'statement\', skip\\: bool, end_index\\: int\\|null, end_index_inclusive\\: bool, initial_indent\\: string, new_indent\\?\\: string, is_indented_block\\: bool\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$fixerConflicts of method PhpCsFixer\\\\FixerFactory\\:\\:generateConflictMessage\\(\\) expects array\\<string, list\\<string\\>\\>, non\\-empty\\-array\\<string, non\\-empty\\-array\\<int\\<0, max\\>, string\\>\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/FixerFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array\\<int\\<0, max\\>, string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/FixerFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function md5 expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/CachingLinter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of function file_put_contents expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/ProcessLinter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of method PhpCsFixer\\\\FileRemoval\\:\\:observe\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/ProcessLinter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of method PhpCsFixer\\\\Linter\\\\ProcessLinter\\:\\:createProcessForFile\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/ProcessLinter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\$path of class Symfony\\\\Component\\\\Filesystem\\\\Exception\\\\IOException constructor expects string\\|null, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/ProcessLinter.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Linter\\\\ProcessLinter\\:\\:\\$temporaryFile \\(string\\|null\\) does not accept string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/ProcessLinter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Preg\\:\\:addUtf8Modifier\\(\\) should return array\\<string\\>\\|string but returns array\\<array\\<string\\>\\|string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Preg\\:\\:removeUtf8Modifier\\(\\) should return array\\<string\\>\\|string but returns array\\<array\\<string\\>\\|string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Preg\\:\\:replace\\(\\) should return string but returns array\\<int, string\\>\\|string\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$pattern of function preg_match expects string, array\\<string\\>\\|string given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$pattern of function preg_match_all expects string, array\\<string\\>\\|string given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$pattern of function preg_split expects string, array\\<string\\>\\|string given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the right side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/AbstractRuleSetDescription.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, array\\<string, mixed\\>\\|bool given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/RuleSet.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method object\\:\\:getName\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/RuleSets.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\RuleSet\\\\RuleSets\\:\\:getSetDefinitions\\(\\) should return array\\<string, PhpCsFixer\\\\RuleSet\\\\RuleSetDescriptionInterface\\> but returns array\\<int\\|string, object\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/RuleSets.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$callback of function uksort expects callable\\(int\\|string, int\\|string\\)\\: int, Closure\\(string, string\\)\\: int\\<\\-1, 1\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/RuleSets.php',
];
$ignoreErrors[] = [
	'message' => '#^Static property PhpCsFixer\\\\RuleSet\\\\RuleSets\\:\\:\\$setDefinitions \\(array\\<string, PhpCsFixer\\\\RuleSet\\\\RuleSetDescriptionInterface\\>\\) does not accept array\\<int\\|string, object\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/RuleSets.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\AttributeAnalyzer\\:\\:collectAttributes\\(\\) should return list\\<array\\{start\\: int, end\\: int, name\\: string\\}\\> but returns non\\-empty\\-array\\<int\\<0, max\\>, array\\{start\\: int, end\\: int, name\\: string\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/AttributeAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$analysis of static method PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\ControlCaseStructuresAnalyzer\\:\\:buildControlCaseStructureAnalysis\\(\\) expects array\\{kind\\: int, index\\: int, open\\: int, end\\: int, cases\\: list\\<array\\{index\\: int, open\\: int\\}\\>, default\\: array\\{index\\: int, open\\: int\\}\\|null\\}, non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\> given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:getContent\\(\\) should return non\\-empty\\-string but returns string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Token.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:toJson\\(\\) should return string but returns string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Token.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:extractTokenKind\\(\\) should return int\\|non\\-empty\\-string but returns int\\|string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findGivenKind\\(\\) should return array\\<int, array\\<int\\<0, max\\>, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\> but returns array\\<\'\'\\|int, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findOppositeBlockEdge\\(\\) should return int\\<0, max\\> but returns int\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) should return non\\-empty\\-array\\<int\\<0, max\\>, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|null but returns non\\-empty\\-array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$others of method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:equalsAny\\(\\) expects list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, non\\-empty\\-array\\<int\\<0, max\\>, array\\{int\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$possibleKind of method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:isGivenKind\\(\\) expects list\\<int\\>\\|int, non\\-empty\\-array\\<int\\<0, max\\>, int\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, iterable\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Tokens given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$name of static method PhpCsFixer\\\\Tokenizer\\\\Processor\\\\ImportProcessor\\:\\:tokenizeName\\(\\) expects class\\-string, string given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Tokenizer/Transformer/NameQualifiedTransformer.php',
];
$ignoreErrors[] = [
	'message' => '#^Generator expects value type PhpCsFixer\\\\Tokenizer\\\\TransformerInterface, object given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Transformers.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$json of function json_decode expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/ToolInfo.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\CiConfigurationTest\\:\\:getPhpVersionsUsedForBuildingLocalImages\\(\\) should return list\\<numeric\\-string\\> but returns array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\CiConfigurationTest\\:\\:getPhpVersionsUsedForBuildingOfficialImages\\(\\) should return list\\<numeric\\-string\\> but returns array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$code of static method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:fromCode\\(\\) expects string, string\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$input of static method Symfony\\\\Component\\\\Yaml\\\\Yaml\\:\\:parse\\(\\) expects string, string\\|false given\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$json of function json_decode expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ComposerFileTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$callback of function array_reduce expects callable\\(array, int\\|string\\)\\: array, Closure\\(array, string\\)\\: array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ComposerFileTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of function file_get_contents expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$json of function json_decode expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$haystack of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringContainsString\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'reflection\' on array\\{reflection\\: ReflectionObject, short_classname\\: string\\}\\|PhpCsFixer\\\\Fixer\\\\FixerInterface\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'short_classname\' on array\\{reflection\\: ReflectionObject, short_classname\\: string\\}\\|PhpCsFixer\\\\Fixer\\\\FixerInterface\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$file of class Symfony\\\\Component\\\\Finder\\\\SplFileInfo constructor expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$file on SimpleXMLElement\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method xpath\\(\\) on SimpleXMLElement\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\ProjectCodeTest\\:\\:extractFunctionNamesCalledInClass\\(\\) should return list\\<string\\> but returns array\\<int, non\\-empty\\-string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\ProjectCodeTest\\:\\:getFileContentForClass\\(\\) should return string but returns string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\ProjectCodeTest\\:\\:getSrcClasses\\(\\) should return list\\<class\\-string\\> but returns list\\<non\\-falsy\\-string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\ProjectCodeTest\\:\\:getTestClasses\\(\\) should return list\\<class\\-string\\<PhpCsFixer\\\\Tests\\\\TestCase\\>\\> but returns list\\<non\\-falsy\\-string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, int\\<0, max\\>\\|false given on the right side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\)\\: mixed\\)\\|null, Closure\\(PhpCsFixer\\\\Tokenizer\\\\Token\\)\\: non\\-empty\\-string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$className of method PhpCsFixer\\\\Tests\\\\AutoReview\\\\ProjectCodeTest\\:\\:createTokensForClass\\(\\) expects class\\-string, string given\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$content of class PhpCsFixer\\\\DocBlock\\\\DocBlock constructor expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$data of function simplexml_load_string expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$objectOrClass of class ReflectionClass constructor expects class\\-string\\<T of object\\>\\|T of object, string given\\.$#',
	'count' => 11,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$callback of function array_filter expects \\(callable\\(PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\)\\: bool\\)\\|null, Closure\\(PhpCsFixer\\\\Tokenizer\\\\Token\\)\\: bool given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$haystack of function str_contains expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ReadmeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/ConfigTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertFileExists\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/ListFilesCommandTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of static method Symfony\\\\Component\\\\Filesystem\\\\Path\\:\\:makeRelative\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/ListFilesCommandTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function ltrim expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/ListFilesCommandTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$basePath of static method Symfony\\\\Component\\\\Filesystem\\\\Path\\:\\:makeRelative\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/ListFilesCommandTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Anonymous function should return string but returns string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/ConfigurationResolverTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function sort expects TArray of array\\<string\\>, array\\<int, string\\>\\|Exception given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/ConfigurationResolverTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$expected of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertInstanceOf\\(\\) expects class\\-string\\<object\\>, string given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Console/ConfigurationResolverTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$stream of class Symfony\\\\Component\\\\Console\\\\Output\\\\StreamOutput constructor expects resource, resource\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Output/ErrorOutputTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\|string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Output/ErrorOutputTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$expected of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertInstanceOf\\(\\) expects class\\-string\\<object\\>, string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Output/Progress/ProgressOutputFactoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\Fixer\\\\Basic\\\\EncodingFixerTest\\:\\:prepareTestCase\\(\\) should return array\\{string, string\\|null, SplFileInfo\\} but returns array\\{string\\|false, string\\|false\\|null, SplFileInfo\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Basic/EncodingFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$objectOrMethod of class ReflectionMethod constructor expects object\\|string, class\\-string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/ControlStructure/NoUselessElseFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an elseif condition, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/FunctionNotation/MethodArgumentSpaceFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$exception of method PHPUnit\\\\Framework\\\\TestCase\\:\\:expectException\\(\\) expects class\\-string\\<Throwable\\>, string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/FunctionNotation/NativeFunctionInvocationFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Generator expects value type array\\{0\\: string, 1\\: string\\|null, 2\\?\\: array\\<string, bool\\>\\}, array\\{0\\: string, 1\\?\\: string\\} given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/FunctionNotation/NullableTypeDeclarationForDefaultNullValueFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/PhpTag/NoClosingTagFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Generator expects value type array\\{0\\: string, 1\\?\\: string\\}, list\\<string\\> given\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/../../tests/Fixer/PhpUnit/PhpUnitDataProviderReturnTypeFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Generator expects value type array\\{string, string\\}, list\\<string\\> given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Fixer/PhpUnit/PhpUnitDataProviderReturnTypeFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\Fixer\\\\PhpUnit\\\\PhpUnitDataProviderReturnTypeFixerTest\\:\\:mapToTemplate\\(\\) should return list\\<string\\> but returns array\\<int\\|string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/PhpUnit/PhpUnitDataProviderReturnTypeFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$exception of method PHPUnit\\\\Framework\\\\TestCase\\:\\:expectException\\(\\) expects class\\-string\\<Throwable\\>, string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/PhpUnit/PhpUnitTargetVersionTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Semicolon/NoEmptyStatementFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Semicolon/SemicolonAfterInstructionFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_map expects array, iterable given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Whitespace/LineEndingFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$code of class PhpCsFixer\\\\FixerDefinition\\\\FileSpecificCodeSample constructor expects string, string\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/FixerDefinition/FileSpecificCodeSampleTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$minimum of class PhpCsFixer\\\\FixerDefinition\\\\VersionSpecification constructor expects int\\<1, max\\>\\|null, int\\|null given\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/FixerDefinition/VersionSpecificationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$maximum of class PhpCsFixer\\\\FixerDefinition\\\\VersionSpecification constructor expects int\\<1, max\\>\\|null, int\\|null given\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/FixerDefinition/VersionSpecificationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method class@anonymous/tests/FixerFactoryTest\\.php\\:223\\:\\:getRuleConfiguration\\(\\) should return array\\<string, mixed\\> but returns array\\<string, mixed\\>\\|true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method class@anonymous/tests/FixerFactoryTest\\.php\\:58\\:\\:getRules\\(\\) should return array\\<string, array\\<string, mixed\\>\\|true\\> but returns array\\<string, array\\<string, mixed\\>\\|bool\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$exception of method PHPUnit\\\\Framework\\\\TestCase\\:\\:expectException\\(\\) expects class\\-string\\<Throwable\\>, string given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/PregTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$pattern of static method PhpCsFixer\\\\Preg\\:\\:replace\\(\\) expects string, array\\<int, string\\>\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/PregTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$pattern of static method PhpCsFixer\\\\Preg\\:\\:replaceCallback\\(\\) expects string, array\\<int, string\\>\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/PregTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of static method PhpCsFixer\\\\Preg\\:\\:replaceCallback\\(\\) expects string, array\\<int, string\\>\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/PregTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/RuleSet/RuleSetsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\|string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$dirs of method Symfony\\\\Component\\\\Filesystem\\\\Filesystem\\:\\:mkdir\\(\\) expects iterable\\|string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/InstallViaComposerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of function unlink expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/InstallViaComposerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$files of method Symfony\\\\Component\\\\Filesystem\\\\Filesystem\\:\\:remove\\(\\) expects iterable\\|string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/InstallViaComposerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$cwd of static method PhpCsFixer\\\\Tests\\\\Smoke\\\\InstallViaComposerTest\\:\\:assertCommandsWork\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/InstallViaComposerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\)\\: mixed\\)\\|null, Closure\\(PhpCsFixer\\\\Tokenizer\\\\Token\\)\\: string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$code of static method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:fromCode\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sequence of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) expects non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, list\\<array\\{0\\: int, 1\\?\\: string\\}\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractIntegrationCaseFactory\\:\\:determineRequirements\\(\\) should return array\\{php\\: int, php\\<\\: int, os\\: list\\<string\\>\\} but returns array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractIntegrationCaseFactory\\:\\:determineSettings\\(\\) should return array\\{checkPriority\\: bool, deprecations\\: list\\<string\\>\\} but returns array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$dirs of method Symfony\\\\Component\\\\Finder\\\\Finder\\:\\:in\\(\\) expects array\\<string\\>\\|string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of function is_dir expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function strlen expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$fixedInputCode of static method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractIntegrationTestCase\\:\\:assertRevertedOrderFixing\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$fixedInputCodeWithReversedFixers of static method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractIntegrationTestCase\\:\\:assertRevertedOrderFixing\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of static method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:getNameForId\\(\\) expects int, int\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractTransformerTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$prototypes of method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractTransformerTestCase\\:\\:countTokenPrototypes\\(\\) expects list\\<array\\{0\\: int, 1\\?\\: string\\}\\>, array\\<int\\<0, max\\>, array\\{int\\}\\|string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractTransformerTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on array\\{0\\: string, 1\\?\\: string\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/TestCaseUtils.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Tests\\\\Test\\\\TokensWithObservedTransformers\\:\\:\\$observedModificationsPerTransformer \\(array\\<string, list\\<int\\|string\\>\\>\\) does not accept non\\-empty\\-array\\<int\\|string, list\\<int\\|string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/TokensWithObservedTransformers.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Tests\\\\TestCase\\:\\:\\$actualDeprecations \\(list\\<string\\>\\) does not accept array\\<int\\<0, max\\>, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/TestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Tests\\\\TestCase\\:\\:\\$expectedDeprecations \\(list\\<string\\>\\) does not accept array\\<int\\<0, max\\>, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/TestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sequence of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) expects non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sequence of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) expects non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, list\\<mixed\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$slices of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:insertSlices\\(\\) expects array\\<int, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\{16\\: array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>, 6\\: array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\} given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
