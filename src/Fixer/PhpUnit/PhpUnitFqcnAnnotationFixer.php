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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class PhpUnitFqcnAnnotationFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'PHPUnit annotations should be a FQCNs including a root namespace.',
            [new CodeSample(
'<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @covers Project\NameSpace\Something
     * @coversDefaultClass Project\Default
     * @uses Project\Test\Util
     */
    public function testSomeTest()
    {
    }
}
'
            )]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before NoUnusedImportsFixer
        return -9;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $token) {
            if ($token->isGivenKind(T_DOC_COMMENT)) {
                $token->setContent(preg_replace(
                    '~^(\s*\*\s*@(?:expectedException|covers|coversDefaultClass|uses)\h+)(\w.*)$~m', '$1\\\\$2',
                    $token->getContent()
                ));
            }
        }
    }
}
