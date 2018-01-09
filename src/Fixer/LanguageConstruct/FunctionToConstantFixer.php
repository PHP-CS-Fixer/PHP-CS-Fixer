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

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\FixerOptionValidatorGenerator;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class FunctionToConstantFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * @var string[]
     */
    private static $availableFunctions;

    /**
     * @var array<string, Token>
     */
    private $functionsFixMap;

    public function __construct()
    {
        if (null === self::$availableFunctions) {
            self::$availableFunctions = [
                'get_class' => new Token([T_CLASS_C, '__CLASS__']),
                'php_sapi_name' => new Token([T_STRING, 'PHP_SAPI']),
                'phpversion' => new Token([T_STRING, 'PHP_VERSION']),
                'pi' => new Token([T_STRING, 'M_PI']),
            ];
        }

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration)
    {
        parent::configure($configuration);

        $this->functionsFixMap = [];
        foreach ($this->configuration['functions'] as $key) {
            $this->functionsFixMap[$key] = self::$availableFunctions[$key];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Replace core functions calls returning constants with the constants.',
            [
                new CodeSample("<?php\necho phpversion();\necho pi();\necho php_sapi_name();\n"),
                new CodeSample("<?php\necho phpversion();\necho pi();\n", ['functions' => ['phpversion']]),
            ],
            null,
            'Risky when any of the configured functions to replace are overridden.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should run before NativeFunctionCasingFixer
        // must run before NoExtraBlankLinesFixer, NoSinglelineWhitespaceBeforeSemicolonsFixer, NoTrailingWhitespaceFixer and NoWhitespaceInBlankLineFixer
        // must run after NoSpacesAfterFunctionNameFixer and NoSpacesInsideParenthesisFixer

        return 1;
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
        for ($index = $tokens->count() - 4; $index > 0; --$index) {
            $candidate = $this->getReplaceCandidate($tokens, $index);
            if (null === $candidate) {
                continue;
            }

            $this->fixFunctionCallToConstant(
                $tokens,
                $index,
                $candidate[0], // brace open
                $candidate[1], // brace close
                $candidate[2]  // replacement
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $functionNames = array_keys(self::$availableFunctions);

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('functions', 'List of function names to fix.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([
                    (new FixerOptionValidatorGenerator())->allowedValueIsSubsetOf($functionNames),
                ])
                ->setDefault($functionNames)
                ->getOption(),
        ]);
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     * @param int    $braceOpenIndex
     * @param int    $braceCloseIndex
     * @param Token  $replacementConst
     */
    private function fixFunctionCallToConstant(Tokens $tokens, $index, $braceOpenIndex, $braceCloseIndex, Token $replacementConst)
    {
        $tokens->clearTokenAndMergeSurroundingWhitespace($braceCloseIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($braceOpenIndex);

        if ($replacementConst->isMagicConstant()) {
            $prevIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevIndex];
            if ($prevToken->isGivenKind(T_NS_SEPARATOR)) {
                $tokens->clearAt($prevIndex);
            }
        }
        $tokens->clearAt($index);
        $tokens->insertAt($index, $replacementConst);
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return null|array
     */
    private function getReplaceCandidate(Tokens $tokens, $index)
    {
        // test if we are at a function all
        if (!$tokens[$index]->isGivenKind(T_STRING)) {
            return null;
        }

        $braceOpenIndex = $tokens->getNextMeaningfulToken($index);
        if (!$tokens[$braceOpenIndex]->equals('(')) {
            return null;
        }

        // test if function call without parameters
        $braceCloseIndex = $tokens->getNextMeaningfulToken($braceOpenIndex);
        if (!$tokens[$braceCloseIndex]->equals(')')) {
            return null;
        }

        $functionNamePrefix = $tokens->getPrevMeaningfulToken($index);
        if ($tokens[$functionNamePrefix]->isGivenKind([T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION])) {
            return null;
        }

        if ($tokens[$functionNamePrefix]->isGivenKind(T_NS_SEPARATOR)) {
            // skip if the call is to a constructor or to a function in a namespace other than the default
            $prevIndex = $tokens->getPrevMeaningfulToken($functionNamePrefix);
            if ($tokens[$prevIndex]->isGivenKind([T_STRING, T_NEW])) {
                return null;
            }
        }

        // test if the function call is to a native PHP function
        $lowerContent = strtolower($tokens[$index]->getContent());
        if (!array_key_exists($lowerContent, $this->functionsFixMap)) {
            return null;
        }

        return [
            $braceOpenIndex,
            $braceCloseIndex,
            clone $this->functionsFixMap[$lowerContent],
        ];
    }
}
