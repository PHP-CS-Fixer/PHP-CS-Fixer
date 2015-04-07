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
 * @author Gregor Harlan <gharlan@web.de>
 */
class SingleQuoteFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        return array(
            array(
                '<?php $a = \'\';',
                '<?php $a = "";',
            ),
            array(
                '<?php $a = \'foo bar\';',
                '<?php $a = "foo bar";',
            ),
            array(
                '<?php $a = \'foo
                    bar\';',
                '<?php $a = "foo
                    bar";',
            ),
            array(
                '<?php $a = \'foo\'.\'bar\'."$baz";',
                '<?php $a = \'foo\'."bar"."$baz";',
            ),
            array(
                '<?php $a = \'foo "bar"\';',
                '<?php $a = "foo \"bar\"";',
            ),
            array(<<<'EOF'
<?php $a = '\\foo\\bar\\\\';
EOF
                , <<<'EOF'
<?php $a = "\\foo\\bar\\\\";
EOF
            ),

            array('<?php $a = \'foo bar\';'),
            array('<?php $a = \'foo "bar"\';'),
            array('<?php $a = "foo \'bar\'";'),
            array('<?php $a = "foo $bar";'),
            array('<?php $a = "foo ${bar}";'),
            array('<?php $a = "foo\n bar";'),
            array(<<<'EOF'
<?php $a = "\\\n";
EOF
            ),
        );
    }
}
