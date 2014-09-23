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
class MultilineArrayTrailingCommaFixerTest extends AbstractFixerTestBase
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
            // long syntax tests
            array('<?php $x = array();'),
            array('<?php $x = array(());'),
            array('<?php $x = array("foo");'),
            array('<?php $x = array("foo", );'),
            array("<?php \$x = array(\n'foo',\n);", "<?php \$x = array(\n'foo'\n);"),
            array("<?php \$x = array('foo',\n);"),
            array("<?php \$x = array('foo',\n);", "<?php \$x = array('foo'\n);"),
            array("<?php \$x = array('foo', /* boo */\n);", "<?php \$x = array('foo' /* boo */\n);"),
            array("<?php \$x = array('foo',\n/* boo */\n);", "<?php \$x = array('foo'\n/* boo */\n);"),
            array("<?php \$x = array(\narray('foo',\n),\n);", "<?php \$x = array(\narray('foo'\n)\n);"),
            array("<?php \$x = array(\narray('foo'),\n);", "<?php \$x = array(\narray('foo')\n);"),
            array("<?php \$x = array(\n /* He */ \n);"),
            array("<?php \$x = array(\n<<<EOT\noet\nEOT\n);"),
            array("<?php \$x = array(\n<<<'EOT'\noet\nEOT\n);"),
            array(
                '<?php

                $foo = array(
                    array(
                    ),
                );',
            ),
            array(
                "<?php
                \$x = array(
                    'foo',
                    'bar',
                    array(
                        'foo',
                        'bar',
                        array(
                            'foo',
                            'bar',
                            array(
                                'foo',
                                ('bar' ? true : !false),
                                ('bar' ? array(true) : !(false)),
                                array(
                                    'foo',
                                    'bar',
                                    array(
                                        'foo',
                                        ('bar'),
                                    ),
                                ),
                            ),
                        ),
                    ),
                );",
                "<?php
                \$x = array(
                    'foo',
                    'bar',
                    array(
                        'foo',
                        'bar',
                        array(
                            'foo',
                            'bar',
                            array(
                                'foo',
                                ('bar' ? true : !false),
                                ('bar' ? array(true) : !(false)),
                                array(
                                    'foo',
                                    'bar',
                                    array(
                                        'foo',
                                        ('bar'),
                                    )
                                )
                            )
                        )
                    )
                );",
            ),

            // short syntax tests
            array('<?php $x = array([]);'),
            array('<?php $x = [[]];'),
            array('<?php $x = ["foo",];'),
            array('<?php $x = bar(["foo",]);'),
            array("<?php \$x = bar(['foo',\n]]);", "<?php \$x = bar(['foo'\n]]);"),
            array("<?php \$x = ['foo', \n];"),
            array('<?php $x = array([],);'),
            array('<?php $x = [[],];'),
            array('<?php $x = [$y[],];'),
            array("<?php \$x = [\n /* He */ \n];"),
            array(
                '<?php

                $foo = [
                    [
                    ],
                ];',
            ),

            // no array tests
            array(
                "<?php

                throw new BadMethodCallException(
                    sprintf(
                        'Method \"%s\" not implemented',
                        __METHOD__
                    )
                );",
            ),
            array(
                "<?php

                throw new BadMethodCallException(sprintf(
                    'Method \"%s\" not implemented',
                    __METHOD__
                ));",
            ),
            array(
                "<?php

                namespace FOS\\RestBundle\\Controller;

                class ExceptionController extends ContainerAware
                {
                    public function showAction(Request \$request, \$exception, DebugLoggerInterface \$logger = null, \$format = 'html')
                    {
                        if (!\$exception instanceof DebugFlattenException && !\$exception instanceof HttpFlattenException) {
                            throw new \\InvalidArgumentException(sprintf(
                                'ExceptionController::showAction can only accept some exceptions (%s, %s), \"%s\" given',
                                'Symfony\\Component\\HttpKernel\\Exception\\FlattenException',
                                'Symfony\\Component\\Debug\\Exception\\FlattenException',
                                get_class(\$exception)
                            ));
                        }
                    }
                }",
            ),
            array(
                '<?php
    function foo(array $a)
    {
        bar(
            baz(
                1
            )
        );
    }',
            ),
        );
    }
}
