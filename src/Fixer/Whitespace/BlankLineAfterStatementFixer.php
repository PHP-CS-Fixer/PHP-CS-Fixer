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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * Fixer for rules defined in PSR2 ¶3.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class BlankLineAfterStatementFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /**
     * @var array
     */
    private static $tokenMap = [
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
        'namespace' => T_NAMESPACE,
        'open_tag' => T_OPEN_TAG,
        'require' => T_REQUIRE,
        'require_once' => T_REQUIRE_ONCE,
        'return' => T_RETURN,
        'switch' => T_SWITCH,
        'throw' => T_THROW,
        'try' => T_TRY,
        'while' => T_WHILE,
        'yield' => T_YIELD,
        'yield_from' => T_YIELD_FROM
    ];

    /**
     * @var array
     */
    private $fixTokenMap = [];
   
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->fixTokenMap = [];

        foreach ($this->configuration['statements'] as $key) {
            $this->fixTokenMap[$key] = self::$tokenMap[$key];
        }

        $this->fixTokenMap = array_values($this->fixTokenMap);
    }

        /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'One blank line after any configured statement.',
            [
                // 'Ensure there is no code on the same line as the PHP open tag and it is followed by a blank line.'
                new CodeSample("<?php \$a = 1;\n\$b = 1;\n",
                    [
                        'statements' => ['open_tag'], //@TODO getpriority 1 here, how to set?
                    ]),
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
                new CodeSample("<?php\nnamespace Sample\\Sample;\n\n\n\$a;\n",
                    [
                        'statements' => ['namespace'],
                    ]),
                new CodeSample("<?php\nnamespace Sample\\Sample;\nClass Test{}\n",
                    [
                        'statements' => ['namespace'],
                    ]),
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

if (true) {
    $foo = $bar;
    yield $foo;
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
     * Must run after NoUnusedImportsFixer.
     */
    public function getPriority(): int
    {
        return -20;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound($this->fixTokenMap);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        /* @TODO Apply fix for BlankLineAfterOpenTag here

        $lineEnding = $this->whitespacesConfig->getLineEnding();

        // ignore files with short open tag and ignore non-monolithic files
        if (!$tokens[0]->isGivenKind(T_OPEN_TAG) || !$tokens->isMonolithicPhp()) {
            return;
        }

        $newlineFound = false;
        /** @var Token $token * /
        foreach ($tokens as $token) {
            if ($token->isWhitespace() && false !== strpos($token->getContent(), "\n")) {
                $newlineFound = true;

                break;
            }
        }

        // ignore one-line files
        if (!$newlineFound) {
            return;
        }

        $token = $tokens[0];

        if (false === strpos($token->getContent(), "\n")) {
            $tokens[0] = new Token([$token->getId(), rtrim($token->getContent()).$lineEnding]);
        }

        if (false === strpos($tokens[1]->getContent(), "\n")) {
            if ($tokens[1]->isWhitespace()) {
                $tokens[1] = new Token([T_WHITESPACE, $lineEnding.$tokens[1]->getContent()]);
            } else {
                $tokens->insertAt(1, new Token([T_WHITESPACE, $lineEnding]));
            }
        }
        */
        
        
        $analyzer = new TokensAnalyzer($tokens);
        
        $lastIndex = $tokens->count() - 1;

        for ($index = $lastIndex; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind($this->fixTokenMap)) {
                continue;
            }
            
            if (!$token->isGivenKind(T_NAMESPACE)) {
                continue;
            }

            $semicolonIndex = $tokens->getNextTokenOfKind($index, [';', '{', [T_CLOSE_TAG]]);
            $semicolonToken = $tokens[$semicolonIndex];

            if (!$semicolonToken->equals(';')) {
                continue;
            }

            $indexToEnsureBlankLineAfter = $this->getIndexToEnsureBlankLineAfter($tokens, $semicolonIndex);
            $indexToEnsureBlankLine = $tokens->getNonEmptySibling($indexToEnsureBlankLineAfter, 1);

            if (null !== $indexToEnsureBlankLine && $tokens[$indexToEnsureBlankLine]->isWhitespace()) {
                $tokens[$indexToEnsureBlankLine] = $this->getTokenToInsert($tokens[$indexToEnsureBlankLine]->getContent(), $indexToEnsureBlankLine === $lastIndex);
            } else {
                $tokens->insertAt($indexToEnsureBlankLineAfter + 1, $this->getTokenToInsert('', $indexToEnsureBlankLineAfter === $lastIndex));
            }
        }
    }

    private function getIndexToEnsureBlankLineAfter(Tokens $tokens, int $index): int
    {
        $indexToEnsureBlankLine = $index;
        $nextIndex = $tokens->getNonEmptySibling($indexToEnsureBlankLine, 1);

        while (null !== $nextIndex) {
            $token = $tokens[$nextIndex];

            if ($token->isWhitespace()) {
                if (1 === Preg::match('/\R/', $token->getContent())) {
                    break;
                }
                $nextNextIndex = $tokens->getNonEmptySibling($nextIndex, 1);

                if (!$tokens[$nextNextIndex]->isComment()) {
                    break;
                }
            }

            if (!$token->isWhitespace() && !$token->isComment()) {
                break;
            }

            $indexToEnsureBlankLine = $nextIndex;
            $nextIndex = $tokens->getNonEmptySibling($indexToEnsureBlankLine, 1);
        }

        return $indexToEnsureBlankLine;
    }

    private function getTokenToInsert(string $currentContent, bool $isLastIndex): Token
    {
        $ending = $this->whitespacesConfig->getLineEnding();

        $emptyLines = $isLastIndex ? $ending : $ending.$ending;
        $indent = 1 === Preg::match('/^.*\R( *)$/s', $currentContent, $matches) ? $matches[1] : '';

        return new Token([T_WHITESPACE, $emptyLines.$indent]);
    }
}
