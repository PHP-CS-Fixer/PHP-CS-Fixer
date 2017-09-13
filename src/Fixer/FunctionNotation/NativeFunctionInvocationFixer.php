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
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * @author Andreas Möller <am@localheinz.com>
 * @author SpacePossum
 */
final class NativeFunctionInvocationFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Add leading `\` before function invocation of internal function to speed up resolving.',
            [
                new CodeSample(
'<?php

function baz($options)
{
    if (!array_key_exists("foo", $options)) {
        throw new \InvalidArgumentException();
    }

    return json_encode($options);
}'
                ),
                new CodeSample(
'<?php

function baz($options)
{
    if (!array_key_exists("foo", $options)) {
        throw new \InvalidArgumentException();
    }

    return json_encode($options);
}',
                    ['exclude' => ['json_encode']]
                ),
                new CodeSample(
                    '<?php
namespace space1 {
    echo count([1]);
}
namespace {
    echo count([1]);
}
',
                    ['scope' => 'namespaced']
                ),
            ],
            null,
            'Risky when any of the functions are overridden.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_STRING);
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
        $functionNames = $this->getFunctionNames();

        if ('namespaced' === $this->configuration['scope']) {
            foreach (array_reverse($this->getUserDefinedNamespaces($tokens)) as $namespace) {
                $this->fixFunctionCalls($tokens, $functionNames, $namespace['open'], $namespace['close']);
            }
        } else {
            $this->fixFunctionCalls($tokens, $functionNames, 0, count($tokens) - 1);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('exclude', 'List of functions to ignore.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([function ($value) {
                    foreach ($value as $functionName) {
                        if (!\is_string($functionName) || '' === \trim($functionName) || \trim($functionName) !== $functionName) {
                            throw new InvalidOptionsException(\sprintf(
                                'Each element must be a non-empty, trimmed string, got "%s" instead.',
                                \is_object($functionName) ? \get_class($functionName) : \gettype($functionName)
                            ));
                        }
                    }

                    return true;
                }])
                ->setDefault([])
                ->getOption(),
            (new FixerOptionBuilder('scope', 'Fix functions only if called in given scope, global or within user defined namespaces only.'))
                ->setAllowedValues(['global', 'namespaced'])
                ->setDefault('global')
                ->getOption(),
        ]);
    }

    /**
     * @param Tokens   $tokens
     * @param string[] $functionNames
     * @param int      $start
     * @param int      $end
     */
    private function fixFunctionCalls(Tokens $tokens, array $functionNames, $start, $end)
    {
        $insertAtIndexes = [];
        for ($index = $start; $index < $end; ++$index) {
            // test if we are at a function call
            if (!$tokens[$index]->isGivenKind(T_STRING)) {
                continue;
            }

            if (!$tokens[$tokens->getNextMeaningfulToken($index)]->equals('(')) {
                continue;
            }

            $functionNamePrefix = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$functionNamePrefix]->isGivenKind([T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION])) {
                continue;
            }

            if ($tokens[$functionNamePrefix]->isGivenKind(T_NS_SEPARATOR)) {
                if ($tokens[$tokens->getPrevMeaningfulToken($functionNamePrefix)]->isGivenKind([T_STRING, T_NEW])) {
                    continue; // skip if the call is to a constructor or to a function in a namespace other than the default
                }
            }

            if (!\in_array(\strtolower($tokens[$index]->getContent()), $functionNames, true)) {
                continue;
            }

            if ($tokens[$index - 1]->isGivenKind(T_NS_SEPARATOR)) {
                continue; // do not bother if previous token is already namespace separator
            }

            $insertAtIndexes[] = $index;
        }

        foreach (\array_reverse($insertAtIndexes) as $index) {
            $tokens->insertAt($index, new Token([T_NS_SEPARATOR, '\\']));
        }
    }

    /**
     * @return string[]
     */
    private function getFunctionNames()
    {
        static $definedFunctions = null;

        if (null === $definedFunctions) {
            $definedFunctions = \get_defined_functions();
            $definedFunctions = $this->normalizeFunctionNames($definedFunctions['internal']);
        }

        return \array_diff(
            $definedFunctions,
            \array_unique($this->normalizeFunctionNames($this->configuration['exclude']))
        );
    }

    /**
     * @param Tokens $tokens
     *
     * @return array<<|array|string, int>>
     */
    private function getUserDefinedNamespaces(Tokens $tokens)
    {
        $namespaces = [];
        for ($index = 1, $count = count($tokens); $index < $count; ++$index) {
            if (!$tokens[$index]->isGivenKind(T_NAMESPACE)) {
                continue;
            }

            $index = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$index]->equals('{')) { // global namespace
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);

                continue;
            }

            while (!$tokens[++$index]->equalsAny(['{', ';', [T_CLOSE_TAG]])) {
                // no-op
            }

            if ($tokens[$index]->equals('{')) {
                // namespace ends at block end of `{`
                $namespaces[] = [
                    'open' => $index,
                    'close' => $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index),
                ];

                continue;
            }

            // namespace ends at next T_NAMESPACE or EOF
            $close = $tokens->getNextTokenOfKind($index, [[T_NAMESPACE]], false);
            if (null === $close) {
                $namespaces[] = ['open' => $index, 'close' => count($tokens) - 1];

                break;
            }

            $namespaces[] = ['open' => $index, 'close' => $close];
        }

        return $namespaces;
    }

    /**
     * @param string[] $functionNames
     *
     * @return string[]
     */
    private function normalizeFunctionNames(array $functionNames)
    {
        return \array_map('strtolower', $functionNames);
    }
}
