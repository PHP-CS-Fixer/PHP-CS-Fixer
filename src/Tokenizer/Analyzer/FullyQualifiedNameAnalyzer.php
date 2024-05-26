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

namespace PhpCsFixer\Tokenizer\Analyzer;

use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class FullyQualifiedNameAnalyzer
{
    public static function getFullyQualifiedName(Tokens $tokens, string $name, int $indexInNamespace): string
    {
        return ltrim(self::getFullyQualifiedNameWithPossiblyLeadingSlash($tokens, $name, $indexInNamespace), '\\');
    }

    private static function getFullyQualifiedNameWithPossiblyLeadingSlash(Tokens $tokens, string $name, int $indexInNamespace): string
    {
        if ('\\' === $name[0]) {
            return $name;
        }

        $namespaceAnalysis = (new NamespacesAnalyzer())->getNamespaceAt($tokens, $indexInNamespace);
        $namespaceUseAnalyses = (new NamespaceUsesAnalyzer())->getDeclarationsInNamespace($tokens, $namespaceAnalysis);

        $declarations = [];
        foreach ($namespaceUseAnalyses as $namespaceUseAnalysis) {
            if (!$namespaceUseAnalysis->isClass()) {
                continue;
            }
            $declarations[strtolower($namespaceUseAnalysis->getShortName())] = $namespaceUseAnalysis->getFullName();
        }

        $lowercaseName = strtolower($name);
        foreach ($declarations as $lowercaseShortName => $fullName) {
            if ($lowercaseName === $lowercaseShortName) {
                return $fullName;
            }

            if (!str_starts_with($lowercaseName, $lowercaseShortName.'\\')) {
                continue;
            }

            return $fullName.substr($name, \strlen($lowercaseShortName));
        }

        return $namespaceAnalysis->getFullName().'\\'.$name;
    }
}
