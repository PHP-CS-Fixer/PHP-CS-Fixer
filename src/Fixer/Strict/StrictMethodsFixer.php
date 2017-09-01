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

namespace PhpCsFixer\Fixer\Strict;

use PhpCsFixer\AbstractFunctionReferenceFixer;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 */
final class StrictMethodsFixer extends AbstractFunctionReferenceFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Force strict types in class methods. Requires PHP >= 7.0.',
            [
                new VersionSpecificCodeSample(
                    '<?php ',
                    new VersionSpecification(70000)
                ),
            ],
            null,
            ''
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must ran before ?????.
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return PHP_VERSION_ID >= 70000 && $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
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
            $annotations = $doc->getAnnotationsOfType(['param', 'return']);

            if (empty($annotations)) {
                continue;
            }

            $this->useAnnotatedTypesAsStrictTypes($tokens, $index, $annotations);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $docBlockIndex
     * @param Annotation[] $annotations
     */
    private function useAnnotatedTypesAsStrictTypes(Tokens $tokens, $docBlockIndex, array $annotations)
    {
        $functionTokenIndex = $this->detectNextFunctionToken($tokens, $docBlockIndex);
        if ($functionTokenIndex === null) {
            return;
        }

        $arguments = $this->detectFunctionArguments($tokens, $functionTokenIndex);

        $doc = new DocBlock($tokens[$docBlockIndex]->getContent());

        foreach ($arguments as $variable => $argument) {
            foreach ($doc->getAnnotationsOfType('param') as $annotation) {
                if (!preg_match('/' . preg_quote($variable, '/') . '\b/', $annotation->getContent())) {
                    continue;
                }

                $types = $annotation->getTypes();
                if (count($types) !== 1) {
                    continue;
                }

                if ($argument['type'] !== '' && $types[0] !== $argument['type']) {
                    continue;
                }

                // TODO: Add type to argument
                $annotation->remove();
            }
        }

        if ($this->detectFunctionReturnType($tokens, $functionTokenIndex) === null) {
            $annotations = $doc->getAnnotationsOfType('return');
            if (count($annotations) === 1) {
                $types = $annotations[0]->getTypes();
                if (count($types) === 1) {
                    // TODO: Add type to method
                    $annotations[0]->remove();
                }
            }
        }

        $tokens[$docBlockIndex] = new Token([T_DOC_COMMENT, $doc->getContent()]);
    }

    /**
     * @param Tokens $tokens
     * @param int    $docBlockIndex
     *
     * @return int|null
     */
    private function detectNextFunctionToken(Tokens $tokens, $docBlockIndex)
    {
        $allowedIntermediateToken = [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL, T_ABSTRACT, T_STATIC];
        $currentIndex = $docBlockIndex;
        do {
            $currentIndex = $tokens->getNextMeaningfulToken($currentIndex);
            $token = $tokens[$currentIndex];

            if ($token->isGivenKind(T_FUNCTION)) {
                return $currentIndex;
            }

            if (!$token->isGivenKind($allowedIntermediateToken)) {
                return null;
            }


        } while($currentIndex < count($tokens));

        return null;
    }

    /**
     * @param Tokens $tokens
     * @param int $methodIndex
     *
     * @return array
     */
    private function detectFunctionArguments(Tokens $tokens, $methodIndex)
    {
        $argumentsStart = $tokens->getNextTokenOfKind($methodIndex, ['(']);
        $argumentsEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $argumentsStart);
        $arguments = [];
        foreach ($this->getArguments($tokens, $argumentsStart, $argumentsEnd) as $start => $end) {
            $argumentInfo = $this->prepareArgumentInformation($tokens, $start, $end);
            $arguments[$argumentInfo['name']] = $argumentInfo;
        }

        if (!count($arguments)) {
            return [];
        }

        return $arguments;
    }

    /**
     * @param Tokens $tokens
     * @param int   $methodIndex
     *
     * @return int|null
     */
    private function detectFunctionReturnType(Tokens $tokens, $methodIndex)
    {
        $argumentsStart = $tokens->getNextTokenOfKind($methodIndex, ['(']);
        $argumentsEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $argumentsStart);

        $colonIndex = $tokens->getNextMeaningfulToken($argumentsEnd);
        if (!$tokens[$colonIndex]->isGivenKind([CT::T_TYPE_COLON])) {
            return null;
        }

        return $tokens->getNextMeaningfulToken($colonIndex);
    }

    /**
     * TODO: This method is copied from \PhpCsFixer\Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixer We might abstract here?
     *
     *
     * @param Tokens $tokens
     * @param int    $start
     * @param int    $end
     *
     * @return array
     */
    private function prepareArgumentInformation(Tokens $tokens, $start, $end)
    {
        $info = [
            'default' => '',
            'name' => '',
            'type' => '',
        ];

        $sawName = false;

        for ($index = $start; $index <= $end; ++$index) {
            $token = $tokens[$index];

            if ($token->isComment() || $token->isWhitespace()) {
                continue;
            }

            if ($token->isGivenKind(T_VARIABLE)) {
                $sawName = true;
                $info['name'] = $token->getContent();

                continue;
            }

            if ($token->equals('=')) {
                continue;
            }

            if ($sawName) {
                $info['default'] .= $token->getContent();
            } else {
                $info['type'] .= $token->getContent();
            }
        }

        return $info;
    }
}
