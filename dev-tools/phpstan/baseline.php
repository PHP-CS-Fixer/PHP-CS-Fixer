<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on list\\<int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/AbstractFopenFlagFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on array\\<int, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/AbstractFopenFlagFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/AbstractFunctionReferenceFixer.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getPriority\\(\\) on PhpCsFixer\\\\Fixer\\\\FixerInterface\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/AbstractProxyFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Cache/Cache.php',
];
$ignoreErrors[] = [
	// identifier: booleanNot.exprNotBoolean
	'message' => '#^Only booleans are allowed in a negated boolean, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Cache/Signature.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Config\\:\\:getFinder\\(\\) should return PhpCsFixer\\\\Finder but returns iterable\\<SplFileInfo\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Config.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<string\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
	// identifier: minus.leftNonNumeric
	'message' => '#^Only numeric types are allowed in \\-, int\\|false given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset non\\-falsy\\-string might not exist on array\\<string, PhpCsFixer\\\\RuleSet\\\\RuleSetDescriptionInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\RuleSet\\\\RuleSetDescriptionInterface\\>\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, array\\<non\\-falsy\\-string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$time of class PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\ReportSummary constructor expects int, float\\|int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$basePath of static method Symfony\\\\Component\\\\Filesystem\\\\Path\\:\\:makeRelative\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/ListFilesCommand.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/ListFilesCommand.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'argv\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/SelfUpdateCommand.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'major\' might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/SelfUpdateCommand.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'files\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/WorkerCommand.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'allow\\-risky\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'cache\\-file\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'config\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'diff\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'dry\\-run\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'path\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 6,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'path\\-mode\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'rules\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'sequential\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'show\\-progress\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'stop\\-on\\-violation\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'using\\-cache\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$filename of function is_file expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array\\<int\\<0, max\\>, string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property PhpCsFixer\\\\Console\\\\ConfigurationResolver\\:\\:\\$path \\(list\\<string\\>\\|null\\) does not accept array\\<non\\-empty\\-string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'line\' might not exist on array\\{function\\?\\: string, line\\?\\: int, file\\: string, class\\?\\: class\\-string, type\\?\\: \'\\-\\>\'\\|\'\\:\\:\', args\\?\\: array, object\\?\\: object\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Output/ErrorOutput.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on array\\<1\\|2\\|3\\|4\\|5\\|6, array\\{symbol\\: string, format\\: string, description\\: string\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Output/Progress/DotsOutput.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, class\\-string\\<PhpCsFixer\\\\Console\\\\Output\\\\Progress\\\\ProgressOutputInterface\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Output/Progress/ProgressOutputFactory.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\CheckstyleReporter\\:\\:generate\\(\\) should return string but returns string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/CheckstyleReporter.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$text of static method Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\:\\:escape\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/CheckstyleReporter.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$diffs of static method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\GitlabReporter\\:\\:getLines\\(\\) expects list\\<SebastianBergmann\\\\Diff\\\\Diff\\>, array\\<SebastianBergmann\\\\Diff\\\\Diff\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/GitlabReporter.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\JunitReporter\\:\\:generate\\(\\) should return string but returns string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/JunitReporter.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$text of static method Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\:\\:escape\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/JunitReporter.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$reporter of method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\ReporterFactory\\:\\:registerReporter\\(\\) expects PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\ReporterInterface, object given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/ReporterFactory.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Console\\\\Report\\\\FixReport\\\\XmlReporter\\:\\:generate\\(\\) should return string but returns string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/XmlReporter.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$text of static method Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\:\\:escape\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/XmlReporter.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$reporter of method PhpCsFixer\\\\Console\\\\Report\\\\ListSetsReport\\\\ReporterFactory\\:\\:registerReporter\\(\\) expects PhpCsFixer\\\\Console\\\\Report\\\\ListSetsReport\\\\ReporterInterface, object given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/ListSetsReport/ReporterFactory.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/SelfUpdate/NewVersionChecker.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on array\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Differ/DiffConsoleFormatter.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'types\' might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'variable\' might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on array\\<int, PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/../../src/DocBlock/Annotation.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/DocBlock.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/DocBlock.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/Line.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/Line.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'_array_shape_inner\' might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'_callable_argument\' might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'_callable_template…\' might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'array_shape_inners\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'array_shape_inner…\' might not exist on array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'array_shape_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'callable_arguments\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'callable_argument…\' might not exist on array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'callable_name\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'callable_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'callable_template\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'callable_template…\' might not exist on array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 6,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'callable_template…\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'conditional_cond…\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'conditional_false…\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'conditional_true…\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'generic_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'generic_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'nullable\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'parenthesized_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'parenthesized_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'type\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'types\' might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on array\\<string\\>\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on list\\<PhpCsFixer\\\\Doctrine\\\\Annotation\\\\Token\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Doctrine/Annotation/DocLexer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/DocumentationLocator.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/DocumentationLocator.php',
];
$ignoreErrors[] = [
	// identifier: plus.leftNonNumeric
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, array\\<non\\-falsy\\-string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\|string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \\(int\\|string\\) might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/RuleSetDocumentationGenerator.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Documentation/RuleSetDocumentationGenerator.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Error\\\\ErrorsManager\\:\\:getExceptionErrors\\(\\) should return list\\<PhpCsFixer\\\\Error\\\\Error\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\Error\\\\Error\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Error/ErrorsManager.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Error\\\\ErrorsManager\\:\\:getInvalidErrors\\(\\) should return list\\<PhpCsFixer\\\\Error\\\\Error\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\Error\\\\Error\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Error/ErrorsManager.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Error\\\\ErrorsManager\\:\\:getLintErrors\\(\\) should return list\\<PhpCsFixer\\\\Error\\\\Error\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\Error\\\\Error\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Error/ErrorsManager.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\AbstractPhpUnitFixer\\:\\:addInternalAnnotation\\(\\) should return list\\<PhpCsFixer\\\\DocBlock\\\\Line\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\DocBlock\\\\Line\\|non\\-falsy\\-string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/AbstractPhpUnitFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on array\\<int, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/EregToPregFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<int, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/EregToPregFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on array\\<int, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/EregToPregFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/../../src/Fixer/Alias/EregToPregFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getNextTokenOfKind\\(\\) expects int, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/PowToExponentiationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, array\\<int, int\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/RandomApiMigrationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/SetTypeToCastFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on list\\<int\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Alias/SetTypeToCastFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on list\\<int\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Alias/SetTypeToCastFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on non\\-empty\\-array\\<int, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/SetTypeToCastFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$slices of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:insertSlices\\(\\) expects array\\<int, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\<\'\'\\|int, array\\{PhpCsFixer\\\\Tokenizer\\\\Token, PhpCsFixer\\\\Tokenizer\\\\Token\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ArrayNotation/YieldFromArrayToYieldsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.nonOffsetAccessible
	'message' => '#^Cannot access offset \'end\' on array\\{name\\: string, start\\: int, end\\: int\\}\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/AttributeNotation/OrderedAttributesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.nonOffsetAccessible
	'message' => '#^Cannot access offset \'end\' on array\\{start\\: int, end\\: int, name\\: string\\}\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/AttributeNotation/OrderedAttributesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<array\\{name\\: string, start\\: int, end\\: int\\}\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/AttributeNotation/OrderedAttributesFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$string of function substr expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/AttributeNotation/OrderedAttributesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Basic/BracesPositionFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Basic/CurlyBracesPositionFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Basic/NoMultipleStatementsPerLineFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/../../src/Fixer/Basic/NumericLiteralSeparatorFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<list\\<string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Basic/NumericLiteralSeparatorFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 3 might not exist on array\\<list\\<string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Basic/NumericLiteralSeparatorFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 4 might not exist on array\\<list\\<string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Basic/NumericLiteralSeparatorFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 6 might not exist on array\\<list\\<string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Basic/NumericLiteralSeparatorFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 7 might not exist on array\\<list\\<string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Basic/NumericLiteralSeparatorFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$string of function strlen expects string, string\\|false given\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/Basic/PsrAutoloadingFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\|null might not exist on array\\<int, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Casing/MagicConstantCasingFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Casing/MagicMethodCasingFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.nonOffsetAccessible
	'message' => '#^Cannot access offset \'elements\' on array\\{index\\: int, open\\: int\\|null, close\\: int\\<0, max\\>, elements\\: list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'property\'\\|\'trait_import\', index\\: int, start\\: int, end\\: int\\}\\>\\}\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.nonOffsetAccessible
	'message' => '#^Cannot access offset \'index\' on array\\{classIndex\\: int, token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'property\'\\|\'trait_import\'\\}\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'end\' might not exist on array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'start\' might not exist on array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\.$#',
	'count' => 11,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on non\\-empty\\-list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<1, max\\> might not exist on non\\-empty\\-list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$class of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:getFirstTokenIndexOfClassElement\\(\\) expects array\\{index\\: int, open\\: int, close\\: int, elements\\: non\\-empty\\-list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\>\\}, array\\{index\\: int, open\\: int\\|null, close\\: int\\<0, max\\>, elements\\: list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'property\'\\|\'trait_import\', index\\: int, start\\: int, end\\: int\\}\\>\\}\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$class of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:getLastTokenIndexOfClassElement\\(\\) expects array\\{index\\: int, open\\: int, close\\: int, elements\\: non\\-empty\\-list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\>\\}, array\\{index\\: int, open\\: int\\|null, close\\: int\\<0, max\\>, elements\\: list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'property\'\\|\'trait_import\', index\\: int, start\\: int, end\\: int\\}\\>\\}\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassDefinitionFixer\\:\\:getClassyDefinitionInfo\\(\\) should return array\\{start\\: int, classy\\: int, open\\: int, extends\\: array\\{start\\: int, numberOfExtends\\: int, multiLine\\: bool\\}\\|false, implements\\: array\\{start\\: int, numberOfImplements\\: int, multiLine\\: bool\\}\\|false, anonymousClass\\: bool, final\\: int\\|false, abstract\\: int\\|false, \\.\\.\\.\\} but returns array\\{classy\\: int, open\\: int\\|null, extends\\: array\\<string, bool\\|int\\>\\|false, implements\\: array\\<string, bool\\|int\\>\\|false, anonymousClass\\: bool, final\\: int\\|false, abstract\\: int\\|false, readonly\\: int\\|false, \\.\\.\\.\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'multiLine\' might not exist on non\\-empty\\-array\\<string, bool\\|int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on non\\-empty\\-array\\<string, bool\\|int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	// identifier: booleanNot.exprNotBoolean
	'message' => '#^Only booleans are allowed in a negated boolean, bool\\|int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	// identifier: elseif.condNotBoolean
	'message' => '#^Only booleans are allowed in an elseif condition, array\\<string, bool\\|int\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	// identifier: if.condNotBoolean
	'message' => '#^Only booleans are allowed in an if condition, array\\<string, bool\\|int\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	// identifier: preInc.nonNumeric
	'message' => '#^Only numeric types are allowed in pre\\-increment, bool\\|int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	// identifier: plus.leftNonNumeric
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoBlankLinesAfterClassOpeningFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<1, max\\> might not exist on array\\<int\\<1, max\\>, int\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoNullPropertyInitializationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<1, max\\> might not exist on non\\-empty\\-array\\<int\\<1, max\\>, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoNullPropertyInitializationFixer.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\NoPhp4ConstructorFixer\\:\\:findFunction\\(\\) should return array\\{nameIndex\\: int, startIndex\\: int, endIndex\\: int, bodyIndex\\: int, modifiers\\: list\\<int\\>\\}\\|null but returns array\\{nameIndex\\: int\\<0, max\\>, startIndex\\: int, endIndex\\: int\\|null, bodyIndex\\: int\\|null, modifiers\\: array\\<\'\'\\|int, int\\>\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\NoPhp4ConstructorFixer\\:\\:getWrapperMethodSequence\\(\\) should return array\\{list\\<list\\<array\\{int, string\\}\\|int\\|string\\>\\>, array\\{3\\: false\\}\\} but returns array\\{list\\<non\\-empty\\-list\\<\'\\(\'\\|\'\\)\'\\|\',\'\\|\';\'\\|\'\\{\'\\|\'\\}\'\\|array\\{0\\: int, 1\\?\\: string\\}\\>\\>, array\\{3\\: false\\}\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<1, max\\> might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$sequence of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) expects non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, list\\<array\\{int, string\\}\\|int\\|string\\> given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.nonOffsetAccessible
	'message' => '#^Cannot access offset \'type\' on array\\{classIndex\\: int, token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: \'case\'\\|\'const\'\\|\'method\'\\|\'property\'\\|\'trait_import\'\\}\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'class_is_final\' might not exist on array\\{classIndex\\: int, token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, class_is_final\\?\\: bool, method_final_index\\: int\\|null, method_is_constructor\\?\\: bool, method_is_private\\: bool, method_of_enum\\: false\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'method_is…\' might not exist on array\\{classIndex\\: int, token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, class_is_final\\: false, method_final_index\\: int\\|null, method_is_constructor\\?\\: bool, method_is_private\\: true, method_of_enum\\: false\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on array\\<int, bool\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:getElements\\(\\) should return list\\<array\\{start\\: int, visibility\\: string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\: string, name\\: string, end\\: int\\}\\> but returns list\\<array\\{start\\: int, visibility\\: \'public\', abstract\\: false, static\\: false, readonly\\: bool, type\\: string, name\\?\\: string, end\\: int\\}\\|array\\{start\\: int, visibility\\: non\\-empty\\-string, abstract\\: bool, static\\: bool, readonly\\: bool\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'abstract\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'end\' might not exist on array\\{abstract\\: bool, end\\?\\: int, name\\?\\: string, readonly\\: bool, start\\: int, static\\: bool, type\\?\\: string, visibility\\: non\\-empty\\-string\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'name\' might not exist on array\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'readonly\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'static\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'type\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'visibility\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<array\\{start\\: int, visibility\\: string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\: string, name\\: string, end\\: int\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset mixed might not exist on array\\<string, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$a of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:sortGroupElements\\(\\) expects array\\{start\\: int, visibility\\: string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\: string, name\\: string, end\\: int\\}, array&T given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$b of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:sortGroupElements\\(\\) expects array\\{start\\: int, visibility\\: string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\: string, name\\: string, end\\: int\\}, array&T given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'normalized\' might not exist on array\\<\'normalized\'\\|\'originalIndex\'\\|\'tokens\'\\|int\\<0, max\\>, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|int\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>\\.$#',
	'count' => 6,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'originalIndex\' might not exist on array\\<\'normalized\'\\|\'originalIndex\'\\|\'tokens\'\\|int\\<0, max\\>, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|int\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'tokens\' might not exist on array\\<\'normalized\'\\|\'originalIndex\'\\|\'tokens\'\\|int\\<0, max\\>, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|int\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.unpackNonIterable
	'message' => '#^Only iterables can be unpacked, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|int\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given in argument \\#3\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$string of function strlen expects string, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|int\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$string1 of function strcasecmp expects string, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|int\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$string2 of function strcasecmp expects string, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|int\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
	// identifier: generator.valueType
	'message' => '#^Generator expects value type array\\<int, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\<int\\|string, PhpCsFixer\\\\Tokenizer\\\\Tokens\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedTraitsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on array\\<int, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedTraitsFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$elements of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedTraitsFixer\\:\\:sort\\(\\) expects array\\<int, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\<int\\<0, max\\>\\|string, PhpCsFixer\\\\Tokenizer\\\\Tokens\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedTraitsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on array\\<int, bool\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ProtectedToPrivateFixer.php',
];
$ignoreErrors[] = [
	// identifier: booleanAnd.leftNotBoolean
	'message' => '#^Only booleans are allowed in &&, int\\|false\\|null given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ProtectedToPrivateFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/SingleClassElementPerStatementFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Comment/CommentToPhpdocFixer.php',
];
$ignoreErrors[] = [
	// identifier: foreach.nonIterable
	'message' => '#^Argument of an invalid type array\\<int\\<0, max\\>, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/IncludeFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/NoBreakCommentFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/NoBreakCommentFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/NoSuperfluousElseifFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\|null might not exist on array\\<int\\|string, bool\\|null\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset non\\-empty\\-string might not exist on array\\<int\\|string, bool\\|null\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$possibleKind of method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:isGivenKind\\(\\) expects list\\<int\\>\\|int, list\\<int\\|string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<int\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/CombineNestedDirnameFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on non\\-empty\\-array\\<int, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/DateTimeCreateFromFormatCallFixer.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\ImplodeCallFixer\\:\\:getArgumentIndices\\(\\) should return array\\<int, int\\> but returns array\\<int\\|string, int\\|null\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/ImplodeCallFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/ImplodeCallFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on non\\-empty\\-array\\<int, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/ImplodeCallFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$others of method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:equalsAny\\(\\) expects list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, array\\<int, array\\<int, int\\|string\\>\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/PhpdocToReturnTypeFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<list\\<string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\Analysis\\\\ArgumentAnalysis\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	// identifier: plus.leftNonNumeric
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\Import\\\\FullyQualifiedStrictTypesFixer\\:\\:\\$discoveredSymbols \\(array\\{const\\?\\: list\\<class\\-string\\>, class\\?\\: list\\<class\\-string\\>, function\\?\\: list\\<class\\-string\\>\\}\\|null\\) does not accept array\\{const\\?\\: list\\<class\\-string\\>, class\\: non\\-empty\\-list\\<string\\>, function\\?\\: list\\<class\\-string\\>\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\Import\\\\FullyQualifiedStrictTypesFixer\\:\\:\\$reservedIdentifiersByLevel \\(array\\<int\\<0, max\\>, array\\<string, true\\>\\>\\) does not accept non\\-empty\\-array\\<int, array\\<string, true\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property PhpCsFixer\\\\Fixer\\\\Import\\\\FullyQualifiedStrictTypesFixer\\:\\:\\$symbolsForImport \\(array\\{const\\?\\: array\\<string, class\\-string\\>, class\\?\\: array\\<string, class\\-string\\>, function\\?\\: array\\<string, class\\-string\\>\\}\\) does not accept array\\{const\\?\\: array\\<string, string\\>, class\\?\\: array\\<string, string\\>, function\\?\\: array\\<string, string\\>\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on array\\<list\\<array\\{string, int\\}\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\Analysis\\\\NamespaceAnalysis\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on non\\-empty\\-array\\<int, PhpCsFixer\\\\DocBlock\\\\DocBlock\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$types of method PhpCsFixer\\\\DocBlock\\\\Annotation\\:\\:setTypes\\(\\) expects list\\<string\\>, array\\<int\\<0, max\\>, string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$imports of method PhpCsFixer\\\\Tokenizer\\\\Processor\\\\ImportProcessor\\:\\:insertImports\\(\\) expects array\\{const\\?\\: array\\<int\\|string, class\\-string\\>, class\\?\\: array\\<int\\|string, class\\-string\\>, function\\?\\: array\\<int\\|string, class\\-string\\>\\}, array\\{const\\?\\: array\\<string, string\\>, function\\?\\: array\\<string, string\\>, class\\?\\: array\\<string, string\\>\\}&non\\-empty\\-array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\Analysis\\\\NamespaceUseAnalysis\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GroupImportFixer.php',
];
$ignoreErrors[] = [
	// identifier: plus.leftNonNumeric
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GroupImportFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/NoUnusedImportsFixer.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Import\\\\OrderedImportsFixer\\:\\:getNewOrder\\(\\) should return array\\<int, array\\{namespace\\: non\\-empty\\-string, startIndex\\: int, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\> but returns array\\<\'\'\\|int, array\\{namespace\\: string, startIndex\\: int\\|null, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on array\\<int, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on list\\<int\\|null\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-array\\<int\\<0, max\\>, non\\-empty\\-list\\<int\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<int\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<1, max\\> might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'count' => 6,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<1, max\\> might not exist on non\\-empty\\-list\\<int\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$indices of method PhpCsFixer\\\\Fixer\\\\Import\\\\OrderedImportsFixer\\:\\:sortByAlgorithm\\(\\) expects array\\<int, array\\{namespace\\: non\\-empty\\-string, startIndex\\: int, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\>, array\\<\'\'\\|int, array\\{namespace\\: string, startIndex\\: int\\|null, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$indices of method PhpCsFixer\\\\Fixer\\\\Import\\\\OrderedImportsFixer\\:\\:sortByAlgorithm\\(\\) expects array\\<int, array\\{namespace\\: non\\-empty\\-string, startIndex\\: int, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\>, non\\-empty\\-array\\<\'\'\\|int, array\\{namespace\\: string, startIndex\\: int\\|null, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array\\<0\\|1\\|2, \'class\'\\|\'const\'\\|\'function\'\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<non\\-falsy\\-string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/SingleImportPerStatementFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Internal/ConfigurableFixerTemplateFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Internal/ConfigurableFixerTemplateFixer.php',
];
$ignoreErrors[] = [
	// identifier: plus.leftNonNumeric
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getNextMeaningfulToken\\(\\) expects int, int\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'get_called_class\'\\|\'get_class\'\\|\'get_class_this\'\\|\'php_sapi_name\'\\|\'phpversion\'\\|\'pi\' might not exist on array\\<string, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'count' => 6,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/IsNullFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'endIndex\' might not exist on array\\<string, bool\\|int\\>\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'isFirst\' might not exist on array\\<string, bool\\|int\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'isToTransform\' might not exist on array\\<string, bool\\|int\\>\\.$#',
	'count' => 6,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'startIndex\' might not exist on array\\<string, bool\\|int\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	// identifier: booleanAnd.leftNotBoolean
	'message' => '#^Only booleans are allowed in &&, bool\\|int given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	// identifier: booleanAnd.rightNotBoolean
	'message' => '#^Only booleans are allowed in &&, bool\\|int given on the right side\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	// identifier: booleanNot.exprNotBoolean
	'message' => '#^Only booleans are allowed in a negated boolean, bool\\|int given\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	// identifier: if.condNotBoolean
	'message' => '#^Only booleans are allowed in an if condition, bool\\|int given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	// identifier: plus.leftNonNumeric
	'message' => '#^Only numeric types are allowed in \\+, bool\\|int given on the left side\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getNextTokenOfKind\\(\\) expects int, bool\\|int given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:getPrevTokenOfKind\\(\\) expects int, bool\\|int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$index of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:insertAt\\(\\) expects int, bool\\|int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'abstract\'\\|\'as\'\\|\'attribute\'\\|\'break\'\\|\'case\'\\|\'catch\'\\|\'class\'\\|\'clone\'\\|\'comment\'\\|\'const\'\\|\'const_import\'\\|\'continue\'\\|\'do\'\\|\'echo\'\\|\'else\'\\|\'elseif\'\\|\'enum\'\\|\'extends\'\\|\'final\'\\|\'finally\'\\|\'for\'\\|\'foreach\'\\|\'function\'\\|\'function_import\'\\|\'global\'\\|\'goto\'\\|\'if\'\\|\'implements\'\\|\'include\'\\|\'include_once\'\\|\'instanceof\'\\|\'insteadof\'\\|\'interface\'\\|\'match\'\\|\'named_argument\'\\|\'namespace\'\\|\'new\'\\|\'open_tag_with_echo\'\\|\'php_doc\'\\|\'php_open\'\\|\'print\'\\|\'private\'\\|\'protected\'\\|\'public\'\\|\'readonly\'\\|\'require\'\\|\'require_once\'\\|\'return\'\\|\'static\'\\|\'switch\'\\|\'throw\'\\|\'trait\'\\|\'try\'\\|\'type_colon\'\\|\'use\'\\|\'use_lambda\'\\|\'use_trait\'\\|\'var\'\\|\'while\'\\|\'yield\'\\|\'yield_from\' might not exist on array\\<string, int\\|null\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/SingleSpaceAroundConstructFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'as\'\\|\'use_lambda\' might not exist on array\\<string, int\\|null\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/SingleSpaceAroundConstructFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'yield_from\' might not exist on array\\<string, int\\|null\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/SingleSpaceAroundConstructFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/NamespaceNotation/BlankLineAfterNamespaceFixer.php',
];
$ignoreErrors[] = [
	// identifier: plus.leftNonNumeric
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/NamespaceNotation/BlankLinesBeforeNamespaceFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on array\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Naming/NoHomoglyphNamesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-array\\<int\\<0, max\\>, string\\>\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	// identifier: booleanNot.exprNotBoolean
	'message' => '#^Only booleans are allowed in a negated boolean, int\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	// identifier: minus.rightNonNumeric
	'message' => '#^Only numeric types are allowed in \\-, int\\<0, max\\>\\|false given on the right side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset non\\-empty\\-string might not exist on array\\<string, array\\{int, string\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/LongToShorthandOperatorFixer.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Operator\\\\TernaryToElvisOperatorFixer\\:\\:getAfterOperator\\(\\) should return array\\{start\\: int, end\\: int\\} but returns array\\{start\\: int\\|null, end\\?\\: int\\|null\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/TernaryToElvisOperatorFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1\\|2\\|3\\|4\\|5\\|6\\|7\\|8\\|9\\|10\\|11\\|12\\|13\\|14 might not exist on array\\<1\\|2\\|3\\|4\\|5\\|6\\|7\\|8\\|9\\|10\\|11\\|12\\|13\\|14, array\\{start\\: array\\{int, string\\}\\|string, end\\: array\\{int, string\\}\\|string\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/TernaryToElvisOperatorFixer.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitAttributesFixer\\:\\:createAttributeTokens\\(\\) should return list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\> but returns non\\-empty\\-array\\<int\\|string, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitAttributesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, bool\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitAttributesFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$name of static method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitAttributesFixer\\:\\:toClassConstant\\(\\) expects class\\-string, string given\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitAttributesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on non\\-empty\\-array\\<int\\<0, max\\>, int\\|null\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitConstructFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitConstructFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset non\\-empty\\-string might not exist on array\\<string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitConstructFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDataProviderNameFixer.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method isGivenKind\\(\\) on PhpCsFixer\\\\Tokenizer\\\\Token\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on list\\<int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on array\\<int, int\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset non\\-empty\\-string might not exist on array\\<string, array\\{positive\\: string, negative\\: string\\|false, argument_count\\?\\: int, swap_arguments\\?\\: true\\}\\|true\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'expectExceptionMess…\' might not exist on array\\<string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitExpectationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitExpectationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<1, max\\> might not exist on list\\<int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitExpectationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset non\\-empty\\-string might not exist on array\\<string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitExpectationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitMethodCasingFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitMethodCasingFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 3 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitMethodCasingFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitMethodCasingFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'expectedException\' might not exist on array\\<string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'assertAttributeEqua…\'\\|\'assertAttributeNotE…\'\\|\'assertEquals\'\\|\'assertNotEquals\' might not exist on array\\<string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitStrictFixer.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitTestAnnotationFixer\\:\\:updateLines\\(\\) should return list\\<PhpCsFixer\\\\DocBlock\\\\Line\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on list\\<string\\>\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-array\\<int\\<0, max\\>, PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'count' => 6,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<8, max\\> might not exist on list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	// identifier: plus.leftNonNumeric
	'message' => '#^Only numeric types are allowed in \\+, int\\<0, max\\>\\|false given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<array\\{int, string\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestCaseStaticMethodCallsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on list\\<array\\{int, string\\}\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestCaseStaticMethodCallsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, list\\<array\\{int, string\\}\\>\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitTestCaseStaticMethodCallsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/AlignMultilineCommentFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/GeneralPhpdocTagRenameFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset mixed might not exist on non\\-empty\\-array\\<string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/GeneralPhpdocTagRenameFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/NoBlankLinesAfterPhpdocFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<list\\<string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/NoSuperfluousPhpdocTagsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAddMissingParamAnnotationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<\\-1, max\\> might not exist on list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAddMissingParamAnnotationFixer.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\PhpdocAlignFixer\\:\\:getMatches\\(\\) should return array\\{indent\\: string\\|null, tag\\: string\\|null, hint\\: string, var\\: string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\|null but returns array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'desc\' might not exist on array\\{indent\\: string\\|null, tag\\: null, hint\\: string, var\\: string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'desc\' might not exist on array\\{indent\\: string\\|null, tag\\: string, hint\\: string, var\\: \'\', static\\: string, desc\\?\\: string\\|null\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'desc\' might not exist on array\\{indent\\: string\\|null, tag\\: string, hint\\: string, var\\: non\\-empty\\-string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'hint2\' might not exist on non\\-empty\\-array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'hint3\' might not exist on non\\-empty\\-array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'signature\' might not exist on non\\-empty\\-array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'static\' might not exist on non\\-empty\\-array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<array\\{indent\\: string\\|null, tag\\: string\\|null, hint\\: string, var\\: string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	// identifier: if.condNotBoolean
	'message' => '#^Only booleans are allowed in an if condition, int\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAnnotationWithoutDotFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAnnotationWithoutDotFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 3 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAnnotationWithoutDotFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocArrayTypeFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocArrayTypeFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocInlineTagNormalizerFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocInlineTagNormalizerFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'general_phpdoc_tag…\' might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocNoAliasTagFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocNoUselessInheritdocFixer.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getEnd\\(\\) on PhpCsFixer\\\\DocBlock\\\\Annotation\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method getStart\\(\\) on PhpCsFixer\\\\DocBlock\\\\Annotation\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on list\\<PhpCsFixer\\\\DocBlock\\\\Annotation\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$content of method PhpCsFixer\\\\DocBlock\\\\Line\\:\\:setContent\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<1, max\\> might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocSingleLineVarSpacingFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'general_phpdoc_tag…\' might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTagCasingFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'inlined_tag\' might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTagTypeFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'inlined_tag_name\' might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTagTypeFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'tag\' might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTagTypeFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'tag_name\' might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTagTypeFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1\\|int\\<3, max\\> might not exist on array\\<int\\<0, max\\>, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTagTypeFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on array\\<int, string\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTagTypeFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<list\\<string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocToCommentFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTypesOrderFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on list\\<int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ReturnNotation/ReturnAssignmentFixer.php',
];
$ignoreErrors[] = [
	// identifier: ternary.condNotBoolean
	'message' => '#^Only booleans are allowed in a ternary operator condition, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Semicolon/MultilineWhitespaceBeforeSemicolonsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Strict/StrictParamFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'string_implicit…\' might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/StringNotation/EscapeImplicitBackslashesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on array\\<string\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/StringNotation/NoTrailingWhitespaceInStringFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'end_index\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'initial_indent\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'new_indent\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'type\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on non\\-empty\\-list\\<non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$str of function preg_quote expects string, int\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$parentScopeEndIndex of method PhpCsFixer\\\\Fixer\\\\Whitespace\\\\ArrayIndentationFixer\\:\\:findExpressionEndIndex\\(\\) expects int, int\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'break\'\\|\'case\'\\|\'continue\'\\|\'declare\'\\|\'default\'\\|\'do\'\\|\'exit\'\\|\'for\'\\|\'foreach\'\\|\'goto\'\\|\'if\'\\|\'include\'\\|\'include_once\'\\|\'phpdoc\'\\|\'require\'\\|\'require_once\'\\|\'return\'\\|\'switch\'\\|\'throw\'\\|\'try\'\\|\'while\'\\|\'yield\'\\|\'yield_from\' might not exist on array\\<string, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/BlankLineBeforeStatementFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on list\\<int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/BlankLineBetweenImportGroupsFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/HeredocIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/IndentationTypeFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/IndentationTypeFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/IndentationTypeFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/MethodChainingIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<1, max\\> might not exist on list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoExtraBlankLinesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\|null might not exist on array\\<int, callable\\(int\\)\\: void\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoExtraBlankLinesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset non\\-empty\\-string might not exist on array\\<string, callable\\(int\\)\\: void\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoExtraBlankLinesFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoTrailingWhitespaceFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoTrailingWhitespaceFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoTrailingWhitespaceFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<1, max\\> might not exist on array\\<int\\<0, max\\>, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoTrailingWhitespaceFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-array\\<int\\<0, max\\>, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoWhitespaceInBlankLineFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'new_indent\' might not exist on array\\{type\\: \'statement\', skip\\: bool, end_index\\: int\\|null, end_index_inclusive\\: bool, initial_indent\\: string, new_indent\\?\\: string, is_indented_block\\: bool\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on array\\<int, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on list\\<array\\{type\\: \'block\'\\|\'block_signature\'\\|\'statement\', skip\\: bool, end_index\\: int\\|null, end_index_inclusive\\: bool, initial_indent\\: string, new_indent\\?\\: string, is_indented_block\\: bool\\}\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<\\-1, max\\> might not exist on list\\<array\\{type\\: \'block\'\\|\'block_signature\'\\|\'statement\', skip\\: bool, end_index\\: int\\|null, end_index_inclusive\\: bool, initial_indent\\: string, new_indent\\?\\: string, is_indented_block\\: bool\\}\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/FixerConfiguration/FixerConfigurationResolver.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on non\\-empty\\-array\\<string, array\\<int\\<0, max\\>, string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/FixerFactory.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$fixerConflicts of method PhpCsFixer\\\\FixerFactory\\:\\:generateConflictMessage\\(\\) expects array\\<string, list\\<string\\>\\>, non\\-empty\\-array\\<string, non\\-empty\\-array\\<int\\<0, max\\>, string\\>\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/FixerFactory.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$names of static method PhpCsFixer\\\\Utils\\:\\:naturalLanguageJoin\\(\\) expects list\\<string\\>, non\\-empty\\-array\\<int\\<0, max\\>, string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/FixerFactory.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset non\\-falsy\\-string might not exist on array\\<string, PhpCsFixer\\\\Linter\\\\LintingResultInterface\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Linter/CachingLinter.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$string of function md5 expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/CachingLinter.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$filename of function file_put_contents expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/ProcessLinter.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$path of method PhpCsFixer\\\\Linter\\\\ProcessLinter\\:\\:createProcessForFile\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/ProcessLinter.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#4 \\$path of class Symfony\\\\Component\\\\Filesystem\\\\Exception\\\\IOException constructor expects string\\|null, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/ProcessLinter.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Preg\\:\\:replace\\(\\) should return string but returns array\\<int, string\\>\\|string\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/RuleSet/AbstractMigrationSetDescription.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/AbstractMigrationSetDescription.php',
];
$ignoreErrors[] = [
	// identifier: plus.rightNonNumeric
	'message' => '#^Only numeric types are allowed in \\+, int\\|false given on the right side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/AbstractRuleSetDescription.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, array\\<string, mixed\\>\\|true\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/RuleSet.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, array\\<string, mixed\\>\\|bool given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/RuleSet.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method object\\:\\:getName\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/RuleSets.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\RuleSet\\\\RuleSets\\:\\:getSetDefinitions\\(\\) should return array\\<string, PhpCsFixer\\\\RuleSet\\\\RuleSetDescriptionInterface\\> but returns array\\<int\\|string, object\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/RuleSets.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$callback of function uksort expects callable\\(int\\|string, int\\|string\\)\\: int, Closure\\(string, string\\)\\: int\\<\\-1, 1\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/RuleSets.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Static property PhpCsFixer\\\\RuleSet\\\\RuleSets\\:\\:\\$setDefinitions \\(array\\<string, PhpCsFixer\\\\RuleSet\\\\RuleSetDescriptionInterface\\>\\) does not accept array\\<int\\|string, object\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/RuleSet/RuleSets.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'action\' might not exist on array\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'file\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'identifier\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'status\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<list\\<string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\AttributeAnalyzer\\:\\:collectAttributes\\(\\) should return list\\<array\\{start\\: int, end\\: int, name\\: string\\}\\> but returns non\\-empty\\-array\\<int\\<0, max\\>, array\\{start\\: int, end\\: int, name\\: string\\}\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/AttributeAnalyzer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'alternative_syntax\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'brace_count\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'default\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'index\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'kind\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<\\-1, max\\> might not exist on array\\<int\\<0, max\\>, non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on list\\<non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-array\\<int\\<0, max\\>, non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\>\\.$#',
	'count' => 6,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$analysis of static method PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\ControlCaseStructuresAnalyzer\\:\\:buildControlCaseStructureAnalysis\\(\\) expects array\\{kind\\: int, index\\: int, open\\: int, end\\: int, cases\\: list\\<array\\{index\\: int, open\\: int\\}\\>, default\\: array\\{index\\: int, open\\: int\\}\\|null\\}, non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\> given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<list\\<string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/DataProviderAnalyzer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset non\\-falsy\\-string might not exist on array\\<string, list\\<int\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/SwitchAnalyzer.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on array\\<10001\\|10002\\|10003\\|10004\\|10005\\|10006\\|10007\\|10008\\|10009\\|10010\\|10011\\|10012\\|10013\\|10014\\|10015\\|10016\\|10017\\|10018\\|10019\\|10020\\|10021\\|10022\\|10023\\|10024\\|10025\\|10026\\|10027\\|10028\\|10029\\|10030\\|10031\\|10032\\|10033\\|10034\\|10035\\|10036\\|10037\\|10038\\|10039, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/CT.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:getContent\\(\\) should return non\\-empty\\-string but returns string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Token.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:toJson\\(\\) should return string but returns string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Token.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:extractTokenKind\\(\\) should return int\\|non\\-empty\\-string but returns int\\|string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findGivenKind\\(\\) should return array\\<int, array\\<int\\<0, max\\>, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\> but returns array\\<\'\'\\|int, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findOppositeBlockEdge\\(\\) should return int\\<0, max\\> but returns int\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) should return non\\-empty\\-array\\<int\\<0, max\\>, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|null but returns non\\-empty\\-array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on non\\-empty\\-array\\<\'\'\\|int, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\|non\\-empty\\-string might not exist on array\\<int\\|non\\-empty\\-string, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\|non\\-empty\\-string might not exist on non\\-empty\\-array\\<int\\|non\\-empty\\-string, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset non\\-empty\\-string might not exist on array\\<non\\-empty\\-string, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$others of method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:equalsAny\\(\\) expects list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, non\\-empty\\-array\\<int\\<0, max\\>, array\\{int\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$possibleKind of method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:isGivenKind\\(\\) expects list\\<int\\>\\|int, non\\-empty\\-array\\<int\\<0, max\\>, int\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, iterable\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Tokens given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$name of static method PhpCsFixer\\\\Tokenizer\\\\Processor\\\\ImportProcessor\\:\\:tokenizeName\\(\\) expects class\\-string, string given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Tokenizer/Transformer/NameQualifiedTransformer.php',
];
$ignoreErrors[] = [
	// identifier: generator.valueType
	'message' => '#^Generator expects value type PhpCsFixer\\\\Tokenizer\\\\TransformerInterface, object given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Transformers.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$json of function json_decode expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/ToolInfo.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Utils.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\CiConfigurationTest\\:\\:getPhpVersionsUsedForBuildingLocalImages\\(\\) should return list\\<numeric\\-string\\> but returns array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\CiConfigurationTest\\:\\:getPhpVersionsUsedForBuildingOfficialImages\\(\\) should return list\\<numeric\\-string\\> but returns array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'major\' might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'minor\' might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'php\\-version\' might not exist on array\\<string, bool\\|float\\|int\\|string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$code of static method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:fromCode\\(\\) expects string, string\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$input of static method Symfony\\\\Component\\\\Yaml\\\\Yaml\\:\\:parse\\(\\) expects string, string\\|false given\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/../../tests/AutoReview/CiConfigurationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<Symfony\\\\Component\\\\Console\\\\Command\\\\Command\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/AutoReview/CommandTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'scripts\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/AutoReview/ComposerFileTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'scripts\\-descriptions\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/ComposerFileTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$json of function json_decode expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ComposerFileTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$callback of function array_reduce expects callable\\(array, int\\|string\\)\\: array, Closure\\(array, string\\)\\: array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ComposerFileTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'after\' might not exist on array\\<int\\|string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'before\' might not exist on array\\<int\\|string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<int\\|string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on array\\<int\\|string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$filename of function file_get_contents expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$json of function json_decode expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$haystack of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringContainsString\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/DocumentationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.nonOffsetAccessible
	'message' => '#^Cannot access offset \'reflection\' on array\\{reflection\\: ReflectionObject, short_classname\\: string\\}\\|PhpCsFixer\\\\Fixer\\\\FixerInterface\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.nonOffsetAccessible
	'message' => '#^Cannot access offset \'short_classname\' on array\\{reflection\\: ReflectionObject, short_classname\\: string\\}\\|PhpCsFixer\\\\Fixer\\\\FixerInterface\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \\(int\\|string\\) might not exist on array\\<string, array\\{reflection\\: ReflectionObject, short_classname\\: string\\}\\|PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on list\\<PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on list\\<PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$file of class Symfony\\\\Component\\\\Finder\\\\SplFileInfo constructor expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	// identifier: property.nonObject
	'message' => '#^Cannot access property \\$file on SimpleXMLElement\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: method.nonObject
	'message' => '#^Cannot call method xpath\\(\\) on SimpleXMLElement\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\ProjectCodeTest\\:\\:extractFunctionNamesCalledInClass\\(\\) should return list\\<string\\> but returns array\\<int, non\\-empty\\-string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\ProjectCodeTest\\:\\:getFileContentForClass\\(\\) should return string but returns string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\ProjectCodeTest\\:\\:getSrcClasses\\(\\) should return list\\<class\\-string\\> but returns list\\<non\\-falsy\\-string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\AutoReview\\\\ProjectCodeTest\\:\\:getTestClasses\\(\\) should return list\\<class\\-string\\<PhpCsFixer\\\\Tests\\\\TestCase\\>\\> but returns list\\<non\\-falsy\\-string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'expected\' might not exist on non\\-empty\\-array\\<non\\-empty\\-string, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'input\' might not exist on non\\-empty\\-array\\<non\\-empty\\-string, int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on array\\<SimpleXMLElement\\|false\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset class\\-string might not exist on array\\<class\\-string, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<ReflectionParameter\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: booleanAnd.rightNotBoolean
	'message' => '#^Only booleans are allowed in &&, int\\<0, max\\>\\|false given on the right side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\)\\: mixed\\)\\|null, Closure\\(PhpCsFixer\\\\Tokenizer\\\\Token\\)\\: non\\-empty\\-string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$className of method PhpCsFixer\\\\Tests\\\\AutoReview\\\\ProjectCodeTest\\:\\:createTokensForClass\\(\\) expects class\\-string, string given\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$content of class PhpCsFixer\\\\DocBlock\\\\DocBlock constructor expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$data of function simplexml_load_string expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$objectOrClass of class ReflectionClass constructor expects class\\-string\\<T of object\\>\\|T of object, string given\\.$#',
	'count' => 11,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$callback of function array_filter expects \\(callable\\(PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\)\\: bool\\)\\|null, Closure\\(PhpCsFixer\\\\Tokenizer\\\\Token\\)\\: bool given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'array_typehint\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'attribute\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'brace\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'brace_class…\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'disjunctive_normal…\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'import\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'name_qualified\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'named_argument\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'namespace_operator\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'nullable_type\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'return_ref\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'square_brace\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'type_alternation\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'type_colon\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 6,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'type_intersection\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'use\' might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\TransformerInterface\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/TransformerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Cache/FileCacheManagerTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$cwd of class PhpCsFixer\\\\Console\\\\ConfigurationResolver constructor expects string, string\\|false given\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/ConfigTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$filename of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertFileExists\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/ListFilesCommandTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$path of static method Symfony\\\\Component\\\\Filesystem\\\\Path\\:\\:makeRelative\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/ListFilesCommandTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$string of function ltrim expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/ListFilesCommandTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$basePath of static method Symfony\\\\Component\\\\Filesystem\\\\Path\\:\\:makeRelative\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/ListFilesCommandTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'argv\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/SelfUpdateCommandTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'action\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/WorkerCommandTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'action\' might not exist on array\\<int\\|string, mixed\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/Console/Command/WorkerCommandTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'status\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Command/WorkerCommandTest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Anonymous function should return non\\-empty\\-string but returns non\\-empty\\-string\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/ConfigurationResolverTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$array of function sort expects TArray of array\\<string\\>, array\\<int, string\\>\\|Exception given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/ConfigurationResolverTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$expected of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertInstanceOf\\(\\) expects class\\-string\\<object\\>, string given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Console/ConfigurationResolverTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$stream of class Symfony\\\\Component\\\\Console\\\\Output\\\\StreamOutput constructor expects resource, resource\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Output/ErrorOutputTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\|string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Output/ErrorOutputTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$expected of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertInstanceOf\\(\\) expects class\\-string\\<object\\>, string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Output/Progress/ProgressOutputFactoryTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'message\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Report/FixReport/GitlabReporterTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'property\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Report/FixReport/GitlabReporterTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'message\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Report/FixReport/JsonReporterTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'property\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Report/FixReport/JsonReporterTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'message\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Report/ListSetsReport/JsonReporterTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'property\' might not exist on array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Console/Report/ListSetsReport/JsonReporterTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int\\<0, max\\> might not exist on list\\<int\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/DocBlock/AnnotationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, array\\<string, string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Alias/NoAliasFunctionsFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\Fixer\\\\Basic\\\\EncodingFixerTest\\:\\:prepareTestCase\\(\\) should return array\\{string, string\\|null, SplFileInfo\\} but returns array\\{string\\|false, string\\|false\\|null, SplFileInfo\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Basic/EncodingFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'classy\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/ClassNotation/ClassDefinitionFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'start\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/Fixer/ClassNotation/ClassDefinitionFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$objectOrMethod of class ReflectionMethod constructor expects object\\|string, class\\-string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/ControlStructure/NoUselessElseFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: elseif.condNotBoolean
	'message' => '#^Only booleans are allowed in an elseif condition, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/FunctionNotation/MethodArgumentSpaceFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$exception of method PHPUnit\\\\Framework\\\\TestCase\\:\\:expectException\\(\\) expects class\\-string\\<Throwable\\>, string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/FunctionNotation/NativeFunctionInvocationFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: generator.valueType
	'message' => '#^Generator expects value type array\\{0\\: string, 1\\: string\\|null, 2\\?\\: array\\<string, bool\\>\\}, array\\{0\\: string, 1\\?\\: string\\} given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/FunctionNotation/NullableTypeDeclarationForDefaultNullValueFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'operators\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Operator/LongToShorthandOperatorFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: booleanNot.exprNotBoolean
	'message' => '#^Only booleans are allowed in a negated boolean, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/PhpTag/NoClosingTagFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: generator.valueType
	'message' => '#^Generator expects value type array\\{0\\: string, 1\\?\\: string\\}, list\\<string\\> given\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/../../tests/Fixer/PhpUnit/PhpUnitDataProviderReturnTypeFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: generator.valueType
	'message' => '#^Generator expects value type array\\{string, string\\}, list\\<string\\> given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Fixer/PhpUnit/PhpUnitDataProviderReturnTypeFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\Fixer\\\\PhpUnit\\\\PhpUnitDataProviderReturnTypeFixerTest\\:\\:mapToTemplate\\(\\) should return list\\<string\\> but returns array\\<int\\|string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/PhpUnit/PhpUnitDataProviderReturnTypeFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$exception of method PHPUnit\\\\Framework\\\\TestCase\\:\\:expectException\\(\\) expects class\\-string\\<Throwable\\>, string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/PhpUnit/PhpUnitTargetVersionTest.php',
];
$ignoreErrors[] = [
	// identifier: booleanNot.exprNotBoolean
	'message' => '#^Only booleans are allowed in a negated boolean, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Semicolon/NoEmptyStatementFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: booleanNot.exprNotBoolean
	'message' => '#^Only booleans are allowed in a negated boolean, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Semicolon/SemicolonAfterInstructionFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$array of function array_map expects array, iterable given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Whitespace/LineEndingFixerTest.php',
];
$ignoreErrors[] = [
	// identifier: new.resultUnused
	'message' => '#^Call to new PhpCsFixer\\\\FixerConfiguration\\\\AliasedFixerOption\\(\\) on a separate line has no effect\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/FixerConfiguration/AliasedFixerOptionTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$code of class PhpCsFixer\\\\FixerDefinition\\\\FileSpecificCodeSample constructor expects string, string\\|false given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/FixerDefinition/FileSpecificCodeSampleTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$minimum of class PhpCsFixer\\\\FixerDefinition\\\\VersionSpecification constructor expects int\\<1, max\\>\\|null, int\\|null given\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/FixerDefinition/VersionSpecificationTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$maximum of class PhpCsFixer\\\\FixerDefinition\\\\VersionSpecification constructor expects int\\<1, max\\>\\|null, int\\|null given\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../../tests/FixerDefinition/VersionSpecificationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, array\\<string, mixed\\>\\|true\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/FixerFactoryTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$exception of method PHPUnit\\\\Framework\\\\TestCase\\:\\:expectException\\(\\) expects class\\-string\\<Throwable\\>, string given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/PregTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$pattern of static method PhpCsFixer\\\\Preg\\:\\:replace\\(\\) expects string, array\\<int, string\\>\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/PregTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$pattern of static method PhpCsFixer\\\\Preg\\:\\:replaceCallback\\(\\) expects string, array\\<int, string\\>\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/PregTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$subject of static method PhpCsFixer\\\\Preg\\:\\:replaceCallback\\(\\) expects string, array\\<int, string\\>\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/PregTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/RuleSet/RuleSetTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\RuleSet\\\\RuleSetDescriptionInterface\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/RuleSet/RuleSetsTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/RuleSet/RuleSetsTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset mixed might not exist on array\\<int\\|string, SplFileInfo\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Runner/FileCachingLintingFileIteratorTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 3 might not exist on non\\-empty\\-list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 4 might not exist on non\\-empty\\-list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 5 might not exist on non\\-empty\\-list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 6 might not exist on non\\-empty\\-list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 7 might not exist on non\\-empty\\-list\\<string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\|string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Smoke/CiIntegrationTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
	// identifier: ternary.condNotBoolean
	'message' => '#^Only booleans are allowed in a ternary operator condition, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\)\\: mixed\\)\\|null, Closure\\(PhpCsFixer\\\\Tokenizer\\\\Token\\)\\: string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$code of static method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:fromCode\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$sequence of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) expects non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, list\\<array\\{0\\: int, 1\\?\\: string\\}\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractFixerTestCase.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractIntegrationCaseFactory\\:\\:determineRequirements\\(\\) should return array\\{php\\: int, php\\<\\: int, os\\: list\\<string\\>\\} but returns array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractIntegrationCaseFactory\\:\\:determineSettings\\(\\) should return array\\{checkPriority\\: bool, deprecations\\: list\\<string\\>\\} but returns array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'checkPriority\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'deprecations\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'indent\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'lineEnding\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'os\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'php\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset \'php\\<\' might not exist on array\\<string, mixed\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	// identifier: booleanNot.exprNotBoolean
	'message' => '#^Only booleans are allowed in a negated boolean, int\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
];
$ignoreErrors[] = [
	// identifier: ternary.condNotBoolean
	'message' => '#^Only booleans are allowed in a ternary operator condition, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$dirs of method Symfony\\\\Component\\\\Finder\\\\Finder\\:\\:in\\(\\) expects array\\<string\\>\\|string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$filename of function is_dir expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$string of function strlen expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$fixedInputCode of static method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractIntegrationTestCase\\:\\:assertRevertedOrderFixing\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#3 \\$fixedInputCodeWithReversedFixers of static method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractIntegrationTestCase\\:\\:assertRevertedOrderFixing\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationTestCase.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$id of static method PhpCsFixer\\\\Tokenizer\\\\Token\\:\\:getNameForId\\(\\) expects int, int\\|string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractTransformerTestCase.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$prototypes of method PhpCsFixer\\\\Tests\\\\Test\\\\AbstractTransformerTestCase\\:\\:countTokenPrototypes\\(\\) expects list\\<array\\{0\\: int, 1\\?\\: string\\}\\>, array\\<int\\<0, max\\>, array\\{int\\}\\|string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractTransformerTestCase.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on array\\{0\\: string, 1\\?\\: string\\}\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/TestCaseUtils.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property PhpCsFixer\\\\Tests\\\\Test\\\\TokensWithObservedTransformers\\:\\:\\$observedModificationsPerTransformer \\(array\\<string, list\\<int\\|string\\>\\>\\) does not accept non\\-empty\\-array\\<int\\|string, list\\<int\\|string\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/TokensWithObservedTransformers.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property PhpCsFixer\\\\Tests\\\\TestCase\\:\\:\\$actualDeprecations \\(list\\<string\\>\\) does not accept array\\<int\\<0, max\\>, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/TestCase.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property PhpCsFixer\\\\Tests\\\\TestCase\\:\\:\\$expectedDeprecations \\(list\\<string\\>\\) does not accept array\\<int\\<0, max\\>, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/TestCase.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on array\\<int, PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\Analysis\\\\AbstractControlCaseStructuresAnalysis\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Tokenizer/Analyzer/ControlCaseStructuresAnalyzerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on array\\<int, bool\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensAnalyzerTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on list\\<int\\<0, max\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 0 might not exist on non\\-empty\\-array\\<int\\<0, 3\\>, array\\{tokens\\: non\\-empty\\-list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>, content\\: literal\\-string&non\\-falsy\\-string\\}\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on list\\<int\\<0, max\\>\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 1 might not exist on non\\-empty\\-array\\<int\\<0, 3\\>, array\\{tokens\\: non\\-empty\\-list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>, content\\: literal\\-string&non\\-falsy\\-string\\}\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 2 might not exist on non\\-empty\\-array\\<int\\<0, 3\\>, array\\{tokens\\: non\\-empty\\-list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>, content\\: literal\\-string&non\\-falsy\\-string\\}\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset 3 might not exist on non\\-empty\\-array\\<int\\<0, 3\\>, array\\{tokens\\: non\\-empty\\-list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>, content\\: literal\\-string&non\\-falsy\\-string\\}\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.notFound
	'message' => '#^Offset int might not exist on array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$sequence of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) expects non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$sequence of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) expects non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>, list\\<mixed\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$slices of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:insertSlices\\(\\) expects array\\<int, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|PhpCsFixer\\\\Tokenizer\\\\Tokens\\>, array\\{16\\: array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>, 6\\: array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\} given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Tokenizer/TokensTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
