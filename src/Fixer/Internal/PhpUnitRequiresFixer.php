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

namespace PhpCsFixer\Fixer\Internal;

use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Fixer\InternalFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class PhpUnitRequiresFixer extends AbstractPhpUnitFixer implements InternalFixerInterface
{
    public function getName(): string
    {
        return 'PhpCsFixerInternal/'.parent::getName();
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'PHPUnit `@requires` annotation must start with `~`, `<` or `>=`.',
            [
                new CodeSample(
                    <<<'PHP'
                        <?php
                        /**
                         * @requires PHP 8.0
                         */
                        final class FooTest extends TestCase {
                            /**
                             * @requires PHP 8.3
                             */
                            public function testSomething1() {}
                            /**
                             * @requires PHP <8.2
                             */
                            public function testSomething2() {}
                        }

                        PHP
                ),
            ],
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_CLASS);
    }

    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $classDocIndex = $tokens->getPrevTokenOfKind($startIndex, [[T_DOC_COMMENT]]);
        if (null !== $classDocIndex) {
            $this->fixDocComment($tokens, $classDocIndex);
        }

        for ($index = $startIndex; $index < $endIndex; ++$index) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }
            $docIndex = $tokens->getPrevTokenOfKind($index, [[T_DOC_COMMENT], '}']);
            $this->fixDocComment($tokens, $docIndex);
        }
    }

    private function fixDocComment(Tokens $tokens, int $index): void
    {
        if (!$tokens[$index]->isGivenKind(T_DOC_COMMENT)) {
            return;
        }

        $content = $tokens[$index]->getContent();

        $content = Preg::replace('/@requires +PHP +/', '@requires PHP ', $content);
        $content = Preg::replace('/@requires PHP ([^\d]*\d)(?!\.)/', '@requires PHP $1.0', $content);
        $content = Preg::replace('/@requires PHP \^?(\d)/', '@requires PHP >=$1', $content);
        $content = Preg::replace('/@requires PHP (<|>=) *(\d)/', '@requires PHP $1 $2', $content);

        if (Preg::match('/@requires PHP /', $content) && !Preg::match('/@requires PHP (~|< |>= )[\d\.]+\R/', $content)) {
            throw new \RuntimeException('Use either "@requires PHP ~VERSION", "@requires PHP >= VERSION" or "@requires PHP < VERSION", found:'.PHP_EOL.$content);
        }

        $tokens[$index] = new Token([T_DOC_COMMENT, $content]);
    }
}
