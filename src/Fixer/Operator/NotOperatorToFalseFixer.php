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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Timothée Garnaud <tgarnaud@gmail.com>
 */
final class NotOperatorToFalseFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound('!');
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Not operator should be replaced by false ==',
            [new CodeSample('<?php

if (!$bar) {
    echo "Help!";
}
')],
            null,
            'Risky with methods returning bool|null (i.e. strpos, preg_match...). Risky when testing non null values'
        );
    }

    public function isRisky(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * Must run before YodaStyleFixer
     * Must run before StrictComparisonFixer
     */
    public function getPriority(): int
    {
        return 39;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if ($token->equals('!')) {
                /** @var Token $nextToken */
                $nextToken = $tokens[$tokens->getNextMeaningfulToken($index)];

                if (false === str_starts_with($nextToken->getContent(), '=')) {
                    $tokens->clearAt($index);
                    $tokens->insertAt($index, new Token([T_STRING, 'false === ']));
                }
            }
        }
    }
}
