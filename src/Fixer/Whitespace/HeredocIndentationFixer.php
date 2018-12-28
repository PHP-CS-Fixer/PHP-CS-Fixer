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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Gregor Harlan
 */
final class HeredocIndentationFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Heredoc/nowdoc content must be properly indented. Requires PHP >= 7.3.',
            [
                new VersionSpecificCodeSample(
                    <<<'SAMPLE'
<?php
    $a = <<<EOD
abc
    def
EOD;

SAMPLE
                    ,
                    new VersionSpecification(70300)
                ),
                new VersionSpecificCodeSample(
                    <<<'SAMPLE'
<?php
    $a = <<<'EOD'
abc
    def
EOD;

SAMPLE
                    ,
                    new VersionSpecification(70300)
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return \PHP_VERSION_ID >= 70300 && $tokens->isTokenKindFound(T_START_HEREDOC);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = \count($tokens) - 1; 0 <= $index; --$index) {
            if (!$tokens[$index]->isGivenKind(T_END_HEREDOC)) {
                continue;
            }

            $end = $index;
            $index = $tokens->getPrevTokenOfKind($index, [[T_START_HEREDOC]]);

            $this->fixIndentation($tokens, $index, $end);
        }
    }

    /**
     * @param int $start
     * @param int $end
     */
    private function fixIndentation(Tokens $tokens, $start, $end)
    {
        $indent = $this->getIndentAt($tokens, $start).$this->whitespacesConfig->getIndent();

        Preg::match('/^[ \t]*/', $tokens[$end]->getContent(), $matches);
        $currentIndent = $matches[0];

        $content = $indent.substr($tokens[$end]->getContent(), \strlen($currentIndent));
        $tokens[$end] = new Token([T_END_HEREDOC, $content]);

        if ($end === $start + 1) {
            return;
        }

        for ($index = $end - 1, $last = true; $index > $start; --$index, $last = false) {
            if (!$tokens[$index]->isGivenKind([T_ENCAPSED_AND_WHITESPACE, T_WHITESPACE])) {
                continue;
            }

            $regexEnd = $last && !$currentIndent ? '(?!$)' : '';
            $content = Preg::replace('/(?<=\v)'.$currentIndent.$regexEnd.'/', $indent, $tokens[$index]->getContent());
            $tokens[$index] = new Token([$tokens[$index]->getId(), $content]);
        }

        ++$index;

        if ($tokens[$index]->isGivenKind(T_ENCAPSED_AND_WHITESPACE)) {
            $content = $indent.substr($tokens[$index]->getContent(), \strlen($currentIndent));
            $tokens[$index] = new Token([T_ENCAPSED_AND_WHITESPACE, $content]);
        } else {
            $tokens->insertAt($index, new Token([T_ENCAPSED_AND_WHITESPACE, $indent]));
        }
    }

    /**
     * @param int $index
     *
     * @return string
     */
    private function getIndentAt(Tokens $tokens, $index)
    {
        for (; $index >= 0; --$index) {
            if (!$tokens[$index]->isGivenKind([T_WHITESPACE, T_INLINE_HTML, T_OPEN_TAG])) {
                continue;
            }

            $content = $tokens[$index]->getContent();

            if ($tokens[$index]->isWhitespace() && $tokens[$index - 1]->isGivenKind(T_OPEN_TAG)) {
                $content = $tokens[$index - 1]->getContent().$content;
            }

            if (1 === Preg::match('/\R([ \t]*)$/', $content, $matches)) {
                return $matches[1];
            }
        }

        return '';
    }
}
