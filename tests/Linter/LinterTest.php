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

use PhpCsFixer\Linter\Linter;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers PhpCsFixer\Linter\Linter
 */
final class LinterTest extends AbstractLinterTestCase
{
    public function testIsAsync()
    {
        $this->assertSame(!defined('TOKEN_PARSE'), $this->createLinter()->isAsync());
    }

    /**
     * {@inheritdoc}
     */
    protected function createLinter()
    {
        return new Linter();
    }
}
