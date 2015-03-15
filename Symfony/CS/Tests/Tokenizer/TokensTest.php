<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Tokenizer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Max Voloshin <voloshin.dp@gmail.com>
 */
class TokensTest extends \PHPUnit_Framework_TestCase
{
    public function testGetClassyElements()
    {
        $source = <<<'PHP'
<?php
class Foo
{
    public $prop0;
    protected $prop1;
    private $prop2 = 1;
    var $prop3 = array(1,2,3);

    public function bar4()
    {
        $a = 5;

        return " ({$a})";
    }
    public function bar5($data)
    {
    }
}
PHP;

        $tokens = Tokens::fromCode($source);
        $elements = array_values($tokens->getClassyElements());

        $this->assertCount(6, $elements);
        $this->assertSame('property', $elements[0]['type']);
        $this->assertSame('property', $elements[1]['type']);
        $this->assertSame('property', $elements[2]['type']);
        $this->assertSame('property', $elements[3]['type']);
        $this->assertSame('method', $elements[4]['type']);
        $this->assertSame('method', $elements[5]['type']);
    }

    public function testReadFromCacheAfterClearing()
    {
        $code = '<?php echo 1;';
        $tokens = Tokens::fromCode($code);

        $countBefore = $tokens->count();

        for ($i = 0; $i < $countBefore; ++$i) {
            $tokens[$i]->clear();
        }

        $tokens = Tokens::fromCode($code);

        $this->assertSame($countBefore, $tokens->count());
    }

    /**
     * @dataProvider provideIsLambdaCases
     */
    public function testIsLambda($source, array $expected)
    {
        $tokens = Tokens::fromCode($source);

        foreach ($expected as $index => $expected) {
            $this->assertSame($expected, $tokens->isLambda($index));
        }
    }

    public function provideIsLambdaCases()
    {
        return array(
            array(
                '<?php function foo () {}',
                array(1 => false),
            ),
            array(
                '<?php function /** foo */ foo () {}',
                array(1 => false),
            ),
            array(
                '<?php $foo = function () {}',
                array(5 => true),
            ),
            array(
                '<?php $foo = function /** foo */ () {}',
                array(5 => true),
            ),
            array(
                '<?php
preg_replace_callback(
    "/(^|[a-z])/",
    function (array $matches) {
        return "a";
    },
    $string
);',
                array(7 => true),
            ),
            array(
                '<?php $foo = function &() {}',
                array(5 => true),
            ),
        );
    }

    /**
     * @dataProvider provideIsShortArrayCases
     */
    public function testIsShortArray($source, array $expected)
    {
        $tokens = Tokens::fromCode($source);

        foreach ($expected as $index => $expected) {
            $this->assertSame($expected, $tokens->isShortArray($index));
        }
    }

    public function provideIsShortArrayCases()
    {
        return array(
            array(
                '<?php [];',
                array(1 => true),
            ),
            array(
                '<?php [1, "foo"];',
                array(1 => true),
            ),
            array(
                '<?php [[]];',
                array(1 => true, 2 => true),
            ),
            array(
                '<?php ["foo", ["bar", "baz"]];',
                array(1 => true, 5 => true),
            ),
            array(
                '<?php (array) [1, 2];',
                array(3 => true),
            ),
            array(
                '<?php [1,2][$x];',
                array(1 => true, 6 => false),
            ),
            array(
                '<?php array();',
                array(1 => false),
            ),
            array(
                '<?php $x[] = 1;',
                array(2 => false),
            ),
            array(
                '<?php $x[1];',
                array(2 => false),
            ),
            array(
                '<?php $x [ 1 ];',
                array(3 => false),
            ),
            array(
                '<?php ${"x"}[1];',
                array(5 => false),
            ),
            array(
                '<?php FOO[1];',
                array(2 => false),
            ),
            array(
                '<?php array("foo")[1];',
                array(5 => false),
            ),
            array(
                '<?php foo()[1];',
                array(4 => false),
            ),
            array(
                '<?php \'foo\'[1];',
                array(2 => false),
            ),
            array(
                '<?php "foo$bar"[1];',
                array(5 => false),
            ),
        );
    }

    /**
     * @dataProvider provideFindSequence
     */
    public function testFindSequence($source, $expected, array $params)
    {
        $tokens = Tokens::fromCode($source);

        $this->assertEquals($expected, call_user_func_array(array($tokens, 'findSequence'), $params));
    }

    public function provideFindSequence()
    {
        return array(
            array(
                '<?php $x = 1;',
                null,
                array(array(
                    array(T_OPEN_TAG),
                    array(T_VARIABLE, '$y'),
                )),
            ),

            array(
                '<?php $x = 1;',
                array(
                    0 => new Token(array(T_OPEN_TAG, '<?php ', 1)),
                    1 => new Token(array(T_VARIABLE, '$x', 1)),
                ),
                array(array(
                    array(T_OPEN_TAG),
                    array(T_VARIABLE, '$x'),
                )),
            ),

            array(
                '<?php $x = 1;',
                array(
                    3 => new Token('='),
                    5 => new Token(array(T_LNUMBER, '1', 1)),
                    6 => new Token(';'),
                ),
                array(array(
                    '=',
                    array(T_LNUMBER, '1'),
                    ';',
                )),
            ),

            array(
                '<?php $x = 1;',
                array(
                    0 => new Token(array(T_OPEN_TAG, '<?php ', 1)),
                    1 => new Token(array(T_VARIABLE, '$x', 1)),
                ),
                array(array(
                    array(T_OPEN_TAG),
                    array(T_VARIABLE, '$x'),
                ), 0),
            ),

            array(
                '<?php $x = 1;',
                null,
                array(array(
                    array(T_OPEN_TAG),
                    array(T_VARIABLE, '$x'),
                ), 1),
            ),

            array(
                '<?php $x = 1;',
                array(
                    3 => new Token('='),
                    5 => new Token(array(T_LNUMBER, '1', 1)),
                    6 => new Token(';'),
                ),
                array(array(
                    '=',
                    array(T_LNUMBER, '1'),
                    ';',
                ), 3, 6),
            ),

            array(
                '<?php $x = 1;',
                null,
                array(array(
                    '=',
                    array(T_LNUMBER, '1'),
                    ';',
                ), 4, 6),
            ),

            array(
                '<?php $x = 1;',
                null,
                array(array(
                    '=',
                    array(T_LNUMBER, '1'),
                    ';',
                ), 3, 5),
            ),

            array(
                '<?php $x = 1;',
                array(
                    0 => new Token(array(T_OPEN_TAG, '<?php ', 1)),
                    1 => new Token(array(T_VARIABLE, '$x', 1)),
                ),
                array(array(
                    array(T_OPEN_TAG),
                    array(T_VARIABLE, '$x'),
                ), 0, 1, true),
            ),

            array(
                '<?php $x = 1;',
                null,
                array(array(
                    array(T_OPEN_TAG),
                    array(T_VARIABLE, '$X'),
                ), 0, 1, true),
            ),

            array(
                '<?php $x = 1;',
                null,
                array(array(
                    array(T_OPEN_TAG),
                    array(T_VARIABLE, '$X'),
                ), 0, 1, array(true, true)),
            ),

            array(
                '<?php $x = 1;',
                array(
                    0 => new Token(array(T_OPEN_TAG, '<?php ', 1)),
                    1 => new Token(array(T_VARIABLE, '$x', 1)),
                ),
                array(array(
                    array(T_OPEN_TAG),
                    array(T_VARIABLE, '$X'),
                ), 0, 1, false),
            ),

            array(
                '<?php $x = 1;',
                array(
                    0 => new Token(array(T_OPEN_TAG, '<?php ', 1)),
                    1 => new Token(array(T_VARIABLE, '$x', 1)),
                ),
                array(array(
                    array(T_OPEN_TAG),
                    array(T_VARIABLE, '$X'),
                ), 0, 1, array(true, false)),
            ),

            array(
                '<?php $x = 1;',
                array(
                    0 => new Token(array(T_OPEN_TAG, '<?php ', 1)),
                    1 => new Token(array(T_VARIABLE, '$x', 1)),
                ),
                array(array(
                    array(T_OPEN_TAG),
                    array(T_VARIABLE, '$X'),
                ), 0, 1, array(1 => false)),
            ),

            array(
                '<?php $x = 1;',
                null,
                array(array(
                    array(T_OPEN_TAG),
                    array(T_VARIABLE, '$X'),
                ), 0, 1, array(2 => false)),
            ),
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider provideFindSequenceExceptions
     */
    public function testFindSequenceException($message, $sequence)
    {
        $tokens = Tokens::fromCode('<?php $x = 1;');
        try {
            $tokens->findSequence($sequence);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame($message, $e->getMessage());
            throw $e;
        }
    }

    public function provideFindSequenceExceptions()
    {
        $emptyToken = new Token('!');
        $emptyToken->clear();

        return array(
            array("Invalid sequence", array()),
            array("Non-meaningful token at position: 0", array(
                array(T_WHITESPACE, "   "),
            )),
            array("Non-meaningful token at position: 1", array(
                '{', array(T_COMMENT, "// Foo"), '}',
            )),
            array("Non-meaningful token at position: 2", array(
                '{', '!', $emptyToken, '}',
            )),
        );
    }

    public function testClearRange()
    {
        $source = <<<'PHP'
<?php
class FooBar
{
    public function foo()
    {
        return 'bar';
    }

    public function bar()
    {
        return 'foo';
    }
}
PHP;

        $tokens = Tokens::fromCode($source);
        $publicIndexes = array_keys($tokens->findGivenKind(T_PUBLIC));
        $fooIndex = $publicIndexes[0];
        $barIndex = $publicIndexes[1];

        $tokens->clearRange($fooIndex, $barIndex - 1);

        $newPublicIndexes = array_keys($tokens->findGivenKind(T_PUBLIC));
        $this->assertEquals($barIndex, reset($newPublicIndexes));

        for ($i = $fooIndex; $i < $barIndex; $i++) {
            $this->assertTrue($tokens[$i]->isWhiteSpace());
        }
    }

    /**
     * @dataProvider provideMonolithicPhpDetection
     *
     * @param string $source
     * @param bool   $monolitic
     */
    public function testMonolithicPhpDetection($source, $monolitic)
    {
        $tokens = Tokens::fromCode($source);
        $this->assertSame($monolitic, $tokens->isMonolithicPhp());
    }

    public function provideMonolithicPhpDetection()
    {
        return array(
            array("<?php\n", true),
            array("<?php\n?>", true),
            array("", false),
            array(" ", false),
            array("#!/usr/bin/env php\n<?php\n", false),
            array(" <?php\n", false),
            array("<?php\n?> ", false),
            array("<?php\n?><?php\n", false),
        );
    }

    /**
     * @dataProvider provideShortOpenTagMonolithicPhpDetection
     *
     * @param string $source
     * @param bool   $monolitic
     */
    public function testShortOpenTagMonolithicPhpDetection($source, $monolitic)
    {
        /*
         * short_open_tag setting is ignored by HHVM
         * @see https://github.com/facebook/hhvm/issues/4758
         */
        if (!ini_get('short_open_tag') && !defined('HHVM_VERSION')) {
            // Short open tag is parsed as T_INLINE_HTML
            $monolitic = false;
        }

        $tokens = Tokens::fromCode($source);
        $this->assertSame($monolitic, $tokens->isMonolithicPhp());
    }

    public function provideShortOpenTagMonolithicPhpDetection()
    {
        return array(
            array("<?\n", true),
            array("<?\n?>", true),
            array(" <?\n", false),
            array("<?\n?> ", false),
            array("<?\n?><?\n", false),
            array("<?\n?><?php\n", false),
            array("<?\n?><?=' ';\n", false),
            array("<?php\n?><?\n", false),
            array("<?=' '\n?><?\n", false),
        );
    }

    /**
     * @dataProvider provideShortOpenTagEchoMonolithicPhpDetection
     *
     * @param string $source
     * @param bool   $monolitic
     */
    public function testShortOpenTagEchoMonolithicPhpDetection($source, $monolitic)
    {
        /*
         * short_open_tag setting is ignored by HHVM
         * @see https://github.com/facebook/hhvm/issues/4758
         */
        if (!ini_get('short_open_tag') && 50400 > PHP_VERSION_ID && !defined('HHVM_VERSION')) {
            // Short open tag echo is parsed as T_INLINE_HTML
            $monolitic = false;
        }

        $tokens = Tokens::fromCode($source);
        $this->assertSame($monolitic, $tokens->isMonolithicPhp());
    }

    public function provideShortOpenTagEchoMonolithicPhpDetection()
    {
        return array(
            array("<?=' ';\n", true),
            array("<?=' '?>", true),
            array(" <?=' ';\n", false),
            array("<?=' '?> ", false),
            array("<?php\n?><?=' ';\n", false),
            array("<?=' '\n?><?php\n", false),
            array("<?=' '\n?><?=' ';\n", false),
        );
    }
}
