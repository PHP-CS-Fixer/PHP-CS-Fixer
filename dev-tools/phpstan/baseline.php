<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Short ternary operator is not allowed\\. Use null coalesce operator if applicable or consider using long ternary\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\+" between int and string results in an error\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
	'message' => '#^Foreach overwrites \\$token with its value variable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Doctrine/Annotation/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array \\(array\\<int, PhpCsFixer\\\\Doctrine\\\\Annotation\\\\Token\\>\\) of method PhpCsFixer\\\\Doctrine\\\\Annotation\\\\Tokens\\:\\:fromArray\\(\\) should be contravariant with parameter \\$array \\(array\\<int, mixed\\>\\) of method SplFixedArray\\<PhpCsFixer\\\\Doctrine\\\\Annotation\\\\Token\\>\\:\\:fromArray\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Doctrine/Annotation/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable method call on \\$this\\(PhpCsFixer\\\\Fixer\\\\Alias\\\\NoMixedEchoPrintFixer\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/NoMixedEchoPrintFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable method call on \\$this\\(PhpCsFixer\\\\Fixer\\\\ArrayNotation\\\\ArraySyntaxFixer\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ArrayNotation/ArraySyntaxFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$prevIndex might not be defined\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Basic/BracesPositionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$i might not be defined\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/CastNotation/NoShortBoolCastFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type string supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^For loop initial assignment overwrites variable \\$index\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_reverse expects array, string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$k might not be defined\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$k2 might not be defined\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable method call on \\$this\\(PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\DeclareEqualNormalizeFixer\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/DeclareEqualNormalizeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\{syntax\\: \'long\'\\|\'short\'\\}\\) of method PhpCsFixer\\\\Fixer\\\\ListNotation\\\\ListSyntaxFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\AbstractFixer\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ListNotation/ListSyntaxFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\{syntax\\: \'long\'\\|\'short\'\\}\\) of method PhpCsFixer\\\\Fixer\\\\ListNotation\\\\ListSyntaxFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ListNotation/ListSyntaxFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable method call on \\$this\\(PhpCsFixer\\\\Fixer\\\\Operator\\\\ConcatSpaceFixer\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/ConcatSpaceFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable method call on \\$this\\(PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitConstructFixer\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitConstructFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type string supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/NoSuperfluousPhpdocTagsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type string supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocToCommentFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Foreach overwrites \\$index with its key variable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTrimConsecutiveBlankLineSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type string supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocVarWithoutNameFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$end might not be defined\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoExtraBlankLinesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable method call on \\$this\\(PhpCsFixer\\\\Fixer\\\\Whitespace\\\\NoExtraBlankLinesFixer\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoExtraBlankLinesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^For loop initial assignment overwrites variable \\$endIndex\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Preg\\:\\:match\\(\\) never assigns null to &\\$matches so it can be removed from the by\\-ref type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Preg\\:\\:matchAll\\(\\) never assigns null to &\\$matches so it can be removed from the by\\-ref type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Preg\\:\\:replace\\(\\) never assigns null to &\\$count so it can be removed from the by\\-ref type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Preg\\:\\:replaceCallback\\(\\) never assigns null to &\\$count so it can be removed from the by\\-ref type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter &\\$matches by\\-ref type of method PhpCsFixer\\\\Preg\\:\\:match\\(\\) expects array\\<string\\>\\|null, \\(int is int \\? array\\<array\\<int, int\\<\\-1, max\\>\\|string\\>\\> \\: \\(int is int \\? array\\<string\\|null\\> \\: \\(int is int \\? array\\<array\\<int, int\\|string\\|null\\>\\> \\: array\\<string\\>\\)\\)\\) given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter &\\$matches by\\-ref type of method PhpCsFixer\\\\Preg\\:\\:matchAll\\(\\) expects array\\<string\\>\\|null, \\(int is int \\? array\\<array\\<int, string\\>\\> \\: \\(int is int \\? array\\<int, array\\<string\\>\\> \\: \\(int is int \\? array\\<array\\<int, array\\<int, int\\|string\\>\\>\\> \\: \\(int is int \\? array\\<int, array\\<array\\<int, int\\|string\\>\\>\\> \\: \\(int is int \\? array\\<array\\<int, string\\|null\\>\\> \\: \\(int is int \\? array\\<int, array\\<string\\|null\\>\\> \\: \\(int is int \\? array\\<int, array\\<array\\<int, int\\|string\\|null\\>\\>\\> \\: array\\)\\)\\)\\)\\)\\)\\) given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Preg.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$className \\(string\\) of method PhpCsFixer\\\\StdinFileInfo\\:\\:getFileInfo\\(\\) should be contravariant with parameter \\$class \\(string\\|null\\) of method SplFileInfo\\:\\:getFileInfo\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/StdinFileInfo.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$className \\(string\\) of method PhpCsFixer\\\\StdinFileInfo\\:\\:getPathInfo\\(\\) should be contravariant with parameter \\$class \\(string\\|null\\) of method SplFileInfo\\:\\:getPathInfo\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/StdinFileInfo.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type array\\<int, string\\> is not subtype of type string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/DataProviderAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array \\(array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\) of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:fromArray\\(\\) should be contravariant with parameter \\$array \\(array\\<int, mixed\\>\\) of method SplFixedArray\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\:\\:fromArray\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index \\(int\\) of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:offsetSet\\(\\) should be contravariant with parameter \\$index \\(int\\|null\\) of method SplFixedArray\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\:\\:offsetSet\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index \\(int\\) of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:offsetSet\\(\\) should be contravariant with parameter \\$offset \\(int\\|null\\) of method ArrayAccess\\<int,PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\>\\:\\:offsetSet\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$newval \\(PhpCsFixer\\\\Tokenizer\\\\Token\\) of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:offsetSet\\(\\) should be contravariant with parameter \\$value \\(PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\) of method ArrayAccess\\<int,PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\>\\:\\:offsetSet\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$newval \\(PhpCsFixer\\\\Tokenizer\\\\Token\\) of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:offsetSet\\(\\) should be contravariant with parameter \\$value \\(PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\) of method SplFixedArray\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\:\\:offsetSet\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$count might not be defined\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$index might not be defined\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$index \\(int\\) of method PhpCsFixer\\\\Tests\\\\Test\\\\TokensWithObservedTransformers\\:\\:offsetSet\\(\\) should be contravariant with parameter \\$offset \\(int\\|null\\) of method ArrayAccess\\<int,PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\>\\:\\:offsetSet\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/TokensWithObservedTransformers.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$newval \\(PhpCsFixer\\\\Tokenizer\\\\Token\\) of method PhpCsFixer\\\\Tests\\\\Test\\\\TokensWithObservedTransformers\\:\\:offsetSet\\(\\) should be contravariant with parameter \\$value \\(PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\) of method ArrayAccess\\<int,PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\>\\:\\:offsetSet\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/TokensWithObservedTransformers.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
