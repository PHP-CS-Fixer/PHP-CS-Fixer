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
 * @author Michal Kierat <kierate@gmail.com>
 */
class VariablePropertyCaseFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php class foo_bar {}',
            ),
            array(
                '<?php public function do_something() {}',
            ),
            array(
                '<?php $this->some_function();',
            ),
            array(
                '<?php private $someVar = "foo";',
                '<?php private $some_var = "foo";',
            ),
            array(
                '<?php public function doSomething($aParameter) {}',
                '<?php public function doSomething($a_parameter) {}',
            ),
            array(
                '<?php $aVariable = $aParameter;',
                '<?php $a_variable = $a_parameter;',
            ),
            array(
                '<?php $oneMore = "This is $aVariable in a string";',
                '<?php $one_more = "This is $a_variable in a string";',
            ),
            array(
                '<?php $usingThis = "This is also {$this->someVar} in a string";',
                '<?php $using_this = "This is also {$this->some_var} in a string";',
            ),
            array(
                '<?php $greatVar = "this example";',
                '<?php $great_var = "this example";',
            ),
            array(
                '<?php echo "In ${greatVar} the variable is in the middle of the string";',
                '<?php echo "In ${great_var} the variable is in the middle of the string";',
            ),
            array(
                '<?php echo "$greatVar uses a variable";',
                '<?php echo "$great_var uses a variable";',
            ),
            array(
                '<?php echo "\$great_var does not use a variable";',
            ),
            array(
                '<?php echo "\${great_var} does not use a variable either";',
            ),
            array(
                '<?php echo "${fooBar} is not the same as ${ foo_bar }";',
                '<?php echo "${foo_bar} is not the same as ${ foo_bar }";',
            ),
            array(
                '<?php return $aVariable;',
                '<?php return $a_variable;',
            ),
        );
    }
}
