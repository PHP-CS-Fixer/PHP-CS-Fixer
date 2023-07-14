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

namespace PhpCsFixer\Fixer\Basic;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class OctalNotationFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Literal octal must be in `0o` notation.',
            [
                new VersionSpecificCodeSample(
                    "<?php \$foo = 0123;\n",
                    new VersionSpecification(8_01_00)
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return \PHP_VERSION_ID >= 8_01_00 && $tokens->isTokenKindFound(T_LNUMBER);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_LNUMBER)) {
                continue;
            }

            $content = $token->getContent();

            if (!Preg::match('#^0[\d_]+$#', $content)) {
                continue;
            }

            $tokens[$index] = Preg::match('#^0+$#', $content)
                ? new Token([T_LNUMBER, '0'])
                : new Token([T_LNUMBER, '0o'.('_' === $content[1] ? '0' : '').substr($content, 1)]);
        }
    }
}
