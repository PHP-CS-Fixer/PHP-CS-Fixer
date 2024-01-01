<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\CheckCommand\\:\\:\\$defaultDescription has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\CheckCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\DescribeCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\DocumentationCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/DocumentationCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\FixCommand\\:\\:\\$defaultDescription has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\FixCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\HelpCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/HelpCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\ListFilesCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/ListFilesCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\ListSetsCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/ListSetsCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\SelfUpdateCommand\\:\\:\\$defaultName has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/SelfUpdateCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Short ternary operator is not allowed\\. Use null coalesce operator if applicable or consider using long ternary\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Foreach overwrites \\$token with its value variable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Doctrine/Annotation/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array \\(array\\<PhpCsFixer\\\\Doctrine\\\\Annotation\\\\Token\\>\\) of method PhpCsFixer\\\\Doctrine\\\\Annotation\\\\Tokens\\:\\:fromArray\\(\\) should be contravariant with parameter \\$array \\(array\\<int, mixed\\>\\) of method SplFixedArray\\<PhpCsFixer\\\\Doctrine\\\\Annotation\\\\Token\\>\\:\\:fromArray\\(\\)$#',
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
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:determineRequiredLineCount\\(\\) has parameter \\$class with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:getFirstTokenIndexOfClassElement\\(\\) has parameter \\$class with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:getFirstTokenIndexOfClassElement\\(\\) has parameter \\$element with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:getLastTokenIndexOfClassElement\\(\\) has parameter \\$class with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:getLastTokenIndexOfClassElement\\(\\) has parameter \\$element with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassDefinitionFixer\\:\\:fixClassyDefinitionExtends\\(\\) has parameter \\$classExtendsInfo with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassDefinitionFixer\\:\\:fixClassyDefinitionExtends\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassDefinitionFixer\\:\\:fixClassyDefinitionImplements\\(\\) has parameter \\$classImplementsInfo with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassDefinitionFixer\\:\\:fixClassyDefinitionImplements\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassDefinitionFixer\\:\\:fixClassyDefinitionOpenSpacing\\(\\) has parameter \\$classDefInfo with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassDefinitionFixer\\:\\:getClassyInheritanceInfo\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\NoPhp4ConstructorFixer\\:\\:getWrapperMethodSequence\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Foreach overwrites \\$pos with its value variable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ControlStructure\\\\YodaStyleFixer\\:\\:getCompareFixableInfo\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\LambdaNotUsedImportFixer\\:\\:countImportsUsedAsArgument\\(\\) has parameter \\$arguments with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/LambdaNotUsedImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\LambdaNotUsedImportFixer\\:\\:countImportsUsedAsArgument\\(\\) has parameter \\$imports with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/LambdaNotUsedImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\LambdaNotUsedImportFixer\\:\\:countImportsUsedAsArgument\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/LambdaNotUsedImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\LambdaNotUsedImportFixer\\:\\:filterArguments\\(\\) has parameter \\$arguments with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/LambdaNotUsedImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\LambdaNotUsedImportFixer\\:\\:filterArguments\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/LambdaNotUsedImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\LambdaNotUsedImportFixer\\:\\:findNotUsedLambdaImports\\(\\) has parameter \\$imports with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/LambdaNotUsedImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^For loop initial assignment overwrites variable \\$index\\.$#',
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
	'message' => '#^Foreach overwrites \\$index with its key variable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTrimConsecutiveBlankLineSeparationFixer.php',
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
	'message' => '#^For loop initial assignment overwrites variable \\$i\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/StatementIndentationFixer.php',
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
	'message' => '#^Foreach overwrites \\$key with its key variable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
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
