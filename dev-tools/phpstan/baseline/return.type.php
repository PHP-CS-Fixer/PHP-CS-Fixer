<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\Fixer\\ClassNotation\\NoPhp4ConstructorFixer::findFunction() should return array{nameIndex: int, startIndex: int, endIndex: int, bodyIndex: int, modifiers: list<int>}|null but returns array{nameIndex: int<0, max>, startIndex: int, endIndex: int|null, bodyIndex: int|null, modifiers: array<int, int>}.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\Fixer\\ClassNotation\\OrderedClassElementsFixer::getElements() should return list<array{start: int, visibility: string, abstract: bool, static: bool, readonly: bool, type: string, name: string, end: int}> but returns list<array{start: int, visibility: \'public\', abstract: false, static: false, readonly: bool, type: string, name?: string, end: int}|array{start: int, visibility: non-empty-string, abstract: bool, static: bool, readonly: bool}>.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\Fixer\\FunctionNotation\\ImplodeCallFixer::getArgumentIndices() should return array<int, int> but returns array<int|string, int|null>.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/FunctionNotation/ImplodeCallFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Anonymous function should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/NoSuperfluousPhpdocTagsFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\Fixer\\Phpdoc\\PhpdocAlignFixer::getMatches() should return array{indent: string|null, tag: string|null, hint: string, var: string|null, static: string, desc?: string|null}|null but returns non-empty-array<string>.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/PhpdocAlignFixer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\RuleSet\\Sets\\AutoPHPMigrationRiskySet::getRulesCandidates() should return array<string, array<string, mixed>|bool> but returns array<string, true>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/Sets/AutoPHPMigrationRiskySet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\RuleSet\\Sets\\AutoPHPMigrationSet::getRulesCandidates() should return array<string, array<string, mixed>|bool> but returns array<string, true>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/Sets/AutoPHPMigrationSet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\RuleSet\\Sets\\AutoPHPUnitMigrationRiskySet::getRulesCandidates() should return array<string, array<string, mixed>|bool> but returns array<string, true>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/Sets/AutoPHPUnitMigrationRiskySet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\RuleSet\\Sets\\AutoRiskySet::getRules() should return array<string, array<string, mixed>|bool> but returns array<string, true>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/Sets/AutoRiskySet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\RuleSet\\Sets\\AutoRiskySet::getRulesCandidates() should return array<string, array<string, mixed>|bool> but returns array<string, true>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/Sets/AutoRiskySet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\RuleSet\\Sets\\AutoSet::getRules() should return array<string, array<string, mixed>|bool> but returns array<string, true>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/Sets/AutoSet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\RuleSet\\Sets\\AutoSet::getRulesCandidates() should return array<string, array<string, mixed>|bool> but returns array<string, true>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/Sets/AutoSet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\Tokenizer\\CT::getMapById() should return array<10001|10002|10003|10004|10005|10006|10007|10008|10009|10010|10011|10012|10013|10014|10015|10016|10017|10018|10019|10020|10021|10022|10023|10024|10025|10026|10027|10028|10029|10030|10031|10032|10033|10034|10035|10036|10037|10038|10039|10040|10041, non-empty-string> but returns array<string>.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/CT.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\Tokenizer\\Token::getKeywords() should return non-empty-list<int> but returns non-empty-array<int, int>.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Token.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\Tokenizer\\Tokens::findGivenKind() should return array<int, array<int<0, max>, PhpCsFixer\\Tokenizer\\Token>|PhpCsFixer\\Tokenizer\\Token> but returns array<int, array<int, PhpCsFixer\\Tokenizer\\Token>|PhpCsFixer\\Tokenizer\\Token>.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Tokens.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\Tokenizer\\Tokens::findSequence() should return non-empty-array<int<0, max>, PhpCsFixer\\Tokenizer\\Token>|null but returns non-empty-array<int, PhpCsFixer\\Tokenizer\\Token>.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Tokens.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
