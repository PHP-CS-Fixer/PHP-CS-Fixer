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

namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for variables case.
 *
 * @author Jennifer Konikowski <jennifer@testdouble.com>
 */
final class VariableCaseFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * @internal
     */
    const CAMEL_CASE = 'camel_case';

    /**
     * @internal
     */
    const SNAKE_CASE = 'snake_case';

    /**
     * Hold the function that will be used to convert the constants.
     *
     * @var callable
     */
    private $fixFunction;

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);

        if (self::CAMEL_CASE === $this->configuration['case']) {
            $this->fixFunction = static function ($token) {
                // TODO: use camel case function
                return VariableCaseFixer::camelCase($token);
            };
        }

        if (self::SNAKE_CASE === $this->configuration['case']) {
            $this->fixFunction = static function ($token) {
                // TODO: use snake case function (yet to be defined)
                return strtolower($token);
            };
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'The PHP constants `true`, `false`, and `null` MUST be written using the correct casing.',
            [new CodeSample("<?php\n\$a = FALSE;\n\$b = True;\n\$c = nuLL;\n")]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_VARIABLE);
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('case', 'Apply `camel_case` or `snake_case` to variables.'))
            ->setAllowedValues([self::CAMEL_CASE, self::SNAKE_CASE])
            ->setDefault(self::CAMEL_CASE)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (T_VARIABLE == $token->getId()) {
                $tokens[$index] = new Token([$token->getId(), $this->updateVariableCasing($token->getContent())]);
            }
        }
    }

    private function updateVariableCasing($variableName)
    {
        if (self::CAMEL_CASE === $this->configuration['case']) {
            return $this->camelCase($variableName);
        } else {
            return $this->snakeCase($variableName);
        }
    }

    private function camelCase($string)
    {
        $string = preg_replace('/_/i', ' ', $string);
        $string = trim($string);
        // uppercase the first character of each word
        $string = ucwords($string);
        $string = str_replace(" ", "", $string);
        $string = lcfirst($string);

        return $string;
    }

    private function snakeCase($string, $separator = "_")
    {
        // insert hyphen between any letter and the beginning of a numeric chain
        $string = preg_replace('/([a-z]+)([0-9]+)/i', '$1'.$separator.'$2', $string);
        // insert hyphen between any lower-to-upper-case letter chain
        $string = preg_replace('/([a-z]+)([A-Z]+)/', '$1'.$separator.'$2', $string);
        // insert hyphen between the end of a numeric chain and the beginning of an alpha chain
        $string = preg_replace('/([0-9]+)([a-z]+)/i', '$1'.$separator.'$2', $string);

        // Lowercase
        $string = strtolower($string);

        return $string;
    }
}
