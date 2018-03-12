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

namespace PhpCsFixer\Tokenizer\Resolver;

use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class TypeShortNameResolver
{
    /**
     * This method will resolve the shortName of a FQCN if possible or otherwise return the inserted type name.
     * E.g.: use Foo\Bar => "Bar".
     *
     * @param string                $type
     * @param array<string, string> $namespaces a list of all FQN namespaces in the file with the short name as key
     * @param array<string, string> $useMap     a list of all FQN use statements in the file with the short name as key
     * @param mixed                 $typeName
     *
     * @return string
     */
    public function resolve(Tokens $tokens, $typeName)
    {
        // First match explicit imports:
        $useMap = $this->getUseMapFromTokens($tokens);
        foreach ($useMap as $shortName => $fullName) {
            $regex = '/^\\\\?'.preg_quote($fullName, '/').'$/';
            if (Preg::match($regex, $typeName)) {
                return $shortName;
            }
        }

        // Next try to match (partial) classes inside the same namespace
        // For now only support one namespace per file:
        $namespaces = $this->getNamespacesFromTokens($tokens);
        if (1 === count($namespaces)) {
            foreach ($namespaces as $shortName => $fullName) {
                $matches = [];
                $regex = '/^\\\\?'.preg_quote($fullName, '/').'\\\\(?P<className>.+)$/';
                if (Preg::match($regex, $typeName, $matches)) {
                    return $matches['className'];
                }
            }
        }

        // Next: Try to match partial use statements:

        foreach ($useMap as $shortName => $fullName) {
            $matches = [];
            $regex = '/^\\\\?'.preg_quote($fullName, '/').'\\\\(?P<className>.+)$/';
            if (Preg::match($regex, $typeName, $matches)) {
                return $shortName.'\\'.$matches['className'];
            }
        }

        return $typeName;
    }

    /**
     * @param Tokens $tokens
     *
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
     *
     * @return array<string, string> A list of all FQN use statements in the file with the short name as key
     */
    private function getUseMapFromTokens(Tokens $tokens)
    {
        return array_map(function (NamespaceUseAnalysis $info) {
            return $info->getFullName();
        }, (new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens));
    }
}
