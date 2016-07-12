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

namespace PhpCsFixer\Tests\Fixer\Semicolon;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class SemicolonAfterInstructionFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php $a = [1,2,3]; echo $a{1}; ?>',
                '<?php $a = [1,2,3]; echo $a{1} ?>',
            ),
            array(
                '<?php $a++;//a ?>',
                '<?php $a++//a ?>',
            ),
            array(
                '<?php $b++; /**/ ?>',
                '<?php $b++ /**/ ?>',
            ),
            array(
                '<?php echo 123; ?>',
                '<?php echo 123 ?>',
            ),
            array(
                "<?php echo 123;\n\t?>",
                "<?php echo 123\n\t?>",
            ),
            array('<?php ?>'),
            array('<?php if($a){}'),
            array('<?php while($a > $b){}'),
        );
    }

    public function testOpenWithEcho()
    {
        if (50400 > PHP_VERSION_ID && !ini_get('short_open_tag')) {
            // On PHP <5.4 short echo tag is parsed as T_INLINE_HTML if short_open_tag is disabled
            // On PHP >=5.4 short echo tag is always parsed properly regardless of short_open_tag  option
            $this->markTestSkipped('PHP 5.4 (or later) or short_open_tag option is required.');
        }

        $this->doTest("<?= '1_'; ?>", "<?= '1_' ?>");
    }
}
