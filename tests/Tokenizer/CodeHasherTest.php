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

namespace PhpCsFixer\Tests\Tokenizer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\CodeHasher;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\CodeHasher
 */
final class CodeHasherTest extends TestCase
{
    public function testCodeHasher()
    {
        $this->assertSame('322920910', CodeHasher::calculateCodeHash('<?php echo 1;'));
        $this->assertSame('322920910', CodeHasher::calculateCodeHash('<?php echo 1;')); // calling twice, hashes should always be the same when the input doesn't change.
    }
}
