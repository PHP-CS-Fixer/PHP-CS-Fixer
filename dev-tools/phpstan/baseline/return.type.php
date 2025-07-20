<?php declare(strict_types = 1);

// total 16 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Method PhpCsFixer\\\\Config\\:\\:getFinder\\(\\) should return PhpCsFixer\\\\Finder but returns iterable\\<SplFileInfo\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Config.php',
];
$ignoreErrors[] = [
    'message' => '#^Method PhpCsFixer\\\\Fixer\\\\AbstractPhpUnitFixer\\:\\:addInternalAnnotation\\(\\) should return list\\<PhpCsFixer\\\\DocBlock\\\\Line\\> but returns non\\-empty\\-list\\<PhpCsFixer\\\\DocBlock\\\\Line\\|non\\-falsy\\-string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/AbstractPhpUnitFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\ClassDefinitionFixer\\:\\:getClassyDefinitionInfo\\(\\) should return array\\{start\\: int, classy\\: int, open\\: int, extends\\: array\\{start\\: int, numberOfExtends\\: int, multiLine\\: bool\\}\\|false, implements\\: array\\{start\\: int, numberOfImplements\\: int, multiLine\\: bool\\}\\|false, anonymousClass\\: bool, final\\: int\\|false, abstract\\: int\\|false, \\.\\.\\.\\} but returns array\\{classy\\: int, open\\: int\\|null, extends\\: array\\<string, bool\\|int\\>\\|false, implements\\: array\\<string, bool\\|int\\>\\|false, anonymousClass\\: bool, final\\: int\\|false, abstract\\: int\\|false, readonly\\: int\\|false, \\.\\.\\.\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/ClassDefinitionFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\NoPhp4ConstructorFixer\\:\\:findFunction\\(\\) should return array\\{nameIndex\\: int, startIndex\\: int, endIndex\\: int, bodyIndex\\: int, modifiers\\: list\\<int\\>\\}\\|null but returns array\\{nameIndex\\: int\\<0, max\\>, startIndex\\: int, endIndex\\: int\\|null, bodyIndex\\: int\\|null, modifiers\\: array\\<\'\'\\|int, int\\>\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\NoPhp4ConstructorFixer\\:\\:getWrapperMethodSequence\\(\\) should return array\\{list\\<list\\<array\\{int, string\\}\\|int\\|string\\>\\>, array\\{3\\: false\\}\\} but returns array\\{list\\<non\\-empty\\-list\\<array\\{0\\: int, 1\\?\\: string\\}\\|\\(literal\\-string&non\\-falsy\\-string\\)\\>\\>, array\\{3\\: false\\}\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Method PhpCsFixer\\\\Fixer\\\\ClassNotation\\\\OrderedClassElementsFixer\\:\\:getElements\\(\\) should return list\\<array\\{start\\: int, visibility\\: string, abstract\\: bool, static\\: bool, readonly\\: bool, type\\: string, name\\: string, end\\: int\\}\\> but returns list\\<array\\{start\\: int, visibility\\: \'public\', abstract\\: false, static\\: false, readonly\\: bool, type\\: string, name\\?\\: string, end\\: int\\}\\|array\\{start\\: int, visibility\\: non\\-empty\\-string, abstract\\: bool, static\\: bool, readonly\\: bool\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Method PhpCsFixer\\\\Fixer\\\\FunctionNotation\\\\ImplodeCallFixer\\:\\:getArgumentIndices\\(\\) should return array\\<int, int\\> but returns array\\<int\\|string, int\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/FunctionNotation/ImplodeCallFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Import\\\\OrderedImportsFixer\\:\\:getNewOrder\\(\\) should return array\\<int, array\\{namespace\\: non\\-empty\\-string, startIndex\\: int, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\> but returns array\\<\'\'\\|int, array\\{namespace\\: string, startIndex\\: int\\|null, endIndex\\: int, importType\\: \'class\'\\|\'const\'\\|\'function\', group\\: bool\\}\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Import/OrderedImportsFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitAttributesFixer\\:\\:createAttributeTokens\\(\\) should return list\\<PhpCsFixer\\\\Tokenizer\\\\Token\\> but returns non\\-empty\\-array\\<int\\|string, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitAttributesFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Method PhpCsFixer\\\\Fixer\\\\PhpUnit\\\\PhpUnitTestAnnotationFixer\\:\\:updateLines\\(\\) should return list\\<PhpCsFixer\\\\DocBlock\\\\Line\\> but returns array\\<int\\<0, max\\>, PhpCsFixer\\\\DocBlock\\\\Line\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Method PhpCsFixer\\\\Fixer\\\\Phpdoc\\\\PhpdocAlignFixer\\:\\:getMatches\\(\\) should return array\\{indent\\: string\\|null, tag\\: string\\|null, hint\\: string, var\\: string\\|null, static\\: string, desc\\?\\: string\\|null\\}\\|null but returns non\\-empty\\-array\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findGivenKind\\(\\) should return array\\<int, array\\<int\\<0, max\\>, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\> but returns array\\<\'\'\\|int, array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
    'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findOppositeBlockEdge\\(\\) should return int\\<0, max\\> but returns int\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/../../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
    'message' => '#^Method PhpCsFixer\\\\Tokenizer\\\\Tokens\\:\\:findSequence\\(\\) should return non\\-empty\\-array\\<int\\<0, max\\>, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\|null but returns non\\-empty\\-array\\<int, PhpCsFixer\\\\Tokenizer\\\\Token\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Tokens.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
