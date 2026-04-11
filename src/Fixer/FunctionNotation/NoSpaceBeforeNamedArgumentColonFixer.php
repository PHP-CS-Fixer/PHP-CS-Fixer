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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author HypeMC <hypemc@gmail.com>
 */
final class NoSpaceBeforeNamedArgumentColonFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'There must be no space before named arguments colons.',
            [new VersionSpecificCodeSample(
                "<?php\n\nfoo(bar : 'baz', qux /* corge */ : 3);\n",
                new VersionSpecification(8_00_00)
            )]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(CT::T_NAMED_ARGUMENT_NAME);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $index = 0;

        while (null !== $index = $tokens->getNextTokenOfKind($index, [[CT::T_NAMED_ARGUMENT_NAME]])) {
            $tokens->removeTrailingWhitespace($index);

            if (!$tokens[$index + 1]->isGivenKind(CT::T_NAMED_ARGUMENT_COLON)) {
                $index = $tokens->getNextTokenOfKind($index, [[CT::T_NAMED_ARGUMENT_COLON]]);
                $tokens->removeLeadingWhitespace($index);
            }
        }
    }
}
