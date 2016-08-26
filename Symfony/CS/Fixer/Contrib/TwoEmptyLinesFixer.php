<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Denis Platov <d.platov@owox.com>
 */
final class TwoEmptyLinesFixer extends AbstractFixer
{
    private $bracesLevel = 0;

    private $curlyBracesLevel = 0;

    private $inClass = false;

    private $prevElementData = array();

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = 0; $index < $tokens->count() - 1; ++$index) {
            if ($this->processBraces($tokens[$index]) or 1 !== $this->curlyBracesLevel or !$tokens[$index]->isArray()) {
                continue;
            } elseif ($elementType = $this->getClassElementType($tokens[$index])) {
                $elementData = $this->getElementData($tokens, $index, $elementType);

                if (empty($this->prevElementData)) {
                    $this->prevElementData['type'] = $elementData['type'];
                    $this->prevElementData['visibility'] = $elementData['visibility'];
                }

                if ($elementData['type'] === 'method'
                    or $this->prevElementData['type'] !== $elementData['type']
                    or $this->prevElementData['visibility'] !== $elementData['visibility']
                ) {
                    $this->prevElementData['type'] = $elementData['type'];
                    $this->prevElementData['visibility'] = $elementData['visibility'];
                    $neededEmptyLinesCount = 2;
                } else {
                    $neededEmptyLinesCount = 1;
                }

                $index += $this->addEmptyLines($tokens, $elementData, $neededEmptyLinesCount);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Add two empty lines between class elements with different visibility or type and between methods.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        //should be run after VisibilityFixer
        return -1;
    }

    private function getElementData(Tokens $tokens, $index, $elementType)
    {
        $elementVisibility = ($elementType === 'method' or $elementType === 'property')
            ? $this->getClassElementVisibility($tokens, $index)
            : '';

        return empty($elementVisibility)
            ? array(
                'type' => $elementType,
                'visibility' => '',
                'index' => $index,
            )
            : array(
                'type' => $elementType,
                'visibility' => $elementVisibility['type'],
                'index' => $elementVisibility['index'],
            );
    }

    private function addEmptyLines(Tokens $tokens, array $currentTokenData, $linesCount)
    {
        $addedLines = 0;
        $tokenIndexToCheck = $tokens->getPrevMeaningfulToken($currentTokenData['index']) + 1;
        $content = str_repeat("\n", $linesCount + 1);

        if ($tokens[$tokenIndexToCheck]->isWhitespace(array("\n"))) {
            // There may be several new line characters in single token
            $old_content = str_replace("\n", '', $tokens[$tokenIndexToCheck]->getContent());
            $tokens[$tokenIndexToCheck]->setContent($content.$old_content);
        } else {
            $tokens->insertAt($tokenIndexToCheck, new Token($content));
            ++$addedLines;
        }

        return $addedLines;
    }

    private function getClassElementVisibility(Tokens $tokens, $index)
    {
        $methodDeclarationTokens = array(
            array(T_PUBLIC),
            array(T_PROTECTED),
            array(T_PRIVATE),
        );

        $prevTokenIndex = $tokens->getPrevTokenOfKind($index, array(array(T_ABSTRACT)));

        $prevTokenIndex = (empty($prevTokenIndex) or $tokens[$prevTokenIndex]->getLine() !== $tokens[$index]->getLine())
            ? $tokens->getPrevTokenOfKind($index, $methodDeclarationTokens)
             : $prevTokenIndex;

        return array(
            'type' => $tokens[$prevTokenIndex]->getContent(),
            'index' => $prevTokenIndex,
        );
    }

    private function processBraces(Token $token)
    {
        if ($token->isGivenKind(T_ENCAPSED_AND_WHITESPACE)) {
            return true;
        }

        if (!$this->inClass) {
            $this->inClass = $token->isClassy();

            return true;
        }

        if ($token->equals('(')) {
            ++$this->bracesLevel;

            return true;
        }

        if ($token->equals(')')) {
            --$this->bracesLevel;

            return true;
        }

        if ($token->equals('{')) {
            ++$this->curlyBracesLevel;

            return true;
        }

        if ($token->equals('}')) {
            --$this->curlyBracesLevel;

            if (0 === $this->curlyBracesLevel) {
                $this->inClass = false;
            }

            return true;
        }

        return false;
    }

    private function getClassElementType(Token $token)
    {
        $result = '';

        if (0 === $this->bracesLevel and $token->isGivenKind(T_VARIABLE)) {
            $result = 'property';
        } elseif ($token->isGivenKind(T_FUNCTION)) {
            $result = 'method';
        } elseif ($token->isGivenKind(T_CONST)) {
            $result = 'const';
        } elseif ($token->isGivenKind(T_USE)) {
            $result = 'trait';
        }

        return $result;
    }
}
