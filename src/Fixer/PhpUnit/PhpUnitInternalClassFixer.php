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

namespace PhpCsFixer\Fixer\PhpUnit;

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Gert de Pagter <BackEndTea@gmail.com>
 */
final class PhpUnitInternalClassFixer extends AbstractPhpUnitFixer implements WhitespacesAwareFixerInterface, ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'All PHPUnit test classes should be marked as internal.',
            [
                new CodeSample("<?php\nclass MyTest extends TestCase {}\n"),
                new CodeSample(
                    "<?php\nclass MyTest extends TestCase {}\nfinal class FinalTest extends TestCase {}\nabstract class AbstractTest extends TestCase {}\n",
                    ['types' => ['final']]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before FinalInternalClassFixer.
     */
    public function getPriority()
    {
        return 68;
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $types = ['normal', 'final', 'abstract'];

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('types', 'What types of classes to mark as internal'))
                ->setAllowedValues([(new AllowedValueSubset($types))])
                ->setAllowedTypes(['array'])
                ->setDefault(['normal', 'final'])
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyPhpUnitClassFix(Tokens $tokens, $startIndex, $endIndex)
    {
        $classIndex = $tokens->getPrevTokenOfKind($startIndex, [[T_CLASS]]);

        if (!$this->isAllowedByConfiguration($tokens, $classIndex)) {
            return;
        }

        $docBlockIndex = $this->getDocBlockIndex($tokens, $classIndex);

        if ($this->isPHPDoc($tokens, $docBlockIndex)) {
            $this->updateDocBlockIfNeeded($tokens, $docBlockIndex);
        } else {
            $this->createDocBlock($tokens, $docBlockIndex);
        }
    }

    /**
     * @param int $i
     *
     * @return bool
     */
    private function isAllowedByConfiguration(Tokens $tokens, $i)
    {
        $typeIndex = $tokens->getPrevMeaningfulToken($i);
        if ($tokens[$typeIndex]->isGivenKind(T_FINAL)) {
            return \in_array('final', $this->configuration['types'], true);
        }

        if ($tokens[$typeIndex]->isGivenKind(T_ABSTRACT)) {
            return \in_array('abstract', $this->configuration['types'], true);
        }

        return \in_array('normal', $this->configuration['types'], true);
    }

    private function createDocBlock(Tokens $tokens, $docBlockIndex)
    {
        $lineEnd = $this->whitespacesConfig->getLineEnding();
        $originalIndent = $this->detectIndent($tokens, $tokens->getNextNonWhitespace($docBlockIndex));
        $toInsert = [
            new Token([T_DOC_COMMENT, '/**'.$lineEnd."{$originalIndent} * @internal".$lineEnd."{$originalIndent} */"]),
            new Token([T_WHITESPACE, $lineEnd.$originalIndent]),
        ];
        $index = $tokens->getNextMeaningfulToken($docBlockIndex);
        $tokens->insertAt($index, $toInsert);
    }

    private function updateDocBlockIfNeeded(Tokens $tokens, $docBlockIndex)
    {
        $doc = new DocBlock($tokens[$docBlockIndex]->getContent());
        if (!empty($doc->getAnnotationsOfType('internal'))) {
            return;
        }
        $doc = $this->makeDocBlockMultiLineIfNeeded($doc, $tokens, $docBlockIndex);
        $lines = $this->addInternalAnnotation($doc, $tokens, $docBlockIndex);
        $lines = implode('', $lines);

        $tokens[$docBlockIndex] = new Token([T_DOC_COMMENT, $lines]);
    }

    /**
     * @param int $index
     *
     * @return string
     */
    private function detectIndent(Tokens $tokens, $index)
    {
        if (!$tokens[$index - 1]->isWhitespace()) {
            return ''; // cannot detect indent
        }

        $explodedContent = explode($this->whitespacesConfig->getLineEnding(), $tokens[$index - 1]->getContent());

        return end($explodedContent);
    }

    /**
     * @param int $docBlockIndex
     *
     * @return Line[]
     */
    private function addInternalAnnotation(DocBlock $docBlock, Tokens $tokens, $docBlockIndex)
    {
        $lines = $docBlock->getLines();
        $originalIndent = $this->detectIndent($tokens, $docBlockIndex);
        $lineEnd = $this->whitespacesConfig->getLineEnding();
        array_splice($lines, -1, 0, $originalIndent.' *'.$lineEnd.$originalIndent.' * @internal'.$lineEnd);

        return $lines;
    }

    /**
     * @param int $docBlockIndex
     *
     * @return DocBlock
     */
    private function makeDocBlockMultiLineIfNeeded(DocBlock $doc, Tokens $tokens, $docBlockIndex)
    {
        $lines = $doc->getLines();
        if (1 === \count($lines) && empty($doc->getAnnotationsOfType('internal'))) {
            $indent = $this->detectIndent($tokens, $tokens->getNextNonWhitespace($docBlockIndex));
            $doc->makeMultiLine($indent, $this->whitespacesConfig->getLineEnding());

            return $doc;
        }

        return $doc;
    }
}
