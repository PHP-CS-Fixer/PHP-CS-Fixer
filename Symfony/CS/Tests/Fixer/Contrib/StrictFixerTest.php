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

use Symfony\CS\Fixer\Contrib\StrictFixer as Fixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class StrictFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideComparisonsExamples
     */
    public function testFixComparisons($expected, $input)
    {
        $fixer = new Fixer();
        $file = $this->getTestFile();

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function provideComparisonsExamples()
    {
        return array(
            array('<?php $a === $b;', '<?php $a == $b;', ),
            array('<?php $a !== $b;', '<?php $a != $b;', ),
            array('<?php $a !== $b;', '<?php $a <> $b;', ),
            array('<?php echo "$a === $b";', '<?php echo "$a === $b";', ),
        );
    }

    private function getTestFile($filename = __FILE__)
    {
        static $files = array();

        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }

        return $files[$filename];
    }
}
