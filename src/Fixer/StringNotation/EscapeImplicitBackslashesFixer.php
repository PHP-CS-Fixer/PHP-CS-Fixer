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

namespace PhpCsFixer\Fixer\StringNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class EscapeImplicitBackslashesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Escape implicit backslashes in strings and heredocs to increase code readability.',
            [new CodeSample(
                <<<'EOF'
<?php

$singleQuoted = 'String with \" and My\Prefix\\';

$doubleQuoted = "Interpret my \n but not my \a";

$hereDoc = <<<HEREDOC
Interpret my \100 but not my \999
HEREDOC;

EOF
)]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([T_ENCAPSED_AND_WHITESPACE, T_CONSTANT_ENCAPSED_STRING]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        static $singleQuotedRegex = '/(?<!\\\\)\\\\(?![\\\'\\\\])/';
        static $doubleQuotedRegex = '/(?<!\\\\)\\\\(?![efnrtv$"\\\\0-7]|x[0-9A-Fa-f]|u{)/';

        foreach ($tokens as $index => $token) {
            $content = $token->getContent();
            if (!$token->isGivenKind([T_ENCAPSED_AND_WHITESPACE, T_CONSTANT_ENCAPSED_STRING]) || false === strpos($content, '\\')) {
                continue;
            }

            // Nowdoc syntax
            if ($token->isGivenKind(T_ENCAPSED_AND_WHITESPACE) && '\'' === substr(rtrim($tokens[$index - 1]->getContent()), -1)) {
                continue;
            }

            $newContent = preg_replace(
                $token->isGivenKind(T_CONSTANT_ENCAPSED_STRING) && '\'' === $content[0]
                    ? $singleQuotedRegex
                    : $doubleQuotedRegex,
                '\\\\\\\\',
                $content
            );
            if ($newContent !== $content) {
                $tokens[$index] = new Token([$token->getId(), $newContent]);
            }
        }
    }
}
