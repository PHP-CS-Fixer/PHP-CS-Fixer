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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class PhpdocMultilineToSinglelineFixer extends AbstractFixer
{
    private $tags = ['throws', 'return', 'param', 'internal', 'deprecated', 'var', 'type'];

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'PHPDoc annotations with single meaningful line should be on a single line.',
            [new CodeSample('<?php
/**
 * @var string $foo
 */
$foo = \'foo\';
')]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());

            $lines = array_filter($doc->getLines(), function (Line $line) {
                return $line->containsUsefulContent();
            });

            if (1 !== \count($lines)) {
                continue;
            }

            $line = reset($lines);

            if (!$this->nextDeclarationAllowsFixer($tokens, $index)) {
                continue;
            }

            $annotations = $doc->getAnnotations();
            if (!$this->annotationsAllowFixer($annotations)) {
                continue;
            }

            $line = preg_replace('~^\s\\*~', '', $line);
            $line = trim($line);
            $singleLinePhpdoc = '/** '.$line.' */';
            $tokens[$index] = new Token([T_DOC_COMMENT, $singleLinePhpdoc]);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $tokenIndex
     *
     * @return bool
     */
    private function nextDeclarationAllowsFixer(Tokens $tokens, $tokenIndex)
    {
        // $nextTokenIndex = $tokens->getNextMeaningfulToken($tokenIndex);
        // if ($nextTokenIndex === null) {
        //     return false;
        // }
        //
        // $nextToken = $tokens[$nextTokenIndex];

        return true;
    }

    /**
     * @param Annotation[] $annotations
     *
     * @return bool
     */
    private function annotationsAllowFixer(array $annotations)
    {
        if (empty($annotations)) {
            return false;
        }

        foreach ($annotations as $annotation) {
            if (
                !$annotation->getTag()->valid()
                || !\in_array($annotation->getTag()->getName(), $this->tags, true)
            ) {
                return false;
            }
        }

        return true;
    }
}
