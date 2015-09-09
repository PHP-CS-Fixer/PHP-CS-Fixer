<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @internal
 */
final class PhpClosingTagFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCasesWithFullOpenTag
     */
    public function testCasesWithFullOpenTag($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    /**
     * @dataProvider provideCasesWithShortOpenTag
     */
    public function testCasesWithShortOpenTag($expected, $input = null)
    {
        if (!ini_get('short_open_tag')) {
            $this->markTestSkipped('PHP short open tags are not enabled.');

            return;
        }

        $this->makeTest($expected, $input);
    }

    public function provideCasesWithFullOpenTag()
    {
        return array(
            array('<?php echo \'Foo\';', '<?php echo \'Foo\'; ?>'),
            array('<?php echo \'Foo\';', '<?php echo \'Foo\';?>'),
            array('<?php echo \'Foo\'; ?> PLAIN TEXT'),
            array('PLAIN TEXT<?php echo \'Foo\'; ?>'),
            array('<?php

echo \'Foo\';',
                  '<?php

echo \'Foo\';

?>',
            ),
            array('<?php echo \'Foo\'; ?>
<p><?php echo \'this is a template\'; ?></p>
<?php echo \'Foo\'; ?>',
            ),
            array('<?php echo "foo";', '<?php echo "foo" ?>'),
            array(
                '<?php
class foo
{
    public function bar()
    {
        echo "Here I am!";
    }
}',
                '<?php
class foo
{
    public function bar()
    {
        echo "Here I am!";
    }
}?>',
            ),
            array(
                '<?php
public function bar()
{
    echo "Here I am!";
}',
                '<?php
public function bar()
{
    echo "Here I am!";
}?>',
            ),
            array(
                '<?php
if (true) {
    echo "Here I am!";
}',
                '<?php
if (true) {
    echo "Here I am!";
}?>',
            ),
        );
    }

    public function provideCasesWithShortOpenTag()
    {
        return array(
            array('<? echo \'Foo\';', '<? echo \'Foo\'; ?>'),
            array('<? echo \'Foo\';', '<? echo \'Foo\';?>'),
            array('<? echo \'Foo\'; ?>
<p><? echo \'this is a template\'; ?></p>
<? echo \'Foo\'; ?>',
            ),
            array('<?= "somestring"; ?> <?= "anotherstring"; ?>'),
        );
    }
}
