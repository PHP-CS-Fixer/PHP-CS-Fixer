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
                '<?php

                class foo_bar
                {

                }',
            ),
            array(
                '<?php

                class foo_bar
                {
                    public function do_something() {}
                }',
            ),
            array(
                '<?php

                class foo_bar
                {
                    public function do_something()
                    {
                        $this->some_function();
                    }
                }',
            ),
            array(
                '<?php

                class FooBar
                {
                    private $someVar = "foo";

                    public function doSomething($aParameter)
                    {
                        $aVariable = $aParameter;
                        $oneMore = "This is $aVariable in a string";
                        $usingThis = "This is also {$this->someVar} in a string";

                        return $aVariable;
                    }
                }',
                '<?php

                class FooBar
                {
                    private $some_var = "foo";

                    public function doSomething($a_parameter)
                    {
                        $a_variable = $a_parameter;
                        $one_more = "This is $a_variable in a string";
                        $using_this = "This is also {$this->some_var} in a string";

                        return $a_variable;
                    }
                }',
            ),
            array(
                '<?php

                class FooBar
                {
                    public function doSomething($aParameter)
                    {
                        $greatVar = "this example";

                        echo "In ${greatVar} the variable is in the middle of the string";
                        echo "$greatVar uses a variable";
                        echo "\$great_var does not";
                        echo "\${great_var} does not use a variable either";
                    }
                }',
                '<?php

                class FooBar
                {
                    public function doSomething($a_parameter)
                    {
                        $great_var = "this example";

                        echo "In ${great_var} the variable is in the middle of the string";
                        echo "$great_var uses a variable";
                        echo "\$great_var does not use a variable";
                        echo "\${great_var} does not use a variable either";
                    }
                }',
            ),
            array(
                '<?php echo "${fooBar} is not the same as ${ foo_bar }";',
                '<?php echo "${foo_bar} is not the same as ${ foo_bar }";',
            ),
        );
    }
}
