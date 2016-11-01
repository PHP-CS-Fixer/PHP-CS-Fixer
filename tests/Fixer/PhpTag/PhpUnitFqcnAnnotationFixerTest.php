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

namespace PhpCsFixer\Tests\Fixer\PhpTag;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 *
 * @internal
 */
final class PhpUnitFqcnAnnotationFixerTest extends AbstractFixerTestCase
{
    public function testFix()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @ExpectedException Value
     * @expectedException \X
     * @expectedException
     * @expectedException \Exception
         * @expectedException \Some\Exception\ClassName
 * @expectedExceptionCode 123
     * @expectedExceptionMessage Foo bar
     */
EOF;
        $input = <<<'EOF'
<?php
    /**
     * @ExpectedException Value
     * @expectedException X
     * @expectedException
     * @expectedException \Exception
         * @expectedException Some\Exception\ClassName
 * @expectedExceptionCode 123
     * @expectedExceptionMessage Foo bar
     */
EOF;

        $this->doTest($expected, $input);
    }
}
