<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Andreas Möller <am@localheinz.com>
 */
final class BlankLineBeforeStatementFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @var array<string, int>
     */
    private static array $tokenMap = [
        'break' => T_BREAK,
        'case' => T_CASE,
        'continue' => T_CONTINUE,
        'declare' => T_DECLARE,
        'default' => T_DEFAULT,
        'do' => T_DO,
        'exit' => T_EXIT,
        'for' => T_FOR,
        'foreach' => T_FOREACH,
        'goto' => T_GOTO,
        'if' => T_IF,
        'include' => T_INCLUDE,
        'include_once' => T_INCLUDE_ONCE,
        'phpdoc' => T_DOC_COMMENT,
        'require' => T_REQUIRE,
        'require_once' => T_REQUIRE_ONCE,
        'return' => T_RETURN,
        'switch' => T_SWITCH,
        'throw' => T_THROW,
        'try' => T_TRY,
        'while' => T_WHILE,
        'yield' => T_YIELD,
        'yield_from' => T_YIELD_FROM,
    ];

    /**
     * @var list<int>
     */
    private array $fixTokenMap = [];

    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->fixTokenMap = [];

        foreach ($this->configuration['statements'] as $key) {
            $this->fixTokenMap[$key] = self::$tokenMap[$key];
        }

        $this->fixTokenMap = array_values($this->fixTokenMap);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'An empty line feed must precede any configured statement.',
            [
                new CodeSample(
                    '<?php
function A() {
    echo 1;
    return 1;
}
'
                ),
                new CodeSample(
                    '<?php
switch ($foo) {
    case 42:
        $bar->process();
        break;
    case 44:
        break;
}
',
                    [
                        'statements' => ['break'],
                    ]
                ),
                new CodeSample(
                    '<?php
foreach ($foo as $bar) {
    if ($bar->isTired()) {
        $bar->sleep();
        continue;
    }
}
',
                    [
                        'statements' => ['continue'],
                    ]
                ),
                new CodeSample(
                    '<?php
$i = 0;
do {
    echo $i;
} while ($i > 0);
',
                    [
                        'statements' => ['do'],
                    ]
                ),
                new CodeSample(
                    '<?php
if ($foo === false) {
    exit(0);
} else {
    $bar = 9000;
    exit(1);
}
',
                    [
                        'statements' => ['exit'],
                    ]
                ),
                new CodeSample(
                    '<?php
a:

if ($foo === false) {
    goto a;
} else {
    $bar = 9000;
    goto b;
}
',
                    [
                        'statements' => ['goto'],
                    ]
                ),
                new CodeSample(
                    '<?php
$a = 9000;
if (true) {
    $foo = $bar;
}
',
                    [
                        'statements' => ['if'],
                    ]
                ),
                new CodeSample(
                    '<?php

if (true) {
    $foo = $bar;
    return;
}
',
                    [
                        'statements' => ['return'],
                    ]
                ),
                new CodeSample(
                    '<?php
$a = 9000;
switch ($a) {
    case 42:
        break;
}
',
                    [
                        'statements' => ['switch'],
                    ]
                ),
                new CodeSample(
                    '<?php
if (null === $a) {
    $foo->bar();
    throw new \UnexpectedValueException("A cannot be null.");
}
',
                    [
                        'statements' => ['throw'],
                    ]
                ),
                new CodeSample(
                    '<?php
$a = 9000;
try {
    $foo->bar();
} catch (\Exception $exception) {
    $a = -1;
}
',
                    [
                        'statements' => ['try'],
                    ]
                ),
                new CodeSample(
                    '<?php
function getValues() {
    yield 1;
    yield 2;
    // comment
    yield 3;
}
',
                    [
                        'statements' => ['yield'],
                    ]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after NoExtraBlankLinesFixer, NoUselessElseFixer, NoUselessReturnFixer, ReturnAssignmentFixer, YieldFromArrayToYieldsFixer.
     */
    public function getPriority(): int
    {
        return -21;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound($this->fixTokenMap);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $analyzer = new TokensAnalyzer($tokens);

        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind($this->fixTokenMap)) {
                continue;
            }

            if ($token->isGivenKind(T_WHILE) && $analyzer->isWhilePartOfDoWhile($index)) {
                continue;
            }

            if ($token->isGivenKind(T_CASE) && $analyzer->isEnumCase($index)) {
                continue;
            }

            $insertBlankLineIndex = $this->getInsertBlankLineIndex($tokens, $index);
            $prevNonWhitespace = $tokens->getPrevNonWhitespace($insertBlankLineIndex);

            if ($this->shouldAddBlankLine($tokens, $prevNonWhitespace)) {
                $this->insertBlankLine($tokens, $insertBlankLineIndex);
            }

            $index = $prevNonWhitespace;
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('statements', 'List of statements which must be preceded by an empty line.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset(array_keys(self::$tokenMap))])
                ->setDefault([
                    'break',
                    'continue',
                    'declare',
                    'return',
                    'throw',
                    'try',
                ])
                ->getOption(),
        ]);
    }

    private function getInsertBlankLineIndex(Tokens $tokens, int $index): int
    {
        while ($index > 0) {
            if ($tokens[$index - 1]->isWhitespace() && substr_count($tokens[$index - 1]->getContent(), "\n") > 1) {
                break;
            }

            $prevIndex = $tokens->getPrevNonWhitespace($index);

            if (!$tokens[$prevIndex]->isComment()) {
                break;
            }

            if (!$tokens[$prevIndex - 1]->isWhitespace()) {
                break;
            }

            if (1 !== substr_count($tokens[$prevIndex - 1]->getContent(), "\n")) {
                break;
            }

            $index = $prevIndex;
        }

        return $index;
    }

    private function shouldAddBlankLine(Tokens $tokens, int $prevNonWhitespace): bool
    {
        $prevNonWhitespaceToken = $tokens[$prevNonWhitespace];

        if ($prevNonWhitespaceToken->isComment()) {
            for ($j = $prevNonWhitespace - 1; $j >= 0; --$j) {
                if (str_contains($tokens[$j]->getContent(), "\n")) {
                    return false;
                }

                if ($tokens[$j]->isWhitespace() || $tokens[$j]->isComment()) {
                    continue;
                }

                return $tokens[$j]->equalsAny([';', '}']);
            }
        }

        return $prevNonWhitespaceToken->equalsAny([';', '}']);
    }

    private function insertBlankLine(Tokens $tokens, int $index): void
    {
        $prevIndex = $index - 1;
        $prevToken = $tokens[$prevIndex];
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        if ($prevToken->isWhitespace()) {
            $newlinesCount = substr_count($prevToken->getContent(), "\n");

            if (0 === $newlinesCount) {
                $tokens[$prevIndex] = new Token([T_WHITESPACE, rtrim($prevToken->getContent(), " \t").$lineEnding.$lineEnding]);
            } elseif (1 === $newlinesCount) {
                $tokens[$prevIndex] = new Token([T_WHITESPACE, $lineEnding.$prevToken->getContent()]);
            }
        } else {
            $tokens->insertAt($index, new Token([T_WHITESPACE, $lineEnding.$lineEnding]));
        }
    }
}
