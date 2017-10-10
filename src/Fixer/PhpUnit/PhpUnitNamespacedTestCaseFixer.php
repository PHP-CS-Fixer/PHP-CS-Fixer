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

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitNamespacedTestCaseFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'The `\PHPUnit\Framework\TestCase` class MUST be used instead of `\PHPUnit_Framework_TestCase` class.',
            [
                new CodeSample(
'<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
}
'
                ),
            ],
            "PHPUnit v6 has finally fully switched to namespaces.\n"
            ."You could start preparing the upgrade by switching from non-namespaced TestCase to namespaced one.\n"
            .'Forward compatibility layer was backported to PHPUnit v4.8.35 and PHPUnit v5.4.0.',
            'Risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_CLASS);
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $fullSubstitute = Tokens::fromArray([
            new Token([T_STRING, 'PHPUnit']),
            new Token([T_NS_SEPARATOR, '\\']),
            new Token([T_STRING, 'Framework']),
            new Token([T_NS_SEPARATOR, '\\']),
            new Token([T_STRING, 'TestCase']),
        ]);
        $shortSubstitute = Tokens::fromArray([
            new Token([T_STRING, 'TestCase']),
        ]);

        $isImported = false;
        $currIndex = 0;

        while (null !== $currIndex) {
            $match = $tokens->findSequence([[T_STRING, 'PHPUnit_Framework_TestCase']], $currIndex, null, false);

            if (null === $match) {
                break;
            }

            $match = array_keys($match);
            $currIndex = $match[0];

            $tokens->clearAt($currIndex);
            $tokens->insertAt(
                $currIndex,
                $isImported ? clone $shortSubstitute : clone $fullSubstitute
            );

            $prevIndex = $tokens->getPrevMeaningfulToken($currIndex);
            if ($tokens[$prevIndex]->isGivenKind(T_USE)) {
                $isImported = true;
            } elseif ($tokens[$prevIndex]->isGivenKind(T_NS_SEPARATOR)) {
                $prevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
                $isImported = $tokens[$prevIndex]->isGivenKind(T_USE);
            }
        }
    }
}
