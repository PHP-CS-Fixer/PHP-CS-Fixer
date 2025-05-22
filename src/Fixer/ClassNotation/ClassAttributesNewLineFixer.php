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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Oleksandr Bredikhin <olbresoft@gmail.com>
 */
final class ClassAttributesNewLineFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    public function isCandidate(Tokens $tokens): bool
    {
        return \PHP_VERSION_ID >= 8_00_00 && $tokens->isTokenKindFound(T_ATTRIBUTE);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Class attributes should be on their own line.',
            [
                new VersionSpecificCodeSample(
                    "<?php
#[Foo] #[Bar] class Baz
{
}\n",
                    new VersionSpecification(8_00_00)
                ),
            ],
        );
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $classIndexes = $tokens->findGivenKind(T_CLASS);
        foreach ($classIndexes as $classIndex => $token) {
            $classEndIndex = $tokens->getPrevTokenOfKind($classIndex, ['{', [T_OPEN_TAG]]);
            for ($index = $classIndex; $index >= $classEndIndex; --$index) {
                if ($tokens[$index]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
                    $this->fixNewline($tokens, $index);
                }
            }
        }
    }

    private function fixNewline(Tokens $tokens, int $endIndex): void
    {
        $nextIndex = $endIndex + 1;
        if ($tokens[$nextIndex]->isWhitespace()) {
            $whitespace = $tokens[$nextIndex]->getContent();

            if (str_contains($whitespace, "\n") || str_contains($whitespace, "\r")) {
                return;
            }

            $tokens->clearAt($nextIndex);
        }

        $tokens->ensureWhitespaceAtIndex($endIndex + 1, 0, $this->whitespacesConfig->getLineEnding());
    }
}
