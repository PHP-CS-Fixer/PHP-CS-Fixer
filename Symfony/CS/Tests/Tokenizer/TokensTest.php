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
    public function bar()
    {
        $a = 5;

        return " ({$a})";
    }
    public function baz($data)
    {
    }
}
PHP;

        $tokens = Tokens::fromCode($source);
        $elements = $tokens->getClassyElements();

        $this->assertCount(2, $elements);

        foreach ($elements as $element) {
            $this->assertSame('method', $element['type']);
        }
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
}
