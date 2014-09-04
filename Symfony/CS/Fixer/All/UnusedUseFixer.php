<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\All;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class UnusedUseFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $namespace = $this->detectNamespace($tokens);
        $useDeclarationsIndexes = $tokens->getNamespaceUseIndexes();
        $useDeclarations = $this->getNamespaceUseDeclarations($tokens, $useDeclarationsIndexes);
        $contentWithoutUseDeclarations = $this->generateCodeWithoutUses($tokens, $useDeclarations);
        $useUsages = $this->detectUseUsages($contentWithoutUseDeclarations, $useDeclarations);

        $this->removeUnusedUseDeclarations($tokens, $useDeclarations, $useUsages);
        $this->removeUsesInSameNamespace($tokens, $useDeclarations, $namespace);

        return $tokens->generateCode();
    }

    private function detectNamespace(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (T_NAMESPACE !== $token->id) {
                continue;
            }

            $declarationEndIndex = null;
            $tokens->getNextTokenOfKind($index, array(';', '{'), $declarationEndIndex);

            $declarationContent = $tokens->generatePartialCode($index + 1, $declarationEndIndex - 1);

            return trim($declarationContent);
        }
    }

    private function detectUseUsages($content, array $useDeclarations)
    {
        $usages = array();

        foreach ($useDeclarations as $shortName => $useDeclaration) {
            $usages[$shortName] = (bool) preg_match('/\b'.preg_quote($shortName).'\b/i', $content);
        }

        return $usages;
    }

    private function generateCodeWithoutUses(Tokens $tokens, array $useDeclarations)
    {
        $content = '';

        foreach ($tokens as $index => $token) {
            $allowToAppend = true;

            foreach ($useDeclarations as $useDeclaration) {
                if ($useDeclaration['declarationStart'] <= $index && $index <= $useDeclaration['declarationEnd']) {
                    $allowToAppend = false;
                    break;
                }
            }

            if ($allowToAppend) {
                $content .= $token->content;
            }
        }

        return $content;
    }

    private function getNamespaceUseDeclarations(Tokens $tokens, array $useIndexes)
    {
        $uses = array();

        foreach ($useIndexes as $index) {
            $declarationEndIndex = null;
            $tokens->getNextTokenOfKind($index, array(';'), $declarationEndIndex);

            $declarationContent = $tokens->generatePartialCode($index + 1, $declarationEndIndex - 1);

            // ignore multiple use statements like: `use BarB, BarC as C, BarD;`
            // that should be split into few separate statements
            if (false !== strpos($declarationContent, ',')) {
                continue;
            }

            $declarationParts = preg_split('/\s+as\s+/i', $declarationContent);

            if (1 === count($declarationParts)) {
                $fullName = $declarationContent;
                $declarationParts = explode('\\', $fullName);
                $shortName = end($declarationParts);
                $aliased = false;
            } else {
                $fullName = $declarationParts[0];
                $shortName = $declarationParts[1];
                $declarationParts = explode('\\', $fullName);
                $aliased = $shortName !== end($declarationParts);
            }

            $shortName = trim($shortName);

            $uses[$shortName] = array(
                'shortName' => $shortName,
                'fullName' => trim($fullName),
                'aliased' => $aliased,
                'declarationStart' => $index,
                'declarationEnd' => $declarationEndIndex,
            );
        }

        return $uses;
    }

    private function removeUnusedUseDeclarations(Tokens $tokens, array $useDeclarations, array $useUsages)
    {
        foreach ($useDeclarations as $shortName => $useDeclaration) {
            if (!$useUsages[$shortName]) {
                $this->removeUseDeclaration($tokens, $useDeclaration);
            }
        }
    }

    private function removeUseDeclaration(Tokens $tokens, array $useDeclaration)
    {
        for ($index = $useDeclaration['declarationStart']; $index <= $useDeclaration['declarationEnd']; ++$index) {
            $tokens[$index]->clear();
        }

        $token = $tokens[$useDeclaration['declarationStart'] - 1];

        if ($token->isWhitespace()) {
            $token->content = rtrim($token->content, " \t");
        }

        $token = $tokens[$useDeclaration['declarationEnd'] + 1];

        if ($token->isWhitespace()) {
            $content = ltrim($token->content, " \t");

            if ($content && "\n" === $content[0]) {
                $content = substr($content, 1);
            }

            $token->content = $content;
        }
    }

    private function removeUsesInSameNamespace(Tokens $tokens, array $useDeclarations, $namespace)
    {
        if (!$namespace) {
            return;
        }

        $nsLength = strlen($namespace.'\\');

        foreach ($useDeclarations as $useDeclaration) {
            if ($useDeclaration['aliased']) {
                continue;
            }

            if (0 !== strpos($useDeclaration['fullName'], $namespace.'\\')) {
                continue;
            }

            $partName = substr($useDeclaration['fullName'], $nsLength);

            if (false === strpos($partName, '\\')) {
                $this->removeUseDeclaration($tokens, $useDeclaration);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the MultipleUseFixer
        return -10;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        // some fixtures are auto-generated by Symfony and may contain unused use statements
        if (false !== strpos($file, DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Unused use statements must be removed.';
    }
}
