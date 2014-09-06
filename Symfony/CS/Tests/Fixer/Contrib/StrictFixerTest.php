<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class StrictFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideComparisonsExamples
     */
    public function testFixComparisons($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideComparisonsExamples()
    {
        return array(
            array('<?php $a === $b;', '<?php $a == $b;'),
            array('<?php $a !== $b;', '<?php $a != $b;'),
            array('<?php $a !== $b;', '<?php $a <> $b;'),
            array('<?php echo "$a === $b";'),
        );
    }
}
