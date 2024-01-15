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

    public static function provideFixCases(): iterable
    {
        // long syntax tests
        yield ['<?php $x = array();'];

        yield ['<?php $x = array("foo");'];

        yield ['<?php $x = array("foo", );'];

        yield ["<?php \$x = array(\n'foo',\n);", "<?php \$x = array(\n'foo'\n);"];

        yield ["<?php \$x = array('foo',\n);"];

        yield ["<?php \$x = array('foo',\n);", "<?php \$x = array('foo'\n);"];

        yield ["<?php \$x = array('foo', /* boo */\n);", "<?php \$x = array('foo' /* boo */\n);"];

        yield ["<?php \$x = array('foo',\n/* boo */\n);", "<?php \$x = array('foo'\n/* boo */\n);"];

        yield ["<?php \$x = array(\narray('foo',\n),\n);", "<?php \$x = array(\narray('foo'\n)\n);"];

        yield ["<?php \$x = array(\narray('foo'),\n);", "<?php \$x = array(\narray('foo')\n);"];

        yield ["<?php \$x = array(\n /* He */ \n);"];

        yield [
            "<?php \$x = array('a', 'b', 'c',\n  'd', 'q', 'z');",
        ];

        yield [
            "<?php \$x = array('a', 'b', 'c',\n'd', 'q', 'z');",
        ];

        yield [
            "<?php \$x = array('a', 'b', 'c',\n'd', 'q', 'z' );",
        ];

        yield [
            "<?php \$x = array('a', 'b', 'c',\n'd', 'q', 'z'\t);",
        ];

        yield ["<?php \$x = array(\n<<<EOT\noet\nEOT\n);"];

        yield ["<?php \$x = array(\n<<<'EOT'\noet\nEOT\n);"];

        yield [
            <<<'EOD'
                <?php
                    $foo = array(
                        array(
                        ),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = array(
                        1 => array(
                            2 => 3,
                        ),
                    );
                EOD,
            <<<'EOD'
                <?php
                    $a = array(
                        1 => array(
                            2 => 3
                        )
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $x = array(
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
                    );
                EOD,
            <<<'EOD'
                <?php
                    $x = array(
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
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                $a = array("foo" => function ($b) {
                                    return "bar".$b;
                                });
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return array(
                        "a" => 1,
                        "b" => 2,
                    );
                EOD,
            <<<'EOD'
                <?php
                    return array(
                        "a" => 1,
                        "b" => 2
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $test = array("foo", <<<TWIG
                        foo
                        bar
                        baz
                TWIG
                        , $twig);
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $test = array("foo", <<<'TWIG'
                        foo
                        bar
                        baz
                TWIG
                        , $twig);
                EOD,
        ];

        // short syntax tests
        yield ['<?php $x = array([]);'];

        yield ['<?php $x = [[]];'];

        yield ['<?php $x = ["foo",];'];

        yield ['<?php $x = bar(["foo",]);'];

        yield ["<?php \$x = bar(['foo',\n]);", "<?php \$x = bar(['foo'\n]);"];

        yield ["<?php \$x = ['foo', \n];"];

        yield ['<?php $x = array([],);'];

        yield ['<?php $x = [[],];'];

        yield ['<?php $x = [$y,];'];

        yield ["<?php \$x = [\n /* He */ \n];"];

        yield [
            <<<'EOD'
                <?php
                    $foo = [
                        [
                        ],
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                $a = ["foo" => function ($b) {
                                    return "bar".$b;
                                }];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return [
                        "a" => 1,
                        "b" => 2,
                    ];
                EOD,
            <<<'EOD'
                <?php
                    return [
                        "a" => 1,
                        "b" => 2
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $test = ["foo", <<<TWIG
                        foo
                        bar
                        baz
                TWIG
                        , $twig];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $test = ["foo", <<<'TWIG'
                        foo
                        bar
                        baz
                TWIG
                        , $twig];
                EOD,
        ];

        // no array tests
        yield [
            <<<'EOD'
                <?php
                    throw new BadMethodCallException(
                        sprintf(
                            'Method "%s" not implemented',
                            __METHOD__
                        )
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    throw new BadMethodCallException(sprintf(
                        'Method "%s" not implemented',
                        __METHOD__
                    ));
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                    namespace FOS\RestBundle\Controller;

                    class ExceptionController extends ContainerAware
                    {
                        public function showAction(Request $request, $exception, DebugLoggerInterface $logger = null, $format = 'html')
                        {
                            if (!$exception instanceof DebugFlattenException && !$exception instanceof HttpFlattenException) {
                                throw new \InvalidArgumentException(sprintf(
                                    'ExceptionController::showAction can only accept some exceptions (%s, %s), "%s" given',
                                    'Symfony\Component\HttpKernel\Exception\FlattenException',
                                    'Symfony\Component\Debug\Exception\FlattenException',
                                    get_class($exception)
                                ));
                            }
                        }
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    function foo(array $a)
                    {
                        bar(
                            baz(
                                1
                            )
                        );
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = array(
                        "string",
                        //comment
                    );
                EOD,
            <<<'EOD'
                <?php
                    $var = array(
                        "string"
                        //comment
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = array(
                        "string",
                        /* foo */);
                EOD,
            <<<'EOD'
                <?php
                    $var = array(
                        "string"
                        /* foo */);
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [
                        "string",
                        /* foo */];
                EOD,
            <<<'EOD'
                <?php
                    $var = [
                        "string"
                        /* foo */];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function a()
                {
                    yield array(
                        "a" => 1,
                        "b" => 2,
                    );
                }
                EOD,
            <<<'EOD'
                <?php
                function a()
                {
                    yield array(
                        "a" => 1,
                        "b" => 2
                    );
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function a()
                {
                    yield [
                        "a" => 1,
                        "b" => 2,
                    ];
                }
                EOD,
            <<<'EOD'
                <?php
                function a()
                {
                    yield [
                        "a" => 1,
                        "b" => 2
                    ];
                }
                EOD,
        ];

        yield [<<<'EOD'
            <?php
            while(
            (
            (
            $a
            )
            )
            ) {}
            EOD,
        ];

        yield [
            "<?php foo('a', 'b', 'c', 'd', 'q', 'z');",
            null,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            "<?php function foo(\$a,\n\$b\n) {};",
            null,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            <<<'EOD'
                <?php foo(1, 2, [
                                    ARRAY_ELEMENT_1,
                                    ARRAY_ELEMENT_2
                                    ], 3, 4);
                EOD,
            null,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            "<?php \$var = array('a', 'b',\n  'c', 'd');",
            null,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            "<?php \$var = list(\$a, \$b,\n \$c, \$d) = [1, 2, 3, 4];",
            null,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            "<?php if (true || \n false) {}",
            null,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            "<?php \$var = foo('a', 'b', 'c',\n  'd', 'q', 'z');",
            null, // do not fix if not configured
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARRAYS]],
        ];

        yield [
            "<?php \$var = foo('a', 'b', 'c',\n  'd', 'q', 'z');",
            null,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            "<?php \$var = foo('a', 'b', 'c',\n  'd', 'q', 'z',\n);",
            "<?php \$var = foo('a', 'b', 'c',\n  'd', 'q', 'z'\n);",
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            "<?php \$var = \$foonction('a', 'b', 'c',\n  'd', 'q', 'z');",
            null,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            "<?php \$var = \$fMap[100]('a', 'b', 'c',\n  'd', 'q', 'z');",
            null,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            "<?php \$var = new Foo('a', 'b', 'c',\n  'd', 'q', 'z',\n);",
            "<?php \$var = new Foo('a', 'b', 'c',\n  'd', 'q', 'z'\n);",
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            "<?php \$var = new class('a', 'b', 'c',\n  'd', 'q', 'z',\n) extends Foo {};",
            "<?php \$var = new class('a', 'b', 'c',\n  'd', 'q', 'z'\n) extends Foo {};",
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            <<<'EOD'
                <?php
                                    $obj->method(
                                        1,
                                        2,
                                    );
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $obj->method(
                                        1,
                                        2
                                    );
                EOD."\n                ",
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            <<<'EOD'
                <?php
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
                EOD."\n                ",
            <<<'EOD'
                <?php
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
                EOD."\n                ",
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARRAYS, TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            <<<'EOD'
                <?php
                while(
                (
                (
                $a
                )
                )
                ) {}
                EOD,
            null,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARRAYS, TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            <<<'EXPECTED'
                <?php
                $a = [
                    <<<'EOD'
                        foo
                        EOD,
                ];
                EXPECTED,
            <<<'INPUT'
                <?php
                $a = [
                    <<<'EOD'
                        foo
                        EOD
                ];
                INPUT,
            ['after_heredoc' => true],
        ];

        yield [
            <<<'EOD'
                <?php $a = new class() {function A() { return new static(
                1,
                2,
                ); }};
                EOD,
            <<<'EOD'
                <?php $a = new class() {function A() { return new static(
                1,
                2
                ); }};
                EOD,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
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
            <<<'EOD'
                <?php function foo(
                                    $x,
                                    $y
                                ) {}
                EOD,
            null, // do not fix if not configured
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_ARRAYS, TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            <<<'EOD'
                <?php function foo(
                                    $x,
                                    $y,
                                ) {}
                EOD,
            <<<'EOD'
                <?php function foo(
                                    $x,
                                    $y
                                ) {}
                EOD,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_PARAMETERS]],
        ];

        yield [
            <<<'EOD'
                <?php $x = function(
                                    $x,
                                    $y,
                                ) {};
                EOD,
            <<<'EOD'
                <?php $x = function(
                                    $x,
                                    $y
                                ) {};
                EOD,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_PARAMETERS]],
        ];

        yield [
            <<<'EOD'
                <?php $x = fn(
                                    $x,
                                    $y,
                                ) => $x + $y;
                EOD,
            <<<'EOD'
                <?php $x = fn(
                                    $x,
                                    $y
                                ) => $x + $y;
                EOD,
            ['elements' => [TrailingCommaInMultilineFixer::ELEMENTS_PARAMETERS]],
        ];

        yield 'match' => [
            <<<'EOD'
                <?php
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
                EOD."\n            ",
            <<<'EOD'
                <?php
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
                EOD."\n            ",
            ['elements' => ['match']],
        ];

        yield 'match with last comma in the same line as closing brace' => [
            <<<'EOD'
                <?php
                $x = match ($a) { 1 => 0,
                                  2 => 1 };
                EOD."\n            ",
            null,
            ['elements' => ['match']],
        ];
    }
}
