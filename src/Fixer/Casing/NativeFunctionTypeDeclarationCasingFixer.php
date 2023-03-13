<?php

declare(strict_types=1);

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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class NativeFunctionTypeDeclarationCasingFixer extends AbstractFixer
{
    /**
     * https://secure.php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration.
     *
     * self     PHP 5.0
     * array    PHP 5.1
     * callable PHP 5.4
     * bool     PHP 7.0
     * float    PHP 7.0
     * int      PHP 7.0
     * string   PHP 7.0
     * iterable PHP 7.1
     * void     PHP 7.1
     * object   PHP 7.2
     * static   PHP 8.0 (return type only)
     * mixed    PHP 8.0
     * false    PHP 8.0 (union return type only)
     * null     PHP 8.0 (union return type only)
     * never    PHP 8.1 (return type only)
     * true     PHP 8.2 (standalone type: https://wiki.php.net/rfc/true-type)
     * false    PHP 8.2 (standalone type: https://wiki.php.net/rfc/null-false-standalone-types)
     * null     PHP 8.2 (standalone type: https://wiki.php.net/rfc/null-false-standalone-types)
     *
     * @var array<string, true>
     */
    private array $hints;

    private FunctionsAnalyzer $functionsAnalyzer;

    public function __construct()
    {
        parent::__construct();

        $this->hints = [
            'array' => true,
            'bool' => true,
            'callable' => true,
            'float' => true,
            'int' => true,
            'iterable' => true,
            'object' => true,
            'self' => true,
            'string' => true,
            'void' => true,
        ];

        if (\PHP_VERSION_ID >= 8_00_00) {
            $this->hints['false'] = true;
            $this->hints['mixed'] = true;
            $this->hints['null'] = true;
            $this->hints['static'] = true;
        }

        if (\PHP_VERSION_ID >= 8_01_00) {
            $this->hints['never'] = true;
        }

        if (\PHP_VERSION_ID >= 8_02_00) {
            $this->hints['true'] = true;
        }

        $this->functionsAnalyzer = new FunctionsAnalyzer();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Native type hints for functions should use the correct case.',
            [
                new CodeSample("<?php\nclass Bar {\n    public function Foo(CALLABLE \$bar)\n    {\n        return 1;\n    }\n}\n"),
                new CodeSample(
                    "<?php\nfunction Foo(INT \$a): Bool\n{\n    return true;\n}\n"
                ),
                new CodeSample(
                    "<?php\nfunction Foo(Iterable \$a): VOID\n{\n    echo 'Hello world';\n}\n"
                ),
                new VersionSpecificCodeSample(
                    "<?php\nfunction Foo(Object \$a)\n{\n    return 'hi!';\n}\n",
                    new VersionSpecification(7_02_00)
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_FUNCTION, T_FN]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokens[$index]->isGivenKind([T_FUNCTION, T_FN])) {
                $this->fixFunctionReturnType($tokens, $index);
                $this->fixFunctionArgumentTypes($tokens, $index);
            }
        }
    }

    private function fixFunctionArgumentTypes(Tokens $tokens, int $index): void
    {
        foreach ($this->functionsAnalyzer->getFunctionArguments($tokens, $index) as $argument) {
            $this->fixArgumentType($tokens, $argument->getTypeAnalysis());
        }
    }

    private function fixFunctionReturnType(Tokens $tokens, int $index): void
    {
        $this->fixArgumentType($tokens, $this->functionsAnalyzer->getFunctionReturnType($tokens, $index));
    }

    private function fixArgumentType(Tokens $tokens, ?TypeAnalysis $type = null): void
    {
        if (null === $type) {
            return;
        }

        for ($index = $type->getStartIndex(); $index <= $type->getEndIndex(); ++$index) {
            if ($tokens[$tokens->getNextMeaningfulToken($index)]->isGivenKind(T_NS_SEPARATOR)) {
                continue;
            }

            $lowerCasedName = strtolower($tokens[$index]->getContent());

            if (!isset($this->hints[$lowerCasedName])) {
                continue;
            }

            $tokens[$index] = new Token([$tokens[$index]->getId(), $lowerCasedName]);
        }
    }
}
