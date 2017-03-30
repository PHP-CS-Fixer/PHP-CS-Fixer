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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 *
 * @internal
 */
final class NoSpacesAroundOffsetFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideInsideCases
     */
    public function testFixSpaceInsideOffset($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideOutsideCases
     */
    public function testFixSpaceOutsideOffset($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function testLeaveNewLinesAlone()
    {
        $expected = <<<'EOF'
<?php

class Foo
{
    private function bar()
    {
        if ([1, 2, 3] && [
            'foo',
            'bar' ,
            'baz'// a comment just to mix things up
        ]) {
            return 1;
        };
    }
}
EOF;
        $this->doTest($expected);
    }

    public function testLeaveCommentsAlone()
    {
        $expected = <<<'EOF'
<?php

$withComments[0] // here is a comment
    [1] // and here is another
    [2] = 3;
EOF;
        $this->doTest($expected);
    }

    public function testLeaveComplexString()
    {
        $expected = <<<'EOF'
<?php

echo "I am printing some spaces here    {$foo->bar[1]}     {$foo->bar[1]}.";
EOF;
        $this->doTest($expected);
    }

    public function testLeaveFunctions()
    {
        $expected = <<<'EOF'
<?php

function someFunc()    {   $someVar = [];   }
EOF;
        $this->doTest($expected);
    }

    public function provideOutsideCases()
    {
        return array(
            array(
                '<?php
$a = $b[0]    ;',
                '<?php
$a = $b   [0]    ;',
            ),
            array(
                '<?php
$a = array($b[0]     ,   $b[0]  );',
                '<?php
$a = array($b      [0]     ,   $b [0]  );',
            ),
            array(
                '<?php
$withComments[0] // here is a comment
    [1] // and here is another
    [2][3] = 4;',
                '<?php
$withComments [0] // here is a comment
    [1] // and here is another
    [2] [3] = 4;',
            ),
            array(
                '<?php
$c = SOME_CONST[0][1][2];',
                '<?php
$c = SOME_CONST [0] [1]   [2];',
            ),
            array(
                '<?php
$f = someFunc()[0][1][2];',
                '<?php
$f = someFunc() [0] [1]   [2];',
            ),
            array(
                '<?php
$foo[][0][1][2] = 3;',
                '<?php
$foo [] [0] [1]   [2] = 3;',
            ),
            array(
                '<?php
$foo[0][1][2] = 3;',
                '<?php
$foo [0] [1]   [2] = 3;',
            ),
            array(
                '<?php
$bar = $foo[0][1][2];',
                '<?php
$bar = $foo [0] [1]   [2];',
            ),
            array(
                '<?php
$baz[0][1][2] = 3;',
                '<?php
$baz [0]
     [1]
     [2] = 3;',
            ),
            array(
                '<?php
$foo{0}{1}{2} = 3;',
                '<?php
$foo {0} {1}   {2} = 3;',
            ),
            array(
                '<?php
$foobar = $foo{0}[1]{2};',
                '<?php
$foobar = $foo {0} [1]   {2};',
            ),
        );
    }

    public function provideInsideCases()
    {
        return array(
            array(
                '<?php
$foo = array(1, 2, 3);
$var = $foo[1];',
                '<?php
$foo = array(1, 2, 3);
$var = $foo[ 1 ];',
            ),
            array(
                '<?php
$arr = [2,   2 , ];
$var = $arr[0];',
                '<?php
$arr = [2,   2 , ];
$var = $arr[ 0 ];',
            ),
            array(
                '<?php
$arr[2] = 3;',
                '<?php
$arr[ 2    ] = 3;',
            ),
            array(
                '<?php
$arr[] = 3;',
                '<?php
$arr[  ] = 3;',
            ),
            array(
                '<?php
$arr[]["some_offset"][] = 3;',
                '<?php
$arr[  ][ "some_offset"   ][     ] = 3;',
            ),
            array(
                '<?php
$arr[]["some  offset with  spaces"][] = 3;',
                '<?php
$arr[  ][ "some  offset with  spaces"   ][     ] = 3;',
            ),
            array(
                '<?php
$var = $arr[0];',
                '<?php
$var = $arr[     0   ];',
            ),
            array(
                '<?php
$var = $arr[0][0];',
                '<?php
$var = $arr[    0        ][ 0  ];',
            ),
            array(
                '<?php
$var = $arr[$a[$b]];',
                '<?php
$var = $arr[    $a    [ $b    ]  ];',
            ),
            array(
                '<?php
$var = $arr[$a[$b]];',
                '<?php
$var = $arr[	$a	[	$b	]	];',
            ),
            array(
                '<?php
$var = $arr[0][
     0];',
                '<?php
$var = $arr[0][
     0 ];',
            ),
            array(
                '<?php
$var = $arr[0][0
         ];',
                '<?php
$var = $arr[0][     0
         ];',
            ),
            array(
                '<?php
$var = $arr[0]{0
         };',
                '<?php
$var = $arr[0]{     0
         };',
            ),
        );
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideConfigurationCases
     */
    public function testFixWithConfiguration(array $configuration, $expected, $input)
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public function provideConfigurationCases()
    {
        return array(
            array(
                array('inside', 'outside'),
                <<<'EOT'
<?php
$arr1[]["some_offset"][]{"foo"} = 3;
EOT
                ,
                <<<'EOT'
<?php
$arr1[  ]  [ "some_offset"   ] [     ] { "foo" } = 3;
EOT
                ,
            ),
            array(
                array('inside'),
                <<<'EOT'
<?php
$arr1[]  ["some_offset"] [] {"foo"} = 3;
EOT
                ,
                <<<'EOT'
<?php
$arr1[  ]  [ "some_offset"   ] [     ] { "foo" } = 3;
EOT
                ,
            ),
            array(
                array('outside'),
                <<<'EOT'
<?php
$arr1[  ][ "some_offset"   ][     ]{ "foo" } = 3;
EOT
                ,
                <<<'EOT'
<?php
$arr1[  ]  [ "some_offset"   ] [     ] { "foo" } = 3;
EOT
                ,
            ),
        );
    }

    public function testWrongConfig()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '/^\[no_spaces_around_offset\] Unknown configuration option "foo"\.$/'
        );

        $this->fixer->configure(array('foo'));
    }

    /**
     * @param array  $config
     * @param string $expected
     * @param string $input
     *
     * @dataProvider providePHP71Cases
     * @requires PHP 7.1
     */
    public function testPHP71($config, $expected, $input)
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function providePHP71Cases()
    {
        return array(
            'Config "default".' => array(
                array('inside', 'outside'),
                '<?php [ $a ] = $a;
if ($controllerName = $request->attributes->get(1)) {
    return false;
}
[  $class  ,   $method  ] = $this->splitControllerClassAndMethod($controllerName);
$a = $b[0];
',
                '<?php [ $a ] = $a;
if ($controllerName = $request->attributes->get(1)) {
    return false;
}
[  $class  ,   $method  ] = $this->splitControllerClassAndMethod($controllerName);
$a = $b   [0];
',
            ),
        );
    }
}
