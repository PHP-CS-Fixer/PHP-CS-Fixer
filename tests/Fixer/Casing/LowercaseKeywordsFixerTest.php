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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer
 */
final class LowercaseKeywordsFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideExamples()
    {
        return [
            ['<?php $x = (1 and 2);', '<?php $x = (1 AND 2);'],
            ['<?php foreach(array(1, 2, 3) as $val) {}', '<?php FOREACH(array(1, 2, 3) AS $val) {}'],
            ['<?php echo "GOOD AS NEW";'],
            ['<?php echo X::class ?>', '<?php echo X::ClASs ?>'],
        ];
    }

    public function testHaltCompiler()
    {
        $this->doTest('<?php __HALT_COMPILER();');
    }
}
