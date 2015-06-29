<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitStrictFixer extends AbstractFixer
{
    static private $phpUnitMethods = array(
        'assertAttributeEquals' => 'assertAttributeSame',
        'assertAttributeNotEquals' => 'assertAttributeNotSame',
        'assertEquals' => 'assertSame',
        'assertNotEquals' => 'assertNotSame',
    );

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHPUnit methods like "assertSame" should be used instead of "assertEquals". Warning! This could change code behavior.';
    }
}
