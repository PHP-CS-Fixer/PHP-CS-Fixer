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
     * @requires PHP 5.4
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
            array(
                '<?= 1; ?>',
                '<?= 1 ?>',
            ),
            array(
                "<?= '1_'; /**/    ?>",
                "<?= '1_' /**/    ?>",
            ),
            array('<?php ?>'),
            array('<?php if($a){}'),
            array('<?php while($a > $b){}'),
        );
    }
}
