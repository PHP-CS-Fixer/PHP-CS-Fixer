<?php

namespace PhpCsFixer\tests\Tokenizer\Generator;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Generator\NamespacedStringTokenGenerator;
use PhpCsFixer\Tokenizer\Token;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Generator\NamespacedStringTokenGenerator
 */
class NamespacedStringTokenGeneratorTest extends TestCase
{
    /**
     * @param $input
     * @param array $expected
     * @dataProvider provideGeneratorCases
     */
    public function testGenerator($input, array $expected)
    {
        $generator = new NamespacedStringTokenGenerator();
        $this->assertSame(
            $expected,
            array_map(
                function (Token $token) {
                    return $token->getContent();
                },
                $generator->generate($input)
            )
        );
    }

    public function provideGeneratorCases()
    {
        return [
            ['test', ['test']],
            ['Some\\Namespace', ['Some', '\\', 'Namespace']],
            ['Some\\Bigger\\Namespace', ['Some', '\\', 'Bigger', '\\', 'Namespace']],
        ];
    }
}
