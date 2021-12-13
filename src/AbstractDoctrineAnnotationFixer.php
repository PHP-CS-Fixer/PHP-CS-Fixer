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

namespace PhpCsFixer;

use PhpCsFixer\Doctrine\Annotation\Tokens as DoctrineAnnotationTokens;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @internal
 */
abstract class AbstractDoctrineAnnotationFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var array
     */
    private $classyElements;

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // fetch indices one time, this is safe as we never add or remove a token during fixing
        $analyzer = new TokensAnalyzer($tokens);
        $this->classyElements = $analyzer->getClassyElements();

        /** @var Token $docCommentToken */
        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $index => $docCommentToken) {
            if (!$this->nextElementAcceptsDoctrineAnnotations($tokens, $index)) {
                continue;
            }

            $doctrineAnnotationTokens = DoctrineAnnotationTokens::createFromDocComment(
                $docCommentToken,
                $this->configuration['ignored_tags']
            );
            $this->fixAnnotations($doctrineAnnotationTokens);
            $tokens[$index] = new Token([T_DOC_COMMENT, $doctrineAnnotationTokens->getCode()]);
        }
    }

    /**
     * Fixes Doctrine annotations from the given PHPDoc style comment.
     */
    abstract protected function fixAnnotations(DoctrineAnnotationTokens $doctrineAnnotationTokens): void;

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('ignored_tags', 'List of tags that must not be treated as Doctrine Annotations.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([static function (array $values): bool {
                    foreach ($values as $value) {
                        if (!\is_string($value)) {
                            return false;
                        }
                    }

                    return true;
                }])
                ->setDefault([
                    // PHPDocumentor 1
                    'abstract',
                    'access',
                    'code',
                    'deprec',
                    'encode',
                    'exception',
                    'final',
                    'ingroup',
                    'inheritdoc',
                    'inheritDoc',
                    'magic',
                    'name',
                    'toc',
                    'tutorial',
                    'private',
                    'static',
                    'staticvar',
                    'staticVar',
                    'throw',

                    // PHPDocumentor 2
                    'api',
                    'author',
                    'category',
                    'copyright',
                    'deprecated',
                    'example',
                    'filesource',
                    'global',
                    'ignore',
                    'internal',
                    'license',
                    'link',
                    'method',
                    'package',
                    'param',
                    'property',
                    'property-read',
                    'property-write',
                    'return',
                    'see',
                    'since',
                    'source',
                    'subpackage',
                    'throws',
                    'todo',
                    'TODO',
                    'usedBy',
                    'uses',
                    'var',
                    'version',

                    // PHPUnit
                    'after',
                    'afterClass',
                    'backupGlobals',
                    'backupStaticAttributes',
                    'before',
                    'beforeClass',
                    'codeCoverageIgnore',
                    'codeCoverageIgnoreStart',
                    'codeCoverageIgnoreEnd',
                    'covers',
                    'coversDefaultClass',
                    'coversNothing',
                    'dataProvider',
                    'depends',
                    'expectedException',
                    'expectedExceptionCode',
                    'expectedExceptionMessage',
                    'expectedExceptionMessageRegExp',
                    'group',
                    'large',
                    'medium',
                    'preserveGlobalState',
                    'requires',
                    'runTestsInSeparateProcesses',
                    'runInSeparateProcess',
                    'small',
                    'test',
                    'testdox',
                    'ticket',
                    'uses',

                    // PHPCheckStyle
                    'SuppressWarnings',

                    // PHPStorm
                    'noinspection',

                    // PEAR
                    'package_version',

                    // PlantUML
                    'enduml',
                    'startuml',

                    // Psalm
                    'psalm',

                    // PHPStan
                    'phpstan',
                    'template',

                    // other
                    'fix',
                    'FIXME',
                    'fixme',
                    'override',
                ])
                ->getOption(),
        ]);
    }

    private function nextElementAcceptsDoctrineAnnotations(Tokens $tokens, int $index): bool
    {
        do {
            $index = $tokens->getNextMeaningfulToken($index);

            if (null === $index) {
                return false;
            }
        } while ($tokens[$index]->isGivenKind([T_ABSTRACT, T_FINAL]));

        if ($tokens[$index]->isClassy()) {
            return true;
        }

        $modifierKinds = [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL, T_ABSTRACT, T_NS_SEPARATOR, T_STRING, CT::T_NULLABLE_TYPE];

        if (\defined('T_READONLY')) { // @TODO: drop condition when PHP 8.1+ is required
            $modifierKinds[] = T_READONLY;
        }

        while ($tokens[$index]->isGivenKind($modifierKinds)) {
            $index = $tokens->getNextMeaningfulToken($index);
        }

        return isset($this->classyElements[$index]);
    }
}
