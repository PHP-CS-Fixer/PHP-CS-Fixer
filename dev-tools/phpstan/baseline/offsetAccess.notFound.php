<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on list\\<int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/AbstractFopenFlagFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on array\\<int, int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/AbstractFopenFlagFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/AbstractFunctionReferenceFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on array\\<string, string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Cache/Cache.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<string\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on array\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 2 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset non\\-falsy\\-string might not exist on array\\<string, PhpCsFixer\\\\RuleSet\\\\RuleSetDescriptionInterface\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\RuleSet\\\\RuleSetDescriptionInterface\\>\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/../../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'argv\' might not exist on array\\<mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/SelfUpdateCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'major\' might not exist on array\\{0\\?\\: string, major\\?\\: numeric\\-string, 1\\?\\: numeric\\-string\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/SelfUpdateCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'files\' might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/WorkerCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'line\' might not exist on array\\{function\\?\\: string, line\\?\\: int, file\\: string, class\\?\\: class\\-string, type\\?\\: \'\\-\\>\'\\|\'\\:\\:\', args\\?\\: array\\<mixed\\>, object\\?\\: object\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Output/ErrorOutput.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/SelfUpdate/NewVersionChecker.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on array\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Differ/DiffConsoleFormatter.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'types\' might not exist on array\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/Annotation.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'variable\' might not exist on array\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/Annotation.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on array\\<int, PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/../../../src/DocBlock/Annotation.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/DocBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/DocBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'_array_shape_inner\' might not exist on array\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'_callable_argument\' might not exist on array\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'_callable_template_inner\' might not exist on array\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'array\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'array_shape_inner_value\' might not exist on array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'array_shape_inners\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'array_shape_name\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'array_shape_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'array_shape_unsealed_type_a\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'array_shape_unsealed_type_comma\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'array_shape_unsealed_type_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'array_shape_unsealed_variadic\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'callable_argument_type\' might not exist on array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'callable_arguments\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'callable_name\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'callable_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'callable_template\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'callable_template_inner_b\' might not exist on array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'callable_template_inner_b_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'callable_template_inner_d\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'callable_template_inner_d_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'callable_template_inners\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'callable_template_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'class_constant\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'class_constant_name\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'conditional_cond_left\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'conditional_cond_middle\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'conditional_cond_right_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'conditional_false_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'conditional_false_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'conditional_true_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'conditional_true_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'generic_name\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'generic_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'generic_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'nullable\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'parenthesized_start\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'parenthesized_types\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'type\' might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'types\' might not exist on array\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on array\\<string\\>\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on non\\-empty\\-array\\<array\\{string, int\\<\\-1, max\\>\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<array\\{start_index\\: int\\<0, max\\>, value\\: string, next_glue\\: string\\|null, next_glue_raw\\: string\\|null\\}\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/DocBlock/TypeExpression.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on list\\<PhpCsFixer\\\\Doctrine\\\\Annotation\\\\Token\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Doctrine/Annotation/DocLexer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Documentation/DocumentationLocator.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 2 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Documentation/DocumentationLocator.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \\(int\\|string\\) might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Documentation/RuleSetDocumentationGenerator.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 2 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/../../../src/Fixer/Alias/EregToPregFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Alias/SetTypeToCastFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on list\\<int\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Alias/SetTypeToCastFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 2 might not exist on list\\<int\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Alias/SetTypeToCastFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on non\\-empty\\-array\\<int, int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Alias/SetTypeToCastFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/../../../src/Fixer/Basic/NumericLiteralSeparatorFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on non\\-empty\\-array\\<\\-844\\|346\\|347\\|348\\|349\\|350\\|351\\|352\\|353\\|354\\|10008, \'__CLASS__\'\\|\'__DIR__\'\\|\'__FILE__\'\\|\'__FUNCTION__\'\\|\'__LINE__\'\\|\'__METHOD__\'\\|\'__NAMESPACE__\'\\|\'__PROPERTY__\'\\|\'__TRAIT__\'\\|\'class\'\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Casing/MagicConstantCasingFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'end\' might not exist on array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'start\' might not exist on array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\.$#',
    'count' => 11,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on non\\-empty\\-list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\>\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<1, max\\> might not exist on non\\-empty\\-list\\<array\\{token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, index\\: int, start\\?\\: int, end\\?\\: int\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on array\\<string, string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'multiLine\' might not exist on non\\-empty\\-array\\<string, bool\\|int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on non\\-empty\\-array\\<string, bool\\|int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<1, max\\> might not exist on array\\<int\\<1, max\\>, int\\>\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/NoNullPropertyInitializationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<1, max\\> might not exist on non\\-empty\\-array\\<int\\<1, max\\>, int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/NoNullPropertyInitializationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 2 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<1, max\\> might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'class_is_final\' might not exist on array\\{classIndex\\: int, token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, class_is_final\\?\\: bool, method_final_index\\: int\\|null, method_is_constructor\\?\\: bool, method_is_private\\: bool, method_of_enum\\: false\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'method_is_constructor\' might not exist on array\\{classIndex\\: int, token\\: PhpCsFixer\\\\Tokenizer\\\\Token, type\\: string, class_is_final\\: false, method_final_index\\: int\\|null, method_is_constructor\\?\\: bool, method_is_private\\: true, method_of_enum\\: false\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on array\\<int, bool\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'abstract\' might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'end\' might not exist on array\\{start\\: int, visibility\\: non\\-empty\\-string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\?\\: string, name\\?\\: string, end\\?\\: int\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'name\' might not exist on array\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'readonly\' might not exist on non\\-empty\\-array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'static\' might not exist on non\\-empty\\-array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'type\' might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'visibility\' might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on list\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset mixed might not exist on array\\<string, int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset mixed might not exist on array\\{setupbeforeclass\\: 1, dosetupbeforeclass\\: 2, teardownafterclass\\: 3, doteardownafterclass\\: 4, setup\\: 5, dosetup\\: 6, assertpreconditions\\: 7, assertpostconditions\\: 8, \\.\\.\\.\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'normalized\' might not exist on array\\<\'normalized\'\\|\'originalIndex\'\\|\'tokens\'\\|int\\<0, max\\>, int\\|list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>\\.$#',
    'count' => 6,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'originalIndex\' might not exist on array\\<\'normalized\'\\|\'originalIndex\'\\|\'tokens\'\\|int\\<0, max\\>, int\\|list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'tokens\' might not exist on array\\<\'normalized\'\\|\'originalIndex\'\\|\'tokens\'\\|int\\<0, max\\>, int\\|list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\|string\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedInterfacesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on array\\<int, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedTraitsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on array\\{\'\\|\'\\: array\\{10024, \'\\|\'\\}, \'&\'\\: array\\{10035, \'&\'\\}\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedTypesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on non\\-empty\\-array\\<int, bool\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/ProtectedToPrivateFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on array\\{1\\: \'\\|\\^\\#\\\\\\\\s\\*\\$\\|\', 3\\: \'\\|\\^/\\\\\\\\\\*\\[\\\\\\\\s\\\\\\\\\\*\\]\\*\\\\\\\\\\*\\+/\\$\\|\', 2\\: \'\\|\\^//\\\\\\\\s\\*\\$\\|\'\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Comment/NoEmptyCommentFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on list\\{0\\?\\: string, 1\\?\\: string, 2\\?\\: non\\-falsy\\-string\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ControlStructure/NoBreakCommentFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 2 might not exist on list\\{0\\?\\: string, 1\\?\\: string, 2\\?\\: non\\-falsy\\-string\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ControlStructure/NoBreakCommentFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on array\\<int\\|string, bool\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on array\\<int\\|string, bool\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ControlStructure/YodaStyleFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<int\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/FunctionNotation/CombineNestedDirnameFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/FunctionNotation/ImplodeCallFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on non\\-empty\\-array\\<int, int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/FunctionNotation/ImplodeCallFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on array\\<string, PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\Analysis\\\\ArgumentAnalysis\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\Analysis\\\\NamespaceAnalysis\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/GlobalNamespaceImportFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Analyzer\\\\Analysis\\\\NamespaceUseAnalysis\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/GroupImportFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on array\\<int, int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on list\\<int\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-array\\<int\\<0, max\\>, non\\-empty\\-list\\<int\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<int\\>\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<1, max\\> might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
    'count' => 6,
    'path' => __DIR__ . '/../../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<1, max\\> might not exist on non\\-empty\\-list\\<int\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<non\\-falsy\\-string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/SingleImportPerStatementFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Internal/ConfigurableFixerTemplateFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Internal/ConfigurableFixerTemplateFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'get_called_class\'\\|\'get_class\'\\|\'get_class_this\'\\|\'php_sapi_name\'\\|\'phpversion\'\\|\'pi\' might not exist on array\\<string, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on array\\<string, list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
    'count' => 6,
    'path' => __DIR__ . '/../../../src/Fixer/LanguageConstruct/IsNullFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on array\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Naming/NoHomoglyphNamesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-array\\<int\\<0, max\\>, string\\>\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/../../../src/Fixer/Operator/BinaryOperatorSpacesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on non\\-empty\\-array\\<string, bool\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitAttributesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on non\\-empty\\-array\\<int\\<0, max\\>, int\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitConstructFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitConstructFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset non\\-empty\\-string might not exist on array\\<string, string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitConstructFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on list\\<int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on array\\<int, int\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'expectExceptionMessageRegExp\' might not exist on array\\<string, string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitExpectationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 2 might not exist on non\\-empty\\-list\\<int\\<0, max\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitExpectationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<1, max\\> might not exist on list\\<int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitExpectationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on array\\<string, string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitExpectationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitMethodCasingFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 2 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitMethodCasingFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 3 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitMethodCasingFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitMethodCasingFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'expectedException\' might not exist on array\\<string, string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on array\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on list\\<string\\>\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-array\\<int\\<0, max\\>, PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
    'count' => 6,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<8, max\\> might not exist on list\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<array\\{int, string\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitTestCaseStaticMethodCallsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on list\\<array\\{int, string\\}\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitTestCaseStaticMethodCallsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on array\\<string, list\\<array\\{int, string\\}\\>\\>\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitTestCaseStaticMethodCallsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset mixed might not exist on non\\-empty\\-array\\<string, string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/GeneralPhpdocTagRenameFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on list\\{0\\?\\: string, 1\\?\\: string\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAddMissingParamAnnotationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<\\-1, max\\> might not exist on list\\<PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAddMissingParamAnnotationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'desc\' might not exist on array\\{indent\\: string\\|null, tag\\: null, hint\\: string, var\\: string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'desc\' might not exist on array\\{indent\\: string\\|null, tag\\: string, hint\\: string, var\\: \'\', static\\: string, desc\\?\\: string\\|null\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'desc\' might not exist on array\\{indent\\: string\\|null, tag\\: string, hint\\: string, var\\: non\\-empty\\-string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'hint2\' might not exist on non\\-empty\\-array\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'hint3\' might not exist on non\\-empty\\-array\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'signature\' might not exist on non\\-empty\\-array\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'static\' might not exist on non\\-empty\\-array\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<array\\{indent\\: string\\|null, tag\\: string\\|null, hint\\: string, var\\: string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAnnotationWithoutDotFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 2 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAnnotationWithoutDotFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 3 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAnnotationWithoutDotFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocArrayTypeFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 2 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocArrayTypeFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on array\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocInlineTagNormalizerFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 2 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocInlineTagNormalizerFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'general_phpdoc_tag_rename\' might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocNoAliasTagFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 2 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocNoUselessInheritdocFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on list\\<PhpCsFixer\\\\DocBlock\\\\Annotation\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<1, max\\> might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocSingleLineVarSpacingFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'general_phpdoc_tag_rename\' might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocTagCasingFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1\\|int\\<3, max\\> might not exist on array\\<int\\<0, max\\>, string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocTagTypeFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on array\\<int, string\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocTagTypeFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocTypesOrderFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on list\\<int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ReturnNotation/ReturnAssignmentFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Strict/StrictParamFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'string_implicit_backslashes\' might not exist on array\\<string, PhpCsFixer\\\\Fixer\\\\FixerInterface\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/StringNotation/EscapeImplicitBackslashesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'end_index\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'initial_indent\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'new_indent\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'type\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on non\\-empty\\-list\\<non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-list\\<non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, int\\|string\\>\\>\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/ArrayIndentationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on list\\<int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/BlankLineBetweenImportGroupsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on array\\{0\\?\\: string\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/HeredocIndentationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/IndentationTypeFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/IndentationTypeFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 2 might not exist on array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/IndentationTypeFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on array\\<int, callable\\(int\\)\\: void\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/NoExtraBlankLinesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<1, max\\> might not exist on list\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/NoExtraBlankLinesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on array\\<string, callable\\(int\\)\\: void\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/NoExtraBlankLinesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/NoTrailingWhitespaceFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<1, max\\> might not exist on array\\<int\\<0, max\\>, string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/NoTrailingWhitespaceFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on non\\-empty\\-array\\<int\\<0, max\\>, string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/NoWhitespaceInBlankLineFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'new_indent\' might not exist on array\\{type\\: \'statement\', skip\\: bool, end_index\\: int\\|null, end_index_inclusive\\: bool, initial_indent\\: string, new_indent\\?\\: string, is_indented_block\\: bool\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on array\\<int, int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on list\\<array\\{type\\: \'block\'\\|\'block_signature\'\\|\'statement\', skip\\: bool, end_index\\: int\\|null, end_index_inclusive\\: bool, initial_indent\\: string, new_indent\\?\\: string, is_indented_block\\: bool\\}\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<\\-1, max\\> might not exist on list\\<array\\{type\\: \'block\'\\|\'block_signature\'\\|\'statement\', skip\\: bool, end_index\\: int\\|null, end_index_inclusive\\: bool, initial_indent\\: string, new_indent\\?\\: string, is_indented_block\\: bool\\}\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Fixer/Whitespace/StatementIndentationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on non\\-empty\\-array\\<string, array\\<int\\<0, max\\>, string\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/FixerFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset string might not exist on array\\<string, array\\<string, mixed\\>\\|true\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/RuleSet.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'action\' might not exist on array\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'file\' might not exist on non\\-empty\\-array\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'identifier\' might not exist on non\\-empty\\-array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'status\' might not exist on non\\-empty\\-array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 might not exist on list\\<non\\-empty\\-string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Runner/Runner.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'alternative_syntax\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'brace_count\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'default\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'index\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'kind\' might not exist on non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<\\-1, max\\> might not exist on list\\<non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/../../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int\\<0, max\\> might not exist on list\\<non\\-empty\\-array\\<literal\\-string&non\\-falsy\\-string, mixed\\>\\>\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Tokenizer/Analyzer/ControlCaseStructuresAnalyzer.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 might not exist on list\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 2 might not exist on list\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset int might not exist on non\\-empty\\-array\\<int, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset non\\-empty\\-string might not exist on array\\<non\\-empty\\-string, PhpCsFixer\\\\Tokenizer\\\\Tokens\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 0 does not exist on array&T of mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Utils.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
