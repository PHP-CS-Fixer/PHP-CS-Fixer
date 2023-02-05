<?php

// src/Fixer/Comment/RemoveCommentsFixer.php

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

namespace PhpCsFixer\Fixer\Comment;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Your name <your@email.com>
 */
final class RemoveCommentsFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinition
    {
        // Return a definition of the fixer, it will be used in the documentation.
        return new FixerDefinition(
            'Removes all comments of the code that are preceded by `;` (semicolon).', // Trailing dot is important. We thrive to use English grammar properly.
            [
                new CodeSample(
                    "<?php echo 123; /* Comment */\n"
                ),
            ]
        );
    }

    // ...

    public function isCandidate(Tokens $tokens): bool
    {
        // Check whether the collection is a candidate for fixing.
        // Has to be ultra cheap to execute.
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // Add the fixing logic of the fixer here.
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_COMMENT)) {
                continue;
            }

            // need to figure out what to do here!
            $prevTokenIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevTokenIndex];

            if ($prevToken->equals(';')) {
                $tokens->clearAt($index);
            }
        }
    }

    private function applyFixNoAction(): void
    {
        // no action
    }
}
