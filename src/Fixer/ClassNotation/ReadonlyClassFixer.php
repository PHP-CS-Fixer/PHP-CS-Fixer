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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Oleksandr Bredikhin <olbresoft@gmail.com>
 */
final class ReadonlyClassFixer extends AbstractFixer
{
    public function isRisky(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * Must run after FinalClassFixer, FinalInternalClassFixer
     */
    public function getPriority(): int
    {
        return 67;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return \PHP_VERSION_ID >= 8_02_00 && $tokens->isTokenKindFound(T_CLASS);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Removes redundant `readonly` from properties where possible and adds `readonly` modifier  if the class is final.',
            [
                new CodeSample(
                    "<?php
            readonly class MyService
            {
                private readonly Foo \$foo;

                public function __construct(
                    FooFactory \$fooFactory,
                    private readonly Bar \$bar,
                ) {
                    \$this->foo = \$fooFactory->create();
                }
            }\n"
                ),
                new CodeSample(
                    "<?php
            final class TestClass
            {
                public function __construct(
                     public readonly string \$foo,
                     public readonly int \$bar,
                ) {}
            }\n"
                ),
            ],
            null,
            'Changing `readonly` properties might cause code execution to break.'
        );
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $classIndex = null;
        $isClassReadonly = false;
        $isClassFinal = false;
        $canMarkClassReadonly = true;
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_READONLY)) {
                $nextToken = $tokens->getNextNonWhitespace($index) ?? null;

                if ($nextToken && $tokens[$nextToken]->isGivenKind(T_CLASS)) {
                    $isClassReadonly = true;
                    $classIndex = $nextToken;

                    continue;
                }
            }

            if ($token->isGivenKind(T_CLASS)) {
                $classIndex = $index;
                $finalIndex = $tokens->getPrevTokenOfKind($classIndex, [[T_FINAL]]);

                if ($finalIndex && $tokens[$finalIndex]->isGivenKind(T_FINAL)) {
                    $isClassFinal = true;

                    continue;
                }
            }

            if ($token->isGivenKind(T_VARIABLE) && null !== $classIndex) {
                $prevTokenIndex = $tokens->getPrevMeaningfulToken($index);
                $prevPrevTokenIndex = $tokens->getPrevMeaningfulToken($prevTokenIndex);

                if ($prevPrevTokenIndex && !$tokens[$prevPrevTokenIndex]->isGivenKind(T_READONLY)) {
                    $canMarkClassReadonly = false;

                    break;
                }
            }
        }

        if (!$isClassReadonly && $isClassFinal && $canMarkClassReadonly) {
            $tokens->insertAt($classIndex, [
                new Token([T_READONLY, 'readonly']),
                new Token([T_WHITESPACE, ' ']),
            ]);
            $classIndex += 2;
            $isClassReadonly = true;
        }

        if (!$isClassReadonly) {
            return;
        }

        $countTokens = $tokens->count();
        for ($index = $classIndex; $index < $countTokens; ++$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(
                [T_READONLY]
            )) {
                $tokens->clearAt($index);
                ++$index;
                if ($tokens[$index]->isGivenKind(T_WHITESPACE)) {
                    $tokens->clearAt($index);
                }
            }
        }
    }
}
