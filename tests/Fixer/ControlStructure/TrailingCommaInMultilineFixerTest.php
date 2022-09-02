<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer
 */
final class TrailingCommaInMultilineFixerTest extends AbstractFixerTestCase
{
    /**
     * @requires PHP <8.0
     *
     * @dataProvider provideInvalidConfigurationCases
     *
     * @param mixed $exceptionMessega
     * @param mixed $configuration
     */
    public function testInvalidConfiguration($exceptionMessega, $configuration): void
    {
        $this->expectException(InvalidForEnvFixerConfigurationException::class);
        $this->expectExceptionMessage($exceptionMessega);

        $this->fixer->configure($configuration);
    }

    public static function provideInvalidConfigurationCases(): iterable
    {
        yield [
            '[trailing_comma_in_multiline] Invalid configuration for env: "parameters" option can only be enabled with PHP 8.0+.',
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_PARAMETERS]],
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
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
            ['<?php
while(
(
(
$a
)
)
) {}',
            ],
            [
                "<?php foo('a', 'b', 'c', 'd', 'q', 'z');",
                null,
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                "<?php function foo(\$a,\n\$b\n) {};",
                null,
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                '<?php foo(1, 2, [
                    ARRAY_ELEMENT_1,
                    ARRAY_ELEMENT_2
                    ], 3, 4);',
                null,
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                "<?php \$var = array('a', 'b',\n  'c', 'd');",
                null,
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                "<?php \$var = list(\$a, \$b,\n \$c, \$d) = [1, 2, 3, 4];",
                null,
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                "<?php if (true || \n false) {}",
                null,
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                "<?php \$var = foo('a', 'b', 'c',\n  'd', 'q', 'z');",
                null, // do not fix if not configured
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARRAYS]],
            ],
            [
                "<?php \$var = foo('a', 'b', 'c',\n  'd', 'q', 'z', );",
                "<?php \$var = foo('a', 'b', 'c',\n  'd', 'q', 'z');",
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                "<?php \$var = foo('a', 'b', 'c',\n  'd', 'q', 'z',\n);",
                "<?php \$var = foo('a', 'b', 'c',\n  'd', 'q', 'z'\n);",
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                "<?php \$var = \$foonction('a', 'b', 'c',\n  'd', 'q', 'z', );",
                "<?php \$var = \$foonction('a', 'b', 'c',\n  'd', 'q', 'z');",
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                "<?php \$var = \$fMap[100]('a', 'b', 'c',\n  'd', 'q', 'z', );",
                "<?php \$var = \$fMap[100]('a', 'b', 'c',\n  'd', 'q', 'z');",
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                "<?php \$var = new Foo('a', 'b', 'c',\n  'd', 'q', 'z',\n);",
                "<?php \$var = new Foo('a', 'b', 'c',\n  'd', 'q', 'z'\n);",
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                "<?php \$var = new class('a', 'b', 'c',\n  'd', 'q', 'z',\n) extends Foo {};",
                "<?php \$var = new class('a', 'b', 'c',\n  'd', 'q', 'z'\n) extends Foo {};",
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                '<?php
                    $obj->method(
                        1,
                        2,
                    );
                ',
                '<?php
                    $obj->method(
                        1,
                        2
                    );
                ',
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                '<?php
                    array(
                        1,
                        2,
                    );
                    [
                        3,
                        4,
                    ];
                    foo(
                        5,
                        6,
                    );
                ',
                '<?php
                    array(
                        1,
                        2
                    );
                    [
                        3,
                        4
                    ];
                    foo(
                        5,
                        6
                    );
                ',
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARRAYS, TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                '<?php
while(
(
(
$a
)
)
) {}',
                null,
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARRAYS, TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
            [
                <<<'EXPECTED'
<?php
$a = [
    <<<'EOD'
        foo
        EOD,
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$a = [
    <<<'EOD'
        foo
        EOD
];
INPUT
                ,
                ['after_heredoc' => true],
            ],
            [
                '<?php $a = new class() {function A() { return new static(
1,
2,
); }};',
                '<?php $a = new class() {function A() { return new static(
1,
2
); }};',
                ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php function foo($x, $y) {}',
            null,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_PARAMETERS]],
        ];

        yield [
            '<?php function foo(
                    $x,
                    $y
                ) {}',
            null, // do not fix if not configured
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARRAYS, TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            '<?php function foo(
                    $x,
                    $y,
                ) {}',
            '<?php function foo(
                    $x,
                    $y
                ) {}',
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_PARAMETERS]],
        ];

        yield [
            '<?php $x = function(
                    $x,
                    $y,
                ) {};',
            '<?php $x = function(
                    $x,
                    $y
                ) {};',
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_PARAMETERS]],
        ];

        yield [
            '<?php $x = fn(
                    $x,
                    $y,
                ) => $x + $y;',
            '<?php $x = fn(
                    $x,
                    $y
                ) => $x + $y;',
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_PARAMETERS]],
        ];

        yield 'match' => [
            '<?php
$m = match ($a) {
    200, 300 => null,
    400 => 1,
    500 => function() {return 2;},
    600 => static function() {return 4;},
    default => 3,
};

$z = match ($a) {
    1 => 0,
    2 => 1,
};

$b = match($c) {19 => 28, default => 333};
            ',
            '<?php
$m = match ($a) {
    200, 300 => null,
    400 => 1,
    500 => function() {return 2;},
    600 => static function() {return 4;},
    default => 3
};

$z = match ($a) {
    1 => 0,
    2 => 1
};

$b = match($c) {19 => 28, default => 333};
            ',
            ['elements' => ['match']],
        ];
    }
}
