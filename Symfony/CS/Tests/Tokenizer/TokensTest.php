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
 *
 * @internal
 */
final class TokensTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param Token[]|null $expected
     * @param Token[]|null $input
     */
    private function assertEqualsTokensArray(array $expected = null, array $input = null)
    {
        if (null === $expected) {
            $this->assertNull($input);

            return;
        }

        $this->assertSame(array_keys($expected), array_keys($input), 'Both arrays need to have same keys.');

        foreach ($expected as $index => $expectedToken) {
            $this->assertTrue(
                $expectedToken->equals($input[$index]),
                sprintf('The token at index %d should be %s, got %s', $index, $expectedToken->toJson(), $input[$index]->toJson())
            );
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
     * @dataProvider provideFindSequence
     */
    public function testFindSequence($source, $expected, array $params)
    {
        $tokens = Tokens::fromCode($source);

        $this->assertEqualsTokensArray($expected, call_user_func_array(array($tokens, 'findSequence'), $params));
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
                    0 => new Token(array(T_OPEN_TAG, '<?php ')),
                    1 => new Token(array(T_VARIABLE, '$x')),
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
                    5 => new Token(array(T_LNUMBER, '1')),
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
                    0 => new Token(array(T_OPEN_TAG, '<?php ')),
                    1 => new Token(array(T_VARIABLE, '$x')),
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
                    5 => new Token(array(T_LNUMBER, '1')),
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
                    0 => new Token(array(T_OPEN_TAG, '<?php ')),
                    1 => new Token(array(T_VARIABLE, '$x')),
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
                    0 => new Token(array(T_OPEN_TAG, '<?php ')),
                    1 => new Token(array(T_VARIABLE, '$x')),
                ),
                array(array(
                    array(T_OPEN_TAG),
                    array(T_VARIABLE, '$X'),
                ), 0, 1, false),
            ),

            array(
                '<?php $x = 1;',
                array(
                    0 => new Token(array(T_OPEN_TAG, '<?php ')),
                    1 => new Token(array(T_VARIABLE, '$x')),
                ),
                array(array(
                    array(T_OPEN_TAG),
                    array(T_VARIABLE, '$X'),
                ), 0, 1, array(true, false)),
            ),

            array(
                '<?php $x = 1;',
                array(
                    0 => new Token(array(T_OPEN_TAG, '<?php ')),
                    1 => new Token(array(T_VARIABLE, '$x')),
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
            array('Invalid sequence', array()),
            array('Non-meaningful token at position: 0', array(
                array(T_WHITESPACE, '   '),
            )),
            array('Non-meaningful token at position: 1', array(
                '{', array(T_COMMENT, '// Foo'), '}',
            )),
            array('Non-meaningful token at position: 2', array(
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
        $this->assertSame($barIndex, reset($newPublicIndexes));

        for ($i = $fooIndex; $i < $barIndex; ++$i) {
            $this->assertTrue($tokens[$i]->isWhitespace());
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
            array('', false),
            array(' ', false),
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

    public function testTokenKindsFound()
    {
        $code = <<<'EOF'
<?php

class Foo
{
    public $foo;
}

if (!function_exists('bar')) {
    function bar()
    {
        return 'bar';
    }
}
EOF;

        $tokens = Tokens::fromCode($code);

        $this->assertTrue($tokens->isTokenKindFound(T_CLASS));
        $this->assertTrue($tokens->isTokenKindFound(T_RETURN));
        $this->assertFalse($tokens->isTokenKindFound(T_INTERFACE));
        $this->assertFalse($tokens->isTokenKindFound(T_ARRAY));

        $this->assertTrue($tokens->isAllTokenKindsFound(array(T_CLASS, T_RETURN)));
        $this->assertFalse($tokens->isAllTokenKindsFound(array(T_CLASS, T_INTERFACE)));

        $this->assertTrue($tokens->isAnyTokenKindsFound(array(T_CLASS, T_RETURN)));
        $this->assertTrue($tokens->isAnyTokenKindsFound(array(T_CLASS, T_INTERFACE)));
        $this->assertFalse($tokens->isAnyTokenKindsFound(array(T_INTERFACE, T_ARRAY)));
    }
}
