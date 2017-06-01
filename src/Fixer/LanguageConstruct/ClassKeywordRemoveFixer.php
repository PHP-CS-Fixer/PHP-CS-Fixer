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
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class ClassKeywordRemoveFixer extends AbstractFixer
{
    /**
     * @var string[]
     */
    private $imports = array();

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Converts `::class` keywords to FQCN strings. Requires PHP >= 5.5.',
            array(
                new VersionSpecificCodeSample(
'<?php

use Foo\Bar\Baz;

$className = Baz::class;
',
                    new VersionSpecification(50500)
                ),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return PHP_VERSION_ID >= 50500 && $tokens->isTokenKindFound(CT::T_CLASS_CONSTANT);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $this->replaceClassKeywords($tokens);
    }

    /**
     * Replaces ::class keyword, namespace by namespace.
     *
     * It uses recursive method to get rid of token index changes.
     *
     * @param Tokens $tokens
     * @param int    $namespaceNumber
     */
    private function replaceClassKeywords(Tokens $tokens, $namespaceNumber = -1)
    {
        $namespaceIndexes = array_keys($tokens->findGivenKind(T_NAMESPACE));

        // Namespace blocks
        if (count($namespaceIndexes) && isset($namespaceIndexes[$namespaceNumber])) {
            $startIndex = $namespaceIndexes[$namespaceNumber];

            $namespaceBlockStartIndex = $tokens->getNextTokenOfKind($startIndex, array(';', '{'));
            $endIndex = $tokens[$namespaceBlockStartIndex]->equals('{')
                ? $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $namespaceBlockStartIndex)
                : $tokens->getNextTokenOfKind($namespaceBlockStartIndex, array(T_NAMESPACE));
            $endIndex = $endIndex ?: $tokens->count() - 1;
        } elseif (-1 === $namespaceNumber) { // Out of any namespace block
            $startIndex = 0;
            $endIndex = count($namespaceIndexes) ? $namespaceIndexes[0] : $tokens->count() - 1;
        } else {
            return;
        }

        $this->storeImports($tokens, $startIndex, $endIndex);
        $tokens->rewind();
        $this->replaceClassKeywordsSection($tokens, $startIndex, $endIndex);
        $this->replaceClassKeywords($tokens, $namespaceNumber + 1);
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     */
    private function storeImports(Tokens $tokens, $startIndex, $endIndex)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $this->imports = array();

        foreach ($tokensAnalyzer->getImportUseIndexes() as $index) {
            if ($index < $startIndex || $index > $endIndex) {
                continue;
            }

            $import = '';
            while ($index = $tokens->getNextMeaningfulToken($index)) {
                if ($tokens[$index]->equalsAny(array(';', array(CT::T_GROUP_IMPORT_BRACE_OPEN))) || $tokens[$index]->isGivenKind(T_AS)) {
                    break;
                }

                $import .= $tokens[$index]->getContent();
            }

            // Imports group (PHP 7 spec)
            if ($tokens[$index]->isGivenKind(CT::T_GROUP_IMPORT_BRACE_OPEN)) {
                $groupEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_GROUP_IMPORT_BRACE, $index);
                $groupImports = array_map(
                    'trim',
                    explode(',', $tokens->generatePartialCode($index + 1, $groupEndIndex - 1))
                );
                foreach ($groupImports as $groupImport) {
                    $groupImportParts = array_map('trim', explode(' as ', $groupImport));
                    if (2 === count($groupImportParts)) {
                        $this->imports[$groupImportParts[1]] = $import.$groupImportParts[0];
                    } else {
                        $this->imports[] = $import.$groupImport;
                    }
                }
            } elseif ($tokens[$index]->isGivenKind(T_AS)) {
                $aliasIndex = $tokens->getNextMeaningfulToken($index);
                $alias = $tokens[$aliasIndex]->getContent();
                $this->imports[$alias] = $import;
            } else {
                $this->imports[] = $import;
            }
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     */
    private function replaceClassKeywordsSection(Tokens $tokens, $startIndex, $endIndex)
    {
        $CTClassTokens = $tokens->findGivenKind(CT::T_CLASS_CONSTANT, $startIndex, $endIndex);
        if (!empty($CTClassTokens)) {
            $this->replaceClassKeyword($tokens, current(array_keys($CTClassTokens)));
            $this->replaceClassKeywordsSection($tokens, $startIndex, $endIndex);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $classIndex
     */
    private function replaceClassKeyword(Tokens $tokens, $classIndex)
    {
        $classEndIndex = $tokens->getPrevMeaningfulToken($classIndex);
        $classEndIndex = $tokens->getPrevMeaningfulToken($classEndIndex);

        $classBeginIndex = $classEndIndex;
        while (true) {
            $prev = $tokens->getPrevMeaningfulToken($classBeginIndex);
            if (!$tokens[$prev]->isGivenKind(array(T_NS_SEPARATOR, T_STRING))) {
                break;
            }

            $classBeginIndex = $prev;
        }

        $classString = $tokens->generatePartialCode(
            $tokens[$classBeginIndex]->isGivenKind(T_NS_SEPARATOR)
                ? $tokens->getNextMeaningfulToken($classBeginIndex)
                : $classBeginIndex,
            $classEndIndex
        );

        $classImport = false;
        foreach ($this->imports as $alias => $import) {
            if ($classString === $alias) {
                $classImport = $import;
                break;
            }

            $classStringArray = explode('\\', $classString);
            $namespaceToTest = $classStringArray[0];

            if (0 === strcmp($namespaceToTest, substr($import, -strlen($namespaceToTest)))) {
                $classImport = $import;
                break;
            }
        }

        for ($i = $classBeginIndex; $i <= $classIndex; ++$i) {
            if (!$tokens[$i]->isComment() && !($tokens[$i]->isWhitespace() && false !== strpos($tokens[$i]->getContent(), "\n"))) {
                $tokens->clearAt($i);
            }
        }

        $tokens->insertAt($classBeginIndex, new Token(array(
            T_CONSTANT_ENCAPSED_STRING,
            "'".$this->makeClassFQN($classImport, $classString)."'",
        )));
    }

    /**
     * @param string|false $classImport
     * @param string       $classString
     *
     * @return string
     */
    private function makeClassFQN($classImport, $classString)
    {
        if (false === $classImport) {
            return $classString;
        }

        $classStringArray = explode('\\', $classString);
        $classStringLength = count($classStringArray);
        $classImportArray = explode('\\', $classImport);
        $classImportLength = count($classImportArray);

        if (1 === $classStringLength) {
            return $classImport;
        }

        return implode('\\', array_merge(
            array_slice($classImportArray, 0, $classImportLength - $classStringLength + 1),
            $classStringArray
        ));
    }
}
