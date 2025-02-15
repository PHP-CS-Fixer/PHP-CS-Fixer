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
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

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
        return 30;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return \PHP_VERSION_ID >= 8_02_00 && $tokens->isTokenKindFound(T_CLASS);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Removes redundant `readonly` from properties where possible and adds `readonly` modifier if the class is final.',
            [
                new VersionSpecificCodeSample(
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
                }\n",
                    new VersionSpecification(8_02_00)
                ),
                new VersionSpecificCodeSample(
                    "<?php
                final class TestClass
                {
                    public function __construct(
                         public readonly string \$foo,
                         public readonly int \$bar,
                    ) {}
                }\n",
                    new VersionSpecification(8_02_00)
                ),
            ],
            null,
            'Changing `readonly` properties might cause code execution to break.'
        );
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $classIndex = null;
        $isClassReadonly = false;
        $isClassFinal = false;
        $canMarkClassReadonly = true;
        $readonlyTokenIndexes = [];

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_CLASS)) {
                $classIndex = $index;
                $modifiers = $tokensAnalyzer->getClassyModifiers($index);
                $isClassFinal = null !== $modifiers['final'];
                $isClassReadonly = null !== $modifiers['readonly'];
            }

            if ($token->isGivenKind(T_VARIABLE)) {
                $prevTokenIndex = $tokens->getPrevMeaningfulToken($index);
                $readonlyIndex = $tokens->getPrevMeaningfulToken($prevTokenIndex);

                if (!$tokens[$readonlyIndex]->isGivenKind(T_READONLY)) {
                    $canMarkClassReadonly = false;
                }

                if ($tokens[$readonlyIndex]->isGivenKind(T_READONLY)) {
                    if (!$isClassReadonly && $isClassFinal) {
                        $readonlyIndex += 2;
                    }

                    $readonlyTokenIndexes[] = $readonlyIndex;
                }
            }
        }

        if (!$isClassReadonly && $isClassFinal && $canMarkClassReadonly) {
            $tokens->insertAt($classIndex, [
                new Token([T_READONLY, 'readonly']),
                new Token([T_WHITESPACE, ' ']),
            ]);

            $isClassReadonly = true;
        }

        if (!$isClassReadonly) {
            return;
        }

        foreach ($readonlyTokenIndexes as $index) {
            $tokens->clearAt($index);

            $whiteSpaceIndex = $index + 1;
            if ($tokens[$whiteSpaceIndex]->isGivenKind(T_WHITESPACE)) {
                $tokens->clearAt($whiteSpaceIndex);
            }
        }
    }
}
