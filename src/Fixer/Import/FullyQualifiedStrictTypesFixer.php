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
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
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
            'Transforms imported FQCN parameters and return types to short version.',
            [
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
        return PHP_VERSION_ID >= 70000 && $tokens->isTokenKindFound(T_FUNCTION);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $fullNameOnly = function (array $info) { return $info['fullName']; };
        $namespaces = array_map($fullNameOnly, (new NamespacesAnalyzer())->getDeclarations($tokens));
        $useMap = array_map($fullNameOnly, (new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens));

        if (!count($namespaces) && !count($useMap)) {
            return;
        }

        $lastIndex = $tokens->count() - 1;
        for ($index = $lastIndex; $index >= 0; --$index) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $this->fixFunctionReturnType($tokens, $index, $namespaces, $useMap);
            $this->fixFunctionArguments($tokens, $index, $namespaces, $useMap);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     * @param array  $namespaces
     * @param array  $useMap
     */
    private function fixFunctionArguments(Tokens $tokens, $index, array $namespaces, array $useMap)
    {
        $arguments = (new FunctionsAnalyzer())->getFunctionArguments($tokens, $index);

        foreach ($arguments as $argument) {
            if (!$argument['type'] || $argument['type_index_start'] < 0) {
                continue;
            }

            $shortType = $this->detectShortType($argument['type'], $namespaces, $useMap);
            if ($shortType === $argument['type']) {
                continue;
            }

            $tokens->overrideRange(
                $argument['type_index_start'],
                $argument['type_index_end'],
                $this->generateTokensForShortType($shortType)
            );
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     * @param array  $namespaces
     * @param array  $useMap
     */
    private function fixFunctionReturnType(Tokens $tokens, $index, array $namespaces, array $useMap)
    {
        $returnType = (new FunctionsAnalyzer())->getFunctionReturnType($tokens, $index);
        if (!$returnType) {
            return;
        }

        $shortType = $this->detectShortType($returnType['type'], $namespaces, $useMap);
        if ($shortType === $returnType['type']) {
            return;
        }

        $tokens->overrideRange(
            $returnType['start_index'],
            $returnType['end_index'],
            $this->generateTokensForShortType($shortType)
        );
    }

    /**
     * @param string $type
     * @param array  $namespaces
     * @param array  $useMap
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
     * @return array
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
