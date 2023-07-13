<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Short ternary operator is not allowed\\. Use null coalesce operator if applicable or consider using long ternary\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Command/SelfUpdateCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an if condition, mixed given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Short ternary operator is not allowed\\. Use null coalesce operator if applicable or consider using long ternary\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/ConfigurationResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/FixReport/ReporterFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Console/Report/ListSetsReport/ReporterFactory.php',
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
	'message' => '#^Only booleans are allowed in &&, string given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, string given on the right side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in \\|\\|, string given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in \\|\\|, string given on the right side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Documentation/FixerDocumentGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, array\\<string, int\\|string\\>\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/FileReader.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Alias\\\\NoAliasFunctionsFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/NoAliasFunctionsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Alias\\\\NoMixedEchoPrintFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/NoMixedEchoPrintFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable method call on \\$this\\(PhpCsFixer\\\\Fixer\\\\Alias\\\\NoMixedEchoPrintFixer\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/NoMixedEchoPrintFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Alias\\\\RandomApiMigrationFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Alias/RandomApiMigrationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\ArrayNotation\\\\ArraySyntaxFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ArrayNotation/ArraySyntaxFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable method call on \\$this\\(PhpCsFixer\\\\Fixer\\\\ArrayNotation\\\\ArraySyntaxFixer\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ArrayNotation/ArraySyntaxFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, mixed given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ArrayNotation/WhitespaceAfterCommaInArrayFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\|null\\) of method PhpCsFixer\\\\Fixer\\\\Basic\\\\BracesFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Basic/BracesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, mixed given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Basic/CurlyBracesPositionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in \\|\\|, mixed given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Basic/CurlyBracesPositionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in \\|\\|, mixed given on the right side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Basic/CurlyBracesPositionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$prevIndex might not be defined\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Basic/CurlyBracesPositionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, mixed given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Basic/NonPrintableCharacterFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Basic\\\\PsrAutoloadingFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Basic/PsrAutoloadingFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Casing\\\\ConstantCaseFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Casing/ConstantCaseFixer.php',
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
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassAttributesSeparationFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
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
	'message' => '#^Only booleans are allowed in a negated boolean, mixed given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, int\\<0, max\\> given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, int\\|null given on the right side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/FinalInternalClassFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, mixed given on the right side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/FinalInternalClassFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an if condition, string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/FinalInternalClassFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\FinalInternalClassFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/FinalInternalClassFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\NoPhp4ConstructorFixer\\:\\:getWrapperMethodSequence\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, mixed given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in \\|\\|, mixed given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in \\|\\|, mixed given on the right side\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Foreach overwrites \\$pos with its value variable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:sortElements\\(\\) has parameter \\$elements with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, array\\<int, string\\>\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, mixed given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Comment\\\\CommentToPhpdocFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Comment/CommentToPhpdocFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an if condition, int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Comment/HeaderCommentFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Comment\\\\NoEmptyCommentFixer\\:\\:getCommentBlock\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Comment/NoEmptyCommentFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Comment\\\\SingleLineCommentStyleFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Comment/SingleLineCommentStyleFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Short ternary operator is not allowed\\. Use null coalesce operator if applicable or consider using long ternary\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Comment/SingleLineCommentStyleFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\) has parameter \\$configuration with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ConfigurableFixerInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\ConstantNotation\\\\NativeConstantInvocationFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ConstantNotation/NativeConstantInvocationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, PhpCsFixer\\\\Tokenizer\\\\Token\\|null given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/ControlStructureBracesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, PhpCsFixer\\\\Tokenizer\\\\Token given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/ControlStructureBracesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in &&, int given on the left side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/IncludeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ControlStructure\\\\YodaStyleFixer\\:\\:getCompareFixableInfo\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\ControlStructure\\\\YodaStyleFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
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
	'message' => '#^Only booleans are allowed in a ternary operator condition, mixed given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/MethodArgumentSpaceFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\MethodArgumentSpaceFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/MethodArgumentSpaceFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\NativeFunctionInvocationFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/NativeFunctionInvocationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\PhpdocToPropertyTypeFixer\\:\\:resolveApplicableType\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/FunctionNotation/PhpdocToPropertyTypeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^For loop initial assignment overwrites variable \\$index\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Import\\\\GlobalNamespaceImportFixer\\:\\:filterUseDeclarations\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Import\\\\GlobalNamespaceImportFixer\\:\\:insertImports\\(\\) has parameter \\$imports with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Import\\\\GlobalNamespaceImportFixer\\:\\:prepareImports\\(\\) has parameter \\$global with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Short ternary operator is not allowed\\. Use null coalesce operator if applicable or consider using long ternary\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/GroupImportFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Import\\\\OrderedImportsFixer\\:\\:getNewOrder\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/OrderedImportsFixer.php',
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
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Import\\\\SingleImportPerStatementFixer\\:\\:getGroupDeclaration\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/SingleImportPerStatementFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an if condition, mixed given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Import/SingleImportPerStatementFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\DeclareEqualNormalizeFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/DeclareEqualNormalizeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable method call on \\$this\\(PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\DeclareEqualNormalizeFixer\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/DeclareEqualNormalizeFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\FunctionToConstantFixer\\:\\:fixGetClassCall\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\FunctionToConstantFixer\\:\\:getReplaceCandidate\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\FunctionToConstantFixer\\:\\:getReplacementTokenClones\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\FunctionToConstantFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\NullableTypeDeclarationFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/NullableTypeDeclarationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\SingleSpaceAfterConstructFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/SingleSpaceAfterConstructFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\LanguageConstruct\\\\SingleSpaceAroundConstructFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/LanguageConstruct/SingleSpaceAroundConstructFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, string\\>\\) of method PhpCsFixer\\\\Fixer\\\\ListNotation\\\\ListSyntaxFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ListNotation/ListSyntaxFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, string\\>\\) of method PhpCsFixer\\\\Fixer\\\\ListNotation\\\\ListSyntaxFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\AbstractFixer\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/ListNotation/ListSyntaxFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Operator\\\\BinaryOperatorSpacesFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Operator\\\\ConcatSpaceFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/ConcatSpaceFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Short ternary operator is not allowed\\. Use null coalesce operator if applicable or consider using long ternary\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/ConcatSpaceFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable method call on \\$this\\(PhpCsFixer\\\\Fixer\\\\Operator\\\\ConcatSpaceFixer\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/ConcatSpaceFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an if condition, mixed given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Operator/NewWithBracesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, mixed given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/NoUselessConcatOperatorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Operator\\\\OperatorLinebreakFixer\\:\\:getNonBooleanOperators\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/OperatorLinebreakFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Operator\\\\OperatorLinebreakFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/OperatorLinebreakFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Operator\\\\TernaryToElvisOperatorFixer\\:\\:getBeforeOperator\\(\\) has parameter \\$blockEdgeDefinitions with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Operator/TernaryToElvisOperatorFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable method call on \\$this\\(PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitConstructFixer\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitConstructFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, mixed given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDataProviderStaticFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitDedicateAssertFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitExpectationFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitExpectationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitMockFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitMockFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitNamespacedFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitNamespacedFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitNoExpectationAnnotationFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\AlignMultilineCommentFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/AlignMultilineCommentFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, mixed given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/GeneralPhpdocTagRenameFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an if condition, mixed given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/GeneralPhpdocTagRenameFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\NoSuperfluousPhpdocTagsFixer\\:\\:annotationIsSuperfluous\\(\\) has parameter \\$info with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/NoSuperfluousPhpdocTagsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\NoSuperfluousPhpdocTagsFixer\\:\\:removeSuperfluousModifierAnnotation\\(\\) has parameter \\$element with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/NoSuperfluousPhpdocTagsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\NoSuperfluousPhpdocTagsFixer\\:\\:toComparableNames\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/NoSuperfluousPhpdocTagsFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, int\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAddMissingParamAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Short ternary operator is not allowed\\. Use null coalesce operator if applicable or consider using long ternary\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAddMissingParamAnnotationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, string\\|null given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in \\|\\|, string\\|null given on the right side\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\PhpdocAlignFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Short ternary operator is not allowed\\. Use null coalesce operator if applicable or consider using long ternary\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\PhpdocNoAliasTagFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocNoAliasTagFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in an if condition, int\\<0, max\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^For loop initial assignment overwrites variable \\$pos\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, mixed given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\PhpdocSeparationFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\PhpdocTagCasingFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTagCasingFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\|null\\) of method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\PhpdocToCommentFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocToCommentFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Foreach overwrites \\$index with its key variable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTrimConsecutiveBlankLineSeparationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\PhpdocTypesFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Phpdoc/PhpdocTypesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Strict\\\\StrictParamFixer\\:\\:fixFunction\\(\\) has parameter \\$functionParams with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Strict/StrictParamFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Whitespace\\\\BlankLineBeforeStatementFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/BlankLineBeforeStatementFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$index might not be defined\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/HeredocIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Whitespace\\\\NoExtraBlankLinesFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/NoExtraBlankLinesFixer.php',
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
	'count' => 2,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^For loop initial assignment overwrites variable \\$i\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$configuration \\(array\\<string, mixed\\>\\) of method PhpCsFixer\\\\Fixer\\\\Whitespace\\\\TypesSpacesFixer\\:\\:configure\\(\\) should be contravariant with parameter \\$configuration \\(array\\) of method PhpCsFixer\\\\Fixer\\\\ConfigurableFixerInterface\\:\\:configure\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Fixer/Whitespace/TypesSpacesFixer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/FixerFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Short ternary operator is not allowed\\. Use null coalesce operator if applicable or consider using long ternary\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Linter/ProcessLintingResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, array\\<string, array\\<int, string\\>\\|string\\>\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, array\\<string, int\\|string\\>\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Short ternary operator is not allowed\\. Use null coalesce operator if applicable or consider using long ternary\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Runner/Runner.php',
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
	'message' => '#^Short ternary operator is not allowed\\. Use null coalesce operator if applicable or consider using long ternary\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/Analysis/ArgumentAnalysis.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, string\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/ArgumentsAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^For loop initial assignment overwrites variable \\$index\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Analyzer/CommentsAnalyzer.php',
];
$ignoreErrors[] = [
	'message' => '#^Foreach overwrites \\$key with its key variable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array \\(array\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\) of method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:fromArray\\(\\) should be contravariant with parameter \\$array \\(array\\<int, mixed\\>\\) of method SplFixedArray\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\:\\:fromArray\\(\\)$#',
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
	'message' => '#^For loop initial assignment overwrites variable \\$index\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Transformer/ConstructorPromotionTransformer.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Tokenizer/Transformers.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, string given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../tests/AutoReview/ProjectCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/DocBlock/TypeExpressionTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, int\\<0, 1\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/DocBlock/TypeExpressionTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$expression might not be defined\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/DocBlock/TypeExpressionTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, SplFileInfo\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Basic/EncodingFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a ternary operator condition, string\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Fixer/Basic/EncodingFixerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Only booleans are allowed in a negated boolean, string\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../tests/Test/AbstractIntegrationCaseFactory.php',
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
