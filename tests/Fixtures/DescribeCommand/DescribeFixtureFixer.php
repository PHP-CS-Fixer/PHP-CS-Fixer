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

namespace PhpCsFixer\Tests\Fixtures\DescribeCommand;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class DescribeFixtureFixer extends AbstractFixer
{
    public function getName(): string
    {
        return 'Vendor/describe_fixture';
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_CONSTANT_ENCAPSED_STRING);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Fixture for describe command.',
            [
                new CodeSample(
                    "<?php\necho 'describe fixture';\n"
                ),
            ],
        );
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if (!$tokens[$index]->isKind(T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }

            if ('\'describe fixture\'' !== strtolower($tokens[$index]->getContent())) {
                continue;
            }

            $tokens[$index] = new Token([T_CONSTANT_ENCAPSED_STRING, '\'fixture for describe\'']);
        }
    }
}
