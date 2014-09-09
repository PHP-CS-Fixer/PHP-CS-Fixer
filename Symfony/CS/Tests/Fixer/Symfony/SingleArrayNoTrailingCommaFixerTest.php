<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class SingleArrayNoTrailingCommaFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideExamples()
    {
        return array(
            array('<?php $x = array();'),
            array('<?php $x = array(());'),
            array('<?php $x = array("foo");'),
            array('<?php $x = array("foo");', '<?php $x = array("foo", );'),
            array("<?php \$x = array(\n'foo', \n);"),
            array("<?php \$x = array('foo', \n);"),
            array("<?php \$x = array(array('foo'), \n);", "<?php \$x = array(array('foo',), \n);"),
            array("<?php \$x = array(array('foo',\n), \n);"),

            // Short syntax
            array('<?php $x = array([]);'),
            array('<?php $x = [[]];'),
            array('<?php $x = ["foo"];', '<?php $x = ["foo",];'),
            array('<?php $x = bar(["foo"]);', '<?php $x = bar(["foo",]);'),
            array("<?php \$x = bar(['foo'],\n]);"),
            array("<?php \$x = ['foo', \n];"),
            array('<?php $x = array([]);', '<?php $x = array([],);'),
            array('<?php $x = [[]];', '<?php $x = [[],];'),
            array('<?php $x = [$y[]];', '<?php $x = [$y[],];'),
        );
    }
}
