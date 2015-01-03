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

use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Tokenizer\TokensAnalyzer;

/**
 * @author Max Voloshin <voloshin.dp@gmail.com>
 */
class TokensAnalyzerTest extends \PHPUnit_Framework_TestCase
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
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $elements = $tokensAnalyzer->getClassyElements();

        $this->assertCount(2, $elements);

        foreach ($elements as $element) {
            $this->assertSame('method', $element['type']);
        }
    }

    /**
     * @dataProvider provideIsLambdaCases
     */
    public function testIsLambda($source, array $expected)
    {
        $tokensAnalyzer = new TokensAnalyzer(Tokens::fromCode($source));

        foreach ($expected as $index => $expected) {
            $this->assertSame($expected, $tokensAnalyzer->isLambda($index));
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
        );
    }
}
