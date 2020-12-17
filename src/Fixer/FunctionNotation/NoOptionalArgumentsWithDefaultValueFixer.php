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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class NoOptionalArgumentsWithDefaultValueFixer extends AbstractFixer
{
    public function getDefinition()
    {
        return new FixerDefinition(
            'Remove arguments whose value is their default’s. Requires PHP >= 8.',
            [
                new VersionSpecificCodeSample(
                    '<?php chunk_split("Second argument has its default value so it can be removed.", 76);'."\n",
                    new VersionSpecification(80000)
                ),
                new VersionSpecificCodeSample(
                    '<?php chunk_split("Third argument must be kept so it will be named.", 76, "\n");'."\n",
                    new VersionSpecification(80000)
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens)
    {
        return PHP_MAJOR_VERSION >= 8 && $tokens->isTokenKindFound('(');
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $argumentsAnalyzer = new ArgumentsAnalyzer();
        $operations = [];

        foreach ($this->getGlobalCalls($tokens) as $parenthesisIndex => $call) {
            if (!$parameters = $this->getParameters($call)) {
                continue;
            }

            $argumentsIndexes = $argumentsAnalyzer->getArguments(
                $tokens,
                $parenthesisIndex,
                $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $parenthesisIndex)
            );

            $parameterIndex = 0;
            $hasAnArgumentBeRemoved = false;
            foreach ($argumentsIndexes as $start => $end) {
                $tokenIndex = $tokens->getNextMeaningfulToken($start - 1);
                $token = $tokens[$tokenIndex];

                if ($token->isGivenKind(CT::T_NAMED_ARGUMENT_NAME)) {
                    $parameter = $parameters[$token->getContent()];

                    // skip CT::T_NAMED_ARGUMENT_COLON
                    $tokenIndex = $tokens->getNextMeaningfulToken($tokens->getNextMeaningfulToken($tokenIndex));
                } else {
                    $parameter = $parameters[$parameterIndex++];
                }

                if ($this->isArgumentValueItsDefault($parameter, $tokens, $tokenIndex, $tokens->getPrevMeaningfulToken($end + 1))) {
                    $hasAnArgumentBeRemoved = true;

                    $clearStart = $start;
                    $clearStop = $end;
                    $beforeArgumentIndex = $tokens->getPrevMeaningfulToken($start);
                    $afterArgumentIndex = $tokens->getNextMeaningfulToken($end);
                    if ($tokens[$beforeArgumentIndex]->equals(',')) {
                        $clearStart = $tokens->getPrevMeaningfulToken($beforeArgumentIndex) + 1;
                    } elseif ($tokens[$afterArgumentIndex]->equals(',')) {
                        $clearStop = $tokens->getNextMeaningfulToken($afterArgumentIndex) - 1;
                    }

                    $operations[] = ['clearRange', [$clearStart, $clearStop]];
                } elseif ($hasAnArgumentBeRemoved && $parameterIndex > $parameter->getPosition()) {
                    $operations[] = ['insertAt', [
                        $tokenIndex,
                        [
                            new Token([CT::T_NAMED_ARGUMENT_NAME, $parameter->getName()]),
                            new Token([CT::T_NAMED_ARGUMENT_COLON, ':']),
                            new Token([T_WHITESPACE, ' ']),
                        ],
                    ]];
                }
            }

            foreach (array_reverse($operations) as $operation) {
                \call_user_func_array([$tokens, $operation[0]], $operation[1]);
            }
        }
    }

    /**
     * @return array<int, string> the open parenthesis token index as key and the function name as value
     */
    private function getGlobalCalls(Tokens $tokens)
    {
        $calls = [];
        $isNamespaceGlobal = true;
        for ($i = 0; $token = $tokens[$i]; ++$i) {
            $nextTokenIndex = $tokens->getNextMeaningfulToken($i);

            if (null === $nextTokenIndex) {
                break;
            }

            $nextToken = $tokens[$nextTokenIndex];

            if ($token->isGivenKind(T_NAMESPACE) && !$nextToken->equals('{')) {
                $isNamespaceGlobal = false;

                continue;
            }

            if (!$token->isGivenKind(T_STRING) || !$nextToken->equals('(')) {
                continue;
            }

            $function = $token->getContent();
            $isCallGlobal = $isNamespaceGlobal;
            $isMethod = false;

            $previousTokenIndex = $tokens->getPrevMeaningfulToken($i);
            $previousToken = $tokens[$previousTokenIndex];

            if ($previousToken->isGivenKind(T_FUNCTION)) {
                continue;
            }

            if ($previousToken->isGivenKind(T_DOUBLE_COLON)) {
                $isMethod = true;

                $classTokenIndex = $tokens->getPrevMeaningfulToken($previousTokenIndex);
                $classToken = $tokens[$classTokenIndex];
                $class = $classToken->getContent();

                if (!$classToken->isGivenKind(T_STRING) || \in_array($class, ['self', 'parent'], true)) {
                    continue;
                }

                $function = "{$class}::{$function}";

                $previousTokenIndex = $tokens->getPrevMeaningfulToken($classTokenIndex);
                $previousToken = $tokens[$previousTokenIndex];
            }

            if ($previousToken->isGivenKind(T_NS_SEPARATOR)) {
                $previousTokenIndex = $tokens->getPrevMeaningfulToken($previousTokenIndex);
                $previousToken = $tokens[$previousTokenIndex];

                if ($previousToken->isGivenKind(T_STRING)) {
                    continue;
                }

                if ($previousToken->isGivenKind(CT::T_NAMESPACE_OPERATOR)) {
                    if (!$isNamespaceGlobal) {
                        continue;
                    }

                    $previousTokenIndex = $tokens->getPrevMeaningfulToken($previousTokenIndex);
                    $previousToken = $tokens[$previousTokenIndex];
                }

                $isCallGlobal = true;
            }

            if ($previousToken->isGivenKind(T_NEW)) {
                $isMethod = true;
                $function = "{$function}::__construct";
            }

            if ($isMethod && !$isCallGlobal) {
                continue;
            }

            $calls[$nextTokenIndex] = $function;
        }

        return $calls;
    }

    /**
     * @param string $function
     *
     * @return \ReflectionParameter[] parameters indexed by position and name
     */
    private function getParameters($function)
    {
        static $cache = [];

        $function = strtolower($function);
        if (!isset($cache[$function])) {
            try {
                $reflection = strpos('::', $function)
                    ? new \ReflectionMethod($function)
                    : new \ReflectionFunction($function);

                $cache[$function] = [];
                foreach ($reflection->getParameters() as $position => $parameter) {
                    $cache[$function] += [
                        $position => $parameter,
                        $parameter->getName() => $parameter,
                    ];
                }
            } catch (\ReflectionException $ex) {
                $cache[$function] = false;
            }
        }

        return $cache[$function];
    }

    private function isArgumentValueItsDefault(\ReflectionParameter $parameter, Tokens $tokens, $start, $end)
    {
        if ($start !== $end || !$parameter->isOptional()) {
            return false;
        }

        $token = $tokens[$start];
        $value = $token->getContent();
        $defaultValue = $parameter->getDefaultValue();

        if ($token->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
            return trim($value, '"\'') === $defaultValue;
        }

        if ($token->isGivenKind(T_LNUMBER)) {
            return var_export($defaultValue, true) === $value;
        }

        if ($token->isGivenKind(T_STRING) && \defined($value)) {
            return \constant($value) === $defaultValue;
        }

        return false;
    }
}
