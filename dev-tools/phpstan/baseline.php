<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Comparison operation "\\<" between int\\<70400, 79999\\>\\|int\\<80001, 80499\\> and 70400 is always false\\.$#',
	'identifier' => 'smaller.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/../../php-cs-fixer',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on list\\<int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/AbstractFopenFlagFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<int, int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/AbstractFopenFlagFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/AbstractFunctionReferenceFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPriority\\(\\) on PhpCsFixer\\\\Fixer\\\\FixerInterface\\|false\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/../../src/AbstractProxyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Cache/Cache.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, string\\|false given\\.$#',
	'identifier' => 'booleanNot.exprNotBoolean',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Cache/Signature.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Config\\:\\:getFinder\\(\\) should return PhpCsFixer\\\\Finder but returns iterable\\<SplFileInfo\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\-, int\\|false given on the left side\\.$#',
	'identifier' => 'minus.leftNonNumeric',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\CheckCommand\\:\\:\\$defaultDescription has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\CheckCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset non\\-falsy\\-string might not exist on array\\<string, PhpCsFixer\\\\RuleSet\\\\RuleSetDescriptionInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\RuleSet\\\\RuleSetDescriptionInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 4,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, array\\<non\\-falsy\\-string\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\DescribeCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\DocumentationCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DocumentationCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$time of class PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\ReportSummary constructor expects int, float\\|int given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\FixCommand\\:\\:\\$defaultDescription has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\FixCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\HelpCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/HelpCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$basePath of static method Symfony\\\\Component\\\\Filesystem\\\\Path\\:\\:makeRelative\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/ListFilesCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/ListFilesCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\ListFilesCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/ListFilesCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\ListSetsCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/ListSetsCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'argv\' might not exist on array\\<mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/SelfUpdateCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'major\' might not exist on array\\{0\\?\\: string, major\\?\\: numeric\\-string, 1\\?\\: numeric\\-string\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/SelfUpdateCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\SelfUpdateCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/SelfUpdateCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'files\' might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/WorkerCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\WorkerCommand\\:\\:\\$defaultDescription has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/WorkerCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\WorkerCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/WorkerCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'allow\\-risky\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'cache\\-file\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'config\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'diff\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'dry\\-run\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'path\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 6,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'path\\-mode\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'rules\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'sequential\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'show\\-progress\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'stop\\-on\\-violation\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'using\\-cache\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of function is_file expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array\\<int\\<0, max\\>, string\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\ConfigurationResolver\\:\\:\\$path \\(list\\<string\\>\\|null\\) does not accept array\\<non\\-empty\\-string\\>\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'line\' might not exist on array\\{function\\?\\: string, line\\?\\: int, file\\: string, class\\?\\: class\\-string, type\\?\\: \'\\-\\>\'\\|\'\\:\\:\', args\\?\\: array\\<mixed\\>, object\\?\\: object\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Output/ErrorOutput.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\CheckstyleReporter\\:\\:generate\\(\\) should return string but returns string\\|false\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/CheckstyleReporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$text of static method Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\:\\:escape\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/CheckstyleReporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$diffs of static method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\GitlabReporter\\:\\:getLines\\(\\) expects list\\<SebastianBergmann\\\\Diff\\\\Diff\\>, array\\<SebastianBergmann\\\\Diff\\\\Diff\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/GitlabReporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\JunitReporter\\:\\:generate\\(\\) should return string but returns string\\|false\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/JunitReporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$text of static method Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\:\\:escape\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/JunitReporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$reporter of method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\ReporterFactory\\:\\:registerReporter\\(\\) expects PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\ReporterInterface, object given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/ReporterFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\XmlReporter\\:\\:generate\\(\\) should return string but returns string\\|false\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/XmlReporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$text of static method Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\:\\:escape\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/XmlReporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$reporter of method PhpCsFixer\\\\Console\\\\Report\\\\ListSetsReport\\\\ReporterFactory\\:\\:registerReporter\\(\\) expects PhpCsFixer\\\\Console\\\\Report\\\\ListSetsReport\\\\ReporterInterface, object given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/ListSetsReport/ReporterFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/SelfUpdate/NewVersionChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Differ/DiffConsoleFormatter.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'types\' might not exist on array\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'variable\' might not exist on array\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on array\\<int, PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 5,
	'path' => __DIR__ . '/../../src/DocBlock/Annotation.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/DocBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/DocBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'_array_shape_inner\' might not exist on array\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'_callable_argument\' might not exist on array\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'_callable_template_inner\' might not exist on array\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'array\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'array_shape_inner_value\' might not exist on array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'array_shape_inners\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'array_shape_name\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'array_shape_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'callable_argument_type\' might not exist on array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'callable_arguments\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'callable_name\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'callable_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'callable_template\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'callable_template_inner_b\' might not exist on array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'callable_template_inner_b_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'callable_template_inner_d\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'callable_template_inner_d_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'callable_template_inners\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'callable_template_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'class_constant\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'class_constant_name\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'conditional_cond_left\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'conditional_cond_middle\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'conditional_cond_right_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'conditional_false_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'conditional_false_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'conditional_true_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'conditional_true_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'generic_name\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'generic_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'generic_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'nullable\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'parenthesized_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'parenthesized_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'type\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'types\' might not exist on array\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on array\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 4,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<array\\{start_index\\: int\\<0, max\\>, value\\: string, next_glue\\: string\\|null, next_glue_raw\\: string\\|null\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on list\\<PhpCsFixer\\\\Doctrine\\\\Annotation\\\\Token\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Doctrine/Annotation/DocLexer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/DocumentationLocator.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/DocumentationLocator.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'identifier' => 'plus.leftNonNumeric',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, array\\<non\\-falsy\\-string\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\<string\\>\\|string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \\(int\\|string\\) might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/RuleSetDocumentationGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Documentation/RuleSetDocumentationGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Error\\\\ErrorsManager\\:\\:getExceptionErrors\\(\\) should return list\\<PhpCsFixer\\\\Error\\\\Error\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\Error\\\\Error\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Error/ErrorsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Error\\\\ErrorsManager\\:\\:getInvalidErrors\\(\\) should return list\\<PhpCsFixer\\\\Error\\\\Error\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\Error\\\\Error\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Error/ErrorsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Error\\\\ErrorsManager\\:\\:getLintErrors\\(\\) should return list\\<PhpCsFixer\\\\Error\\\\Error\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\Error\\\\Error\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Error/ErrorsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\AbstractPhpUnitFixer\\:\\:addInternalAnnotation\\(\\) should return list\\<PhpCsFixer\\\\DocBlock\\\\Line\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\DocBlock\\\\Line\\|non\\-falsy\\-string\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/AbstractPhpUnitFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 4,
	'path' => __DIR__ . '/../../src/Fixer/Alias/EregToPregFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getNextTokenOfKind\\(\\) expects int, int\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/PowToExponentiationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/SetTypeToCastFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on list\\<int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Alias/SetTypeToCastFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on list\\<int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Alias/SetTypeToCastFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on non\\-empty\\-array\\<int, int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/SetTypeToCastFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$slices of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:insertSlices\\(\\) expects array\\<int, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\<\'\'\\|int, array\\{PhpCsFixer\\\\Tokenizer\\\\Token, PhpCsFixer\\\\Tokenizer\\\\Token\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ArrayNotation/YieldFromArrayToYieldsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 5,
	'path' => __DIR__ . '/../../src/Fixer/Basic/NumericLiteralSeparatorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function strlen expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/Basic/PsrAutoloadingFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\|null might not exist on array\\<int, string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Casing/MagicConstantCasingFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'elements\' on array\\{index\\: int, open\\: int\\|null, close\\: int\\<0, max\\>, elements\\: list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'property\'\\|\'trait_import\', index\\: int, start\\: int, end\\: int\\}\\>\\}\\|false\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'end\' might not exist on array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'start\' might not exist on array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 11,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on non\\-empty\\-list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<1, max\\> might not exist on non\\-empty\\-list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$class of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:getFirstTokenIndexOfClassElement\\(\\) expects array\\{index\\: int, open\\: int, close\\: int, elements\\: non\\-empty\\-list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\>\\}, array\\{index\\: int, open\\: int\\|null, close\\: int\\<0, max\\>, elements\\: list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'property\'\\|\'trait_import\', index\\: int, start\\: int, end\\: int\\}\\>\\}\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$class of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:getLastTokenIndexOfClassElement\\(\\) expects array\\{index\\: int, open\\: int, close\\: int, elements\\: non\\-empty\\-list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\>\\}, array\\{index\\: int, open\\: int\\|null, close\\: int\\<0, max\\>, elements\\: list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'property\'\\|\'trait_import\', index\\: int, start\\: int, end\\: int\\}\\>\\}\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassDefinitionFixer\\:\\:getClassyDefinitionInfo\\(\\) should return array\\{start\\: int, classy\\: int, open\\: int, extends\\: array\\{start\\: int, numberOfExtends\\: int, multiLine\\: bool\\}\\|false, implements\\: array\\{start\\: int, numberOfImplements\\: int, multiLine\\: bool\\}\\|false, anonymousClass\\: bool, final\\: int\\|false, abstract\\: int\\|false, \\.\\.\\.\\} but returns array\\{classy\\: int, open\\: int\\|null, extends\\: array\\<string, bool\\|int\\>\\|false, implements\\: array\\<string, bool\\|int\\>\\|false, anonymousClass\\: bool, final\\: int\\|false, abstract\\: int\\|false, readonly\\: int\\|false, \\.\\.\\.\\}\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'multiLine\' might not exist on non\\-empty\\-array\\<string, bool\\|int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on non\\-empty\\-array\\<string, bool\\|int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, bool\\|int given\\.$#',
	'identifier' => 'booleanNot.exprNotBoolean',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an elseif condition, array\\<string, bool\\|int\\>\\|false given\\.$#',
	'identifier' => 'elseif.condNotBoolean',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an if condition, array\\<string, bool\\|int\\>\\|false given\\.$#',
	'identifier' => 'if.condNotBoolean',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in pre\\-increment, bool\\|int given\\.$#',
	'identifier' => 'preInc.nonNumeric',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'identifier' => 'plus.leftNonNumeric',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoBlankLinesAfterClassOpeningFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<1, max\\> might not exist on array\\<int\\<1, max\\>, int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoNullPropertyInitializationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<1, max\\> might not exist on non\\-empty\\-array\\<int\\<1, max\\>, int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoNullPropertyInitializationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\NoPhp4ConstructorFixer\\:\\:findFunction\\(\\) should return array\\{nameIndex\\: int, startIndex\\: int, endIndex\\: int, bodyIndex\\: int, modifiers\\: list\\<int\\>\\}\\|null but returns array\\{nameIndex\\: int\\<0, max\\>, startIndex\\: int, endIndex\\: int\\|null, bodyIndex\\: int\\|null, modifiers\\: array\\<\'\'\\|int, int\\>\\}\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\NoPhp4ConstructorFixer\\:\\:getWrapperMethodSequence\\(\\) should return array\\{list\\<list\\<array\\{int, string\\}\\|int\\|string\\>\\>, array\\{3\\: false\\}\\} but returns array\\{list\\<non\\-empty\\-list\\<\'\\(\'\\|\'\\)\'\\|\',\'\\|\';\'\\|\'\\{\'\\|\'\\}\'\\|array\\{0\\: int, 1\\?\\: string\\}\\>\\>, array\\{3\\: false\\}\\}\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 4,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<1, max\\> might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sequence of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) expects non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, list\\<array\\{int, string\\}\\|int\\|string\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'class_is_final\' might not exist on array\\{classIndex\\: int, token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, class_is_final\\?\\: bool, method_final_index\\: int\\|null, method_is_constructor\\?\\: bool, method_is_private\\: bool, method_of_enum\\: false\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'method_is_constructor\' might not exist on array\\{classIndex\\: int, token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, class_is_final\\: false, method_final_index\\: int\\|null, method_is_constructor\\?\\: bool, method_is_private\\: true, method_of_enum\\: false\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<int, bool\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:getElements\\(\\) should return list\\<array\\{start\\: int, visibility\\: string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\: string, name\\: string, end\\: int\\}\\> but returns list\\<array\\{start\\: int, visibility\\: \'public\', abstract\\: false, static\\: false, readonly\\: bool, type\\: string, name\\?\\: string, end\\: int\\}\\|array\\{start\\: int, visibility\\: non\\-empty\\-string, abstract\\: bool, static\\: bool, readonly\\: bool\\}\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'abstract\' might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'end\' might not exist on array\\{start\\: int, visibility\\: non\\-empty\\-string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\?\\: string, name\\?\\: string, end\\?\\: int\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'name\' might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'readonly\' might not exist on non\\-empty\\-array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'static\' might not exist on non\\-empty\\-array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'type\' might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'visibility\' might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<array\\{start\\: int, visibility\\: string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\: string, name\\: string, end\\: int\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset mixed might not exist on array\\<string, int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$a of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:sortGroupElements\\(\\) expects array\\{start\\: int, visibility\\: string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\: string, name\\: string, end\\: int\\}, array&T given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$b of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:sortGroupElements\\(\\) expects array\\{start\\: int, visibility\\: string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\: string, name\\: string, end\\: int\\}, array&T given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'normalized\' might not exist on array\\<\'normalized\'\\|\'originalIndex\'\\|\'tokens\'\\|int\\<0, max\\>, int\\|list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 6,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'originalIndex\' might not exist on array\\<\'normalized\'\\|\'originalIndex\'\\|\'tokens\'\\|int\\<0, max\\>, int\\|list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'tokens\' might not exist on array\\<\'normalized\'\\|\'originalIndex\'\\|\'tokens\'\\|int\\<0, max\\>, int\\|list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only iterables can be unpacked, int\\|list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given in argument \\#3\\.$#',
	'identifier' => 'argument.unpackNonIterable',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function strlen expects string, int\\|list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string1 of function strcasecmp expects string, int\\|list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$string2 of function strcasecmp expects string, int\\|list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Generator expects value type array\\<int, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\<int\\|string, PhpCsFixer\\\\Tokenizer\\\\Tokens\\> given\\.$#',
	'identifier' => 'generator.valueType',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedTraitsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<int, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedTraitsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$elements of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedTraitsFixer\\:\\:sort\\(\\) expects array\\<int, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\<int\\<0, max\\>\\|string, PhpCsFixer\\\\Tokenizer\\\\Tokens\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedTraitsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on non\\-empty\\-array\\<int, bool\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ProtectedToPrivateFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, int\\|false\\|null given on the left side\\.$#',
	'identifier' => 'booleanAnd.leftNotBoolean',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ProtectedToPrivateFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\<int\\<0, max\\>, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/IncludeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on array\\{0\\?\\: string, 1\\?\\: string, 2\\?\\: non\\-falsy\\-string\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/NoBreakCommentFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on array\\{0\\?\\: string, 1\\?\\: string, 2\\?\\: non\\-falsy\\-string\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/NoBreakCommentFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\|null might not exist on array\\<int\\|string, bool\\|null\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<int\\|string, bool\\|null\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$possibleKind of method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:isGivenKind\\(\\) expects int\\|list\\<int\\>, list\\<int\\|string\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/CombineNestedDirnameFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on non\\-empty\\-array\\<int, int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/DateTimeCreateFromFormatCallFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\ImplodeCallFixer\\:\\:getArgumentIndices\\(\\) should return array\\<int, int\\> but returns array\\<int\\|string, int\\|null\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/ImplodeCallFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/ImplodeCallFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on non\\-empty\\-array\\<int, int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/ImplodeCallFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$others of method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:equalsAny\\(\\) expects list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, array\\<int, array\\<int, int\\|string\\>\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/PhpdocToReturnTypeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\Analysis\\\\ArgumentAnalysis\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'identifier' => 'plus.leftNonNumeric',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\Import\\\\FullyQualifiedStrictTypesFixer\\:\\:\\$reservedIdentifiersByLevel \\(array\\<int\\<0, max\\>, array\\<string, true\\>\\>\\) does not accept non\\-empty\\-array\\<int, array\\<string, true\\>\\>\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\Analysis\\\\NamespaceAnalysis\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on non\\-empty\\-array\\<int, PhpCsFixer\\\\DocBlock\\\\DocBlock\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$types of method PhpCsFixer\\\\DocBlock\\\\Annotation\\:\\:setTypes\\(\\) expects list\\<string\\>, array\\<int\\<0, max\\>, string\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\Analysis\\\\NamespaceUseAnalysis\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GroupImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'identifier' => 'plus.leftNonNumeric',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GroupImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/NoUnusedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Import\\\\OrderedImportsFixer\\:\\:getNewOrder\\(\\) should return array\\<int, array\\{namespace\\: non\\-empty\\-string, startIndex\\: int, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\> but returns array\\<\'\'\\|int, array\\{namespace\\: string, startIndex\\: int\\|null, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<int, int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on list\\<int\\|null\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-array\\<int\\<0, max\\>, non\\-empty\\-list\\<int\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<1, max\\> might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 6,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<1, max\\> might not exist on non\\-empty\\-list\\<int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$indices of method PhpCsFixer\\\\Fixer\\\\Import\\\\OrderedImportsFixer\\:\\:sortByAlgorithm\\(\\) expects array\\<int, array\\{namespace\\: non\\-empty\\-string, startIndex\\: int, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\>, array\\<\'\'\\|int, array\\{namespace\\: string, startIndex\\: int\\|null, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$indices of method PhpCsFixer\\\\Fixer\\\\Import\\\\OrderedImportsFixer\\:\\:sortByAlgorithm\\(\\) expects array\\<int, array\\{namespace\\: non\\-empty\\-string, startIndex\\: int, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\>, non\\-empty\\-array\\<\'\'\\|int, array\\{namespace\\: string, startIndex\\: int\\|null, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array\\<0\\|1\\|2, \'class\'\\|\'const\'\\|\'function\'\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<non\\-falsy\\-string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/SingleImportPerStatementFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to static method PhpCsFixer\\\\Preg\\:\\:match\\(\\) with arguments \'\\#\\^\\.\\*\\?\\(\\?P\\<annotation…\', mixed and array\\{\\} will always evaluate to false\\.$#',
	'identifier' => 'staticMethod.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Internal/ConfigurableFixerTemplateFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Internal/ConfigurableFixerTemplateFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Internal/ConfigurableFixerTemplateFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'identifier' => 'plus.leftNonNumeric',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getNextMeaningfulToken\\(\\) expects int, int\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'get_called_class\'\\|\'get_class\'\\|\'get_class_this\'\\|\'php_sapi_name\'\\|\'phpversion\'\\|\'pi\' might not exist on array\\<string, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 6,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/IsNullFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'endIndex\' might not exist on array\\<string, bool\\|int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'endIndex\' might not exist on non\\-empty\\-array\\<string, bool\\|int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'isFirst\' might not exist on array\\<string, bool\\|int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'isFirst\' might not exist on non\\-empty\\-array\\<string, bool\\|int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'isToTransform\' might not exist on array\\<string, bool\\|int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 5,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'isToTransform\' might not exist on non\\-empty\\-array\\<string, bool\\|int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'startIndex\' might not exist on non\\-empty\\-array\\<string, bool\\|int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, bool\\|int given on the left side\\.$#',
	'identifier' => 'booleanAnd.leftNotBoolean',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, bool\\|int given on the right side\\.$#',
	'identifier' => 'booleanAnd.rightNotBoolean',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, bool\\|int given\\.$#',
	'identifier' => 'booleanNot.exprNotBoolean',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an if condition, bool\\|int given\\.$#',
	'identifier' => 'if.condNotBoolean',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, bool\\|int given on the left side\\.$#',
	'identifier' => 'plus.leftNonNumeric',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getNextTokenOfKind\\(\\) expects int, bool\\|int given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getPrevTokenOfKind\\(\\) expects int, bool\\|int given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:insertAt\\(\\) expects int, bool\\|int given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'abstract\'\\|\'as\'\\|\'attribute\'\\|\'break\'\\|\'case\'\\|\'catch\'\\|\'class\'\\|\'clone\'\\|\'comment\'\\|\'const\'\\|\'const_import\'\\|\'continue\'\\|\'do\'\\|\'echo\'\\|\'else\'\\|\'elseif\'\\|\'enum\'\\|\'extends\'\\|\'final\'\\|\'finally\'\\|\'for\'\\|\'foreach\'\\|\'function\'\\|\'function_import\'\\|\'global\'\\|\'goto\'\\|\'if\'\\|\'implements\'\\|\'include\'\\|\'include_once\'\\|\'instanceof\'\\|\'insteadof\'\\|\'interface\'\\|\'match\'\\|\'named_argument\'\\|\'namespace\'\\|\'new\'\\|\'open_tag_with_echo\'\\|\'php_doc\'\\|\'php_open\'\\|\'print\'\\|\'private\'\\|\'protected\'\\|\'public\'\\|\'readonly\'\\|\'require\'\\|\'require_once\'\\|\'return\'\\|\'static\'\\|\'switch\'\\|\'throw\'\\|\'trait\'\\|\'try\'\\|\'type_colon\'\\|\'use\'\\|\'use_lambda\'\\|\'use_trait\'\\|\'var\'\\|\'while\'\\|\'yield\'\\|\'yield_from\' might not exist on array\\<string, int\\|null\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/SingleSpaceAroundConstructFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'as\'\\|\'else\'\\|\'elseif\'\\|\'use_lambda\' might not exist on array\\<string, int\\|null\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/SingleSpaceAroundConstructFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'yield_from\' might not exist on array\\<string, int\\|null\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/SingleSpaceAroundConstructFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'identifier' => 'plus.leftNonNumeric',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/NamespaceNotation/BlankLinesBeforeNamespaceFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Naming/NoHomoglyphNamesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-array\\<int\\<0, max\\>, string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 5,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, int\\|false given\\.$#',
	'identifier' => 'booleanNot.exprNotBoolean',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\-, int\\<0, max\\>\\|false given on the right side\\.$#',
	'identifier' => 'minus.rightNonNumeric',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitAttributesFixer\\:\\:createAttributeTokens\\(\\) should return list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\> but returns non\\-empty\\-array\\<int\\|string, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitAttributesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on non\\-empty\\-array\\<string, bool\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitAttributesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$name of static method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitAttributesFixer\\:\\:toClassConstant\\(\\) expects class\\-string, string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 5,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitAttributesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on non\\-empty\\-array\\<int\\<0, max\\>, int\\|null\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitConstructFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitConstructFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset non\\-empty\\-string might not exist on array\\<string, string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitConstructFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method isGivenKind\\(\\) on PhpCsFixer\\\\Tokenizer\\\\Token\\|false\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on list\\<int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<int, int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'expectExceptionMessageRegExp\' might not exist on array\\<string, string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitExpectationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitExpectationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<1, max\\> might not exist on list\\<int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitExpectationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitExpectationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitMethodCasingFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitMethodCasingFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 3 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitMethodCasingFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitMethodCasingFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'expectedException\' might not exist on array\\<string, string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitTestAnnotationFixer\\:\\:updateLines\\(\\) should return list\\<PhpCsFixer\\\\DocBlock\\\\Line\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 4,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-array\\<int\\<0, max\\>, PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 6,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<8, max\\> might not exist on list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\<0, max\\>\\|false given on the left side\\.$#',
	'identifier' => 'plus.leftNonNumeric',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<array\\{int, string\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestCaseStaticMethodCallsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on list\\<array\\{int, string\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestCaseStaticMethodCallsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, list\\<array\\{int, string\\}\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestCaseStaticMethodCallsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/GeneralPhpdocTagRenameFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset mixed might not exist on non\\-empty\\-array\\<string, string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/GeneralPhpdocTagRenameFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/NoBlankLinesAfterPhpdocFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type non\\-empty\\-string is not subtype of native type non\\-falsy\\-string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/NoSuperfluousPhpdocTagsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on array\\{0\\?\\: string, 1\\?\\: string\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAddMissingParamAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<\\-1, max\\> might not exist on list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAddMissingParamAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\PhpdocAlignFixer\\:\\:getMatches\\(\\) should return array\\{indent\\: string\\|null, tag\\: string\\|null, hint\\: string, var\\: string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\|null but returns non\\-empty\\-array\\<string\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'desc\' might not exist on array\\{indent\\: string\\|null, tag\\: null, hint\\: string, var\\: string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'desc\' might not exist on array\\{indent\\: string\\|null, tag\\: string, hint\\: string, var\\: \'\', static\\: string, desc\\?\\: string\\|null\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'desc\' might not exist on array\\{indent\\: string\\|null, tag\\: string, hint\\: string, var\\: non\\-empty\\-string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'hint2\' might not exist on non\\-empty\\-array\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'hint3\' might not exist on non\\-empty\\-array\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'signature\' might not exist on non\\-empty\\-array\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'static\' might not exist on non\\-empty\\-array\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<array\\{indent\\: string\\|null, tag\\: string\\|null, hint\\: string, var\\: string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAnnotationWithoutDotFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAnnotationWithoutDotFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 3 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAnnotationWithoutDotFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocArrayTypeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocArrayTypeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocInlineTagNormalizerFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocInlineTagNormalizerFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'general_phpdoc_tag_rename\' might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocNoAliasTagFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocNoUselessInheritdocFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEnd\\(\\) on PhpCsFixer\\\\DocBlock\\\\Annotation\\|false\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getStart\\(\\) on PhpCsFixer\\\\DocBlock\\\\Annotation\\|false\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on list\\<PhpCsFixer\\\\DocBlock\\\\Annotation\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$content of method PhpCsFixer\\\\DocBlock\\\\Line\\:\\:setContent\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<1, max\\> might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocSingleLineVarSpacingFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'general_phpdoc_tag_rename\' might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTagCasingFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1\\|int\\<3, max\\> might not exist on array\\<int\\<0, max\\>, string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTagTypeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<int, string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTagTypeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTypesOrderFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on list\\<int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ReturnNotation/ReturnAssignmentFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Strict/StrictParamFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'string_implicit_backslashes\' might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/StringNotation/EscapeImplicitBackslashesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'end_index\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 4,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'initial_indent\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'new_indent\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'type\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on non\\-empty\\-list\\<non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$str of function preg_quote expects string, int\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$parentScopeEndIndex of method PhpCsFixer\\\\Fixer\\\\Whitespace\\\\ArrayIndentationFixer\\:\\:findExpressionEndIndex\\(\\) expects int, int\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on list\\<int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/BlankLineBetweenImportGroupsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on array\\{0\\?\\: string\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/HeredocIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/IndentationTypeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/IndentationTypeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/IndentationTypeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<1, max\\> might not exist on list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoExtraBlankLinesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\|null might not exist on array\\<int, callable\\(int\\)\\: void\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoExtraBlankLinesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, callable\\(int\\)\\: void\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoExtraBlankLinesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoTrailingWhitespaceFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<1, max\\> might not exist on array\\<int\\<0, max\\>, string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoTrailingWhitespaceFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-array\\<int\\<0, max\\>, string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoWhitespaceInBlankLineFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'new_indent\' might not exist on array\\{type\\: \'statement\', skip\\: bool, end_index\\: int\\|null, end_index_inclusive\\: bool, initial_indent\\: string, new_indent\\?\\: string, is_indented_block\\: bool\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<int, int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on list\\<array\\{type\\: \'block\'\\|\'block_signature\'\\|\'statement\', skip\\: bool, end_index\\: int\\|null, end_index_inclusive\\: bool, initial_indent\\: string, new_indent\\?\\: string, is_indented_block\\: bool\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<\\-1, max\\> might not exist on list\\<array\\{type\\: \'block\'\\|\'block_signature\'\\|\'statement\', skip\\: bool, end_index\\: int\\|null, end_index_inclusive\\: bool, initial_indent\\: string, new_indent\\?\\: string, is_indented_block\\: bool\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to static method PhpCsFixer\\\\Preg\\:\\:match\\(\\) with arguments \'/array\\<\\\\\\\\w\\+,\\\\\\\\s\\*\\(\\\\\\\\\\?\\?\\[…\', string and array\\{\\} will always evaluate to false\\.$#',
	'identifier' => 'staticMethod.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/../../src/FixerConfiguration/FixerConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on non\\-empty\\-array\\<string, array\\<int\\<0, max\\>, string\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/FixerFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$fixerConflicts of method PhpCsFixer\\\\FixerFactory\\:\\:generateConflictMessage\\(\\) expects array\\<string, list\\<string\\>\\>, non\\-empty\\-array\\<string, non\\-empty\\-array\\<int\\<0, max\\>, string\\>\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/FixerFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array\\<int\\<0, max\\>, string\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/FixerFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function md5 expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/CachingLinter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of function file_put_contents expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/ProcessLinter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of method PhpCsFixer\\\\Linter\\\\ProcessLinter\\:\\:createProcessForFile\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/ProcessLinter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\$path of class Symfony\\\\Component\\\\Filesystem\\\\Exception\\\\IOException constructor expects string\\|null, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/ProcessLinter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Preg\\:\\:replace\\(\\) should return string but returns list\\<string\\>\\|string\\.$#',
	'identifier' => 'return.type',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to static method PhpCsFixer\\\\Preg\\:\\:match\\(\\) with arguments \'\\#\\^@PHP\\(\\[\\\\\\\\d\\]\\{2\\}…\', string and array\\{\\} will always evaluate to false\\.$#',
	'identifier' => 'staticMethod.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/AbstractMigrationSetDescription.php',
];
$ignoreErrors[] = [
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the right side\\.$#',
	'identifier' => 'plus.rightNonNumeric',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/AbstractRuleSetDescription.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, array\\<string, mixed\\>\\|true\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/RuleSet.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, array\\<string, mixed\\>\\|bool given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/RuleSet.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'action\' might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'file\' might not exist on non\\-empty\\-array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'identifier\' might not exist on non\\-empty\\-array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'status\' might not exist on non\\-empty\\-array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<non\\-empty\\-string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'alternative_syntax\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'brace_count\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'default\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'index\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'kind\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<\\-1, max\\> might not exist on array\\<int\\<0, max\\>, non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on list\\<non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-array\\<int\\<0, max\\>, non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 6,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$analysis of static method PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\ControlCaseStructuresAnalyzer\\:\\:buildControlCaseStructureAnalysis\\(\\) expects array\\{kind\\: int, index\\: int, open\\: int, end\\: int, cases\\: list\\<array\\{index\\: int, open\\: int\\}\\>, default\\: array\\{index\\: int, open\\: int\\}\\|null\\}, non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset non\\-falsy\\-string might not exist on array\\<string, list\\<int\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/SwitchAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findGivenKind\\(\\) should return array\\<int, array\\<int\\<0, max\\>, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\> but returns array\\<\'\'\\|int, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findOppositeBlockEdge\\(\\) should return int\\<0, max\\> but returns int\\.$#',
	'identifier' => 'return.type',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) should return non\\-empty\\-array\\<int\\<0, max\\>, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|null but returns non\\-empty\\-array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on non\\-empty\\-array\\<\'\'\\|int, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\|string might not exist on non\\-empty\\-array\\<int\\|string, int\\<0, max\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset non\\-empty\\-string might not exist on array\\<non\\-empty\\-string, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$others of method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:equalsAny\\(\\) expects list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, non\\-empty\\-array\\<int\\<0, max\\>, array\\{int\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$possibleKind of method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:isGivenKind\\(\\) expects int\\|list\\<int\\>, non\\-empty\\-array\\<int\\<0, max\\>, int\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, iterable\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Tokens given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$name of static method PhpCsFixer\\\\Tokenizer\\\\Processor\\\\ImportProcessor\\:\\:tokenizeName\\(\\) expects class\\-string, string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Tokenizer/Transformer/NameQualifiedTransformer.php',
];
$ignoreErrors[] = [
	'message' => '#^Generator expects value type PhpCsFixer\\\\Tokenizer\\\\TransformerInterface, object given\\.$#',
	'identifier' => 'generator.valueType',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Transformers.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$json of function json_decode expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../src/ToolInfo.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 does not exist on array&T of mixed\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Utils.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\CiConfigurationTest\\:\\:getPhpVersionsUsedForBuildingLocalImages\\(\\) should return list\\<numeric\\-string\\> but returns array\\<string\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\CiConfigurationTest\\:\\:getPhpVersionsUsedForBuildingOfficialImages\\(\\) should return list\\<numeric\\-string\\> but returns array\\<mixed\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$code of static method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:fromCode\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<Symfony\\\\Component\\\\Console\\\\Command\\\\Command\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/AutoReview/CommandTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'scripts\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/AutoReview/ComposerFileTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'scripts\\-descriptions\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/ComposerFileTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$json of function json_decode expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ComposerFileTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$callback of function array_reduce expects callable\\(array, int\\|string\\)\\: array, Closure\\(array, string\\)\\: array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ComposerFileTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of function file_get_contents expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$json of function json_decode expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$haystack of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringContainsString\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'reflection\' on array\\{reflection\\: ReflectionObject, short_classname\\: string\\}\\|PhpCsFixer\\\\Fixer\\\\FixerInterface\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'short_classname\' on array\\{reflection\\: ReflectionObject, short_classname\\: string\\}\\|PhpCsFixer\\\\Fixer\\\\FixerInterface\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \\(int\\|string\\) might not exist on array\\<string, array\\{reflection\\: ReflectionObject, short_classname\\: string\\}\\|PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 4,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on list\\<PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on list\\<PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$file of class Symfony\\\\Component\\\\Finder\\\\SplFileInfo constructor expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$file on SimpleXMLElement\\|false\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method xpath\\(\\) on SimpleXMLElement\\|false\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\ProjectCodeTest\\:\\:extractFunctionNamesCalledInClass\\(\\) should return list\\<string\\> but returns array\\<int, string\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\ProjectCodeTest\\:\\:getFileContentForClass\\(\\) should return string but returns string\\|false\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on array\\<SimpleXMLElement\\|false\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset class\\-string might not exist on array\\<class\\-string, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<ReflectionParameter\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, int\\<0, max\\>\\|false given on the right side\\.$#',
	'identifier' => 'booleanAnd.rightNotBoolean',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\)\\: mixed\\)\\|null, Closure\\(PhpCsFixer\\\\Tokenizer\\\\Token\\)\\: string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$content of class PhpCsFixer\\\\DocBlock\\\\DocBlock constructor expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$data of function simplexml_load_string expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$callback of function array_filter expects \\(callable\\(PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\)\\: bool\\)\\|null, Closure\\(PhpCsFixer\\\\Tokenizer\\\\Token\\)\\: bool given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'array_typehint\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'attribute\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'brace\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 4,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'brace_class_instantiation\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'disjunctive_normal_form_type_parenthesis\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'import\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'name_qualified\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'named_argument\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'namespace_operator\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'nullable_type\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'return_ref\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'square_brace\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'type_alternation\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'type_colon\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 6,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'type_intersection\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'use\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Cache/FileCacheManagerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/ConfigTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertFileExists\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/ListFilesCommandTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of static method Symfony\\\\Component\\\\Filesystem\\\\Path\\:\\:makeRelative\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/ListFilesCommandTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function ltrim expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/ListFilesCommandTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$basePath of static method Symfony\\\\Component\\\\Filesystem\\\\Path\\:\\:makeRelative\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/ListFilesCommandTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'argv\' might not exist on array\\<mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/SelfUpdateCommandTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'action\' might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/WorkerCommandTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'action\' might not exist on array\\<int\\|string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/Console/Command/WorkerCommandTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'status\' might not exist on non\\-empty\\-array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/WorkerCommandTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Anonymous function should return non\\-empty\\-string but returns non\\-empty\\-string\\|false\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/ConfigurationResolverTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function sort expects TArray of array\\<string\\>, Exception\\|list\\<string\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/ConfigurationResolverTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$stream of class Symfony\\\\Component\\\\Console\\\\Output\\\\StreamOutput constructor expects resource, resource\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Output/ErrorOutputTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\<string\\>\\|string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Output/ErrorOutputTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'message\' might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Report/FixReport/GitlabReporterTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'property\' might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Report/FixReport/GitlabReporterTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'message\' might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Report/FixReport/JsonReporterTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'property\' might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Report/FixReport/JsonReporterTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'message\' might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Report/ListSetsReport/JsonReporterTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'property\' might not exist on array\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Report/ListSetsReport/JsonReporterTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int\\<0, max\\> might not exist on list\\<int\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/DocBlock/AnnotationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, array\\<string, string\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Alias/NoAliasFunctionsFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\Fixer\\\\Basic\\\\EncodingFixerTest\\:\\:prepareTestCase\\(\\) should return array\\{string, string\\|null, SplFileInfo\\} but returns array\\{string\\|false, string\\|false\\|null, SplFileInfo\\}\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Basic/EncodingFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'classy\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/ClassNotation/ClassDefinitionFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'start\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/Fixer/ClassNotation/ClassDefinitionFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an elseif condition, int\\|false given\\.$#',
	'identifier' => 'elseif.condNotBoolean',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/FunctionNotation/MethodArgumentSpaceFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Generator expects value type array\\{0\\: string, 1\\: string\\|null, 2\\?\\: array\\<string, bool\\>\\}, array\\{0\\: string, 1\\?\\: string\\} given\\.$#',
	'identifier' => 'generator.valueType',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/FunctionNotation/NullableTypeDeclarationForDefaultNullValueFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, string\\|false given\\.$#',
	'identifier' => 'booleanNot.exprNotBoolean',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/PhpTag/NoClosingTagFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Generator expects value type array\\{0\\: string, 1\\?\\: string\\}, list\\<string\\> given\\.$#',
	'identifier' => 'generator.valueType',
	'count' => 5,
	'path' => __DIR__ . '/../../tests/Fixer/PhpUnit/PhpUnitDataProviderReturnTypeFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Generator expects value type array\\{string, string\\}, list\\<string\\> given\\.$#',
	'identifier' => 'generator.valueType',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Fixer/PhpUnit/PhpUnitDataProviderReturnTypeFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\Fixer\\\\PhpUnit\\\\PhpUnitDataProviderReturnTypeFixerTest\\:\\:mapToTemplate\\(\\) should return list\\<string\\> but returns array\\<int\\|string, string\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/PhpUnit/PhpUnitDataProviderReturnTypeFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, string\\|false given\\.$#',
	'identifier' => 'booleanNot.exprNotBoolean',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Semicolon/NoEmptyStatementFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, string\\|false given\\.$#',
	'identifier' => 'booleanNot.exprNotBoolean',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Semicolon/SemicolonAfterInstructionFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to new PhpCsFixer\\\\FixerConfiguration\\\\AliasedFixerOption\\(\\) on a separate line has no effect\\.$#',
	'identifier' => 'new.resultUnused',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/FixerConfiguration/AliasedFixerOptionTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$code of class PhpCsFixer\\\\FixerDefinition\\\\FileSpecificCodeSample constructor expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/FixerDefinition/FileSpecificCodeSampleTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$minimum of class PhpCsFixer\\\\FixerDefinition\\\\VersionSpecification constructor expects int\\<1, max\\>\\|null, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/FixerDefinition/VersionSpecificationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$maximum of class PhpCsFixer\\\\FixerDefinition\\\\VersionSpecification constructor expects int\\<1, max\\>\\|null, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/FixerDefinition/VersionSpecificationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, array\\<string, mixed\\>\\|true\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$pattern of static method PhpCsFixer\\\\Preg\\:\\:replace\\(\\) expects string, list\\<string\\>\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/PregTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$pattern of static method PhpCsFixer\\\\Preg\\:\\:replaceCallback\\(\\) expects string, list\\<string\\>\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/PregTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of static method PhpCsFixer\\\\Preg\\:\\:replaceCallback\\(\\) expects string, list\\<string\\>\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/PregTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/RuleSet/RuleSetTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\RuleSet\\\\RuleSetDescriptionInterface\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/RuleSet/RuleSetsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/RuleSet/RuleSetsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset mixed might not exist on array\\<int\\|string, SplFileInfo\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Runner/FileCachingLintingFileIteratorTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 3 might not exist on non\\-empty\\-list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 4 might not exist on non\\-empty\\-list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 5 might not exist on non\\-empty\\-list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 6 might not exist on non\\-empty\\-list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 7 might not exist on non\\-empty\\-list\\<string\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\<string\\>\\|string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, string\\|false given\\.$#',
	'identifier' => 'ternary.condNotBoolean',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\)\\: mixed\\)\\|null, Closure\\(PhpCsFixer\\\\Tokenizer\\\\Token\\)\\: string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$code of static method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:fromCode\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sequence of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) expects non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, list\\<array\\{0\\: int, 1\\?\\: string\\}\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractIntegrationCaseFactory\\:\\:determineRequirements\\(\\) should return array\\{php\\: int, php\\<\\: int, os\\: list\\<string\\>\\} but returns non\\-empty\\-array\\<string, mixed\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractIntegrationCaseFactory\\:\\:determineSettings\\(\\) should return array\\{checkPriority\\: bool, deprecations\\: list\\<string\\>\\} but returns non\\-empty\\-array\\<string, mixed\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'checkPriority\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'deprecations\' might not exist on non\\-empty\\-array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'indent\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'lineEnding\' might not exist on non\\-empty\\-array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'os\' might not exist on non\\-empty\\-array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'php\' might not exist on array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'php\\<\' might not exist on non\\-empty\\-array\\<string, mixed\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, int\\|false given\\.$#',
	'identifier' => 'booleanNot.exprNotBoolean',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, string\\|false given\\.$#',
	'identifier' => 'ternary.condNotBoolean',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$dirs of method Symfony\\\\Component\\\\Finder\\\\Finder\\:\\:in\\(\\) expects array\\<string\\>\\|string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of function is_dir expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function strlen expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$fixedInputCode of static method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractIntegrationTestCase\\:\\:assertRevertedOrderFixing\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$fixedInputCodeWithReversedFixers of static method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractIntegrationTestCase\\:\\:assertRevertedOrderFixing\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of static method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:getNameForId\\(\\) expects int, int\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractTransformerTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$prototypes of method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractTransformerTestCase\\:\\:countTokenPrototypes\\(\\) expects list\\<array\\{0\\: int, 1\\?\\: string\\}\\>, array\\<int\\<0, max\\>, array\\{int\\}\\|string\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractTransformerTestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on array\\{0\\: string, 1\\?\\: string\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/TestCaseUtils.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Tests\\\\TestCase\\:\\:\\$actualDeprecations \\(list\\<string\\>\\) does not accept array\\<int\\<0, max\\>, string\\>\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/TestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Tests\\\\TestCase\\:\\:\\$expectedDeprecations \\(list\\<string\\>\\) does not accept array\\<int\\<0, max\\>, string\\>\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/TestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<int, PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\Analysis\\\\AbstractControlCaseStructuresAnalysis\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Tokenizer/Analyzer/ControlCaseStructuresAnalyzerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<int, bool\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensAnalyzerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on list\\<int\\<0, max\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 0 might not exist on non\\-empty\\-array\\<int\\<0, 3\\>, array\\{tokens\\: non\\-empty\\-list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>, content\\: literal\\-string&non\\-falsy\\-string\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on list\\<int\\<0, max\\>\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 1 might not exist on non\\-empty\\-array\\<int\\<0, 3\\>, array\\{tokens\\: non\\-empty\\-list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>, content\\: literal\\-string&non\\-falsy\\-string\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 2 might not exist on non\\-empty\\-array\\<int\\<0, 3\\>, array\\{tokens\\: non\\-empty\\-list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>, content\\: literal\\-string&non\\-falsy\\-string\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset 3 might not exist on non\\-empty\\-array\\<int\\<0, 3\\>, array\\{tokens\\: non\\-empty\\-list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>, content\\: literal\\-string&non\\-falsy\\-string\\}\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset int might not exist on array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sequence of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) expects non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sequence of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) expects non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, list\\<mixed\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$slices of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:insertSlices\\(\\) expects array\\<int, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\{16\\: array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>, 6\\: array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\} given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
