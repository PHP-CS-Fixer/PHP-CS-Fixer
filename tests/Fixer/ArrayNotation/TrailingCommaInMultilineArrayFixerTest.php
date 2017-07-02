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

namespace PhpCsFixer\Tests\Fixer\ArrayNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer
 */
final class TrailingCommaInMultilineArrayFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideExamples()
    {
        return [
            // long syntax tests
            ['<?php $x = array();'],
            ['<?php $x = array("foo");'],
            ['<?php $x = array("foo", );'],
            ["<?php \$x = array(\n'foo',\n);", "<?php \$x = array(\n'foo'\n);"],
            ["<?php \$x = array('foo',\n);"],
            ["<?php \$x = array('foo',\n);", "<?php \$x = array('foo'\n);"],
            ["<?php \$x = array('foo', /* boo */\n);", "<?php \$x = array('foo' /* boo */\n);"],
            ["<?php \$x = array('foo',\n/* boo */\n);", "<?php \$x = array('foo'\n/* boo */\n);"],
            ["<?php \$x = array(\narray('foo',\n),\n);", "<?php \$x = array(\narray('foo'\n)\n);"],
            ["<?php \$x = array(\narray('foo'),\n);", "<?php \$x = array(\narray('foo')\n);"],
            ["<?php \$x = array(\n /* He */ \n);"],
            [
                "<?php \$x = array('a', 'b', 'c',\n  'd', 'q', 'z', );",
                "<?php \$x = array('a', 'b', 'c',\n  'd', 'q', 'z');",
            ],
            [
                "<?php \$x = array('a', 'b', 'c',\n'd', 'q', 'z', );",
                "<?php \$x = array('a', 'b', 'c',\n'd', 'q', 'z');",
            ],
            [
                "<?php \$x = array('a', 'b', 'c',\n'd', 'q', 'z', );",
                "<?php \$x = array('a', 'b', 'c',\n'd', 'q', 'z' );",
            ],
            [
                "<?php \$x = array('a', 'b', 'c',\n'd', 'q', 'z',\t);",
                "<?php \$x = array('a', 'b', 'c',\n'd', 'q', 'z'\t);",
            ],
            ["<?php \$x = array(\n<<<EOT\noet\nEOT\n);"],
            ["<?php \$x = array(\n<<<'EOT'\noet\nEOT\n);"],
            [
                '<?php
    $foo = array(
        array(
        ),
    );',
            ],
            [
                '<?php
    $a = array(
        1 => array(
            2 => 3,
        ),
    );',
                '<?php
    $a = array(
        1 => array(
            2 => 3
        )
    );',
            ],
            [
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
            ],
            [
                '<?php

                $a = array("foo" => function ($b) {
                    return "bar".$b;
                });',
            ],
            [
                '<?php
    return array(
        "a" => 1,
        "b" => 2,
    );',
                '<?php
    return array(
        "a" => 1,
        "b" => 2
    );',
            ],
            [
                '<?php
    $test = array("foo", <<<TWIG
        foo
        bar
        baz
TWIG
        , $twig);',
            ],
            [
                '<?php
    $test = array("foo", <<<\'TWIG\'
        foo
        bar
        baz
TWIG
        , $twig);',
            ],

            // short syntax tests
            ['<?php $x = array([]);'],
            ['<?php $x = [[]];'],
            ['<?php $x = ["foo",];'],
            ['<?php $x = bar(["foo",]);'],
            ["<?php \$x = bar(['foo',\n]);", "<?php \$x = bar(['foo'\n]);"],
            ["<?php \$x = ['foo', \n];"],
            ['<?php $x = array([],);'],
            ['<?php $x = [[],];'],
            ['<?php $x = [$y,];'],
            ["<?php \$x = [\n /* He */ \n];"],
            [
                '<?php
    $foo = [
        [
        ],
    ];',
            ],
            [
                '<?php

                $a = ["foo" => function ($b) {
                    return "bar".$b;
                }];',
            ],
            [
                '<?php
    return [
        "a" => 1,
        "b" => 2,
    ];',
                '<?php
    return [
        "a" => 1,
        "b" => 2
    ];',
            ],
            [
                '<?php
    $test = ["foo", <<<TWIG
        foo
        bar
        baz
TWIG
        , $twig];',
            ],
            [
                '<?php
    $test = ["foo", <<<\'TWIG\'
        foo
        bar
        baz
TWIG
        , $twig];',
            ],

            // no array tests
            [
                "<?php
    throw new BadMethodCallException(
        sprintf(
            'Method \"%s\" not implemented',
            __METHOD__
        )
    );",
            ],
            [
                "<?php
    throw new BadMethodCallException(sprintf(
        'Method \"%s\" not implemented',
        __METHOD__
    ));",
            ],
            [
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
            ],
            [
                '<?php
    function foo(array $a)
    {
        bar(
            baz(
                1
            )
        );
    }',
            ],
            [
                '<?php
    $var = array(
        "string",
        //comment
    );',
                '<?php
    $var = array(
        "string"
        //comment
    );',
            ],
            [
                '<?php
    $var = array(
        "string",
        /* foo */);',
                '<?php
    $var = array(
        "string"
        /* foo */);',
            ],
            [
                '<?php
    $var = [
        "string",
        /* foo */];',
                '<?php
    $var = [
        "string"
        /* foo */];',
            ],
            [
                '<?php
function a()
{
    yield array(
        "a" => 1,
        "b" => 2,
    );
}',
                '<?php
function a()
{
    yield array(
        "a" => 1,
        "b" => 2
    );
}',
            ],
            [
                '<?php
function a()
{
    yield [
        "a" => 1,
        "b" => 2,
    ];
}',
                '<?php
function a()
{
    yield [
        "a" => 1,
        "b" => 2
    ];
}',
            ],
        ];
    }
}
