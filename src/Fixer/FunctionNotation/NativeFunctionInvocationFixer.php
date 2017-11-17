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
}
'
                ),
                new CodeSample(
'<?php

function baz($options)
{
    if (!array_key_exists("foo", $options)) {
        throw new \InvalidArgumentException();
    }

    return json_encode($options);
}
',
                    [
                        'exclude' => [
                            'json_encode',
                        ],
                    ]
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

        $indexes = [];

        for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
            $token = $tokens[$index];

            $tokenContent = $token->getContent();

            // test if we are at a function call
            if (!$token->isGivenKind(T_STRING)) {
                continue;
            }

            $next = $tokens->getNextMeaningfulToken($index);
            if (!$tokens[$next]->equals('(')) {
                continue;
            }

            $functionNamePrefix = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$functionNamePrefix]->isGivenKind([T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION])) {
                continue;
            }

            if ($tokens[$functionNamePrefix]->isGivenKind(T_NS_SEPARATOR)) {
                // skip if the call is to a constructor or to a function in a namespace other than the default
                $prev = $tokens->getPrevMeaningfulToken($functionNamePrefix);
                if ($tokens[$prev]->isGivenKind([T_STRING, T_NEW])) {
                    continue;
                }
            }

            $lowerFunctionName = \strtolower($tokenContent);

            if (!\in_array($lowerFunctionName, $functionNames, true)) {
                continue;
            }

            // do not bother if previous token is already namespace separator
            if ($tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(T_NS_SEPARATOR)) {
                continue;
            }

            $indexes[] = $index;
        }

        $indexes = \array_reverse($indexes);
        foreach ($indexes as $index) {
            $tokens->insertAt($index, new Token([T_NS_SEPARATOR, '\\']));
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
                ->setAllowedValues([static function ($value) {
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
        ]);
    }

    /**
     * @return string[]
     */
    private function getFunctionNames()
    {
        $definedFunctions = \get_defined_functions();

        return \array_diff(
            $this->normalizeFunctionNames($definedFunctions['internal']),
            \array_unique($this->normalizeFunctionNames($this->configuration['exclude']))
        );
    }

    /**
     * @param string[] $functionNames
     *
     * @return string[]
     */
    private function normalizeFunctionNames(array $functionNames)
    {
        return \array_map(static function ($functionName) {
            return \strtolower($functionName);
        }, $functionNames);
    }
}
