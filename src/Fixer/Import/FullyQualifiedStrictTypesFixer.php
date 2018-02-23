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

namespace PhpCsFixer\Fixer\Import;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 */
final class FullyQualifiedStrictTypesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Transforms imported FQCN parameters and return types in function arguments to short version.',
            [
                new CodeSample(
                    '<?php

use Foo\Bar;

class SomeClass
{
    public function doSomething(\Foo\Bar $foo)
    {
    }
}
'
                ),
                new VersionSpecificCodeSample(
                    '<?php

use Foo\Bar;
use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(\Foo\Bar $foo): \Foo\Bar\Baz
    {
    }
}
',
                    new VersionSpecification(70000)
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_FUNCTION);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $namespaces = $this->getNamespacesFromTokens($tokens);
        $useMap = $this->getUseMapFromTokens($tokens);

        if (!count($namespaces) && !count($useMap)) {
            return;
        }

        $lastIndex = $tokens->count() - 1;
        for ($index = $lastIndex; $index >= 0; --$index) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            // Return types are only available since PHP 7.0
            $this->fixFunctionReturnType($tokens, $index, $namespaces, $useMap);
            $this->fixFunctionArguments($tokens, $index, $namespaces, $useMap);
        }
    }

    /**
     * @param Tokens $tokens
     * @return array<string, string> A list of all FQN namespaces in the file with the short name as key
     */
    private function getNamespacesFromTokens(Tokens $tokens)
    {
        return array_map(function (NamespaceAnalysis $info) {
            return $info->getFullName();
        }, (new NamespacesAnalyzer())->getDeclarations($tokens));
    }

    /**
     * @param Tokens $tokens
     * @return array<string, string> A list of all FQN use statements in the file with the short name as key
     */
    private function getUseMapFromTokens(Tokens $tokens)
    {
        return array_map(function (NamespaceUseAnalysis $info) {
            return $info->getFullName();
        }, (new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens));
    }

    /**
     * @param Tokens                $tokens
     * @param int                   $index
     * @param array<string, string> $namespaces A list of all FQN namespaces in the file with the short name as key
     * @param array<string, string> $useMap     A list of all FQN use statements in the file with the short name as key
     */
    private function fixFunctionArguments(Tokens $tokens, $index, array $namespaces, array $useMap)
    {
        $arguments = (new FunctionsAnalyzer())->getFunctionArguments($tokens, $index);

        foreach ($arguments as $argument) {
            if (!$argument->hasType()) {
                continue;
            }

            $this->detectAndReplaceTypeWithShortType($tokens, $argument->getType(), $namespaces, $useMap);
        }
    }

    /**
     * @param Tokens                $tokens
     * @param int                   $index
     * @param array<string, string> $namespaces a list of all FQN namespaces in the file with the short name as key
     * @param array<string, string> $useMap     a list of all FQN use statements in the file with the short name as key
     */
    private function fixFunctionReturnType(Tokens $tokens, $index, array $namespaces, array $useMap)
    {
        if (PHP_VERSION_ID < 70000) {
            return;
        }

        $returnType = (new FunctionsAnalyzer())->getFunctionReturnType($tokens, $index);
        if (!$returnType) {
            return;
        }

        $this->detectAndReplaceTypeWithShortType($tokens, $returnType, $namespaces, $useMap);
    }

    /**
     * @param Tokens $tokens
     * @param TypeAnalysis $type
     * @param array<string, string> $namespaces a list of all FQN namespaces in the file with the short name as key
     * @param array<string, string> $useMap     a list of all FQN use statements in the file with the short name as key
     */
    private function detectAndReplaceTypeWithShortType(
        Tokens $tokens,
        TypeAnalysis $type,
        array $namespaces,
        array $useMap
    )
    {
        if ($type->isScalar()) {
            return;
        }

        $typeName = $type->getName();
        $shortType = $this->detectShortType($typeName, $namespaces, $useMap);
        if ($shortType === $typeName) {
            return;
        }

        $tokens->overrideRange(
            $type->getStartIndex(),
            $type->getEndIndex(),
            $this->generateTokensForShortType($shortType)
        );
    }

    /**
     * The short type is the last part of the FQCN.
     * E.g.: use Foo\Bar => "Bar"
     *
     * @param string                $type
     * @param array<string, string> $namespaces a list of all FQN namespaces in the file with the short name as key
     * @param array<string, string> $useMap     a list of all FQN use statements in the file with the short name as key
     *
     * @return string
     */
    private function detectShortType($type, array $namespaces, array $useMap)
    {
        // First match explicit imports:
        foreach ($useMap as $shortName => $fullName) {
            $regex = '/^\\\\?'.preg_quote($fullName, '/').'$/';
            if (preg_match($regex, $type)) {
                return $shortName;
            }
        }

        // Next try to match (partial) classes inside the same namespace
        // For now only support one namespace per file:
        if (1 === count($namespaces)) {
            foreach ($namespaces as $shortName => $fullName) {
                $matches = [];
                $regex = '/^\\\\?'.preg_quote($fullName, '/').'\\\\(?P<className>.+)$/';
                if (preg_match($regex, $type, $matches)) {
                    return $matches['className'];
                }
            }
        }

        // Next: Try to match partial use statements:
        foreach ($useMap as $shortName => $fullName) {
            $matches = [];
            $regex = '/^\\\\?'.preg_quote($fullName, '/').'\\\\(?P<className>.+)$/';
            if (preg_match($regex, $type, $matches)) {
                return $shortName.'\\'.$matches['className'];
            }
        }

        return $type;
    }

    /**
     * @param string $shortType
     *
     * @return Token[]
     */
    private function generateTokensForShortType($shortType)
    {
        $tokens = [];
        $parts = explode('\\', $shortType);

        foreach ($parts as $index => $part) {
            $tokens[] = new Token([T_STRING, $part]);
            if ($index !== count($parts) - 1) {
                $tokens[] = new Token([T_NS_SEPARATOR, '\\']);
            }
        }

        return $tokens;
    }
}
