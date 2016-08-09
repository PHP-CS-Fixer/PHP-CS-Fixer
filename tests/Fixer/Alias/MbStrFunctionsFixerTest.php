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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 */
final class MbStrFunctionsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideExamples()
    {
        return array(
            array('<?php $x = "strlen";'),
            array('<?php $x = Foo::strlen("bar");'),
            array('<?php $x = new strlen("bar");'),
            array('<?php $x = new \strlen("bar");'),
            array('<?php $x = new Foo\strlen("bar");'),
            array('<?php $x = Foo\strlen("bar");'),
            array('<?php $x = strlen::call("bar");'),
            array('<?php $x = $foo->strlen("bar");'),

            array('<?php $x = mb_strlen("bar");', '<?php $x = strlen("bar");'),
            array('<?php $x = \mb_strlen("bar");', '<?php $x = \strlen("bar");'),
            array('<?php $x = mb_strtolower(mb_strstr("bar"));', '<?php $x = strtolower(strstr("bar"));'),
            array('<?php $x = mb_strtolower( \mb_strstr ("bar"));', '<?php $x = strtolower( \strstr ("bar"));'),
            array('<?php $x = mb_substr("bar", 2, 1);', '<?php $x = substr("bar", 2, 1);'),
        );
    }
}
