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

namespace PhpCsFixer\Tests\Linter;

use PhpCsFixer\Linter\TokenizerLinter;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @requires PHP 7.0
 * @covers \PhpCsFixer\Linter\TokenizerLinter
 * @covers \PhpCsFixer\Linter\TokenizerLintingResult
 */
final class TokenizerLinterTest extends AbstractLinterTestCase
{
    public function testIsAsync()
    {
        $this->assertFalse($this->createLinter()->isAsync());
    }

    /**
     * {@inheritdoc}
     */
    protected function createLinter()
    {
        return new TokenizerLinter();
    }
}
