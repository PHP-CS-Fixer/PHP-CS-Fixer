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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Jonathan Gruber <gruberjonathan@gmail.com>
 */
final class PhpdocParamOrderFixer extends AbstractFixer
{
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
    public function getDefinition()
    {
        return new FixerDefinition(
            'Orders all `@param` annotations in DocBlocks according to method signature.',
            [
                new CodeSample(
                    '<?php
class C {
    /**
     * @param bool   $a
     * @param string $c
     * @param string $b
     */
    public function m($a, $b, $c) {}
}
'
                ),
            ]
        );
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

            // Check for function signature
            $functionSequence = $tokens->findSequence([[T_FUNCTION], [T_STRING], '('], $index);

            if (null === $functionSequence) {
                return;
            }

            $doc = new DocBlock($tokens[$index]->getContent());
            $paramAnnotations = $doc->getAnnotationsOfType('param');

            if (count($paramAnnotations)) {
                $funcParamNames = $this->getFuncParamNames($tokens, $functionSequence);
                $doc = $this->sortDocBlockParamAnnotations($doc, $funcParamNames, $paramAnnotations);
            }

            $tokens[$index] = new Token([T_DOC_COMMENT, $doc->getContent()]);
        }
    }

    /**
     * Fetches a list of function parameter names.
     *
     * @param Tokens            $tokens
     * @param array<int, Token> $functionSequence
     *
     * @return string[]
     */
    private function getFuncParamNames(Tokens $tokens, array $functionSequence)
    {
        $paramBlockStart = array_keys($functionSequence)[2];
        $paramBlockEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $paramBlockStart);

        $paramNames = [];
        for (
            $i = $tokens->getNextTokenOfKind($paramBlockStart, [[T_VARIABLE]]);
            null !== $i && $i < $paramBlockEnd;
            $i = $tokens->getNextTokenOfKind($i, [[T_VARIABLE]])
        ) {
            $paramNames[] = $tokens[$i]->getContent();
        }

        return $paramNames;
    }

    /**
     * Sorts the param annotations according to the function parameters.
     *
     * @param DocBlock $doc
     * @param Token[]  $funcParamNames
     * @param array    $paramAnnotations
     *
     * @return DocBlock
     */
    private function sortDocBlockParamAnnotations(DocBlock $doc, array $funcParamNames, array $paramAnnotations)
    {
        $validParams = [];
        foreach ($funcParamNames as $paramName) {
            $indices = $this->findParamAnnotationByIdentifier($paramAnnotations, $paramName);

            // Found an exactly matching @param annotation
            if (is_array($indices)) {
                foreach ($indices as $index) {
                    $validParams[$index] = $paramAnnotations[$index]->getContent();
                }
            }
        }

        // Detect superfluous annotations
        /** @var Annotation[] $invalidParams */
        $invalidParams = array_diff_key($paramAnnotations, $validParams);
        $invalidParams = array_values($invalidParams);

        // Append invalid parameters to the (ordered) valid ones
        $orderedAnnotations = array_values($validParams);
        foreach ($invalidParams as $i => $params) {
            $orderedAnnotations[$i + count($validParams)] = $params->getContent();
        }

        // Rewrite the param annotations in order
        foreach ($paramAnnotations as $i => $docAnnotation) {
            $docAnnotation->remove();
            $doc
                ->getLine($docAnnotation->getStart())
                ->setContent($orderedAnnotations[$i]);
        }

        return $doc;
    }

    /**
     * Returns the indices of the lines of a specific parameter annotation.
     *
     * @param Annotation[] $docParams
     * @param string       $identifier
     *
     * @return null|array
     */
    private function findParamAnnotationByIdentifier(array $docParams, $identifier)
    {
        $blockLevel = 0;
        $blockMatch = false;
        $blockIndices = [];

        $typeDeclaration = sprintf('[\w\s<>,%s]*', preg_quote('\[]|?'));
        $paramRegex = sprintf(
            '/\*\s*@param\s*%s\s*&?\$\b%s\b/',
            $typeDeclaration,
            substr($identifier, 1) // Remove starting `$` from variable name
        );

        foreach ($docParams as $i => $param) {
            $blockStart = Preg::match('/\s*{\s*/', $param->getContent());
            $blockEndMatches = Preg::matchAll('/}[\*\s\n]*/', $param->getContent());

            if (0 === $blockLevel && Preg::match($paramRegex, $param->getContent())) {
                if ($blockStart) {
                    $blockMatch = true; // Start of a nested block
                } else {
                    return [$i]; // Top level match
                }
            }

            if ($blockStart) {
                ++$blockLevel;
            }

            if ($blockEndMatches) {
                $blockLevel -= $blockEndMatches;
            }

            if ($blockMatch) {
                $blockIndices[] = $i;
                if (0 === $blockLevel) {
                    return $blockIndices;
                }
            }
        }

        return null;
    }
}
