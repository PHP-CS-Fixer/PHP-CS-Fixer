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
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Andreas Möller <am@localheinz.com>
 */
final class NativeFunctionInvocationFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var array
     */
    private $exclude = array();

    /**
     * @var array
     */
    private static $defaultConfiguration = array(
        'exclude' => array(),
    );

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            return;
        }

        $key = 'exclude';

        if (!\array_key_exists($key, $configuration) || !\is_array($configuration[$key])) {
            throw new InvalidFixerConfigurationException(
                $this->getName(),
                \sprintf(
                    'Configuration must define "%s" as an array.',
                    $key
                )
            );
        }

        foreach ($configuration[$key] as $functionName) {
            if (!\is_string($functionName) || \trim($functionName) === '' || \trim($functionName) !== $functionName) {
                throw new InvalidFixerConfigurationException(
                    $this->getName(),
                    \sprintf(
                        'Each element must be a non-empty, trimmed string, got "%s" instead.',
                        \is_object($functionName) ? \get_class($functionName) : \gettype($functionName)
                    )
                );
            }
        }

        $this->exclude = $configuration[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        static $internalFunctionNames = null;

        if (null === $internalFunctionNames) {
            $internalFunctionNames = $this->getInternalFunctionNames();
        }

        $indexes = array();

        $openBracesCount = 0;
        $isNamespaceWithBraces = false;
        $isWithinNamespace = false;

        for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
            $token = $tokens[$index];

            $tokenContent = $token->getContent();

            if ('{' === $tokenContent) {
                ++$openBracesCount;

                continue;
            }

            if ('}' === $tokenContent) {
                --$openBracesCount;

                if (true === $isWithinNamespace
                    && true === $isNamespaceWithBraces
                    && 0 === $openBracesCount
                ) {
                    $isWithinNamespace = false;
                }

                continue;
            }

            if ($token->isGivenKind(T_NAMESPACE)) {
                $namespaceDeclarationEndIndex = $tokens->getNextTokenOfKind($index, array(
                    ';',
                    '{',
                ));

                if ($tokens[$namespaceDeclarationEndIndex]->getContent() === '{') {
                    $isNamespaceWithBraces = true;
                }

                $isWithinNamespace = true;
            }

            if (!$isWithinNamespace) {
                continue;
            }

            // test if we are at a function call
            if (!$token->isGivenKind(T_STRING)) {
                continue;
            }

            $next = $tokens->getNextMeaningfulToken($index);
            if (!$tokens[$next]->equals('(')) {
                continue;
            }

            $functionNamePrefix = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$functionNamePrefix]->isGivenKind(array(T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION))) {
                continue;
            }

            if ($tokens[$functionNamePrefix]->isGivenKind(T_NS_SEPARATOR)) {
                // skip if the call is to a constructor or to a function in a namespace other than the default
                $prev = $tokens->getPrevMeaningfulToken($functionNamePrefix);
                if ($tokens[$prev]->isGivenKind(array(T_STRING, T_NEW))) {
                    continue;
                }
            }

            $lowerFunctionName = \strtolower($tokenContent);

            if (!\in_array($lowerFunctionName, $internalFunctionNames, true) || \in_array($lowerFunctionName, $this->exclude, true)) {
                continue;
            }

            // do not bother if previous token is already namespace separator
            if ($tokens[$index - 1]->isGivenKind(T_NS_SEPARATOR)) {
                continue;
            }

            $indexes[] = $index;
        }

        $indexes = \array_reverse($indexes);

        $namespaceSeparator = new Token(array(
            T_NS_SEPARATOR,
            '\\',
        ));

        foreach ($indexes as $index) {
            $tokens->insertAt($index, $namespaceSeparator);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Add leading `\` before function invocation of internal function within namespaces to speed up resolving.',
            array(
                new CodeSample(
'<?php

namespace Foo;

class Bar
{
    public function baz($options) 
    {
        if (!array_key_exists("foo", $options)) {
            throw new \InvalidArgumentException();
        }
        
        return json_encode($options);
    }
    
}'
                ),
                new CodeSample(
'<?php

namespace Foo;

class Bar
{
    public function baz($options) 
    {
        if (!array_key_exists("foo", $options)) {
            throw new \InvalidArgumentException();
        }
        
        return json_encode($options);
    }
    
}',
                    array(
                        'exclude' => array(
                            'json_encode',
                        ),
                    )
                ),
            ),
            null,
            'Configure names of functions to exclude',
            self::$defaultConfiguration,
            'Risky if a function with the same name as a native function exists in the current namespace, or a polyfill is used.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound(array(
            T_NAMESPACE,
            T_STRING,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * @return string[]
     */
    private function getInternalFunctionNames()
    {
        $definedFunctions = \get_defined_functions();

        return \array_map(function ($name) {
            return \strtolower($name);
        }, $definedFunctions['internal']);
    }
}
