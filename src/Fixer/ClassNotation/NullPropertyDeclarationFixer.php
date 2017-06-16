<?php

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
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author ntzm
 */
final class NullPropertyDeclarationFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([T_PUBLIC, T_PROTECTED, T_PRIVATE, T_VAR]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Simplifies null class property declarations.',
            [
                new CodeSample('<?php
class Foo {
    public $foo = null;
}'),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
            if (!$tokens[$index]->isGivenKind([T_PUBLIC, T_PROTECTED, T_PRIVATE, T_VAR])) {
                continue;
            }

            while (true) {
                $varTokenIndex = $index = $tokens->getNextMeaningfulToken($index);

                if (!$tokens[$index]->isGivenKind(T_VARIABLE)) {
                    break;
                }

                $index = $tokens->getNextMeaningfulToken($index);

                if ($tokens[$index]->equals('=')) {
                    $valueTokenIndex = $index = $tokens->getNextMeaningfulToken($index);

                    if ($tokens[$index]->equals([T_STRING, 'null'], false)) {
                        $tokens->clearRange($varTokenIndex + 1, $valueTokenIndex);
                    }

                    ++$index;
                }

                if (!$tokens[$index]->equals(',')) {
                    break;
                }
            }
        }
    }
}
