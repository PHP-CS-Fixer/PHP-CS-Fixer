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

namespace PhpCsFixer\Fixer\DoctrineAnnotation;

use Doctrine\Common\Annotations\DocLexer;
use PhpCsFixer\AbstractDoctrineAnnotationFixer;
use PhpCsFixer\Doctrine\Annotation\Tokens;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;

final class DoctrineAnnotationIndentationFixer extends AbstractDoctrineAnnotationFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Doctrine annotations must be indented with four spaces.',
            [
                new CodeSample("<?php\n/**\n *  @Foo(\n *   foo=\"foo\"\n *  )\n */\nclass Bar {}"),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function fixAnnotations(Tokens $tokens)
    {
        $annotationPositions = [];
        for ($index = 0, $max = count($tokens); $index < $max; ++$index) {
            if (!$tokens[$index]->isType(DocLexer::T_AT)) {
                continue;
            }

            $annotationEndIndex = $tokens->getAnnotationEnd($index);
            if (null === $annotationEndIndex) {
                return;
            }

            $annotationPositions[] = [$index, $annotationEndIndex];
            $index = $annotationEndIndex;
        }

        $previousLineBracesDelta = 0;
        $indentLevel = 0;
        foreach ($tokens as $index => $token) {
            if (!$token->isType(DocLexer::T_NONE) || false === strpos($token->getContent(), "\n")) {
                continue;
            }

            if (!$this->indentationCanBeFixed($tokens, $index, $annotationPositions)) {
                continue;
            }

            $currentLineDelta = $this->getLineBracesDelta($tokens, $index);

            if ($previousLineBracesDelta > 0) {
                ++$indentLevel;
            }
            if ($currentLineDelta < 0) {
                --$indentLevel;
            }

            $previousLineBracesDelta = $currentLineDelta;

            $token->setContent(preg_replace(
                '/(\n( +\*)?) *$/',
                '$1'.str_repeat(' ', 4 * $indentLevel + 1),
                $token->getContent()
            ));
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return int
     */
    private function getLineBracesDelta(Tokens $tokens, $index)
    {
        $lineBracesDelta = 0;
        while (isset($tokens[++$index])) {
            $token = $tokens[$index];
            if ($token->isType(DocLexer::T_NONE) && false !== strpos($token->getContent(), "\n")) {
                break;
            }

            if ($token->isType([DocLexer::T_OPEN_PARENTHESIS, DocLexer::T_OPEN_CURLY_BRACES])) {
                ++$lineBracesDelta;
                continue;
            }

            if ($token->isType([DocLexer::T_CLOSE_PARENTHESIS, DocLexer::T_CLOSE_CURLY_BRACES])) {
                --$lineBracesDelta;
                continue;
            }
        }

        return $lineBracesDelta;
    }

    /**
     * @param Tokens            $tokens
     * @param int               $newLineTokenIndex
     * @param array<array<int>> $annotationPositions Pairs of begin and end indexes of main annotations
     *
     * @return bool
     */
    private function indentationCanBeFixed(Tokens $tokens, $newLineTokenIndex, array $annotationPositions)
    {
        foreach ($annotationPositions as $position) {
            if ($newLineTokenIndex >= $position[0] && $newLineTokenIndex <= $position[1]) {
                return true;
            }
        }

        for ($index = $newLineTokenIndex + 1, $max = count($tokens); $index < $max; ++$index) {
            $token = $tokens[$index];

            if (false !== strpos($token->getContent(), "\n")) {
                return false;
            }

            return $tokens[$index]->isType(DocLexer::T_AT);
        }

        return false;
    }
}
