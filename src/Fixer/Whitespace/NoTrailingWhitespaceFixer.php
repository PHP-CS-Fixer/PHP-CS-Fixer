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

namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.3.
 *
 * Don't add trailing spaces at the end of non-blank lines.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoTrailingWhitespaceFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Remove trailing whitespace at the end of non-blank lines.',
            [new CodeSample("<?php\n\$a = 1;     \n")]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after NoEmptyPhpdocFixer, NoUnneededControlParenthesesFixer, ClassDefinitionFixer, CombineConsecutiveUnsetsFixer, NoEmptyStatementFixer and NoUselessElseFixer.
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isWhitespace()) {
                continue;
            }

            $lines = preg_split("/([\r\n]+)/", $token->getContent(), -1, PREG_SPLIT_DELIM_CAPTURE);
            $linesSize = count($lines);

            // fix only multiline whitespaces or singleline whitespaces at the end of file
            if ($linesSize > 1 || !isset($tokens[$index + 1])) {
                $lines[0] = rtrim($lines[0], " \t");

                for ($i = 1; $i < $linesSize; ++$i) {
                    $trimmedLine = rtrim($lines[$i], " \t");
                    if ('' !== $trimmedLine) {
                        $lines[$i] = $trimmedLine;
                    }
                }

                $token->setContent(implode($lines));
            }
        }
    }
}
