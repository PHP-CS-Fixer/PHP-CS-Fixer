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

namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class NumericLiteralCaseFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Number literals must be in correct case.',
            [
                new CodeSample(
                    "<?php\n\$foo = 0Xff;\n\$bar = 0B11111111;\n\n\$foo = 3E14;\n\$bar = 7.6E-5;\n"
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_LNUMBER, T_DNUMBER]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_LNUMBER)) {
                $content = $token->getContent();
                $newContent = Preg::replaceCallback('#^0([boxBOX])([0-9a-fA-F_]+)$#', static fn ($matches) => '0'.strtolower($matches[1]).strtoupper($matches[2]), $content);

                if ($content !== $newContent) {
                    $tokens[$index] = new Token([T_LNUMBER, $newContent]);
                }
            } elseif ($token->isGivenKind(T_DNUMBER)) {
                $content = $token->getContent();
                $newContent = strtolower($content);

                if ($content !== $newContent) {
                    $tokens[$index] = new Token([T_DNUMBER, $newContent]);
                }
            }
        }
    }
}
